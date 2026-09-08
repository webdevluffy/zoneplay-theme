# Zone Play Cardiff theme (`zoneplay`)

Custom classic WordPress theme, converted from `astro-build-site`. Uses
Tailwind CSS v4. The head, header, footer and template chrome live in the
theme; **page bodies are authored with the Editable HTML Block plugin**,
which compiles its own per‑post Tailwind — the theme never scans post
content.

## Structure

```
style.css              Theme header only
functions.php           Loads inc/*
header.php / footer.php  Static nav + footer (Astro parity). Menus come later.
index.php page.php single.php archive.php search.php 404.php
searchform.php comments.php
inc/setup.php           Theme supports, menu locations, content width
inc/enqueue.php         Fonts + main.css + navigation.js + opening-hours.js
inc/cleanup.php         Strips unused WP defaults (emoji, block-library CSS, …)
assets/css/src.css      Tailwind source (mirrors Astro global.css)
assets/css/main.css     Compiled + minified — committed, no build needed to run
assets/fonts/           Self-hosted Fredoka + Nunito (no Google Fonts)
assets/js/              navigation.js (mobile menu), opening-hours.js
assets/images/          logo.webp, favicon.png
tools/                  Dev-only CSS build (Tailwind + oxide + lightningcss)
```

## Rebuilding the CSS

Only needed after editing template markup or `src.css`:

```sh
cd wp-content/themes/zoneplay/tools
npm install
npm run build          # writes ../assets/css/main.css
npm run watch          # rebuild on change
```

`tools/build-css.mjs` runs `compile()` from `tailwindcss` + the oxide
`Scanner` over the theme's own `*.php` / `assets/js/*.js`, then minifies
with Lightning CSS.

## Not yet done

- `screenshot.png` (1200×900) — add for the Appearance picker.
- Dynamic nav (menu location `primary`) and footer widgets — registered,
  wired next.
- `theme.json` — intentionally omitted (classic theme; content is
  plugin‑styled).
