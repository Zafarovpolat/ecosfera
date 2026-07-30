/**
 * Icon synthesis from Figma path geometry.
 *
 * Replaces the network round-trip to Figma's SVG exporter. The node trees were
 * fetched with `geometry=paths`, so every vector already carries its outline —
 * assembling the SVGs locally is both offline-safe and immune to the failure
 * that motivated it: 29 icon exports came back as an error string, hashed
 * identically, and collapsed into a single broken file during dedupe.
 */
import fs from 'node:fs';
import path from 'node:path';

const OUT = 'ecosfera/public/icons';
const FRAMES = {
  home: ['fig_home.json', '95:587'],
  mobile: ['fig_mob.json', '129:720'],
};

const hex2 = (v) => Math.round(Math.max(0, Math.min(1, v)) * 255).toString(16).padStart(2, '0');
const toCss = (c, o = 1) => {
  const a = (c.a ?? 1) * (o ?? 1);
  return a >= 0.999
    ? `#${hex2(c.r)}${hex2(c.g)}${hex2(c.b)}`
    : `rgba(${Math.round(c.r * 255)}, ${Math.round(c.g * 255)}, ${Math.round(c.b * 255)}, ${+a.toFixed(3)})`;
};
const n6 = (v) => +v.toFixed(4);

/** relativeTransform → affine, mapping the node's local space into its parent's. */
const affineOf = (node) => {
  const t = node.relativeTransform;
  if (!t) return null;
  return { a: t[0][0], b: t[1][0], c: t[0][1], d: t[1][1], e: t[0][2], f: t[1][2] };
};
const IDENT = { a: 1, b: 0, c: 0, d: 1, e: 0, f: 0 };
const mul = (m, k) => ({
  a: m.a * k.a + m.c * k.b,
  b: m.b * k.a + m.d * k.b,
  c: m.a * k.c + m.c * k.d,
  d: m.b * k.c + m.d * k.d,
  e: m.a * k.e + m.c * k.f + m.e,
  f: m.b * k.e + m.d * k.f + m.f,
});
const isIdent = (m) =>
  Math.abs(m.a - 1) < 1e-6 && Math.abs(m.d - 1) < 1e-6 && Math.abs(m.b) < 1e-6 &&
  Math.abs(m.c) < 1e-6 && Math.abs(m.e) < 1e-6 && Math.abs(m.f) < 1e-6;

const DRAW = new Set(['VECTOR', 'BOOLEAN_OPERATION', 'LINE', 'STAR', 'POLYGON', 'ELLIPSE', 'RECTANGLE']);
const ART = new Set(['VECTOR', 'BOOLEAN_OPERATION', 'LINE', 'STAR', 'POLYGON']);

const slug = (s) =>
  String(s).toLowerCase().replace(/[^a-z0-9]+/gi, '-').replace(/^-+|-+$/g, '').replace(/-{2,}/g, '-').slice(0, 52) || 'icon';

let gradSeq = 0;

/** Paint for one shape → SVG attribute plus any <defs> it needs. */
function paintAttr(fills, kind, size) {
  const f = (fills || []).find((x) => x.visible !== false);
  if (!f) return { attr: `${kind}="none"`, def: null };
  if (f.type === 'SOLID') return { attr: `${kind}="${toCss(f.color, f.opacity)}"`, def: null };
  if (f.type?.startsWith('GRADIENT')) {
    const id = `g${gradSeq++}`;
    const hp = f.gradientHandlePositions || [];
    const stops = (f.gradientStops || [])
      .map((s) => `<stop offset="${+s.position.toFixed(4)}" stop-color="${toCss(s.color)}"/>`)
      .join('');
    if (f.type === 'GRADIENT_RADIAL' && hp.length >= 2) {
      const r = Math.hypot((hp[1].x - hp[0].x) * (size?.x ?? 1), (hp[1].y - hp[0].y) * (size?.y ?? 1));
      return {
        attr: `${kind}="url(#${id})"`,
        def: `<radialGradient id="${id}" gradientUnits="userSpaceOnUse" cx="${n6(hp[0].x * (size?.x ?? 1))}" cy="${n6(hp[0].y * (size?.y ?? 1))}" r="${n6(r)}">${stops}</radialGradient>`,
      };
    }
    const x1 = n6((hp[0]?.x ?? 0) * (size?.x ?? 1)), y1 = n6((hp[0]?.y ?? 0) * (size?.y ?? 1));
    const x2 = n6((hp[1]?.x ?? 0) * (size?.x ?? 1)), y2 = n6((hp[1]?.y ?? 1) * (size?.y ?? 1));
    return {
      attr: `${kind}="url(#${id})"`,
      def: `<linearGradient id="${id}" gradientUnits="userSpaceOnUse" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}">${stops}</linearGradient>`,
    };
  }
  return { attr: `${kind}="none"`, def: null };
}

