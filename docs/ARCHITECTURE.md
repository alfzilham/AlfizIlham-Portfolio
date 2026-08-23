# ARCHITECTURE.md — Alfiz Ilham Portfolio

Technical architecture: file structure, module responsibilities, data flow, and build/deploy notes. Pairs with `DESIGN.md` (visual spec) and `SPEC.md` (functional requirements).

---

## 1. Tech Stack

PHP 8.x MVC (vanilla, no framework) with vanilla HTML/CSS/JS frontend.

| Purpose | Technology | Notes |
|---------|-----------|-------|
| Backend | PHP 8.x | Vanilla MVC — Router, Database, View, Request classes |
| Database | SQLite | Via PDO, stored in `data/database.sqlite` |
| Frontend | HTML5, CSS3, JS (ES6+) | No bundler, no npm |
| Font | Plus Jakarta Sans (400–800) | Google Fonts CDN |
| UI Icons | Lucide Icons | CDN `<script>` |
| Brand Icons | Bootstrap Icons | CDN `<link>` (font only) |
| Map | Leaflet.js + CARTO Dark tiles | CDN |
| Smooth Scroll | Lenis.js | CDN |
| Animation (fold text, counters) | GSAP 3 | CDN |
| WebGL gallery (projects) | OGL 1.0.11 (dynamic ESM import via esm.sh) | CDN |
| Contact Form | EmailJS (primary) + PHP fallback | Client-side + server-side |
| i18n | PHP session-based | EN/ID toggle via `$_SESSION['lang']` |
| Server | XAMPP (Apache) | `.htaccess` URL rewriting |

---

## 2. File Structure

```
alfizilham/
├── public/                     ← Web root (XAMPP document root)
│   ├── index.php               ← Front controller (entry point)
│   ├── .htaccess               ← Apache URL rewriting
│   └── assets/
│       ├── css/                ← global.css, components.css, responsive.css
│       ├── js/                 ← main.js (i18n, visitor counter, interactivity)
│       ├── favicon/            ← favicon.ico, Logo.png
│       ├── cv/                 ← Alfiz_Ilham_CV.md, .pdf
│       └── image/              ← avatars, gallery, icons, people, projects
│
├── app/                        ← Backend MVC
│   ├── Core/
│   │   ├── Router.php          ← URL routing dispatch
│   │   ├── Database.php        ← PDO SQLite singleton
│   │   ├── View.php            ← Template renderer
│   │   └── Request.php         ← HTTP request wrapper
│   ├── Controllers/
│   │   ├── PageController.php  ← Renders main page
│   │   └── ApiController.php   ← /api/contact, /api/visitor, etc.
│   ├── Models/
│   │   ├── Project.php         ← CRUD projects
│   │   ├── Tool.php            ← CRUD tools (filter + search)
│   │   ├── Faq.php             ← CRUD FAQs
│   │   ├── Testimonial.php     ← CRUD testimonials
│   │   ├── Service.php         ← CRUD services
│   │   ├── Gallery.php         ← CRUD gallery items
│   │   └── Visitor.php         ← Visitor counting
│   ├── Services/
│   │   ├── ContactService.php  ← Email handler + DB log
│   │   └── VisitorService.php  ← Session-based visitor tracking
│   └── Helpers/
│       ├── i18n.php            ← String translation
│       └── helpers.php         ← Utility functions
│
├── views/                      ← PHP templates
│   ├── layouts/main.php        ← Base HTML layout
│   ├── sections/               ← 15 section views
│   │   ├── intro.php, hero.php, curved-marquee.php
│   │   ├── about.php, bio-stats.php, skills.php
│   │   ├── projects-teaser.php
│   │   ├── services.php, tech-marquee.php
│   │   ├── gallery.php, testimonials.php, faq.php
│   │   ├── contact.php, cta-closing.php
│   └── partials/
│       ├── navbar.php          ← Shared navbar
│       └── footer.php          ← Shared footer
│
├── lang/                       ← i18n string dictionaries
│   ├── en.php                  ← English (80+ strings)
│   └── id.php                  ← Indonesian (80+ strings)
│
├── config/
│   ├── config.php              ← Site config (name, email, social)
│   ├── database.php            ← SQLite path config
│   └── .env.example            ← Environment template
│
├── data/
│   └── database.sqlite         ← SQLite database (runtime)
│
├── docs/                       ← Documentation
├── logs/                       ← Error logs
├── .opencode/                  ← Agent configuration
├── bootstrap.php               ← Autoloader + initialization
├── seed.php                     ← Database seeder (CLI only)
├── AGENT.md                    ← Agent rules
├── README.md                   ← Project readme
├── LICENSE                     ← All Rights Reserved
└── .gitignore                  ← Git ignore rules
```

