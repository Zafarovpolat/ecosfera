/** Prefixes a public asset path with the deploy basePath (GitHub Pages subdir). */
const BASE = process.env.NEXT_PUBLIC_BASE_PATH ?? '';

export function asset(p: string): string {
  return `${BASE}${p}`;
}
