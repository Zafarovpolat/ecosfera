/**
 * Shared render core.
 *
 * Turns a Figma spec into a serialiser-agnostic element tree. Both the TSX
 * codegen and the static HTML emitter consume this, so the two outputs can
 * never drift apart.
 */
import fs from 'node:fs';

export const num = (v) => String(Math.round(v * 100) / 100);
export const px = (v) => `${num(v)}px`;

const FONT = {
  'CoFo Sans': 'var(--font-cofo)',
  'Forma DJR Cyrillic Display': 'var(--font-forma)',
  'Forma DJR Display': 'var(--font-forma)',
  'CoFo Robert': 'var(--font-robert)',
  Onest: 'var(--font-onest)',
  // Three different families are used for price labels — an inconsistency in
  // the source file rather than an intent. Unify on the display face.
  BravoSC: 'var(--font-forma)',
  'Helvetica Neue': 'var(--font-forma)',
  'Atyp Display': 'var(--font-forma)',
  'Yandex Sans Text': 'var(--font-cofo)',
  'SF Pro Text': '-apple-system, BlinkMacSystemFont, system-ui, sans-serif',
};
export const fontOf = (f) => FONT[f] ?? 'var(--font-cofo)';

const TRANSLIT = {
  а:'a',б:'b',в:'v',г:'g',д:'d',е:'e',ё:'e',ж:'zh',з:'z',и:'i',й:'y',к:'k',л:'l',м:'m',
  н:'n',о:'o',п:'p',р:'r',с:'s',т:'t',у:'u',ф:'f',х:'h',ц:'c',ч:'ch',ш:'sh',щ:'sch',
  ъ:'',ы:'y',ь:'',э:'e',ю:'yu',я:'ya',
};
export const translit = (s) =>
  String(s).toLowerCase().split('').map((c) => TRANSLIT[c] ?? c).join('');

/** Layer names here are mostly bare numbers, so name sections after their own
 *  headline instead. */
export const pascal = (raw, i) => {
  const words = translit(raw)
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()
    .split(/\s+/)
    .filter((w) => w && !/^\d+$/.test(w))
    .slice(0, 4)
    .map((w) => w[0].toUpperCase() + w.slice(1));
  const latin = words.join('').replace(/[^A-Za-z0-9]/g, '');
  return `S${String(i).padStart(2, '0')}${latin || 'Block'}`;
};

/** Geometry + paint + effects for any non-text node. */
function baseStyle(node, parent) {
  const b = node.box;
  const st = {};
  st.left = px(b.x - (parent?.box?.x ?? 0));
  st.top = px(b.y - (parent?.box?.y ?? 0));
  st.width = px(b.w);
  st.height = px(b.h);
  // Full 2x2 rather than an angle: the design mirrors 20 nodes (the hero photo
  // is [[-1,0],[0,1]]) and transposes the chevrons. atan2 would read a mirror
  // as a 180° rotation and silently flip artwork.
  if (b.lin) st.transform = `matrix(${num(b.lin.a)}, ${num(b.lin.b)}, ${num(b.lin.c)}, ${num(b.lin.d)}, 0, 0)`;
  if (node.opacity < 1) st.opacity = num(node.opacity);
  if (node.blend) st.mixBlendMode = node.blend.toLowerCase().replace(/_/g, '-');
  if (node.radius) st.borderRadius = typeof node.radius === 'number' ? px(node.radius) : node.radius;
  if (node.clip) st.overflow = 'hidden';

  if (node.strokes.length && node.strokeWeight) {
    const c = node.strokes.find((s) => s.kind === 'solid')?.color;
    if (c) {
      const decl = `${px(node.strokeWeight)} ${node.strokeDashes?.length ? 'dashed' : 'solid'} ${c}`;
      if (node.strokeAlign === 'OUTSIDE') st.outline = decl;
      else st.border = decl;
    }
  }
  if (node.shadows.length) st.boxShadow = node.shadows.map((s) => s.css).join(', ');

  // Figma blur radius is roughly 2x the CSS equivalent
  const lb = node.blurs.find((x) => x.kind === 'layer');
  const bb = node.blurs.find((x) => x.kind === 'background');
  if (lb) st.filter = `blur(${px(lb.radius / 2)})`;
  if (bb) {
    st.backdropFilter = `blur(${px(bb.radius / 2)})`;
    st.WebkitBackdropFilter = `blur(${px(bb.radius / 2)})`;
  }
  return st;
}

