/*
 * Compiles ../assets/css/src.css to ../assets/css/main.css.
 *
 * DEV-ONLY. main.css is committed, so running the theme never needs Node.
 * Re-run after editing template markup or src.css:
 *
 *   cd wp-content/themes/zoneplay/tools && npm install && npm run build
 *
 * Uses the same engine as Tailwind's CLI: compile() from `tailwindcss`
 * plus the oxide Scanner, pointed at the theme's own *.php / assets/js so
 * main.css carries only the classes the theme chrome actually uses. Page
 * content (Editable HTML Block) compiles its own Tailwind elsewhere.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';
import { compile } from 'tailwindcss';
import { Scanner } from '@tailwindcss/oxide';
import { transform as lightning, Features } from 'lightningcss';

// Mirror @tailwindcss/vite's own Lightning CSS pass (@tailwindcss/node's
// optimize()): force media-query + nesting lowering — regardless of the
// targets below — and use the same browser floor. Without `include`,
// Lightning leaves Tailwind v4's `@media (width >= 64rem)` range syntax
// as-is; @tailwindcss/vite lowers it to `@media (min-width: 64rem)`, and
// the Editable HTML Block plugin's compiler now does too, so the theme's
// main.css must match to keep one media-query style across the site.
const LIGHTNING_INCLUDE = Features.MediaQueries | Features.Nesting;
const LIGHTNING_TARGETS = {
  safari: (16 << 16) | (4 << 8), // 16.4
  ios_saf: (16 << 16) | (4 << 8), // 16.4
  firefox: 128 << 16,
  chrome: 111 << 16,
};

const require = createRequire(import.meta.url);
const here = path.dirname(fileURLToPath(import.meta.url));
const themeDir = path.resolve(here, '..');
const srcFile = path.join(themeDir, 'assets/css/src.css');
const outFile = path.join(themeDir, 'assets/css/main.css');

// Resolve `@import "tailwindcss"` (and its sub-imports) from node_modules.
function loadStylesheet(id, base) {
  let file;
  if (id === 'tailwindcss') file = require.resolve('tailwindcss/index.css');
  else if (id.startsWith('tailwindcss/')) file = require.resolve(id);
  else file = path.resolve(base, id);
  return { base: path.dirname(file), content: fs.readFileSync(file, 'utf8') };
}

const SOURCES = [
  { base: themeDir, pattern: '*.php', negated: false },
  { base: path.join(themeDir, 'inc'), pattern: '**/*.php', negated: false },
  { base: path.join(themeDir, 'assets/js'), pattern: '**/*.js', negated: false },
];

async function build() {
  const input = fs.readFileSync(srcFile, 'utf8');
  const compiler = await compile(input, {
    base: path.dirname(srcFile),
    loadStylesheet,
    loadModule: () => {
      throw new Error('@plugin / @config are not supported in this theme build');
    },
  });

  const scanner = new Scanner({ sources: SOURCES });
  const candidates = scanner.scan();
  const raw = compiler.build(candidates);

  const { code } = lightning({
    filename: 'main.css',
    code: Buffer.from(raw),
    minify: true,
    include: LIGHTNING_INCLUDE,
    targets: LIGHTNING_TARGETS,
  });

  fs.writeFileSync(outFile, code);
  console.log(
    `main.css: ${code.length} bytes (from ${raw.length} raw, ${candidates.length} candidates)`
  );
}

await build();

if (process.argv.includes('--watch')) {
  console.log('watching for changes… (Ctrl+C to stop)');
  const watch = (dir) =>
    fs.watch(dir, { recursive: true }, (_e, f) => {
      if (f && /\.(php|js|css)$/.test(f)) {
        build().catch((err) => console.error(err.message));
      }
    });
  watch(themeDir);
}
