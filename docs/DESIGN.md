# DESIGN.md — Alfiz Ilham Portfolio

Design specification for rebuilding this personal portfolio from scratch using **vanilla HTML, CSS, and JavaScript**. This document is the single source of truth for layout, typography, color, spacing, and component behavior.

---

## 1. Tech Stack & Dependencies

| Purpose                             | Library                                                  | CDN                                                                                                    |
| ----------------------------------- | -------------------------------------------------------- | ------------------------------------------------------------------------------------------------------ |
| Font                                | Plus Jakarta Sans (all weights: 400, 500, 600, 700, 800) | `https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap`     |
| UI Icons                            | Lucide Icons                                             | `https://unpkg.com/lucide@latest/dist/umd/lucide.js`                                                   |
| Brand Icons                         | Bootstrap Icons                                          | `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css`                         |
| Map                                 | Leaflet.js + CARTO Dark Tiles                            | `https://unpkg.com/leaflet@1.9.4/dist/leaflet.js` + `https://unpkg.com/leaflet@1.9.4/dist/leaflet.css` |
| Animation (scroll reveal)           | Vanilla JS `IntersectionObserver`                        | —                                                                                                      |
| Animation (fold text, counters)     | GSAP 3                                                   | `https://unpkg.com/gsap@3/dist/gsap.min.js`                                                            |

No frameworks (no React/Tailwind/Bootstrap CSS) — pure HTML/CSS/JS only. Bootstrap is used **only** for its icon font, not its CSS framework.

---

## 2. Design Tokens

### 2.1 Color Palette

```css
:root {
  /* Base */
  --color-bg: #ffffff;
  --color-bg-soft: #f5f5f5; /* card backgrounds, subtle panels */
  --color-bg-dark: #0a0a0a; /* footer, dark sections, map */
  --color-text: #0a0a0a; /* primary text, near-black */
  --color-text-muted: #6b6b6b; /* secondary text, descriptions */
  --color-text-faint: #a3a3a3; /* placeholders, tiny labels */
  --color-border: #e5e5e5; /* dividers, input borders */
  --color-border-dark: #2a2a2a; /* borders on dark bg */

  /* Accent (minimal use — this is a monochrome design) */
  --color-accent: #0a0a0a; /* buttons use black, not a color accent */
  --color-white: #ffffff;
  --color-black: #000000;

  /* Status */
  --color-live-green: #22c55e; /* "LIVE" visitor indicator dot */

  /* Footer */
  --color-footer-bg: #0d0d0d;
  --color-footer-text: #ffffff;
  --color-footer-muted: #8a8a8a;
}
```

This is a **monochrome / black-and-white design**. There is no colored accent brand palette — contrast and typography scale carry the visual hierarchy. The only non-grayscale colors appear in: tech-stack brand icons (their native brand colors), the green "LIVE" dot, and star ratings (gold/yellow `#facc15` optional, or kept black outline).

### 2.2 Typography

Font family: `'Plus Jakarta Sans', sans-serif` — used for **everything** (headings and body).

| Token          | Size (desktop) | Size (mobile) | Weight | Line-height | Usage                                                                                                |
| -------------- | -------------- | ------------- | ------ | ----------- | ---------------------------------------------------------------------------------------------------- |
| `--fs-hero`    | 120px          | 48px          | 800    | 0.95        | Giant background wordmark ("ALFIZ ILHAM" repeat, footer "alfzilham")                                 |
| `--fs-h1`      | 64px           | 36px          | 800    | 1.05        | Section big headings ("Let's Work Together", "HOW CAN I HELP YOU", "Ready to Build Something Bold?") |
| `--fs-h2`      | 44px           | 30px          | 700    | 1.1         | Name heading ("Alfiz Ilham"), sub-section titles                                                     |
| `--fs-h3`      | 28px           | 22px          | 700    | 1.2         | Card titles, "I BUILD SOFTWARE..."                                                                   |
| `--fs-h4`      | 20px           | 18px          | 600    | 1.3         | Service item titles, FAQ questions                                                                   |
| `--fs-body-lg` | 18px           | 16px          | 400    | 1.6         | Intro paragraphs, descriptions                                                                       |
| `--fs-body`    | 16px           | 15px          | 400    | 1.6         | Standard body text                                                                                   |
| `--fs-body-sm` | 14px           | 13px          | 400    | 1.5         | Labels, meta text, form labels                                                                       |
| `--fs-caption` | 12px           | 11px          | 500    | 1.4         | Tiny uppercase labels ("01", "SERVICES", nav category dots)                                          |

Letter-spacing: headings use `-0.02em` (tight), uppercase labels use `0.08em` (wide tracking).

### 2.3 Spacing Scale

```css
:root {
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-7: 48px;
  --space-8: 64px;
  --space-9: 96px;
  --space-10: 128px;
}
```

- Section vertical padding (desktop): `120px` top/bottom (`--space-9` + `--space-7`)
- Section vertical padding (mobile): `64px` top/bottom
- Container max-width: `1280px`, centered, horizontal padding `24px` (mobile) / `64px` (desktop)
- Grid gap standard: `24px` (cards), `32px` (major columns)

