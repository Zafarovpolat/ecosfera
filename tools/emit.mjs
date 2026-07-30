/**
 * Serialises the shared render tree two ways:
 *   • TSX components  → the Next.js source in the repo
 *   • static HTML/CSS → dist/, deployable to GitHub Pages with no build step
 *
 * `output: 'export'` would produce the same static result, so the two are
 * equivalent deliverables; emitting HTML directly means delivery never depends
 * on the npm registry being reachable.
 */
import fs from 'node:fs';
import path from 'node:path';
import { buildTree } from './lib-render.mjs';

const PROJ = 'ecosfera';
const GEN = path.join(PROJ, 'src/components/generated');
const DIST = 'dist';
const manifest = JSON.parse(fs.readFileSync('spec/manifest.json', 'utf8'));

const camelToKebab = (k) =>
  k
    // lowercase the char after the vendor prefix, or it kebabs again into
    // "-webkit--backdrop-filter" and Safari drops the declaration
    .replace(/^Webkit([A-Z])/, (_, c) => `-webkit-${c.toLowerCase()}`)
    .replace(/[A-Z]/g, (m) => '-' + m.toLowerCase());

const escHtml = (s) =>
  String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

// ----------------------------------------------------------------- TSX side
const jsxStr = (s) => String(s).replace(/[\\`$]/g, (m) => '\\' + m).replace(/\r/g, '');

function styleObj(o) {
  const parts = [];
  for (const [k, v] of Object.entries(o || {})) {
    if (v === undefined || v === null || v === '') continue;
    const key = /^[a-zA-Z][a-zA-Z0-9]*$/.test(k) ? k : `'${k}'`;
    parts.push(`${key}: '${String(v).replace(/'/g, "\\'")}'`);
  }
  return parts.length ? `{ ${parts.join(', ')} }` : '{}';
}

function toTsx(el, indent) {
  const pad = '  '.repeat(indent);
  if (el.tag === 'img') {
    const st = el.style ? ` style={${styleObj(el.style)}}` : '';
    return `${pad}<img src={asset('${el.src}')} alt="${el.alt ?? ''}"${st} />`;
  }
  const cls = el.cls ? ` className="${el.cls}"` : '';
  const st = ` style={${styleObj(el.style)}}`;
  const aria = el.ariaHidden ? ' aria-hidden' : '';

  let inner = '';
  if (el.runs) {
    // One wrapper, because the text box is a grid container for vertical
    // alignment — bare sibling spans would each become their own grid row and
    // break a styled phrase onto separate lines.
    const parts = el.runs
      .map((r) => {
        if (!r.style) return `{\`${jsxStr(r.text)}\`}`;
        const c = r.cls ? ` className="${r.cls}"` : '';
        return `<span${c} style={${styleObj(r.style)}}>{\`${jsxStr(r.text)}\`}</span>`;
      })
      .join('');
    inner = `<span>${parts}</span>`;
  } else if (el.text != null) {
    inner = `{\`${jsxStr(el.text)}\`}`;
  }

  const kids = (el.children || []).map((c) => toTsx(c, indent + 1));
  if (inner && !kids.length) return `${pad}<div${cls}${st}${aria}>${inner}</div>`;
  if (!inner && !kids.length) return `${pad}<div${cls}${st}${aria} />`;
  return `${pad}<div${cls}${st}${aria}>${inner}\n${kids.join('\n')}\n${pad}</div>`;
}

// ---------------------------------------------------------------- HTML side
/** Imagery in the first screen must not be lazy, or it pops in after paint. */
let EAGER = false;

