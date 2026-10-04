# AGO Theme — Claude Context

## Project Overview
**AGO** — Corporate/tech company site.
Custom WordPress theme built from scratch with Full Site Editing (FSE).

## Stack
| Layer       | Technology                                        |
|-------------|----------------------------------------------------|
| CMS         | WordPress with FSE                                |
| CSS         | Tailwind CSS v4 via Vite (`@tailwindcss/vite`)     |
| Build       | Vite + TailPress framework (`pnpm dev` / `pnpm build`) |
| Fonts       | Golos, Instrument Serif via Google Fonts           |

> ⚠️ **Build note:** the theme currently has two CSS source trees. Only `resources/css/` + `resources/js/` are wired up (imported by `functions.php` via `TailPress\Framework\Assets\ViteCompiler`, compiled by `vite.config.js` into `dist/`). The `assets/src/css/` tree + `tailwind.config.js` (Tailwind v3 style) are **not** part of the active build — leftover from an earlier setup. Edit CSS in `resources/css/`, not `assets/src/css/`.

---

## Key Commands
```bash
pnpm install   # Install dependencies
pnpm dev       # Vite dev server (watch mode)
pnpm build     # Production build → outputs to /dist (manifest read by TailPress ViteCompiler)
```
Yes — same as the previous theme, you need to run `pnpm build` after any CSS/JS change in `resources/` so WordPress picks up the compiled, hashed assets from `/dist`. There's no separate "Tailwind CLI" step; `pnpm build` already runs Tailwind v4 through the Vite plugin.

---

## Directory Structure
```
theme-ago/
├── assets/
│   ├── src/
│   │   └── css/
│   │       ├── app.css                ← main entry, imports all layers
│   │       ├── base/
│   │       │   └── typography.css
│   │       ├── components/
│   │       │   ├── buttons.css        ← .ago-btn (átomo botón)
│   │       │   └── containers.css     ← (legacy, fuera del build)
│   │       └── blocks/
│   │           └── [name].css         ← one file per block
│   └── dist/
│       └── css/
│           └── app.css                ← compiled output (NEVER edit)
├── blocks/
│   └── [name]/
│       └── render.php
├── parts/
│   ├── header.php
│   └── footer.php
├── templates/
│   └── index.html
├── inc/
│   └── setup.php
├── functions.php
├── style.css                          ← theme header + CSS custom properties
├── theme.json                         ← WP FSE tokens
├── tailwind.config.js
└── package.json
```

---

## Design Tokens — AGO

> Fuente única de verdad: `resources/css/theme.css` (bloque `@theme`). Mantener `theme.json` sincronizado. Los valores van a cambiar: **nunca** hardcodear hex ni px de estos tokens — siempre `var(--…)`.

### Colores

| Token              | Hex       | Uso                                  |
|--------------------|-----------|--------------------------------------|
| `--color-ago-navy` | `#091628` | Mayoría de títulos, botón oscuro     |
| `--color-ago-blue` | `#003187` | Tono secundario: círculo del botón, texto botón claro |
| `--color-white`    | `#ffffff` | Fondos, texto sobre oscuro           |

### Tipografía — tamaños

| Token             | Tamaño   | Uso                 |
|-------------------|----------|---------------------|
| `--text-h1`       | 80px     | Títulos principales |
| `--text-h2`       | 60px     | Otros títulos       |
| `--text-subtitle` | 24px     | Subtítulos          |
| `--text-body`     | 25px     | Contenido general   |
| `--text-btn`      | 24px     | Texto del botón (medido de la imagen — por confirmar) |

Familia tipográfica: **pendiente de definir para AGO** (hoy se cargan Golos Text + Instrument Serif heredadas).

---

## Botón — átomo único `.ago-btn`

- Markup: siempre vía `get_template_part( 'template-parts/atoms/button', null, [ 'text', 'url', 'variant', 'class' ] )`.
- Estructura BEM: `.ago-btn` > `.ago-btn__icon` (círculo + flecha) + `.ago-btn__label`.
- Color por modificador: `.ago-btn--dark` (fondo navy, texto blanco) · `.ago-btn--light` (fondo blanco, texto azul) · `.ago-btn--outline` (transparente, borde navy, texto azul).
- Ícono por argumento `icon`: `arrow` (default) · `mail` · `phone` (con mail/phone la URL se completa sola a mailto:/tel:).
- Medida base: `--btn-width: 337px` · `--btn-height: 79px`.
- **Tamaño distinto = mismo botón**, sobreescribiendo variables desde la clase del bloque padre (nunca crear otro botón):
```css
.hero__cta { --ago-btn-width: 280px; --ago-btn-height: 64px; --ago-btn-font: 1.25rem; }
```
  El círculo y el padding se calculan solos a partir de la altura.

---

## Absolute Rules — CSS

### Naming convention: BEM with block prefix `ago-`
Pattern: `[block]__[element]--[modifier]`

| ✅ CORRECT               | ❌ WRONG           |
|--------------------------|--------------------|
| `.hero__title`           | `.title`           |
| `.header__nav`           | `.nav`             |
| `.services__card`        | `.card`            |
| `.hero__title--large`    | `.large`           |
| `.ago-btn--dark`         | `.btn-dark`        |

- **Shared cross-block classes** → prefix `.ago-`
- **Tailwind in HTML** → ONLY for one-off non-repeatable tweaks
- **Structural / repeated styles** → ALWAYS in `@layer components`
- **Never hardcode colors** → always `var(--color-ago-*)` / tokens de `theme.css`
- **One CSS file per block** → `assets/src/css/blocks/[block-name].css`