### 2.4 Border Radius

```css
--radius-sm: 8px; /* inputs, small buttons */
--radius-md: 16px; /* cards, panels */
--radius-lg: 24px; /* large image containers, map card */
--radius-full: 999px; /* pill buttons, avatar circles, floating icon badges */
```

### 2.5 Shadows

```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
--shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
--shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.1);
--shadow-icon-badge: 0 2px 8px rgba(0, 0, 0, 0.12); /* floating tech icon bubbles */
```

### 2.6 Buttons

- **Primary button** (e.g. "Send Message", "Start a Project", "Get Started"): black background (`#0a0a0a`), white text, `border-radius: var(--radius-full)`, padding `14px 28px`, font-weight 600, includes trailing icon (arrow/external-link from Lucide), hover: slight scale `1.02` + opacity `0.9`.
- **Secondary/ghost button** (e.g. "View My Work →", "Or reach me on WhatsApp →"): transparent background, black text, underline or arrow suffix, no border.
- **Outline button** (e.g. "VIEW MORE PROJECTS", filter tabs "ALL"): `1px solid var(--color-border)`, `border-radius: var(--radius-full)`, padding `10px 20px`, active/selected state = black background + white text.

---

## 3. Global Layout Rules

- **Navbar**: fixed/sticky top, white background, height `72px`, padding `0 32px`. On mobile ≤480px the inner bar keeps `20px` side padding (and the hamburger drops its negative right margin) so the "Alfiz." brand and hamburger never touch the viewport edges. Layout (left → right):
  - **Logo**: text wordmark **"Alfiz."** (not an icon/badge) — `--fs-h4` scale, weight 800, black, with the trailing period styled as part of the brand.
  - **Nav links** (centered): About, Skills, Projects, Services, Gallery, FAQ, Contact — `--fs-body-sm`, weight 500, `--color-text-muted`, hover → black.
  - **Language toggle pill**: rounded pill switch with "EN" / "ID" segments, active segment = black bg + white text, inactive = transparent + muted text. `border-radius: var(--radius-full); border: 1px solid var(--color-border);`
  - **Social icons** (far right): Instagram, LinkedIn, GitHub — Bootstrap Icons, `20px`, `--color-text`, spaced `16px` apart, no background/border (plain icon links).