function synth(root) {
  const w = root.size?.x ?? root.absoluteBoundingBox?.width ?? 0;
  const h = root.size?.y ?? root.absoluteBoundingBox?.height ?? 0;
  if (!w || !h) return null;

  const body = [], defs = [];

  (function walk(node, M) {
    if (node.visible === false) return;
    const local = node === root ? IDENT : (affineOf(node) ?? IDENT);
    const cur = node === root ? IDENT : mul(M, local);

    if (DRAW.has(node.type)) {
      const tf = isIdent(cur)
        ? ''
        : ` transform="matrix(${n6(cur.a)} ${n6(cur.b)} ${n6(cur.c)} ${n6(cur.d)} ${n6(cur.e)} ${n6(cur.f)})"`;
      const op = (node.opacity ?? 1) < 1 ? ` opacity="${n6(node.opacity)}"` : '';

      for (const g of node.fillGeometry || []) {
        const p = paintAttr(node.fills, 'fill', node.size);
        if (p.def) defs.push(p.def);
        const rule = g.windingRule === 'EVENODD' ? ' fill-rule="evenodd" clip-rule="evenodd"' : '';
        body.push(`<path d="${g.path}"${rule} ${p.attr}${op}${tf}/>`);
      }
      for (const g of node.strokeGeometry || []) {
        const p = paintAttr(node.strokes, 'fill', node.size);
        if (p.def) defs.push(p.def);
        body.push(`<path d="${g.path}" ${p.attr}${op}${tf}/>`);
      }
    }
    for (const c of node.children || []) walk(c, cur);
  })(root, IDENT);

  if (!body.length) return null;
  const defBlock = defs.length ? `<defs>${defs.join('')}</defs>` : '';
  return `<svg width="${n6(w)}" height="${n6(h)}" viewBox="0 0 ${n6(w)} ${n6(h)}" fill="none" xmlns="http://www.w3.org/2000/svg">${defBlock}${body.join('')}</svg>`;
}

/** Same rule as before: shallowest node whose whole subtree is line art. */
function iconRoots(frame) {
  const memo = new Map();
  function vectorOnly(n) {
    if (memo.has(n)) return memo.get(n);
    let res;
    if (n.type === 'TEXT') res = false;
    else if ((n.fills || []).some((f) => f.type === 'IMAGE' && f.visible !== false)) res = false;
    else if (!n.children || n.children.length === 0) res = DRAW.has(n.type);
    else res = n.children.every(vectorOnly);
    memo.set(n, res);
    return res;
  }
  const roots = [];
  (function scan(n, depth) {
    if (depth > 0 && vectorOnly(n)) {
      const sub = [];
      (function c(m) { sub.push(m); (m.children || []).forEach(c); })(n);
      if (sub.some((m) => ART.has(m.type))) { roots.push(n); return; }
    }
    (n.children || []).forEach((c) => scan(c, depth + 1));
  })(frame, 0);
  return roots;
}

// ---------------------------------------------------------------- run
fs.mkdirSync(OUT, { recursive: true });
for (const f of fs.readdirSync(OUT)) fs.unlinkSync(path.join(OUT, f));

const manifest = JSON.parse(fs.readFileSync('spec/manifest.json', 'utf8'));
const icons = {};
const byHash = new Map();
let made = 0, reused = 0, failed = 0;

for (const [label, [file, id]] of Object.entries(FRAMES)) {
  const frame = JSON.parse(fs.readFileSync(file, 'utf8')).nodes[id].document;
  const roots = iconRoots(frame);
  let ok = 0;
  for (const r of roots) {
    gradSeq = 0; // keep ids stable per file so identical art dedupes
    const svg = synth(r);
    if (!svg) { failed++; continue; }
    const key = svg;
    if (byHash.has(key)) {
      icons[r.id] = byHash.get(key);
      reused++; ok++;
      continue;
    }
    let name = `${slug(r.name)}.svg`;
    let i = 2;
    const taken = new Set(Object.values(icons));
    while (taken.has(`/icons/${name}`)) name = `${slug(r.name)}-${i++}.svg`;
    fs.writeFileSync(path.join(OUT, name), svg);
    const rel = `/icons/${name}`;
    byHash.set(key, rel);
    icons[r.id] = rel;
    made++; ok++;
  }
  console.log(`${label.padEnd(7)} roots=${roots.length} resolved=${ok}`);
}

manifest.icons = icons;
fs.writeFileSync('spec/manifest.json', JSON.stringify(manifest, null, 1));

// sanity
const files = fs.readdirSync(OUT);
const bad = files.filter((f) => !fs.readFileSync(path.join(OUT, f), 'utf8').includes('<svg'));
console.log(`\nicon refs: ${Object.keys(icons).length} → ${made} unique files (${reused} deduped, ${failed} empty)`);
console.log(`files on disk: ${files.length} | invalid: ${bad.length}`);
console.log(`total size: ${(files.reduce((s, f) => s + fs.statSync(path.join(OUT, f)).size, 0) / 1024).toFixed(0)} KB`);