### @layer components vs Tailwind in HTML
- `@layer components` → layout, repeatable patterns, BEM classes
- `Tailwind utilities in HTML` → minor one-off adjustments (e.g., `mt-4` on a single element)

---

## Absolute Rules — PHP

- `render.php` → markup + semantic classes only, **no complex logic**
- Complex logic → goes in `inc/` as named functions
- Always sanitize output:
```php
esc_html()      // plain text
esc_url()       // URLs
wp_kses_post()  // HTML content
```
- No closing `?>` at end of PHP-only files
- Use `defined('ABSPATH') || exit;` at top of all PHP files

---

## WordPress FSE Rules

- Templates go in `/templates/` as `.html` files
- Template parts go in `/parts/` as `.php` or `.html` files
- Custom blocks go in `/blocks/[name]/render.php`
- Register blocks in `functions.php` or `inc/blocks.php`
- Use `theme.json` for WP color/font presets — keep in sync with CSS tokens

---

## Adding a New Block

Referencia: `blocks/hero/`. Registro automático (`inc/blocks.php` escanea `/blocks/*/block.json`), no tocar `functions.php`.

1. `/blocks/[name]/block.json` — `"name": "ago/[name]"`, `"category": "ago"`, atributos, `"render": "file:./render.php"`
2. `/blocks/[name]/render.php` — solo markup + clases BEM, escapado
3. `/blocks/[name]/editor.js` (+ `editor.asset.php` con dependencias) — campos en InspectorControls + ServerSideRender (JS plano, sin build)
4. `/resources/css/blocks/[name].css` — importarlo en `resources/css/app.css` **y** en `resources/css/editor-style.css`
5. Botones: siempre `template-parts/atoms/button.php`. Títulos fino+negrita: `template-parts/atoms/title.php` (`.ago-title`, CSS en `resources/css/atoms/title.css`)
6. `pnpm build`

### Bloques existentes
- `ago/hero` — Hero con fondo, pre-título, título, contenido, fecha y botón (campos en barra lateral + ServerSideRender).
- `ago/text-image` — Texto + Imagen genérico. Opciones: título al lado o arriba centrado (`--side` / `--top`), invertir lados (`--reverse`), título fino+negrita en una o dos líneas, imagen secundaria superpuesta, botón opcional. Contenido = InnerBlocks (párrafo, lista, heading) → admite bullets.
- `ago/banner-cta` — Banner de lado a lado (sin max-width en el fondo, sin degradado): imagen de fondo, título fino+negrita, contenido con negritas (RichText) y botón.
- `ago/section-header` — Encabezado de sección. `--side`: texto en el contenedor + imagen decorativa a la derecha a sangre. `--center`: texto centrado + imágenes a sangre en ambos lados. Título vía átomo `atoms/title.php`, contenido InnerBlocks.
- `ago/logo-grid` — Cuadrícula de logos: N por fila (1–6, en móvil 2), los sobrantes bajan centrados; enlace opcional por logo.
- `ago/point-list` — Lista con puntos: título + puntos con círculo azul conectados por línea vertical y separador bajo cada texto; 1 o 2 columnas (reparto en orden, mitad hacia arriba).
- `ago/cta-buttons` — Frase + botones: frase centrada (24px negrita) y hasta 2 botones (átomo) centrados.
- `ago/cta-card` — Tarjeta CTA redondeada dentro del contenedor: imagen de fondo (sin degradado), título (átomo), contenido opcional, hasta 2 botones con ícono flecha/correo/teléfono.
- `ago/timeline` — Línea de tiempo: tarjetas título/contenido/año que alternan claro/oscuro solas (:nth-child). Muesca curva bajo el año (95×35px, a 60px de la derecha) con `mask` + `mask-composite` en `.timeline__card` (no clip-path fijo); sombra con drop-shadow en el `<li>`.
- `ago/zigzag-items` — Pilares en zigzag: ícono en círculo + número automático (01…), título y contenido. Impares imagen arriba / pares imagen abajo (:nth-child + order); N por fila (2 en tablet, 1 en móvil sin zigzag).
- `ago/tab-cards` + hija `ago/tab-card` — Tarjetas con encabezado navy + flecha (Visión/Misión/Valores). Grid 2 col; `wide` = fila completa. Contenido InnerBlocks (párrafo/lista → viñetas y negritas). Primer bloque con hijo (`parent` en block.json).
- `ago/team-grid` — Equipo / junta directiva (opción `nameStyle`: normal con puesto / centrado en negrita): título + fotos con corte diagonal inferior izq. (clip-path %) y panel blanco encima (también con corte) con puesto (píldora navy) y nombre. Cortes/medidas en variables.
- `ago/section-bg` — Sección con fondo: contenedor full width que solo pinta el fondo (gris claro / blanco / imagen) detrás de cualquier bloque hijo; opcional imágenes decorativas izq./der. centradas verticalmente a TODA la sección; colapsa el relleno entre hijos. Por eso los bloques de sección tienen fondo transparente.
- `ago/numbered-cards` — Tarjetas numeradas (01…): título + contenido, alternan blanco/azul (:nth-child), esquina inf. der. recortada con mask SVG (`--nc-corner`); borde de la blanca = capa exterior 1px con la misma máscara. N por fila, sobrantes centrados.

El header (`header.php`) NO es bloque. En páginas con `ago/hero` recibe `.site-header--overlay` y flota encima del hero.

---

## Design Principles

- Mobile-first always
- Sections alternate between `--white` and `--light` backgrounds unless design specifies otherwise
- Dark sections use white text and `ago-btn--light`
- Titles in `--color-ago-navy` unless on dark background
- Body text color: pendiente de definir para AGO