function textStyle(node, st) {
  const t = node.text;
  st.fontFamily = fontOf(t.family);
  if (t.size) st.fontSize = px(t.size);
  if (t.weight) st.fontWeight = String(t.weight);
  if (t.lineHeight) st.lineHeight = px(t.lineHeight);
  if (t.letterSpacing) st.letterSpacing = px(t.letterSpacing);
  if (t.italic) st.fontStyle = 'italic';
  if (t.case === 'UPPER') st.textTransform = 'uppercase';
  else if (t.case === 'LOWER') st.textTransform = 'lowercase';
  else if (t.case === 'TITLE') st.textTransform = 'capitalize';
  if (t.decoration === 'UNDERLINE') st.textDecoration = 'underline';
  else if (t.decoration === 'STRIKETHROUGH') st.textDecoration = 'line-through';
  st.display = 'grid';
  st.alignContent = t.valign === 'CENTER' ? 'center' : t.valign === 'BOTTOM' ? 'end' : 'start';
  st.textAlign =
    t.align === 'CENTER' ? 'center' : t.align === 'RIGHT' ? 'right' : t.align === 'JUSTIFIED' ? 'justify' : 'left';
  return st;
}

/**
 * @returns {{bands: Array, backdrop: Array, frame: object, stats: object}}
 * Element nodes are `{ tag, cls, style, src?, alt?, text?, runs?, children }`.
 */
