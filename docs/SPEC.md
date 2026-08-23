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
| Services | `Service::all()` | `services` | 4 |
| Gallery | `Gallery::all()` | `gallery` | 16 |

Data is passed from Controller → View → JS via `window.__TOOLS`, `window.__PROJECTS`, etc.

---

## 3. Skill Filter + Search

**Data flow**: `Tool::all()` → PHP → `window.__TOOLS` → JS client-side filtering.

- Categories (8, matching `tools.category` in SQLite): `all`, `languages`, `frontend`, `backend`, `database`, `devops`, `ai-ml`, `design`, `tools`
- Search: case-insensitive substring match on tool name; combines with active category (AND)
- Single icon grid (7 columns desktop) renders the filtered tools — no separate card grid
- Empty state shows "No tools match your search." when a filter/search combination yields nothing

**Acceptance criteria**: clicking a category tab re-renders the icon grid with only matching icons; typing in search further narrows visible icons live; empty state appears when nothing matches; hovering an icon plays a one-shot wiggle animation and shows a tooltip with its `category_label`.

---

## 4. Project Filter

**Data flow**: `Project::byCategory($category)` → PHP → JSON → JS rendering.

- Filter tabs: `ALL / WEBSITE / DESIGN / CALLIGRAPHY`
- Initial render: 6 cards (2 rows × 3 cols)
- "VIEW MORE PROJECTS" reveals remaining cards with fade-in
- Clicking again becomes "SHOW LESS"

**Acceptance criteria**: filter tabs correctly show/hide cards; "VIEW MORE" reveals remaining cards without page reload.

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
