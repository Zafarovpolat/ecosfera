#!/usr/bin/env python3
"""
Per-usage raster pipeline.

Figma encodes cropping as scaleMode STRETCH + an imageTransform matrix, and one
master can serve many different crops (image-35 is a six-cell spritesheet).
Rather than ship masters plus CSS crop math, we cut each usage into its own
right-sized WebP. Verified: the visible source region is
(tx, ty) → (tx+sx, ty+sy) in normalized coords, and its aspect matches the
layout box to 3 decimal places on every sampled node.
"""
import json, pathlib, hashlib
from PIL import Image

Image.MAX_IMAGE_PIXELS = None
MASTERS = pathlib.Path("masters")
OUT = pathlib.Path("ecosfera/public/images")
OUT.mkdir(parents=True, exist_ok=True)
for f in OUT.glob("*"):
    f.unlink()

manifest = json.load(open("spec/manifest.json"))
masters = manifest["masters"]
MAX_EDGE = 4000
QUALITY = 86

_open = {}
def master(ref):
    if ref not in _open:
        _open[ref] = Image.open(MASTERS / masters[ref])
    return _open[ref]

def slug(s):
    keep = "".join(c if (c.isalnum() or c in "-_") else "-" for c in s.lower())
    while "--" in keep:
        keep = keep.replace("--", "-")
    return keep.strip("-")[:48] or "img"

usages, dedupe = {}, {}
stats = {"cut": 0, "reused": 0, "bytes": 0}

for label in ("home", "mobile"):
    spec = json.load(open(f"spec/{label}.json"))
    for n in spec["nodes"]:
        b = n.get("box")
        if not b:
            continue
        for idx, f in enumerate(n["fills"]):
            if f["kind"] != "image" or not f.get("imageRef"):
                continue
            ref = f["imageRef"]
            if ref not in masters:
                continue
            im = master(ref)
            sw, sh = im.size
            t = f.get("transform")

            if t:  # cropped
                sx, sy = t[0][0], t[1][1]
                tx, ty = t[0][2], t[1][2]
                left, top = tx * sw, ty * sh
                right, bottom = (tx + sx) * sw, (ty + sy) * sh
            else:  # FILL / FIT — cover-crop centred on the box aspect
                box_ar = b["w"] / max(b["h"], 0.01)
                src_ar = sw / sh
                if f.get("scaleMode") == "FIT" or src_ar == box_ar:
                    left, top, right, bottom = 0, 0, sw, sh
                elif src_ar > box_ar:      # source wider — trim sides
                    w = sh * box_ar
                    left, top, right, bottom = (sw - w) / 2, 0, (sw + w) / 2, sh
                else:                      # source taller — trim top/bottom
                    h = sw / box_ar
                    left, top, right, bottom = 0, (sh - h) / 2, sw, (sh + h) / 2

            # clamp overscan (some crops sit a few % outside the master)
            left, top = max(0, left), max(0, top)
            right, bottom = min(sw, right), min(sh, bottom)
            if right - left < 1 or bottom - top < 1:
                continue
            crop_box = (int(round(left)), int(round(top)),
                        int(round(right)), int(round(bottom)))
            cw, ch = crop_box[2] - crop_box[0], crop_box[3] - crop_box[1]

            # target: layout size, retina for small elements, never upscale
            dpr = 2.0 if max(b["w"], b["h"]) < 700 else 1.0
            tw, th = b["w"] * dpr, b["h"] * dpr
            k = min(1.0, cw / max(tw, 0.01), ch / max(th, 0.01))
            if k < 1.0:
                tw, th = tw * k, th * k
            if max(tw, th) > MAX_EDGE:
                s = MAX_EDGE / max(tw, th)
                tw, th = tw * s, th * s
            tw, th = max(1, int(round(tw))), max(1, int(round(th)))

            key = hashlib.md5(f"{ref}|{crop_box}|{tw}x{th}".encode()).hexdigest()[:10]
            if key in dedupe:
                usages[f"{n['id']}:{idx}"] = dedupe[key]
                stats["reused"] += 1
                continue

            piece = im.crop(crop_box)
            if (piece.width, piece.height) != (tw, th):
                piece = piece.resize((tw, th), Image.LANCZOS)
            if piece.mode in ("RGBA", "LA", "P"):
                piece = piece.convert("RGBA")
                if piece.getchannel("A").getextrema()[0] == 255:
                    piece = piece.convert("RGB")
            else:
                piece = piece.convert("RGB")

            name = f"{slug(n['name'])}-{key}.webp"
            piece.save(OUT / name, "WEBP", quality=QUALITY, method=6)
            rel = f"/images/{name}"
            dedupe[key] = rel
            usages[f"{n['id']}:{idx}"] = rel
            stats["cut"] += 1
            stats["bytes"] += (OUT / name).stat().st_size

manifest["usages"] = usages
manifest.pop("images", None)
json.dump(manifest, open("spec/manifest.json", "w"), indent=1)

print(f"usages mapped : {len(usages)}")
print(f"files cut     : {stats['cut']}  (deduped {stats['reused']} repeats)")
print(f"shipped weight: {stats['bytes']/1e6:.2f} MB")
print("\nlargest:")
for p in sorted(OUT.glob("*.webp"), key=lambda p: -p.stat().st_size)[:10]:
    im = Image.open(p)
    print(f"  {p.name[:52]:<54}{im.width}x{im.height:<6} {p.stat().st_size/1e3:>7.0f} KB")
