/**
 * Asset pipeline: pull every raster fill and every icon out of Figma.
 * Icons are exported at their container level (one SVG per icon) rather than
 * per vector path, which keeps the output readable and deduplicated.
 */
import fs from 'node:fs';
import path from 'node:path';

const KEY = 'KUvTLMk5FgQQJoGGZc2MhU';
const TOK = process.env.FIGTOK;
const IMG_DIR = 'ecosfera/public/images';
const ICON_DIR = 'ecosfera/public/icons';
fs.mkdirSync(IMG_DIR, { recursive: true });
fs.mkdirSync(ICON_DIR, { recursive: true });

const api = async (url) => {
  const r = await fetch(url, { headers: { 'X-Figma-Token': TOK } });
  if (!r.ok) throw new Error(`${r.status} ${url}`);
  return r.json();
};

const slug = (s) =>
  s.toLowerCase()
    .replace(/[^a-z0-9а-яё]+/gi, '-')
    .replace(/^-+|-+$/g, '')
    .replace(/-{2,}/g, '-')
    .slice(0, 60) || 'node';

// ---------- 1. raster fills ----------
const specs = {
  home: JSON.parse(fs.readFileSync('spec/home.json', 'utf8')),
  mobile: JSON.parse(fs.readFileSync('spec/mobile.json', 'utf8')),
  kit: JSON.parse(fs.readFileSync('spec/kit.json', 'utf8')),
};

console.log('→ resolving imageRef URLs…');
const meta = await api(`https://api.figma.com/v1/files/${KEY}/images`);
const refMap = meta.meta.images;
console.log(`  Figma returned ${Object.keys(refMap).length} refs`);

// name each ref after the first node that uses it
const refNames = new Map();
for (const [label, spec] of Object.entries(specs)) {
  for (const n of spec.nodes) {
    for (const f of n.fills) {
      if (f.kind === 'image' && f.imageRef && !refNames.has(f.imageRef)) {
        refNames.set(f.imageRef, slug(n.name));
      }
    }
  }
}

const imgManifest = {};
let okImg = 0, failImg = 0;
for (const [ref, url] of Object.entries(refMap)) {
  if (!refNames.has(ref)) continue; // unused by our frames
  const base = refNames.get(ref);
  try {
    const r = await fetch(url);
    if (!r.ok) throw new Error(String(r.status));
    const buf = Buffer.from(await r.arrayBuffer());
    // sniff format
    let ext = 'png';
    if (buf[0] === 0xff && buf[1] === 0xd8) ext = 'jpg';
    else if (buf.slice(8, 12).toString() === 'WEBP') ext = 'webp';
    let file = `${base}.${ext}`;
    let i = 2;
    while (fs.existsSync(path.join(IMG_DIR, file)) && imgManifest[ref] !== file) file = `${base}-${i++}.${ext}`;
    fs.writeFileSync(path.join(IMG_DIR, file), buf);
    imgManifest[ref] = `/images/${file}`;
    okImg++;
    console.log(`  ✓ ${file}  ${(buf.length / 1024).toFixed(0)}KB`);
  } catch (e) {
    failImg++;
    console.log(`  ✗ ${base}: ${e.message}`);
  }
}
console.log(`rasters: ${okImg} ok, ${failImg} failed`);

// ---------- 2. icon detection ----------
/** An icon root is the shallowest node whose entire subtree draws only vectors
 *  (no text, no raster fill) — i.e. a self-contained piece of line art. */
