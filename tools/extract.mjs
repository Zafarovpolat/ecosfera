/**
 * Figma → render spec extractor.
 * Flattens a Figma frame into an ordered list of render nodes carrying exact
 * geometry, paint, type and asset data. Output is the single source of truth
 * for codegen — nothing downstream may guess a value.
 */
import fs from 'node:fs';
import path from 'node:path';

const OUT = 'spec';
fs.mkdirSync(OUT, { recursive: true });

// ---------- colour helpers ----------
const hex2 = (v) => Math.round(Math.max(0, Math.min(1, v)) * 255).toString(16).padStart(2, '0');
function toCss(color, opacity = 1) {
  if (!color) return null;
  const a = (color.a ?? 1) * (opacity ?? 1);
  const r = Math.round(color.r * 255), g = Math.round(color.g * 255), b = Math.round(color.b * 255);
  if (a >= 0.999) return `#${hex2(color.r)}${hex2(color.g)}${hex2(color.b)}`;
  return `rgba(${r}, ${g}, ${b}, ${+a.toFixed(3)})`;
}

// ---------- geometry ----------
function rotationOf(node) {
  const t = node.relativeTransform;
  if (!t) return 0;
  const deg = Math.atan2(t[1][0], t[0][0]) * (180 / Math.PI);
  return Math.abs(deg) < 0.01 ? 0 : +deg.toFixed(2);
}

/** Unrotated box. Figma's absoluteBoundingBox is the AABB, so for rotated
 *  nodes we must use `size` and re-derive the top-left corner. */
function boxOf(node, origin) {
  const bb = node.absoluteBoundingBox;
  if (!bb) return null;
  const rot = rotationOf(node);
  const w = node.size ? node.size.x : bb.width;
  const h = node.size ? node.size.y : bb.height;
  let x = bb.x - origin.x;
  let y = bb.y - origin.y;
  if (rot !== 0 && node.size) {
    // centre of the AABB equals centre of the rotated box
    const cx = bb.x + bb.width / 2 - origin.x;
    const cy = bb.y + bb.height / 2 - origin.y;
    x = cx - w / 2;
    y = cy - h / 2;
  }
  const r = (n) => +n.toFixed(2);
  return { x: r(x), y: r(y), w: r(w), h: r(h), rot };
}

// ---------- paint ----------
function gradientCss(fill) {
  const stops = (fill.gradientStops || []).map(
    (s) => `${toCss(s.color)} ${+(s.position * 100).toFixed(1)}%`
  );
  const hp = fill.gradientHandlePositions || [];
  if (fill.type === 'GRADIENT_RADIAL' || fill.type === 'GRADIENT_DIAMOND') {
    return `radial-gradient(${stops.join(', ')})`;
  }
  // linear: derive angle from handles (Figma y grows downward)
  let deg = 180;
  if (hp.length >= 2) {
    const dx = hp[1].x - hp[0].x, dy = hp[1].y - hp[0].y;
    deg = +((Math.atan2(dy, dx) * 180) / Math.PI + 90).toFixed(2);
  }
  return `linear-gradient(${deg}deg, ${stops.join(', ')})`;
}

function paintsOf(list, imageRefs) {
  const out = [];
  for (const f of list || []) {
    if (f.visible === false) continue;
    if (f.type === 'SOLID') out.push({ kind: 'solid', color: toCss(f.color, f.opacity), blend: f.blendMode });
    else if (f.type?.startsWith('GRADIENT')) out.push({ kind: 'gradient', css: gradientCss(f), blend: f.blendMode, opacity: f.opacity ?? 1 });
    else if (f.type === 'IMAGE') {
      if (f.imageRef) imageRefs.add(f.imageRef);
      out.push({
        kind: 'image',
        imageRef: f.imageRef,
        scaleMode: f.scaleMode, // FILL | FIT | TILE | STRETCH
        opacity: f.opacity ?? 1,
        blend: f.blendMode,
        transform: f.imageTransform ?? null,
        scalingFactor: f.scalingFactor ?? null,
      });
    }
  }
  return out;
}