export function buildTree(label, manifest) {
  const spec = JSON.parse(fs.readFileSync(`spec/${label}.json`, 'utf8'));
  const byId = new Map(spec.nodes.map((n) => [n.id, n]));
  const kids = new Map();
  for (const n of spec.nodes) {
    if (n.parent === null) continue;
    if (!kids.has(n.parent)) kids.set(n.parent, []);
    kids.get(n.parent).push(n);
  }
  for (const l of kids.values()) l.sort((a, b) => a.z - b.z);

  const icons = manifest.icons || {};
  const usages = manifest.usages || {};

  const inIcon = new Set();
  for (const id of Object.keys(icons)) {
    if (!byId.has(id)) continue;
    (function mark(nid) {
      for (const c of kids.get(nid) || []) { inIcon.add(c.id); mark(c.id); }
    })(id);
  }

  const stats = { nodes: 0, texts: 0, imgs: 0, icons: 0, skipped: 0 };

  function walk(node, parent) {
    if (node.hidden || inIcon.has(node.id)) { stats.skipped++; return null; }
    const b = node.box;
    if (!b) { stats.skipped++; return null; }

    // Figma LINE nodes are zero-thickness on one axis. Dropping them for having
    // no area silently loses the header, footer and tag-row rules, so give them
    // their stroke weight as thickness instead.
    const hairline = b.w <= 0 || b.h <= 0;
    if (hairline && !node.strokes.length) { stats.skipped++; return null; }
    if (!hairline && (b.w <= 0 || b.h <= 0)) { stats.skipped++; return null; }

    const st = baseStyle(node, parent);
    if (hairline) {
      const t = node.strokeWeight || 1;
      st.width = px(Math.max(b.w, t));
      st.height = px(Math.max(b.h, t));
      const c = node.strokes.find((s) => s.kind === 'solid')?.color;
      if (c) st.background = c;
      delete st.border;
      delete st.outline;
      stats.nodes++;
      stats.rules = (stats.rules || 0) + 1;
      return { tag: 'div', cls: 'n', style: st, children: [] };
    }
    const isText = node.type === 'TEXT';
    const grads = node.fills.filter((f) => f.kind === 'gradient');
    const solid = node.fills.filter((f) => f.kind === 'solid');

    // exported icon — its subtree is baked into the SVG
    const icon = icons[node.id];
    if (icon) {
      stats.icons++; stats.nodes++;
      return { tag: 'div', cls: 'n', style: st, ariaHidden: true, children: [{ tag: 'img', src: icon, alt: '' }] };
    }

    if (isText && node.text) {
      stats.texts++; stats.nodes++;
      textStyle(node, st);
      let cls = 't';
      if (grads[0]) { st.background = grads[0].css; cls += ' gradient-text'; }
      else if (solid.length) st.color = solid[solid.length - 1].color;

      const runs = node.text.runs
        ? node.text.runs.map((r) => {
            const o = r.override;
            if (!o) return { text: r.text, style: null, cls: '' };
            const s = {};
            if (o.family) s.fontFamily = fontOf(o.family);
            if (o.size) s.fontSize = px(o.size);
            if (o.weight) s.fontWeight = String(o.weight);
            if (o.lineHeight) s.lineHeight = px(o.lineHeight);
            if (o.letterSpacing) s.letterSpacing = px(o.letterSpacing);
            if (o.decoration === 'UNDERLINE') s.textDecoration = 'underline';
            const og = o.fills?.find((f) => f.kind === 'gradient');
            const oc = o.fills?.find((f) => f.kind === 'solid')?.color;
            if (og) s.background = og.css;
            else if (oc) s.color = oc;
            return { text: r.text, style: s, cls: og ? 'gradient-text' : '' };
          })
        : null;

      return { tag: 'div', cls, style: st, text: runs ? null : node.text.chars, runs, children: [] };
    }

    const children = [];

    // Figma paints fills bottom-first; CSS background layers paint top-first,
    // and an <img> child always sits above the node's own background. So when a
    // node stacks more than one fill — the page backdrop is photo + dark green
    // veil — emit an explicit layer per fill in Figma's order. Getting this
    // wrong buries the veil under the photo and the tint disappears.
    const single = node.fills.length === 1;
    if (single && grads.length) st.background = grads[0].css;
    else if (single && solid.length) st.background = solid[0].color;

    node.fills.forEach((f, i) => {
      if (single && f.kind !== 'image') return; // already on the node
      const s = { position: 'absolute', inset: '0', borderRadius: 'inherit' };
      if (f.opacity !== undefined && f.opacity < 1) s.opacity = num(f.opacity);
      if (f.blend && f.blend !== 'NORMAL') s.mixBlendMode = f.blend.toLowerCase().replace(/_/g, '-');

      if (f.kind === 'image') {
        const src = usages[`${node.id}:${i}`];
        if (!src) return;
        stats.imgs++;
        children.push({ tag: 'img', src, alt: '', style: s });
      } else {
        s.background = f.kind === 'gradient' ? f.css : f.color;
        children.push({ tag: 'div', cls: '', style: s, children: [] });
      }
    });

    for (const c of kids.get(node.id) || []) {
      const r = walk(c, node);
      if (r) children.push(r);
    }
    stats.nodes++;
    return { tag: 'div', cls: 'n', style: st, children };
  }

  const frameNode = spec.nodes[0];
  const top = (kids.get(frameNode.id) || []).filter((n) => !n.hidden && n.box);
  const isBackdrop = (n) => n.box.h > spec.frame.h * 0.5;

  const backdrop = top.filter(isBackdrop).map((n) => walk(n, frameNode)).filter(Boolean);

  const flow = top.filter((n) => !isBackdrop(n)).sort((a, b) => a.box.y - b.box.y || a.box.x - b.box.x);
  const raw = [];
  for (const n of flow) {
    const last = raw[raw.length - 1];
    if (last && n.box.y <= last.end) {
      last.items.push(n);
      last.end = Math.max(last.end, n.box.y + n.box.h);
    } else raw.push({ items: [n], start: n.box.y, end: n.box.y + n.box.h });
  }
  // fold the small chevron dividers into the section they punctuate
  for (let i = raw.length - 1; i > 0; i--) {
    if (raw[i].end - raw[i].start < 40) {
      raw[i - 1].items.push(...raw[i].items);
      raw[i - 1].end = Math.max(raw[i - 1].end, raw[i].end);
      raw.splice(i, 1);
    }
  }

  function headline(band) {
    let best = null;
    for (const root of band.items)
      (function scan(n) {
        if (n.type === 'TEXT' && n.text?.chars?.trim() && !inIcon.has(n.id) && !n.hidden) {
          const size = n.text.size ?? 0;
          if (!best || size > best.size) best = { size, chars: n.text.chars };
        }
        for (const c of kids.get(n.id) || []) scan(c);
      })(root);
    return best?.chars ?? band.items.reduce((a, b) => (a.box.w * a.box.h > b.box.w * b.box.h ? a : b)).name;
  }

  const bands = raw.map((band, i) => ({
    index: i + 1,
    name: pascal(headline(band), i + 1),
    layers: band.items.map((n) => n.name),
    start: Math.round(band.start),
    end: Math.round(band.end),
    els: band.items.map((n) => walk(n, frameNode)).filter(Boolean),
  }));

  return { bands, backdrop, frame: spec.frame, stats };
}
