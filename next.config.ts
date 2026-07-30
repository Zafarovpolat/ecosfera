import type { NextConfig } from 'next';

/** Repo name on GitHub Pages — the site is served from
 *  https://<user>.github.io/<basePath>/ , so assets need the prefix. */
const basePath = process.env.NEXT_PUBLIC_BASE_PATH ?? '';

const nextConfig: NextConfig = {
  output: 'export',
  basePath: basePath || undefined,
  images: { unoptimized: true },
  trailingSlash: true,
};

export default nextConfig;