function effectsOf(node) {
  const shadows = [], blurs = [];
  for (const e of node.effects || []) {
    if (e.visible === false) continue;
    const off = e.offset || { x: 0, y: 0 };
    if (e.type === 'DROP_SHADOW')
      shadows.push({ kind: 'drop', css: `${off.x}px ${off.y}px ${e.radius}px ${e.spread || 0}px ${toCss(e.color)}` });
    else if (e.type === 'INNER_SHADOW')
      shadows.push({ kind: 'inner', css: `inset ${off.x}px ${off.y}px ${e.radius}px ${e.spread || 0}px ${toCss(e.color)}` });
    else if (e.type === 'LAYER_BLUR') blurs.push({ kind: 'layer', radius: e.radius });
    else if (e.type === 'BACKGROUND_BLUR') blurs.push({ kind: 'background', radius: e.radius });
  }
  return { shadows, blurs };
}

function radiusOf(node) {
  if (Array.isArray(node.rectangleCornerRadii)) {
    const [a, b, c, d] = node.rectangleCornerRadii;
    return a === b && b === c && c === d ? a : `${a}px ${b}px ${c}px ${d}px`;
  }
  return node.cornerRadius ?? 0;
}

// ---------- text ----------
function textOf(node) {
  const s = node.style || {};
  const lineHeight = s.lineHeightPx ? +s.lineHeightPx.toFixed(2) : null;
  const base = {
    chars: node.characters ?? '',
    family: s.fontFamily,
    postScript: s.fontPostScriptName ?? null,
    weight: s.fontWeight,
    size: s.fontSize ? +s.fontSize.toFixed(2) : null,
    lineHeight,
    lineHeightPct: s.lineHeightPercentFontSize ?? null,
    letterSpacing: s.letterSpacing ? +s.letterSpacing.toFixed(3) : 0,
    align: s.textAlignHorizontal,
    valign: s.textAlignVertical,
    case: s.textCase ?? null,
    decoration: s.textDecoration ?? null,
    italic: !!s.italic,
    autoResize: s.textAutoResize ?? null,
    paragraphSpacing: s.paragraphSpacing ?? 0,
  };
  // mixed-style runs (e.g. a single headline with a highlighted phrase)
  const overrides = node.characterStyleOverrides || [];
  const table = node.styleOverrideTable || {};
  let runs = null;
  if (overrides.length && Object.keys(table).length) {
    runs = [];
    let cur = null;
    for (let i = 0; i < node.characters.length; i++) {
      const key = overrides[i] ?? 0;
      if (!cur || cur.key !== key) {
        cur = { key, text: '', style: key === 0 ? null : table[key] ?? null };
        runs.push(cur);
      }
      cur.text += node.characters[i];
    }
    runs = runs.map((r) => ({
      text: r.text,
      override: r.style
        ? {
            family: r.style.fontFamily,
            weight: r.style.fontWeight,
            size: r.style.fontSize,
            lineHeight: r.style.lineHeightPx,
            letterSpacing: r.style.letterSpacing,
            fills: r.style.fills ? paintsOf(r.style.fills, new Set()) : null,
            decoration: r.style.textDecoration ?? null,
          }
        : null,
    }));
    if (runs.length === 1) runs = null; // uniform after all
  }
  return { ...base, runs };
}

// ---------- main walk ----------
const VECTORISH = new Set(['VECTOR', 'BOOLEAN_OPERATION', 'STAR', 'POLYGON', 'LINE']);

