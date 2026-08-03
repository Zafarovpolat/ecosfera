#!/usr/bin/env node
/**
 * Сборка index.html из src/template.html и src/partials/*.html.
 *
 * Зависимостей нет — только стандартная библиотека Node.
 *
 *   <!--@include 05-houses-->   подставляет src/partials/05-houses.html
 *   {{v}}                       метка версии для сброса кеша css/js
 *
 * Отступ строки с плейсхолдером достаётся первой строке вставки —
 * ровно так же, как было в исходной вёрстке, поэтому diff остаётся читаемым.
 */

import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = dirname(fileURLToPath(import.meta.url));
const SRC = join(ROOT, 'src');
const OUT = join(ROOT, 'index.html');

const INCLUDE = /^(\s*)<!--@include\s+([\w-]+)-->\s*$/;
const MAX_DEPTH = 10;

/** Разворачивает @include-плейсхолдеры, рекурсивно. */
function render(text, depth = 0, trail = []) {
  if (depth > MAX_DEPTH) {
    throw new Error(`Слишком глубокая вложенность include: ${trail.join(' → ')}`);
  }

  return text
    .split('\n')
    .map((line) => {
      const m = line.match(INCLUDE);
      if (!m) return line;

      const [, indent, name] = m;
      const file = join(SRC, 'partials', `${name}.html`);
      if (!existsSync(file)) {
        throw new Error(`Партиал не найден: src/partials/${name}.html (из ${trail.at(-1) ?? 'template.html'})`);
      }
      if (trail.includes(name)) {
        throw new Error(`Циклический include: ${[...trail, name].join(' → ')}`);
      }

      const body = readFileSync(file, 'utf8').replace(/\n$/, '');
      return indent + render(body, depth + 1, [...trail, name]);
    })
    .join('\n');
}

const stamp = Math.floor(Date.now() / 1000);
const template = readFileSync(join(SRC, 'template.html'), 'utf8');
const html = render(template).replaceAll('{{v}}', String(stamp));

writeFileSync(OUT, html, 'utf8');

const kb = (Buffer.byteLength(html, 'utf8') / 1024).toFixed(0);
console.log(`index.html собран — ${kb} КБ, версия ассетов ${stamp}`);