function toHtml(el, indent) {
  const pad = '  '.repeat(indent);
  const style = (o) =>
    Object.entries(o || {})
      .filter(([, v]) => v !== undefined && v !== null && v !== '')
      .map(([k, v]) => `${camelToKebab(k)}:${v}`)
      .join(';');

  if (el.tag === 'img') {
    const st = el.style ? ` style="${style(el.style)}"` : '';
    // Relative, so the static build works from any subdirectory — GitHub Pages
    // serves project sites from /<repo>/ and absolute paths would 404 there.
    const src = String(el.src).replace(/^\//, '');
    const load = EAGER ? 'eager' : 'lazy';
    return `${pad}<img src="${src}" alt="${el.alt ?? ''}"${st} loading="${load}" decoding="async">`;
  }
  const cls = el.cls ? ` class="${el.cls}"` : '';
  const st = ` style="${style(el.style)}"`;
  const aria = el.ariaHidden ? ' aria-hidden="true"' : '';

  let inner = '';
  if (el.runs) {
    const parts = el.runs
      .map((r) =>
        r.style
          ? `<span${r.cls ? ` class="${r.cls}"` : ''} style="${style(r.style)}">${escHtml(r.text)}</span>`
          : escHtml(r.text)
      )
      .join('');
    inner = `<span>${parts}</span>`; // single grid item — see toTsx
  } else if (el.text != null) inner = escHtml(el.text);

  const kids = (el.children || []).map((c) => toHtml(c, indent + 1));
  if (!inner && !kids.length) return `${pad}<div${cls}${st}${aria}></div>`;
  if (inner && !kids.length) return `${pad}<div${cls}${st}${aria}>${inner}</div>`;
  return `${pad}<div${cls}${st}${aria}>${inner}\n${kids.join('\n')}\n${pad}</div>`;
}

// -------------------------------------------------------------------- build
const frames = {
  desktop: { tree: buildTree('home', manifest), prefix: 'D' },
  mobile: { tree: buildTree('mobile', manifest), prefix: 'M' },
};

// --- 1. TSX components
fs.rmSync(GEN, { recursive: true, force: true });
for (const [key, { tree, prefix }] of Object.entries(frames)) {
  const dir = path.join(GEN, key);
  fs.mkdirSync(dir, { recursive: true });
  const files = [];

  if (tree.backdrop.length) {
    const name = `${prefix}Backdrop`;
    files.push(name);
    fs.writeFileSync(
      path.join(dir, `${name}.tsx`),
      `import { asset } from '@/lib/asset';\n\n/** Page-wide background photography layer. */\nexport function ${name}() {\n  return (\n    <>\n${tree.backdrop
        .map((e) => toTsx(e, 3))
        .join('\n')}\n    </>\n  );\n}\n`
    );
  }

  for (const band of tree.bands) {
    const name = prefix + band.name;
    files.push(name);
    const doc = `/** ${band.layers.join(' · ')}\n *  y ${band.start} → ${band.end} (${band.end - band.start}px) */`;
    fs.writeFileSync(
      path.join(dir, `${name}.tsx`),
      `import { asset } from '@/lib/asset';\n\n${doc}\nexport function ${name}() {\n  return (\n    <>\n${band.els
        .map((e) => toTsx(e, 3))
        .join('\n')}\n    </>\n  );\n}\n`
    );
  }

  fs.writeFileSync(
    path.join(dir, 'index.tsx'),
    `${files.map((f) => `import { ${f} } from './${f}';`).join('\n')}\n\n` +
      `/** ${key} artboard — ${tree.frame.w}×${tree.frame.h}, ${tree.bands.length} sections. */\n` +
      `export function ${prefix}Artboard() {\n  return (\n    <>\n${files.map((f) => `      <${f} />`).join('\n')}\n    </>\n  );\n}\n`
  );
}

// --- 2. static site
fs.rmSync(DIST, { recursive: true, force: true });
fs.mkdirSync(DIST, { recursive: true });
for (const d of ['images', 'icons', 'fonts']) {
  fs.cpSync(path.join(PROJ, 'public', d), path.join(DIST, d), { recursive: true });
}

const section = (label, els, name) =>
  `      <!-- ${name} -->\n${els.map((e) => toHtml(e, 4)).join('\n')}`;

const FOLD = { desktop: 1100, mobile: 900 };

const body = (key) => {
  const { tree } = frames[key];
  const out = [];
  if (tree.backdrop.length) { EAGER = false; out.push(section(key, tree.backdrop, 'backdrop')); }
  for (const b of tree.bands) {
    EAGER = b.start < FOLD[key];
    out.push(section(key, b.els, `${b.name}  (y ${b.start}→${b.end})`));
  }
  EAGER = false;
  return out.join('\n');
};

const html = `<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Экосфера — отдых вне города</title>
<meta name="description" content="Уютные домики, свежий воздух, тишина леса и всё необходимое для идеальных выходных, отпуска или семейного отдыха. 40 минут от Владивостока.">
<meta property="og:title" content="Экосфера — отдых вне города">
<meta property="og:description" content="Уютные домики в лесу, 40 минут от Владивостока.">
<meta property="og:locale" content="ru_RU">
<meta name="theme-color" content="#123206">
<link rel="preload" href="fonts/FormaDJRCyrillicDisplay-Regular-Testing.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="fonts/CoFoSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="styles.css">
<script>
(function(){var d=document.documentElement;
function s(){var w=d.clientWidth;d.style.setProperty('--sd',w/${frames.desktop.tree.frame.w});d.style.setProperty('--sm',w/${frames.mobile.tree.frame.w});}
s();addEventListener('resize',s,{passive:true});addEventListener('orientationchange',s);})();
</script>
</head>
<body>
<main>
  <div class="canvas canvas--desktop only-desktop">
    <div class="canvas__inner">
${body('desktop')}
    </div>
  </div>
  <div class="canvas canvas--mobile only-mobile">
    <div class="canvas__inner">
${body('mobile')}
    </div>
  </div>
</main>
</body>
</html>
`;
fs.writeFileSync(path.join(DIST, 'index.html'), html);
fs.writeFileSync(path.join(DIST, '.nojekyll'), '');

// stylesheet — the same rules as globals.css, minus the Tailwind layer
const css = `@font-face{font-family:'Forma DJR Cyrillic Display';src:url('fonts/FormaDJRCyrillicDisplay-Regular-Testing.woff2') format('woff2');font-weight:400;font-display:swap}
@font-face{font-family:'CoFo Sans';src:url('fonts/CoFoSans-Regular.woff2') format('woff2');font-weight:400;font-display:swap}
@font-face{font-family:'CoFo Sans';src:url('fonts/CoFoSans-Medium.woff2') format('woff2');font-weight:500;font-display:swap}
@font-face{font-family:'CoFo Sans';src:url('fonts/CoFoSans-Bold.woff2') format('woff2');font-weight:700;font-display:swap}
@font-face{font-family:'CoFo Sans';src:url('fonts/CoFoSans-Black.woff2') format('woff2');font-weight:900;font-display:swap}
@font-face{font-family:'CoFo Robert';src:url('fonts/CoFoRobert-RegularItalic.woff2') format('woff2');font-weight:400;font-style:italic;font-display:swap}

:root{
--font-forma:'Forma DJR Cyrillic Display';
--font-cofo:'CoFo Sans';
--font-robert:'CoFo Robert';
--font-onest:'Onest','CoFo Sans',system-ui,sans-serif;
--sd:1;--sm:1;
--design-w:${frames.desktop.tree.frame.w};--design-h:${frames.desktop.tree.frame.h};
--design-w-m:${frames.mobile.tree.frame.w};--design-h-m:${frames.mobile.tree.frame.h};
--color-forest:#123206;--color-lime:#a0f447;
}
*{box-sizing:border-box}
html,body{margin:0;padding:0;background:var(--color-forest);overflow-x:hidden}
body{font-family:var(--font-cofo),system-ui,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;text-rendering:optimizeLegibility}

.canvas{position:relative;overflow:hidden;width:100%}
.canvas__inner{position:relative;transform-origin:top left;will-change:transform}
.canvas--desktop{height:calc(var(--design-h) * 1px * var(--sd))}
.canvas--desktop .canvas__inner{width:calc(var(--design-w) * 1px);height:calc(var(--design-h) * 1px);transform:scale(var(--sd))}
.canvas--mobile{height:calc(var(--design-h-m) * 1px * var(--sm))}
.canvas--mobile .canvas__inner{width:calc(var(--design-w-m) * 1px);height:calc(var(--design-h-m) * 1px);transform:scale(var(--sm))}

.only-desktop{display:none}
.only-mobile{display:block}
@media (min-width:768px){.only-desktop{display:block}.only-mobile{display:none}}

.n{position:absolute}
.n>img{display:block;width:100%;height:100%;object-fit:fill;pointer-events:none;user-select:none}
.t{position:absolute;white-space:pre-wrap;overflow-wrap:break-word}
.gradient-text{-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent}
a{color:inherit;text-decoration:none}
`;
fs.writeFileSync(path.join(DIST, 'styles.css'), css);

// -------------------------------------------------------------------- report
for (const [k, { tree }] of Object.entries(frames)) {
  const s = tree.stats;
  console.log(
    `${k.padEnd(8)} ${tree.frame.w}x${tree.frame.h}  sections=${String(tree.bands.length).padStart(2)}  ` +
      `nodes=${s.nodes} text=${s.texts} img=${s.imgs} icon=${s.icons} skipped=${s.skipped}`
  );
}
const size = (p) => (fs.statSync(p).size / 1024).toFixed(0);
console.log(`\ndist/index.html  ${size(path.join(DIST, 'index.html'))} KB`);
console.log(`dist/styles.css  ${size(path.join(DIST, 'styles.css'))} KB`);
console.log('\nsections:');
for (const [k, { tree }] of Object.entries(frames))
  console.log(`  ${k}: ${tree.bands.map((b) => b.name).join(', ')}`);