function extract(frame, label) {
  const origin = { x: frame.absoluteBoundingBox.x, y: frame.absoluteBoundingBox.y };
  const imageRefs = new Set();
  const vectorIds = new Set();
  const nodes = [];
  let order = 0;

  (function walk(node, parentId, depth, inheritedHidden) {
    const hidden = inheritedHidden || node.visible === false;
    const box = boxOf(node, origin);
    const fills = paintsOf(node.fills, imageRefs);
    const strokes = paintsOf(node.strokes, imageRefs);
    const { shadows, blurs } = effectsOf(node);

    const rec = {
      id: node.id,
      name: node.name,
      type: node.type,
      parent: parentId,
      depth,
      z: order++,
      hidden,
      box,
      opacity: node.opacity ?? 1,
      blend: node.blendMode && node.blendMode !== 'PASS_THROUGH' ? node.blendMode : null,
      clip: node.clipsContent ?? false,
      radius: radiusOf(node),
      fills,
      strokes,
      strokeWeight: node.strokeWeight ?? null,
      strokeAlign: node.strokeAlign ?? null,
      strokeDashes: node.strokeDashes ?? null,
      shadows,
      blurs,
      layout: node.layoutMode && node.layoutMode !== 'NONE'
        ? {
            mode: node.layoutMode,
            gap: node.itemSpacing ?? 0,
            pad: {
              t: node.paddingTop ?? 0, r: node.paddingRight ?? 0,
              b: node.paddingBottom ?? 0, l: node.paddingLeft ?? 0,
            },
            main: node.primaryAxisAlignItems ?? null,
            cross: node.counterAxisAlignItems ?? null,
            wrap: node.layoutWrap ?? null,
          }
        : null,
      constraints: node.constraints ?? null,
      childCount: (node.children || []).length,
    };

    if (node.type === 'TEXT') rec.text = textOf(node);
    if (VECTORISH.has(node.type)) { rec.vector = true; vectorIds.add(node.id); }

    nodes.push(rec);
    for (const c of node.children || []) walk(c, node.id, depth + 1, hidden);
  })(frame, null, 0, false);

  // section = direct child of the frame, ordered by y
  const sections = (frame.children || [])
    .filter((c) => c.absoluteBoundingBox)
    .map((c) => {
      const b = boxOf(c, origin);
      return { id: c.id, name: c.name, type: c.type, ...b, hidden: c.visible === false };
    })
    .sort((a, b) => a.y - b.y || a.x - b.x);

  const spec = {
    label,
    frame: { id: frame.id, name: frame.name, w: Math.round(frame.absoluteBoundingBox.width), h: Math.round(frame.absoluteBoundingBox.height) },
    background: paintsOf(frame.fills, imageRefs),
    sections,
    nodes,
  };
  fs.writeFileSync(path.join(OUT, `${label}.json`), JSON.stringify(spec, null, 1));
  return { spec, imageRefs: [...imageRefs], vectorIds: [...vectorIds] };
}

// ---------- run ----------
const files = {
  home: ['fig_home.json', '95:587'],
  mobile: ['fig_mob.json', '129:720'],
  kit: ['fig_kit.json', '143:1922'],
};
const allImages = new Set(), allVectors = {};
const summary = {};

for (const [label, [file, id]] of Object.entries(files)) {
  const doc = JSON.parse(fs.readFileSync(file, 'utf8')).nodes[id].document;
  const { spec, imageRefs, vectorIds } = extract(doc, label);
  imageRefs.forEach((r) => allImages.add(r));
  allVectors[label] = vectorIds;
  summary[label] = {
    frame: `${spec.frame.w}x${spec.frame.h}`,
    nodes: spec.nodes.length,
    sections: spec.sections.length,
    texts: spec.nodes.filter((n) => n.type === 'TEXT').length,
    vectors: vectorIds.length,
    imageFills: spec.nodes.filter((n) => n.fills.some((f) => f.kind === 'image')).length,
    gradients: spec.nodes.filter((n) => n.fills.some((f) => f.kind === 'gradient')).length,
    rotated: spec.nodes.filter((n) => n.box?.rot).length,
    shadowed: spec.nodes.filter((n) => n.shadows.length).length,
    blurred: spec.nodes.filter((n) => n.blurs.length).length,
    autolayout: spec.nodes.filter((n) => n.layout).length,
    hidden: spec.nodes.filter((n) => n.hidden).length,
  };
}

fs.writeFileSync(path.join(OUT, 'assets.json'), JSON.stringify({ imageRefs: [...allImages], vectors: allVectors }, null, 1));

console.log('=== EXTRACTION SUMMARY ===');
for (const [k, v] of Object.entries(summary)) {
  console.log(`\n${k}: ${v.frame}`);
  for (const [kk, vv] of Object.entries(v)) if (kk !== 'frame') console.log(`   ${kk.padEnd(12)} ${vv}`);
}
console.log(`\nunique image refs: ${allImages.size}`);
console.log(`vector nodes: home=${allVectors.home.length} mobile=${allVectors.mobile.length} kit=${allVectors.kit.length}`);