---

## 3. MVC Flow

```
Browser Request
    ↓
public/index.php (Front Controller)
    ↓
bootstrap.php (Autoloader + Config + i18n)
    ↓
Router::dispatch($uri)
    ↓
Controller Method (e.g. PageController::index())
    ↓
Models load data (Project::all(), Tool::filtered(), etc.)
    ↓
View::render('layouts/main', $data)
    ↓
HTML Output → Browser
```

### Request/Response Cycle

1. **Request** → `public/index.php` receives all requests
2. **Bootstrap** → Load config, autoloader, i18n strings
3. **Route** → `Router` matches URI to controller method
4. **Controller** → Gathers data from Models
5. **Model** → Queries SQLite via PDO
6. **View** → Renders PHP templates with data
7. **Response** → HTML sent to browser

---

## 4. Database Schema (SQLite)

### visitors
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER PK | Auto-increment |
| ip_address | TEXT | Client IP |
| user_agent | TEXT | Browser user agent |
| country | TEXT | Default 'ID' |
| visited_at | DATETIME | Timestamp |

### contact_submissions
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER PK | Auto-increment |
| name | TEXT | Submitter name |
| email | TEXT | Submitter email |
| phone | TEXT | Phone number |
| service | TEXT | Selected service |
| budget | TEXT | Budget estimate |
| timeline | TEXT | Project timeline |
| message | TEXT | Project details |
| submitted_at | DATETIME | Timestamp |

### projects, tools, faqs, testimonials, services, gallery
Standard CRUD tables with `id`, content fields, and `sort_order`.

---

## 5. API Endpoints

| Method | Endpoint | Controller | Description |
|--------|----------|-----------|-------------|
| GET | `/` | PageController::index() | Render main page |
| POST | `/api/contact` | ApiController::contact() | Submit contact form |
| GET | `/api/visitor` | ApiController::visitorCount() | Get visitor count |
| GET | `/api/tools` | ApiController::tools() | Get filtered tools |
| GET | `/api/projects` | ApiController::projects() | Get filtered projects |
| GET | `/lang/{lang}` | Closure | Switch language (en/id) |

---

## 6. i18n Implementation

- **Storage:** `lang/en.php` and `lang/id.php` — PHP arrays
- **Loading:** `i18n::load($lang)` in `public/index.php`
- **Usage:** `<?= i18n::t('key') ?>` in views
- **Toggle:** `?lang=en` or `?lang=id` → stored in `$_SESSION['lang']`
- **JS:** Language data passed via `window.__LANG_DATA`

---

## 7. Performance Notes

- All images use `.webp` for compression
- `<img>` tags include `loading="lazy"` for below-fold images
- CSS split into 3 files (global, components, responsive) for maintainability
- Single `main.js` file (consolidated, no bundler needed)
- SQLite for lightweight, serverless database
- `.htaccess` enables gzip compression and browser caching

---

## 8. Deployment

**Local:** XAMPP (Apache) — `http://localhost/alfizilham`

**Production:** Any PHP-compatible host (shared hosting, VPS, DigitalOcean App Platform). No build step required — pure PHP with SQLite.

**Note:** For production, consider:
- Moving `data/database.sqlite` outside web root
- Setting proper file permissions on `data/`
- Configuring PHP OPcache for performance
- Using PHPMailer with SMTP instead of `mail()`
