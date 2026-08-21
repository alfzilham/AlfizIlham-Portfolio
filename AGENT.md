# AGENT.md — Alfiz Ilham Portfolio

## Project Overview

Personal portfolio website for **Alfiz Ilham** — Software & AI Engineer.
Built with **PHP MVC** (vanilla, no framework), **SQLite**, and **vanilla HTML/CSS/JS** frontend.
Bilingual support (EN/ID), responsive design, and real-time visitor tracking.

---

## Rules

### Rule 1: Plan Before Execute

**Do NOT execute any file changes (write, edit, bash) while in `/plan` mode — even if the user explicitly requests it.** Wait until the user switches to `/build` mode before executing.

| Mode | Allowed | Not Allowed |
|------|---------|-------------|
| `/plan` | read, grep, glob, analysis | write, edit, bash (non-read) |
| `/build` | Everything including write, edit, bash | — |

### Rule 2: Git Workflow

Run git commands after every code update cycle:

```bash
git add .
git commit -m "<type>: <description>"
git push origin master
```

### Rule 3: Commit Message Convention

Use [Conventional Commits](https://www.conventionalcommits.org/) format:

| Type | When to Use | Example |
|------|-------------|---------|
| `chore:` | New files, config, non-code changes | `chore: add .gitignore and LICENSE` |
| `feat:` | New features or functionality | `feat: add i18n toggle EN/ID` |
| `fix:` | Bug fixes | `fix: correct visitor counter increment` |
| `refactor:` | Code restructuring without behavior change | `refactor: extract ContactService from controller` |
| `docs:` | Documentation updates | `docs: update ARCHITECTURE.md for MVC structure` |
| `style:` | Formatting, whitespace, semicolons | `style: apply PSR-12 formatting` |
| `test:` | Adding or updating tests | `test: add unit tests for VisitorService` |
| `perf:` | Performance improvements | `perf: add OPcache configuration` |
| `ci:` | CI/CD pipeline changes | `ci: add GitHub Actions workflow` |

### Rule 4: Update Documentation

After every code change, update the relevant documentation file:

| Code Changed | Documentation to Update |
|--------------|------------------------|
| Models / Controllers / Services | `docs/ARCHITECTURE.md` |
| New features / i18n / forms | `docs/SPEC.md` |
| Visual design / layout changes | `docs/DESIGN.md` |
| Project goals / audience | `docs/CONTEXT.md` |
| New files / restructure | `README.md` |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.x (vanilla MVC, no framework) |
| **Frontend** | Vanilla HTML5, CSS3, JavaScript (ES6+) |
| **Database** | SQLite (via PDO) |
| **Server** | XAMPP (Apache) |
| **Font** | Plus Jakarta Sans (Google Fonts CDN) |
| **UI Icons** | Lucide Icons (CDN) |
| **Brand Icons** | Bootstrap Icons (CDN) |
| **Map** | Leaflet.js + CARTO Dark tiles (CDN) |
| **Smooth Scroll** | Lenis.js (CDN) |
| **Contact Form** | EmailJS (primary) + PHP fallback |
| **i18n** | PHP session-based EN/ID toggle |

---

## Project Structure

```
alfizilham/
├── public/                     ← Web root (XAMPP document root)
│   ├── index.php               ← Front controller
│   ├── .htaccess               ← URL rewriting
│   └── assets/                 ← CSS, JS, images, CV, favicon
│
├── app/                        ← Backend MVC
│   ├── Core/                   ← Router, Database, View, Request
│   ├── Controllers/            ← PageController, ApiController
│   ├── Models/                 ← 7 models (Project, Tool, Faq, etc)
│   ├── Services/               ← ContactService, VisitorService
│   └── Helpers/                ← i18n, helpers
│
├── views/                      ← PHP Templates
│   ├── layouts/main.php        ← Base HTML layout
│   ├── sections/               ← 15 section views
│   └── partials/               ← navbar, footer
│
├── lang/                       ← i18n strings
│   ├── en.php                  ← English
│   └── id.php                  ← Indonesian
│
├── config/                     ← Configuration
├── data/                       ← SQLite database
├── docs/                       ← Documentation
├── logs/                       ← Error logs
├── .opencode/                  ← Agent configuration
├── bootstrap.php               ← Autoloader
├── setup.php                   ← DB seeder
├── AGENT.md                    ← This file
├── README.md                   ← Project readme
├── LICENSE                     ← All Rights Reserved
└── .gitignore                  ← Git ignore rules
```

---

## Database Schema (SQLite)

| Table | Purpose |
|-------|---------|
| `visitors` | Track unique visitors (IP, user_agent, country, timestamp) |
| `contact_submissions` | Log contact form submissions |
| `projects` | Portfolio projects (name, category, image, description) |
| `tools` | Tech stack directory (name, category, icon) |
| `faqs` | FAQ items (category, question, answer) |
| `testimonials` | Client reviews (name, role, avatar, text, rating) |
| `services` | Service offerings (number, title, description) |
| `gallery` | Gallery photos (image, description) |

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Render main page |
| POST | `/api/contact` | Submit contact form |
| GET | `/api/visitor` | Get visitor count |
| GET | `/api/tools?category=&search=` | Get filtered tools |
| GET | `/api/projects?category=` | Get filtered projects |
| GET | `/lang/{lang}` | Switch language (en/id) |

---

## How to Run

1. Start XAMPP (Apache)
2. Place project in `C:\xampp\htdocs\alfizilham`
3. Run `php setup.php` to initialize database
4. Open `http://localhost/alfizilham`
