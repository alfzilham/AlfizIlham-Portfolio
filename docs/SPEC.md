# SPEC.md — Alfiz Ilham Portfolio

Functional specification: what each interactive feature must do, data shapes, acceptance criteria, and implementation details. Read alongside `CONTEXT.md` (why), `DESIGN.md` (how it looks), and `ARCHITECTURE.md` (how it's organized).

---

## 1. Navigation

**Requirement**: Sticky navbar with anchor-scroll links to each major section.

| Link | Target section ID |
|------|------------------|
| About | `#about` |
| Skills | `#skills` |
| Projects | `#project` |
| Services | `#service` |
| Gallery | `#gallery` |
| Certificates | `#certificate` |
| FAQ | `#faq` |
| Contact | `#contact` |

- Smooth scroll via Lenis.js with `scroll-behavior: smooth` fallback.
- Mobile (<1024px): nav links collapse into hamburger menu (glassmorphism overlay).
- Language toggle (EN/ID): navbar pill switch, stores preference in `$_SESSION['lang']`.

**Acceptance criteria**: clicking a nav link scrolls to the correct section. Navbar hides on scroll down, reappears on scroll up. Glassmorphism effect when scrolled past intro.

---

## 2. Data Sources (PHP Models → SQLite)

All data previously hardcoded in JS arrays now lives in SQLite, accessed via PHP Models.

| Data | Model | Table | Count |
|------|-------|-------|-------|
| Projects | `Project::all()` | `projects` | 46 |
| Tools | `Tool::filtered()` | `tools` | 44 |
| FAQs | `Faq::all()` | `faqs` | 15 |
| Testimonials | `Testimonial::all()` | `testimonials` | 3 |
| Services | `Service::all()` | `services` | 5 |
| Gallery | `Gallery::all()` | `gallery` | 16 |

Data is passed from Controller → View → JS via `window.__TOOLS`, `window.__FAQS`, etc.

---

## 3. Skill Filter + Search

**Data flow**: `Tool::all()` → PHP → `window.__TOOLS` → JS client-side filtering.

- Categories (8, matching `tools.category` in SQLite): `all`, `languages`, `frontend`, `backend`, `database`, `devops`, `ai-ml`, `design`, `tools`
- Search: case-insensitive substring match on tool name; combines with active category (AND)
- Single icon grid (7 columns desktop) renders the filtered tools — no separate card grid
- Empty state shows "No tools match your search." when a filter/search combination yields nothing

**Acceptance criteria**: clicking a category tab re-renders the icon grid with only matching icons; typing in search further narrows visible icons live; empty state appears when nothing matches; hovering an icon plays a one-shot wiggle animation and shows a tooltip with its `category_label`.

---

## 4. Projects Teaser + Circular Gallery

**Data flow**: `Project::all()` → PHP filters out `website` category → items JSON (`window.__GALLERY_ITEMS`) → WebGL gallery (OGL via dynamic `import()` from esm.sh).

- Section `#project` (anchor target for navbar, mobile menu, footer, and hero "View My Work" CTA)
- Content: eyebrow "My Projects" + heading "Curious What I've Built?" + role-based subtitle
- Circular gallery: 27 design & calligraphy projects, infinite loop, drag to scroll, keyboard arrows; labels rendered as canvas textures in Plus Jakarta Sans
- Wheel navigation intentionally omitted (avoids conflict with Lenis page scroll)
- Fallback: static horizontal image strip when WebGL is unavailable or the OGL module fails to load
- Render loop pauses when the section is offscreen

**Acceptance criteria**: gallery renders with bend arc and rounded cards; dragging scrolls infinitely with snap-back easing; keyboard ← → navigates; fallback strip appears without WebGL; all `#project` anchors scroll to the section.

---

## 5. FAQ Accordion

**Data flow**: `Faq::all()` → PHP → JSON → JS rendering.

- 6 sidebar categories: All, General, Services, Pricing, Process, Contact
- Clicking category filters visible FAQ items
- Clicking question expands/collapses answer with `max-height` animation
- `+` icon rotates 45° to become `×` when expanded
- Multiple items can be open simultaneously

**Acceptance criteria**: accordion expands/collapses smoothly; category filter works; `aria-expanded` attribute toggled correctly.

---

## 5B. Certificates & Credentials

**Data flow**: `Certificate::all()` → PHP → JSON → JS (OptionWheel + DepthCarousel).

- Section `#certificate` — 2-column grid layout
- **Left column**: OptionWheel (vertical curved wheel) showing certificate titles; scroll/click/drag to select; keyboard ↑↓ navigation; grayscale blur on non-active items
- **Right column**: DepthCarousel (depth-perspective stacked cards) showing certificate images; drag/wheel to navigate; no arrow controls or dot indicators
- **Sync**: OptionWheel ↔ DepthCarousel bidirectional — selecting in one updates the other
- **Auto-slide**: both advance every 5 seconds; pauses on hover over DepthCarousel
- **Hover overlay**: certificate data (title + credential ID as hyperlink if link exists) shown in black gradient at bottom of card, visible only on hover
- **Editor mode**: "+ Add Certificate" button in toolbar → modal form (title*, credential_id, credential_link, image*) → saves to SQLite `certificates` table
- **CRUD endpoints**: GET/POST/DELETE `/api/admin/certificates[/{id}]` (admin auth required)

**Acceptance criteria**: wheel and carousel stay in sync; auto-slide advances both; hover pauses; editor mode allows full CRUD; images served as `.webp`.

---

## 6. Contact Form

**Implementation**: EmailJS (primary) + PHP `/api/contact` endpoint (fallback).

**Fields**: Name, Email, Phone, Service (select), Budget, Timeline, Message.

**Validation** (client-side + server-side):
- Name: required, min 2 chars
- Email: required, valid format
- Phone: required, min 5 chars
- Service: required (select)
- Message: required, min 10 chars

**Service dropdown options**: Full-Stack Development, AI & Automation, Tech Consultation, Design & Calligraphy, Others.

**Flow**:
1. Client validates → submits via EmailJS
2. If EmailJS fails → falls back to `POST /api/contact` (PHP)
3. PHP validates → saves to `contact_submissions` table → attempts `mail()`
4. Success/error toast displayed

**Acceptance criteria**: form cannot submit with invalid fields; success clears form and shows confirmation; error shows retryable message with WhatsApp link.

---

## 7. i18n Toggle

**Implementation**: PHP session-based language switching.

**Flow**:
1. User clicks EN/ID in navbar
2. JS calls `switchLang('id')` → redirects to `?lang=id`
3. PHP stores `$_SESSION['lang'] = 'id'`
4. `i18n::load('id')` loads `lang/id.php`
5. All `<?= i18n::t('key') ?>` in views render Indonesian text
6. JS receives `window.__LANG = 'id'` for client-side strings

**Acceptance criteria**: all visible text changes when toggling language; preference persists across page loads; default is English.

---

## 8. Visitor Counter

**Implementation**: Real tracking via SQLite + session deduplication.

**Flow**:
1. `VisitorService::track()` called on every page load
2. Checks `$_SESSION['visitor_tracked']` — skips if already tracked
3. Filters bots via user-agent pattern matching
4. Inserts into `visitors` table with IP, user-agent, timestamp
5. JS fetches `GET /api/visitor` → updates counter in footer

**Acceptance counter**: counter increments on new unique visits; same session doesn't double-count; bots are filtered.

---

## 9. Gallery

**Implementation**: Server-side rendered in `views/sections/gallery.php`.

- 16 images, CSS column masonry (4 cols desktop, 2 cols mobile)
- Grayscale filter via CSS (`filter: grayscale(100%)`)
- Hover: scale 1.05 + grayscale removal
- Tooltip follows cursor with image description
- No lightbox (out of scope for initial build)

**Acceptance criteria**: images render in masonry layout; grayscale on rest, color on hover; tooltip shows description.

---

## 10. Contact Map

**Implementation**: Leaflet.js with CARTO dark tiles, centered on Banda Aceh.

- Coordinates: `lat: 5.5483, lng: 95.3238`
- Zoom level: 14
- `scrollWheelZoom: false` (prevents scroll hijack)
- Circle marker at center point
- Zoom controls bottom-right

**Acceptance criteria**: map loads centered on Banda Aceh; doesn't trap page scroll; marker visible.

---

## 11. Live Clock

**Implementation**: `setInterval` in JS, visitor's local timezone.

- Format: `hh:mm:ss AM/PM`
- Updates every 1000ms
- Uses `new Date()` for browser local time
- i18n prefix: "Your time —" (EN) / "Waktu Anda —" (ID)

**Acceptance criteria**: clock updates every second; reflects visitor's system clock.

---

## 12. API Endpoints

| Method | Endpoint | Description | Response |
|--------|----------|-------------|----------|
| POST | `/api/contact` | Submit contact form | `{ success: bool, message: string }` |
| GET | `/api/visitor` | Get visitor count | `{ count: int, today: int }` |
| GET | `/api/tools?category=&search=` | Get filtered tools | `{ tools: array }` |
| GET | `/api/projects?category=` | Get filtered projects | `{ projects: array }` |

---

## 13. Open Resolved

---

## 14. Editor Mode (Admin Showcase CRUD)

**Auth**: `Ctrl+Shift+E` → password modal → `POST /api/admin/login` (bcrypt hash in `config/config.php`, verified via `password_verify()`) → `$_SESSION['is_admin']`. All write endpoints return 401 without session.

**Endpoints**:

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| GET | `/api/cards` | public | List showcase cards |
| POST | `/api/admin/login` | public | Login {password} |
| POST | `/api/admin/logout` | — | Clear session |
| GET | `/api/admin/session` | — | Check auth state |
| POST | `/api/admin/cards` | admin | Create card (multipart: title, description, image) |
| POST | `/api/admin/cards/{id}` | admin | Update card (image optional) |
| DELETE | `/api/admin/cards/{id}` | admin | Delete card (+ unlink image file) |

**Upload rules**: finfo MIME whitelist (jpeg/png/webp/gif), max 5 MB, resized to max 1600px, converted to WebP quality 85 via GD, random filename stored in `public/assets/uploads/showcase/`.

**UI behaviors**:
- ChromaGrid (3×320px columns, **white cards**, thin border `1px var(--color-border)`, neumorphic shadow `--neu-shadow-out`) sits between the subtitle and the circular gallery; hidden when empty; cards whose image file is missing on disk are filtered out server-side (prevents broken cards + 404s)
- Card hover: lifts `translateY(-4px)`, shadow deepens to `--neu-shadow-out-lg`; the grid-level grayscale spotlight remains the sole colorization mechanism (hovered cards do not bypass the mask); no image zoom, no border-color change
- Kebab ⋮ button (editor mode only): Edit reopens prefilled form modal; Delete opens a **custom confirmation modal** (centered layout, red warning badge, card name, Cancel / Delete actions with `.btn-danger`)
- Optional **Live URL** field in the form (auto-prepends https://, validated); styled identically to other inputs (`input[type="url"]`); surfaced in the lightbox as an external-link action + inline caption link
- Editor modals are viewport-clamped (`max-height: calc(100dvh - 48px)`) and scroll internally when content overflows; the scrollbar is hidden (`scrollbar-width: none` + `::-webkit-scrollbar { display: none }`) while scrolling stays functional; page scroll is locked while any overlay is open via `lenis.stop()`/`lenis.start()` plus `data-lenis-prevent` on the overlay containers (verified: Lenis checks `data-lenis-prevent` before its stopped-state `preventDefault`, so native modal scrolling keeps working)
- Public view paginates: first 6 cards visible, **Load More Projects** reveals +6 per click; editor mode always shows all cards
- Feature highlights below the grid (2 centered columns, max-width 720px, no divider): "Full-Stack + AI Built-In" · "Design & Calligraphy Craft" — bold title + muted description per column, center-aligned; stacks to 1 column on mobile
- Click card outside editor mode → **macOS-style lightbox**: black immersive backdrop, index counter, external-link + close actions, wrap-around prev/next arrows, bottom filmstrip with active highlight, title/description scrim on the image; keyboard ← → navigates; the stage auto-sizes to the image (`width: auto`), images may span edge-to-edge (`max-width: 100vw`, no side padding), height capped to reserve filmstrip space; the active slide is resolved by card `data-id` (image-path fallback), so duplicate images can never open the wrong card
- `Ctrl+Shift+E` toggles editor UI when already authenticated; floating glass toolbar shows status dot · Add Project · Logout
- **Editor mode is desktop-only (viewport > 1024px)**: the shortcut is ignored on smaller viewports, all editor UI is hidden via CSS, and resizing below the threshold auto-exits editor mode; public features (cards, hover, lightbox) remain fully available

**Acceptance criteria**: unauthenticated writes get 401; created cards persist in SQLite (`showcase_projects`, newest first) and appear for all visitors after reload; uploaded images are served as `.webp`; deleting a card removes its image file.

All original open questions from the pre-MVC spec have been resolved:

| # | Question | Resolution |
|---|----------|-----------|
| 1 | Contact form backend | EmailJS + PHP fallback |
| 2 | Indonesian copy ready? | Yes — complete in `lang/id.php` |
| 3 | Mobile nav menu | Glassmorphism overlay (implemented) |
| 4 | "VIEW MORE" behavior | Inline expand (implemented) |
| 5 | Design/Calligraphy titles | All 27 pieces have titles in DB |
| 6 | FAQ content for all categories | 15 FAQs across 5 categories |
| 7 | Service dropdown options | 5 options (implemented) |
| 8 | Visitor counter | Real SQLite tracking (implemented) |
| 9 | Gallery lightbox | Out of scope (tooltip only) |
| 10 | Custom cursor `pointer.png` | Not in scope (asset unused) |