- **Floating "N" avatar badges**: small circular black badges (36px diameter) with a white "N" logo (Alfiz's initial-mark, distinct from the navbar text logo) appear repeatedly down the **left edge** of the page, one per section boundary — purely decorative scroll-position markers, not clickable/functional. `position: absolute; left: 24px;` relative to each section, `background: #0a0a0a; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; z-index: 5;`. These repeat roughly once per major section (~16+ instances down the full page).
- **Grid system**: CSS Grid / Flexbox only, no framework. 12-column conceptual grid, common splits: 50/50, 60/40 (text/visual), 4-column card grids collapsing to 2-column (tablet) then 1-column (mobile).
- **Responsive breakpoints**:
  - `--bp-mobile: 480px`
  - `--bp-tablet: 768px`
  - `--bp-desktop: 1024px`
  - `--bp-wide: 1280px`

---

## 4. Section-by-Section Specification

**Confirmed scroll order (top → bottom), verified against source screenshots:**

1. Hero (giant wordmark + mascot)
2. Intro / About Me (name + tagline)
3. Marquee strip ("DEVELOPER ✦ ALFIZ ILHAM")
4. About Me Detail ("I BUILD SOFTWARE...")
5. Bio Statement Block + 4-stat counter row
6. My Skill Set (icon grid + filterable tool list)
7. Projects Teaser ("Curious What I've Built?")
8. Projects Grid (filterable portfolio)
9. Services ("How Can I Help You")
10. Tech Marquee Bar (dark, dual-row)
11. Gallery (B&W masonry)
12. Testimonials
13. FAQ ("Questions? We Have Answers")
14. Contact ("Let's Work Together")
15. Closing CTA ("Ready to Build Something Bold?")
16. Footer

### 4.1 Hero Section

**Purpose**: First-impression, name-brand introduction.

- Full-width background filled with repeating tiled text "ALFIZ ILHAM" in `--fs-hero` (approx 100-120px), font-weight 800, black text, tightly packed rows with **negative line-height / overlap** so rows visually touch/slightly overlap vertically (`line-height: 0.85`), arranged as a wallpaper/pattern (rows repeat edge-to-edge, no wrapping, `white-space: nowrap`, achieved via a horizontally repeated flex row or CSS background pattern).
- The **very first row is intentionally clipped at the top edge** of the section (only the lower half of the first line of letters is visible, cut off by the section/viewport boundary) — this is a deliberate crop, not a bug; achieve via `overflow: hidden` on the section wrapper with the text block positioned so its top edge starts above `y: 0`.
- Centered on top of the text pattern: a 3D-illustrated mascot character (Claude/Alfiz avatar — curly hair, sunglasses, black hoodie, black pants, sneakers) as a **placeholder image** (`<img src="assets/mascot-hero.png" alt="Alfiz mascot">`), roughly 380px tall, drop-shadow beneath. The mascot renders **on top of** the wordmark pattern (higher `z-index`), with the background text visible around/behind its silhouette — no card or background plate behind the mascot, it sits directly on the text pattern.
- No CTA button in this exact viewport — it's purely a visual/branding banner. Height: `100vh` or `~600px` fixed, `overflow: hidden`.

```css
.hero-wordmark {
  font-family: "Plus Jakarta Sans";
  font-weight: 800;
  font-size: clamp(48px, 10vw, 120px);
  line-height: 0.95;
  color: #0a0a0a;
  overflow: hidden;
  user-select: none;
}
.hero-mascot {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  max-height: 420px;
  filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.15));
}
```

---

### 4.2 Intro / About Me (Name + Tagline)

**Purpose**: Name reveal, one-line value proposition, primary CTAs.

- Left column:
  - Small eyebrow label: "Hi, My Name Is" (`--fs-body-sm`, `--color-text-muted`, letter-spacing wide)
  - Big name heading: "Alfiz Ilham" (`--fs-h1`, weight 800, two lines)
  - Tagline paragraph: "Building software, AI workflows & interfaces that leave a mark." (`--fs-body-lg`, `--color-text-muted`, max-width `480px`)
  - Meta row: bullet + "50+ Projects Delivered" (`--fs-body-sm`)
  - Button row: primary pill button "Let's Build Something" + ghost link "View My Work →"
- Right column:
  - Mascot illustration (standing, arms crossed, sunglasses) — placeholder image, ~450px tall
  - Two floating circular icon badges overlapping the image edges: one orange sunburst/asterisk icon (top-right), one purple gem/diamond icon (left), one blue atom/react icon (bottom-right) — each in a white circle, `width: 56px; height: 56px; border-radius: 50%; box-shadow: var(--shadow-icon-badge);`
  - Below/beside: a small floating card showing 3 overlapping circular avatar thumbnails + "50+ Projects Delivered" + 2 bullet lines ("Free Consultation", "Fast Response") inside a white rounded card with border and shadow.

Layout: 2-column grid, `grid-template-columns: 1fr 1fr;` gap `48px`, stacks to 1-column under 768px.

---

### 4.3 Marquee Strip ("DEVELOPER ✦ ALFIZ ILHAM")

**Purpose**: Decorative kinetic-typography divider.

- Full-width black bold text repeating "DEVELOPER ✦ ALFIZ ILHAM ✦" infinitely scrolling horizontally (CSS `@keyframes marquee` translateX loop, `animation: marquee 20s linear infinite;`).
- Text is rotated slightly (`transform: rotate(-2deg)`) and bleeds off both edges of the viewport.
- Font-size ~64-80px, font-weight 800, single line, no wrap. On tablet/mobile the curved SVG marquee scales up: `clamp(60px, 12vw, 88px)` at ≤768px and `clamp(72px, 18vw, 104px)` at ≤480px for stronger presence on small screens.
- Background: white (transparent to page bg).

```css
@keyframes marquee {
  from {
    transform: translateX(0) rotate(-2deg);
  }
  to {
    transform: translateX(-50%) rotate(-2deg);
  }
}
.marquee-track {
  display: flex;
  width: max-content;
  animation: marquee 25s linear infinite;
}
```

---

### 4.4 About Me Detail ("I BUILD SOFTWARE, AI & DIGITAL EXPERIENCES")

**Purpose**: Deeper bio + stats + tech ticker.

- Left column: mascot illustration seated at a desk with dual monitors (placeholder image), with 2 floating stat cards overlapping top corners of the image:
  - Card 1 (top-left): "50+" bold number + "Projects Completed" label — layered **behind** the image (`z-index: 0` vs image `z-index: 1`)
  - Card 2 (top-right): "4+" bold number + "Years Experience" label — in front of the image (`z-index: 2`)
  - Each stat card: white bg, compact padding `10px 14px`, number at `--fs-h3` weight 800, label at `--fs-caption` muted, `border-radius: var(--neu-radius)`, `box-shadow: var(--neu-shadow-out)`, positioned `absolute`
  - Both cards float gently (vertical bob ±8px, `3.5s ease-in-out infinite`) in opposite phases (card 2 delayed `-1.75s`); disabled under `prefers-reduced-motion`.
  - Below image: small pill label "Fullstack Developer, AI-Integrated Apps" centered, overlapping the image's bottom edge.
- Right column:
  - Eyebrow pill badge: "About Me" (small pill, border, `--fs-caption`)
  - Heading: "I BUILD SOFTWARE, AI & DIGITAL EXPERIENCES" (`--fs-h1`, weight 800, uppercase, tight line-height)
  - Paragraph description (`--fs-body`, `--color-text-muted`, max-width 480px)
  - Button row: primary "Let's Talk" (with external-link Lucide icon) + secondary "Download CV" (with download Lucide icon)
  - Decorative small squiggle/scribble SVG line icon to the right of the buttons.
- Below both columns (full width): horizontal scrolling logo strip of tech icons (HTML, CSS, JS, TS, Python, C#, Sass, React, Next.js, Tailwind, Redux...) each icon ~28px with label text beside it, muted grayscale style, separated by small gaps, in a single horizontal row (scrollable/overflow on mobile).

---

### 4.5 Bio Statement Block

**Purpose**: Short paragraph restating value prop in bold display type, followed by a 4-stat counter row.

- Full-width large bold paragraph (`--fs-h2`, weight 700, no max-width — fills the container, line-height 1.3): _"Building software and AI workflows for production websites, tools, and data-driven projects. With a background in design and 500+ commissions, I deliver work that's both functional and polished."_
- Section uses a reduced top padding override (`.bio-stats.section-padding { padding-top: 72px }`) to tighten the gap after the About section's tech ticker; mobile (≤768px) reverts to the standard `64px` rhythm.
- **Entrance animation ("FoldText")**: statement splits per word at runtime (`data-fold-text`, vanilla JS + GSAP CDN). Each word folds down from a top hinge — `rotateX: -92° → 0°`, `transform-origin: 50% 0%`, perspective `700px`, stagger `45ms`, `power3.out`, duration `0.65s` — with a crease-shading overlay (`::after` gradient, `mix-blend-mode: multiply`) driven by the `--fold-crease` CSS var. Triggered once via IntersectionObserver (~82% viewport). This section is excluded from the generic scroll reveal to avoid double animation.
- **Stat counter animation**: each `.stat-item-value` number counts up from `0` to target (`50+`, `4+`, `20+`, `30+`) over `1.2s` with `power2.out` when scrolled into view; suffix preserved, labels static.
- Both animations respect `prefers-reduced-motion` (shortened/skipped) and degrade gracefully to static text if GSAP fails to load.
- Horizontal divider line below (`1px solid var(--color-border)`).
- 4-column stat row: each column = big number (`--fs-h2`, weight 800) + label beneath (`--fs-body-sm`, `--color-text-muted`):
  - Projects Completed — 50+
  - Years of Experience — 4+
  - Tech Stack Mastered — 20+
  - Clients Satisfied — 30+

---

### 4.6 My Skill Set

**Purpose**: Filterable technology showcase — a single icon grid driven by category tabs + search.

- Section heading centered: "My Skill Set" (`--fs-h1`) + subheading paragraph centered, muted, max-width `560px`: "A curated collection of tools and technologies I work with — from frontend and backend to AI, design, and deployment."
- **Filter bar (top)**: pill tab row "ALL / LANGUAGES / FRONTEND / BACKEND / DATABASE / DEVOPS / AI & ML / DESIGN / TOOLS" (active = black bg/white text, inactive = white bg neumorphic shadow) + search input on the right (Lucide search icon, placeholder "Search tools...", `border-radius: var(--radius-full)`). Wraps below `--space-4` gap; stacks vertically on tablet/mobile — on ≤768px the category pills wrap into multiple stacked rows (no horizontal scroll) and the search input takes the full row width.
- **Icon grid (below filter bar)**: responsive grid, 7 columns desktop → 5 tablet → 4 mobile → 3 mobile-small, `row-gap: 40px; column-gap: 24px`. Each cell = **plain icon glyph only** (no colored box/badge background) at `48px` (36px on small screens), native brand SVG in its own brand color, sitting directly on the white page background. Below each icon: uppercase label (`--fs-caption`, weight 600, letter-spacing `0.05em`, centered).
- **No tool cards** — the former card grid was removed; the icon grid itself is the filtered surface.
- **Hover interaction**: each icon wiggles once on hover (rotate keyframes ±6°, `0.5s ease-in-out`, no loop) and a black pill tooltip fades in above the cell showing the tool's micro category label (`category_label`, e.g. "Library", "Runtime", "AI Platform") — implemented pure-CSS via `data-tooltip` attribute + `::after`; both respect `prefers-reduced-motion`.
- Empty state message ("No tools match your search.") centered below the grid when a filter/search combination yields nothing.

JS behavior: clicking a tab re-renders the icon grid filtered by tool `category`; typing in search narrows by name via `input` event + `.toLowerCase().includes()`. Both combine (category AND search).

---

### 4.7 Projects Teaser ("Curious What I've Built?")

**Purpose**: Section anchor introducing the portfolio, now with an infinite WebGL circular gallery as its centerpiece.

- Section id `#project` — anchor target for navbar, mobile menu, footer, and hero "View My Work" CTA
- Small eyebrow label "My Projects" centered
- Heading: "Curious What I've Built?" (`--fs-h1`, centered)
- Subheading paragraph centered, muted: "Explore production websites, dashboards, and AI-integrated applications built end-to-end with modern stacks."
- **ChromaGrid Showcase** between the subtitle and the circular gallery (admin-curated via Editor Mode — see SPEC.md §14): 3×320px **white cards** with a thin neutral border (`1px var(--color-border)`) and **neumorphic elevation** (`--neu-shadow-out`, deepening to `--neu-shadow-out-lg` on hover); per-card radial spotlight on hover; grid-level grayscale mask follows the cursor (grayscale only — no dimming) and is the sole colorization mechanism — hovering a card lifts it (`translateY(-4px)`) but does not bypass the mask; no image zoom or border-color change on hover; kebab ⋮ menu visible only in editor mode; click opens the macOS-style lightbox; public view paginates 6 at a time with a "Load More Projects" outline button
- **Feature highlights** below the grid: 2-column centered text layout (max-width 720px, no divider) — bold title + muted description per column ("Full-Stack + AI Built-In" · "Design & Calligraphy Craft"); stacks to 1 column on mobile
- **Lightbox** (macOS Photos-style): black immersive backdrop, index counter top-left, external-link ↗ + close actions top-right, wrap-around prev/next arrows, bottom filmstrip thumbnails with active white border, title/description scrim on the image's lower-left, optional "Visit Live Site" link
- **Editor toolbar** (editor mode only, desktop ≥1025px): floating dark-glass pill bottom-center — status dot · EDITOR MODE label · divider · white "Add Project" CTA · Exit · Logout; slide-up fade entrance
- **Circular WebGL Gallery** below, **full-bleed** (outside the container — no side padding/max-width; header text stays centered inside the container). Vanilla port of ReactBits CircularGallery, OGL loaded via dynamic `import()` from esm.sh:
  - Items: all non-website projects (9 design posters + 18 calligraphy pieces) with project name as canvas-texture label under each card
  - Label font: `bold 30px "Plus Jakarta Sans"`, color `#0a0a0a` (adapted from the component's white default for our light background)
  - Config: bend `-3` (curves downward — vertically mirrored valley), border radius `0.05`, scrollSpeed `2`, scrollEase `0.05`; items duplicated ×2 for seamless infinite loop
  - Interactions: drag (pointer events scoped to the gallery), keyboard ← → ; wheel navigation intentionally omitted to avoid conflict with Lenis page scroll
  - Performance: render loop pauses via IntersectionObserver when offscreen; dpr capped at 2
  - Fallback: if WebGL is unavailable or the OGL module fails to load, a simple horizontal scrolling image strip renders instead
  - Height: `420px` desktop → `320px` ≤1024px

---

### 4.9 Services ("How Can I Help You")

**Purpose**: List of 5 core service offerings.

- Big heading: "HOW CAN I HELP YOU" (`--fs-hero`-ish scale, ~72-90px, weight 800, uppercase, two lines)
- Below heading, a horizontal divider, then a row: left = short paragraph "From code to design to AI. Turning ideas into working products." (`--fs-h4`, weight 600, max-width 480px) + right side = primary button "Let's Talk" + small muted caption "Need a website, an AI workflow, or just a sharp logo? Here's how I can help."
- Another divider line.
- **AccordionGallery** (vanilla port of ReactBits component, 460px height, 5 panels): each panel shows a service illustration (monochrome line-art, `assets/image/services/service-N.webp`) with an active-expand / inactive-compress layout driven by GSAP (`flexGrow` animation, `rotateY` tilt, grayscale filter, parallax drift, label reveal stagger). Hover/click/focus/keyboard ←→ triggers the active state; description text updates dynamically below the accordion. Collapses to vertical stacking on mobile ≤520px. Panel overlay scrim is pure black globally (`rgba(0,0,0,…)` bottom gradient + dim layer).

---

### 4.10 Tech Marquee Bar (Dark)

**Purpose**: Secondary scrolling ticker, dark-themed, listing services + tech stack.

- Full-width **black background** bar, two stacked rows of scrolling text, each row scrolling in **opposite directions** (row 1 left→right, row 2 right→left) infinitely.
- Row 1 text: "AI & AUTOMATION ✦ TECH CONSULTATION ✦ DESIGN & CALLIGRAPHY ✦ FULL-STACK DEVELOPMENT ✦ AI & AUTOMATION ✦ TECH CONSUL..." — white text, uppercase, bold, small-caps feel, `--fs-body` size (~16-18px), letter-spacing wide.
- Row 2 text: "PYTHON ✦ NODE.JS ✦ DOCKER ✦ TENSORFLOW ✦ CLAUDE API ✦ POSTGRESQL ✦ TAILWIND CSS ✦ GIT ✦ FRAMER MOTION ✦ REACT ✦ NEXT..." — same style.
- Padding `16px 0` per row, `background: #0a0a0a`, `color: #fff`.

---

### 4.11 Gallery

**Purpose**: Personal/organizational black-and-white photo grid (masonry).

- Section uses a **CSS masonry/column layout** (`column-count: 4;` desktop, `column-count: 2;` mobile, `column-gap: 16px;`), images `margin-bottom: 16px; border-radius: var(--radius-md); width: 100%;` — all photos rendered in **grayscale** (`filter: grayscale(100%);`) for consistent monochrome aesthetic regardless of source color.
- No captions overlaid — pure photo wall of activities/community/organizational documentation.

```css
.gallery-masonry {
  column-count: 4;
  column-gap: 16px;
}
.gallery-masonry img {
  width: 100%;
  display: block;
  margin-bottom: 16px;
  border-radius: 16px;
  filter: grayscale(100%);
}
@media (max-width: 768px) {
  .gallery-masonry {
    column-count: 2;
  }
}
```

---

### 4.12 Testimonials

**Purpose**: Social proof, 3 client reviews.

- 3-column grid (1-column mobile), gap `24px`, each testimonial card:
  - 5-star rating row at top: 5× filled star icons (`bi-star-fill` from Bootstrap Icons or Lucide `star` with `fill: currentColor`), **solid black** (`--color-text`), `16px` each, `gap: 2px`, no gold/yellow — matches the monochrome design system.
  - Quote paragraph in double quotes (`--fs-body`, italic optional, `--color-text`)
  - Divider or spacing
  - Reviewer row: circular avatar photo (40px) + name (bold, `--fs-body-sm`) + role/title (muted, `--fs-caption`) beneath name
  - Card style: `background: var(--color-bg-soft)`, `border-radius: var(--radius-md)`, padding `24px`, no heavy shadow (flat card).

---

### 4.12B Certificates & Credentials

**Purpose**: 2-column interactive section showcasing verified certifications.

- **Left column** — OptionWheel: vertical curved wheel of certificate titles, grayscale blur on non-active items, scroll/click/drag to select, keyboard ↑↓ navigation
- **Right column** — DepthCarousel: depth-perspective stacked cards showing certificate images, drag/wheel to navigate, no arrow controls or dot indicators
- **Sync**: bidirectional — OptionWheel selection updates DepthCarousel and vice versa
- **Auto-slide**: both advance every 5 seconds; pauses on hover over DepthCarousel
- **Hover overlay**: certificate data (title + credential ID as hyperlink if link exists) in black gradient at bottom of card
- **Editor mode**: "+ Add Certificate" button in toolbar → modal form with title*, credential_id, credential_link, image*
- **Data source**: SQLite `certificates` table, CRUD via admin API

---

### 4.13 FAQ ("Questions? We Have Answers")

**Purpose**: Categorized accordion FAQ.

- Small eyebrow "— FAQ" label above heading.
- Big heading: "Questions? We Have Answers." (`--fs-h1`, weight 800)
- Layout: 2-column, `grid-template-columns: 240px 1fr;` gap `32px` — **left sidebar** (narrow, ~240px, top-aligned) lists categories vertically with `12px` spacing: "— All" (active state, preceded by a short dash "—" marker, weight 700, black), "General", "Services", "Pricing", "Process", "Contact" — each preceded by a small bullet dot (`•`) when inactive, as a clickable list item (`--fs-body`, `--color-text-muted` when inactive, `--color-text` + weight 700 + dash marker when active).
- **Right panel**: accordion list inside a soft gray rounded container — `background: var(--color-bg-soft)` (`#f5f5f5`), `border-radius: var(--radius-lg)`, padding `32px 40px`, no border. Container height is generous with noticeable empty white space below the last FAQ item (the panel doesn't hug its content tightly — matches the source screenshot's tall card). Each FAQ item = row with question text (`--fs-h4`, weight 600, `--color-text`) + a `+` icon (Lucide `plus`, `20px`, `--color-text-muted`) on the far right that rotates 45° to become `×` when expanded, revealing answer text below (`--fs-body`, `--color-text-muted`) with slide-down animation. Divider line between each FAQ row (`border-bottom: 1px solid var(--color-border)`, no divider after the last item). Row padding: `20px 0`.

```js
document.querySelectorAll(".faq-item").forEach((item) => {
  item.querySelector(".faq-question").addEventListener("click", () => {
    item.classList.toggle("open");
  });
});
```

```css
.faq-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}
.faq-item.open .faq-answer {
  max-height: 200px;
}
.faq-item.open .faq-icon {
  transform: rotate(45deg);
}
```

---

### 4.14 Contact ("Let's Work Together")

**Purpose**: Contact form + map + contact info strip.

- Big heading "Let's Work Together" (`--fs-h1`) left-aligned, paired with a right-aligned muted caption: "Tell me what you need and I'll get back to you within 24 hours."
- **2-column layout** (stacks on mobile):
  - **Left: contact form card** — white bg, `border-radius: var(--radius-lg)`, padding `32px`, `border: 1px solid var(--color-border)`:
    - Row 1: Name (text input) + Email (text input) — 2-col grid
    - Row 2: Phone Number (text input) + Service (select dropdown) — 2-col grid
    - Row 3: Budget (text input) + Timeline (text input) — 2-col grid
    - Row 4: Message / Project Details (textarea, full width, ~4 rows)
    - Submit button: "Send Message" (primary black pill, with external-link Lucide icon), full width or left-aligned.
    - Input style: `border: 1px solid var(--color-border)`, `border-radius: var(--radius-sm)`, padding `12px 16px`, `background: #fafafa`, label above each field (`--fs-body-sm`, weight 500).
  - **Right: map card** — `border-radius: var(--radius-lg)`, `overflow: hidden`, full height matching form card. Leaflet map instance:
    - Dark tile layer (CARTO dark matter: `https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png`)
    - Centered on Banda Aceh, Indonesia coordinates: `lat: 5.5483, lng: 95.3238`
    - Single marker/circle indicator at center point, zoom controls bottom-right (`+`/`-`), attribution text bottom ("© CARTO, © OpenStreetMap contributors").
- Below the 2-column block, a **3-column contact info strip** separated by a top divider line:
  - Icon (phone, Lucide `phone`) + "Call & WhatsApp" label + number
  - Newsletter email form (input + submit button)
  - Icon (paper-plane, Lucide `send`) + "Write to Us" label + email address
- On mobile ≤768px the strip becomes a 2-column grid: "Call & WhatsApp" and "Write to Us" sit side by side on the first row — icons hidden, "Call & WhatsApp" left-aligned and "Write to Us" right-aligned — while the newsletter form spans the full width beneath them (centered, max-width `360px`).

```js
const map = L.map("contact-map", { zoomControl: false }).setView(
  [5.5483, 95.3238],
  14,
);
L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {
  attribution: "&copy; CARTO, &copy; OpenStreetMap contributors",
}).addTo(map);
L.control.zoom({ position: "bottomright" }).addTo(map);
L.circleMarker([5.5483, 95.3238], {
  radius: 8,
  color: "#fff",
  fillColor: "#000",
  fillOpacity: 1,
}).addTo(map);
```

---

### 4.15 Closing CTA ("Ready to Build Something Bold?")

**Purpose**: Final conversion push before footer.

- Center-aligned content:
  - Small live clock text: "Your time — HH:MM:SS PM" (`--fs-body-sm`, muted) — updated via `setInterval` JS every second showing visitor's local time.
  - Big heading: "Ready to Build Something Bold?" (`--fs-hero`-ish, ~56-72px, weight 800, centered, max-width 700px)
  - Paragraph: "Let's turn your idea into a fast, beautiful, and functional digital experience. I'm open for new projects." (muted, centered)
  - Primary pill button: "Start a Project" (with arrow-up-right Lucide icon)
  - Small ghost link beneath: "Or reach me on WhatsApp →"
- **Fan cards**: six tech icon cards (React, Node.js, Python, Claude, JavaScript, n8n) fan out horizontally under the heading — white rounded squares (`118px` desktop) rotated ±3–17° and offset via `--tx` custom properties, each showing its brand SVG (`52px`) with a black pill tooltip on hover. Tablet ≤1024px shrinks them to `82px`; mobile ≤480px tightens further to `56px` cards with dense offsets `--tx: -65px … 55px` (24px steps) so all six stay fully inside the viewport (no edge clipping).
- **Decorative side columns**: on both left and right edges, a curved/arched vertical stack of circular icon badges (each ~56px white circle with shadow) showing tech tool icons (React, Next.js "N" logo, JS, GitHub, a 3D-cube icon, a code/terminal icon, an "A" caligraphy icon, Photoshop "Ps", Lightroom "Lr" on the left; HTML5, CSS3, Claude sunburst, a bit-icon, a gem/diamond icon, a square/app icon, a "C" circle icon, a pen/design icon, a calligraphy/Arabic-script icon on the right) — arranged along a curved path (achievable via absolute positioning with calculated offsets or CSS `transform: rotate()` per item along an arc).

```css
.floating-icon-badge {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-icon-badge);
  position: absolute;
}
```

Positions computed individually per badge (7-8 badges per side) to form a gentle S-curve down the page edge.

---

### 4.16 Footer

**Purpose**: Site-wide closing, giant wordmark, nav links, social, visitor widget, legal.

- **Giant wordmark**: "alfzilham" (note: stylized/condensed, appears as compressed lowercase wordmark) spanning full width, extremely large font-size (clamp ~90px–200px), weight 800/900, `color: #0a0a0a`, with a subtle 3D extrusion/shadow effect (layered text-shadow to simulate depth, e.g. `text-shadow: 2px 2px 0 #ccc, 4px 4px 0 #bbb...` repeated or an SVG duplicate layer behind at offset). Overflow hidden so it bleeds off container edges.
- **Dark footer panel** below the wordmark: `background: var(--color-footer-bg)`, `color: #fff`, padding `64px 24px 32px`:
  - **Column 1**: Heading "READY TO BUILD SOMETHING BOLD?" (`--fs-h3`, weight 700, white) + "GET STARTED" outline button (white border, white text, pill) + row of social icons below (Discord, Threads, TikTok, YouTube, Facebook, Pinterest, WhatsApp, and a "print/other" icon) — Bootstrap Icons, `28px`, white, circular hover background.
  - **Column 2** ("NAVIGATE"): link list — About, Skills, Projects, Services, Gallery, FAQ. (`--fs-body`, `--color-footer-muted`, hover white)
  - **Column 3** ("SERVICES"): link list — Full-Stack Development, AI & Automation, Tech Consultation, Design & Calligraphy.
  - **Column 4** ("VISITORS"): a small dark card widget (`background: #1a1a1a`, `border-radius: var(--radius-md)`, padding `16px`) showing:
    - Top row: green pulsing dot + "LIVE" label + "1 unique visitors" text (right-aligned, muted)
    - A world-map silhouette graphic (dot-map style, decorative, static SVG/image, dark themed) filling the card body
    - Bottom row: country code "ID" + "Indonesia" + a number badge "17" (visitor count), small flag or globe icon.
  - 4-column grid layout, collapsing to 1-column stacked on mobile, columns separated by generous gap `48px`.
  - Bottom bar (full-width, top border `1px solid var(--color-border-dark)`, padding-top `24px`): "© 2026 Alfiz Ilham" (left) + "All Rights Reserved." (center) + "Privacy Policy · Terms of Service" links (right) — all `--fs-body-sm`, `--color-footer-muted`.

```css
.footer-wordmark {
  font-size: clamp(90px, 18vw, 220px);
  font-weight: 900;
  line-height: 0.9;
  color: #0a0a0a;
  white-space: nowrap;
  overflow: hidden;
}
.visitor-live-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--color-live-green);
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.4;
  }
}
```

---

## 5. Assets Checklist (Placeholders Needed)

| Asset                       | Description                                 | Suggested filename            |
| --------------------------- | ------------------------------------------- | ----------------------------- |
| Mascot — hero standing      | 3D character, arms at side, sunglasses      | `assets/mascot-hero.png`      |
| Mascot — arms crossed       | 3D character, arms crossed, confident pose  | `assets/mascot-confident.png` |
| Mascot — sitting at desk    | 3D character at dual-monitor desk setup     | `assets/mascot-desk.png`      |
| World map visitor widget    | Dark dot-map silhouette graphic             | `assets/world-map-dark.png`   |
| Gallery photos (~19 images) | Black & white activity/documentation photos | `assets/gallery/*.jpg`        |
| Project screenshots (~19+)  | Website/dashboard mockup screenshots        | `assets/projects/*.png`       |
| Design work images (~9)     | Poster/banner design samples                | `assets/designs/*.png`        |
| Calligraphy images (~18)    | Naskh calligraphy artwork photos            | `assets/calligraphy/*.jpg`    |
| Reviewer avatars (3)        | Small circular headshot placeholders        | `assets/avatars/*.jpg`        |

---

## 6. File Structure (PHP MVC)

```
alfizilham/
├── public/                     ← Web root
│   ├── index.php               ← Front controller
│   ├── .htaccess               ← URL rewriting
│   └── assets/
│       ├── css/
│       │   ├── global.css      ← Reset, tokens, base typography
│       │   ├── components.css  ← Buttons, cards, forms, sections
│       │   └── responsive.css  ← Media query overrides
│       ├── js/
│       │   └── main.js         ← All JS logic (consolidated)
│       ├── favicon/
│       ├── cv/
│       └── image/              ← avatars, gallery, icons, projects
├── app/                        ← Backend MVC
│   ├── Core/                   ← Router, Database, View, Request
│   ├── Controllers/            ← PageController, ApiController
│   ├── Models/                 ← Project, Tool, Faq, etc.
│   ├── Services/               ← ContactService, VisitorService
│   └── Helpers/                ← i18n, helpers
├── views/                      ← PHP templates
│   ├── layouts/main.php
│   ├── sections/               ← 15 section views
│   └── partials/               ← navbar, footer
├── lang/                       ← en.php, id.php
├── config/                     ← config.php, database.php
├── data/                       ← database.sqlite
└── docs/                       ← Documentation
```

---

## 7. Motion & Interaction Summary

| Element                    | Interaction                                                      |
| -------------------------- | ---------------------------------------------------------------- |
| Marquee strips (4.3, 4.10) | Infinite CSS `translateX` loop, `linear`, no pause on hover      |
| FAQ accordion              | Click toggles `max-height` + rotates `+` icon to `×`             |
| Skill filter tabs          | Click sets active class, filters icons by `data-filter`          |
| Search input (skills)      | `input` event filters visible cards by name match                |
| Project/tool cards         | Hover: `translateY(-4px)` + shadow grow                          |
| Buttons                    | Hover: opacity `0.9` + `scale(1.02)`, transition `0.2s ease`     |
| Live clock (CTA section)   | `setInterval` updates text every 1000ms, format `hh:mm:ss AM/PM` |
| Visitor "LIVE" dot         | CSS `@keyframes pulse` opacity loop                              |
| Scroll reveal (optional)   | `IntersectionObserver` adds `.in-view` class → fade+slide-up     |
| Bio statement (4.5)        | "FoldText" word-by-word fold entrance (GSAP, top hinge, once)    |
| Stat numbers (4.5)         | Count-up `0 → target` on scroll (GSAP, suffix preserved)         |
| Skill icons (4.6)          | Hover: one-shot rotate wiggle + black pill tooltip (`category_label`) |
| Circular gallery (4.7)     | Infinite WebGL drag gallery with bend + wave shader (OGL, esm.sh)|
