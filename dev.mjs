#!/usr/bin/env node
/**
 * Локальный сервер для разработки. Зависимостей нет.
 *
 *   node dev.mjs        → http://localhost:3000
 *   node dev.mjs 8080   → другой порт
 *
 * index.html пересобирается на каждый запрос страницы, так что правки
 * в src/ видны сразу после обновления вкладки.
 */

import { createServer } from 'node:http';
import { readFile, stat } from 'node:fs/promises';
import { dirname, join, normalize, extname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';

const ROOT = dirname(fileURLToPath(import.meta.url));
const PORT = Number(process.argv[2] ?? 3000);

const TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.mjs': 'text/javascript; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.webp': 'image/webp',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.woff2': 'font/woff2',
  '.ico': 'image/x-icon',
  '.json': 'application/json; charset=utf-8',
};

function rebuild() {
  try {
    execFileSync(process.execPath, [join(ROOT, 'build.mjs')], { stdio: 'inherit' });
    return null;
  } catch {
    return 'Сборка упала — смотри сообщение выше.';
  }
}

createServer(async (req, res) => {
  const url = decodeURIComponent(new URL(req.url, 'http://localhost').pathname);
  const isPage = url === '/' || url.endsWith('.html');

  if (isPage) {
    const err = rebuild();
    if (err) {
      res.writeHead(500, { 'content-type': 'text/plain; charset=utf-8' });
      return res.end(err);
    }
  }

  // normalize + отрезание ведущих ../ не даёт выйти за пределы каталога
  const rel = normalize(url === '/' ? 'index.html' : url).replace(/^(\.\.[/\\])+/, '');
  const file = join(ROOT, rel);

  try {
    const info = await stat(file);
    if (info.isDirectory()) throw new Error('directory');
    const body = await readFile(file);
    res.writeHead(200, {
      'content-type': TYPES[extname(file)] ?? 'application/octet-stream',
      'cache-control': 'no-store',
    });
    res.end(body);
  } catch {
    res.writeHead(404, { 'content-type': 'text/plain; charset=utf-8' });
    res.end(`404 — ${rel}`);
  }
}).listen(PORT, () => {
  console.log(`Экосфера → http://localhost:${PORT}`);
});