function iconRoots(spec) {
  const byId = new Map(spec.nodes.map((n) => [n.id, n]));
  const kids = new Map();
  for (const n of spec.nodes) {
    if (!kids.has(n.parent)) kids.set(n.parent, []);
    kids.get(n.parent).push(n);
  }
  const DRAW = new Set(['VECTOR', 'BOOLEAN_OPERATION', 'LINE', 'STAR', 'POLYGON', 'ELLIPSE', 'RECTANGLE']);
  const memo = new Map();
  function vectorOnly(n) {
    if (memo.has(n.id)) return memo.get(n.id);
    let res;
    if (n.type === 'TEXT') res = false;
    else if (n.fills.some((f) => f.kind === 'image')) res = false;
    else {
      const ch = kids.get(n.id) || [];
      if (ch.length === 0) res = DRAW.has(n.type);
      else res = ch.every(vectorOnly);
    }
    memo.set(n.id, res);
    return res;
  }
  const roots = [];
  (function scan(n) {
    if (n.depth > 0 && vectorOnly(n) && n.box && n.box.w > 0 && n.box.h > 0) {
      const hasVec = n.type !== 'RECTANGLE' && n.type !== 'ELLIPSE'
        ? true
        : (kids.get(n.id) || []).length > 0;
      // plain rects/ellipses are CSS, not icons — only export real line art
      const subtree = [];
      (function collect(m) { subtree.push(m); (kids.get(m.id) || []).forEach(collect); })(n);
      const isArt = subtree.some((m) => ['VECTOR', 'BOOLEAN_OPERATION', 'LINE', 'STAR', 'POLYGON'].includes(m.type));
      if (isArt && hasVec) { roots.push(n); return; }
    }
    (kids.get(n.id) || []).forEach(scan);
  })(spec.nodes[0]);
  return roots;
}

const iconTargets = new Map(); // nodeId -> {name, label}
for (const [label, spec] of Object.entries(specs)) {
  if (label === 'kit') continue;
  const roots = iconRoots(spec);
  console.log(`→ ${label}: ${roots.length} icon roots detected`);
  for (const r of roots) iconTargets.set(r.id, { name: slug(r.name), label, w: r.box.w, h: r.box.h });
}

// ---------- 3. export icons as SVG ----------
const ids = [...iconTargets.keys()];
console.log(`→ exporting ${ids.length} icons as SVG…`);
const iconManifest = {};
const BATCH = 40;
for (let i = 0; i < ids.length; i += BATCH) {
  const chunk = ids.slice(i, i + BATCH);
  let res;
  try {
    res = await api(`https://api.figma.com/v1/images/${KEY}?ids=${chunk.map(encodeURIComponent).join(',')}&format=svg`);
  } catch (e) {
    console.log(`  ✗ batch ${i / BATCH + 1}: ${e.message}`);
    continue;
  }
  const entries = Object.entries(res.images || {});
  await Promise.all(entries.map(async ([id, url]) => {
    if (!url) return;
    try {
      const r = await fetch(url);
      const svg = await r.text();
      const info = iconTargets.get(id);
      let file = `${info.name}.svg`;
      let n = 2;
      const existing = Object.values(iconManifest);
      while (existing.includes(`/icons/${file}`)) file = `${info.name}-${n++}.svg`;
      fs.writeFileSync(path.join(ICON_DIR, file), svg);
      iconManifest[id] = `/icons/${file}`;
    } catch (e) { /* keep going */ }
  }));
  console.log(`  batch ${Math.floor(i / BATCH) + 1}/${Math.ceil(ids.length / BATCH)} → ${Object.keys(iconManifest).length} total`);
}

// ---------- 4. dedupe identical icons ----------
const byHash = new Map();
const alias = {};
for (const [id, rel] of Object.entries(iconManifest)) {
  const p = path.join('ecosfera/public', rel.replace(/^\//, ''));
  if (!fs.existsSync(p)) continue;
  const body = fs.readFileSync(p, 'utf8');
  const h = body.replace(/\s+/g, '');
  if (byHash.has(h)) {
    alias[id] = byHash.get(h);
    fs.unlinkSync(p);
  } else {
    byHash.set(h, rel);
    alias[id] = rel;
  }
}
const uniqueIcons = new Set(Object.values(alias));
console.log(`icons: ${Object.keys(alias).length} refs → ${uniqueIcons.size} unique files after dedupe`);

fs.writeFileSync('spec/manifest.json', JSON.stringify({ images: imgManifest, icons: alias }, null, 1));
console.log('\n✓ spec/manifest.json written');
