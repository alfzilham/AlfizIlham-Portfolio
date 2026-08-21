# Alfiz Ilham — Portfolio

Personal portfolio website for **Alfiz Ilham** — Software & AI Engineer, Co-Founder of [althentic.dev](https://althentic.dev).

## Tech Stack

- **Backend:** PHP 8.x (vanilla MVC, no framework)
- **Frontend:** Vanilla HTML5, CSS3, JavaScript (ES6+)
- **Database:** SQLite
- **Server:** XAMPP (Apache)

## Features

- 15-section single-page portfolio
- Bilingual support (EN/ID) with PHP session-based toggle
- Filterable skills directory (44 tools, 6 categories)
- Filterable projects grid (46 projects: 19 websites, 9 design, 18 calligraphy)
- Contact form with EmailJS + PHP fallback
- Real-time visitor counter (SQLite-based)
- Responsive design (4 breakpoints: 480px, 768px, 1024px, 1280px)
- Smooth scroll (Lenis.js)
- Interactive elements (magnetic cursor, parallax, curved marquee, gallery tooltip)

## Installation

1. Start XAMPP (Apache)
2. Clone this repository:
   ```bash
   git clone https://github.com/alfzilham/AlfizIlham-Portfolio.git
   ```
3. Move to XAMPP htdocs:
   ```bash
   mv AlfizIlham-Portfolio C:\xampp\htdocs\alfizilham
   ```
4. Initialize database:
   ```bash
   php setup.php
   ```
5. Open in browser: `http://localhost/alfizilham`

## Project Structure

```
alfizilham/
├── public/                 ← Web root
│   ├── index.php           ← Front controller
│   ├── .htaccess           ← URL rewriting
│   └── assets/             ← CSS, JS, images
├── app/                    ← Backend MVC
│   ├── Core/               ← Router, Database, View, Request
│   ├── Controllers/        ← PageController, ApiController
│   ├── Models/             ← Project, Tool, Faq, Testimonial, etc.
│   ├── Services/           ← ContactService, VisitorService
│   └── Helpers/            ← i18n, helpers
├── views/                  ← PHP templates
│   ├── layouts/            ← main.php
│   ├── sections/           ← 15 section views
│   └── partials/           ← navbar, footer
├── lang/                   ← i18n (en.php, id.php)
├── config/                 ← Configuration
├── data/                   ← SQLite database
└── docs/                   ← Documentation
```

## Documentation

- [ARCHITECTURE.md](docs/ARCHITECTURE.md) — Technical architecture
- [CONTEXT.md](docs/CONTEXT.md) — Project background and goals
- [DESIGN.md](docs/DESIGN.md) — Design specification
- [SPEC.md](docs/SPEC.md) — Functional specification

## Contact

- **WhatsApp:** [+62 852-1389-6460](https://wa.me/6285213896460)
- **Email:** alfizilham@gmail.com
- **GitHub:** [github.com/alfzilham](https://github.com/alfzilham)
- **LinkedIn:** [linkedin.com/in/alfiz-ilham093a2](https://www.linkedin.com/in/alfiz-ilham093a2/)
- **Instagram:** [@alfzilham](https://www.instagram.com/alfzilham/)

## License

Copyright (c) 2026 Alfiz Ilham. All Rights Reserved.
