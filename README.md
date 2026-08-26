<div align="center">
  <img src="public/assets/favicon/Logo.png" alt="Alfiz Ilham Logo" width="120" />
  <h1>Alfiz Ilham — Portfolio</h1>
  <p>Personal portfolio website for <strong>Alfiz Ilham</strong> — Full Stack Developer, AI-Integrated Apps</p>
</div>

---

## Tech Stack

- **Backend:** PHP 8.x (vanilla MVC, no framework)
- **Frontend:** Vanilla HTML5, CSS3, JavaScript (ES6+)
- **Database:** SQLite
- **Server:** XAMPP (Apache)

## Features

- 15-section single-page portfolio
- Bilingual support (EN/ID) with PHP session-based toggle
- Filterable skills directory (56 tools, 8 categories)
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
   php seed.php
   ```
5. Open in browser: `http://localhost/alfizilham`

## Project Structure

```
alfizilham/
├── public/                 ← Web root
│   ├── index.php           ← Front controller
│   ├── .htaccess           ← URL rewriting
│   └── assets/             ← CSS, JS, images
├── app/                      ← Backend MVC
│   ├── Core/                 ← Router, Database, View, Request
│   ├── Controllers/          ← PageController, ApiController, AdminController
│   ├── Models/               ← Project, Tool, Faq, Testimonial, etc.
│   ├── Services/             ← ContactService, VisitorService
│   └── Helpers/              ← i18n, helpers, session (CSRF + cookie flags)
├── views/                    ← PHP templates
│   ├── layouts/              ← main.php, legal.php
│   ├── pages/                ← privacy.php, terms.php (legal content)
│   ├── sections/             ← 15 section views
│   └── partials/             ← navbar, footer
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

## Curriculum Vitae

### Summary

Fullstack developer who builds AI directly into products rather than bolting it on after. Freelance work so far includes production websites, an n8n-based WhatsApp automation for filtering important messages, a workflow tool with live database sync, and a productivity app with an in-app AI assistant wired through the OpenRouter API — built with Vanilla JavaScript, React, and Next.js on the frontend, and Node.js, Python, and PHP further back. Also spent 2 years as an independent freelance graphic designer and 4 years as a calligrapher completing 500+ commissions, work that runs on the same deadline discipline and client feedback loops as software development.

### Experience

#### Independent — Remote

**Freelance Software Developer**
_June 2026 – Present_

- Built an AI-powered WhatsApp automation using n8n and the WAHA (WhatsApp HTTP API) to automatically filter important incoming messages — webhook-triggered flow that detects image attachments, downloads and converts them to Base64, and routes text through the OpenRouter API and images through the Opencode.zen API (Ox Alpha model) for AI analysis before conditional filtering.
- Built Personal Habit Tracker (Aug 2026), a productivity app with an in-app AI assistant powered by the OpenRouter API — reads and tracks user habit data directly so users can ask questions instead of digging through FAQs.
- Built PusakaApp (Jul 2026), a full-stack school reunion attendance system: Excel import, payment-proof photo upload to Vercel Blob, and one-click export to Excel, PDF, and JSON. Delivered free as a community project.
- Built FlowGram (May 2026), a workflow builder with drag-and-drop infinite canvas and multi-project dashboard, wired to Google OAuth and a Neon PostgreSQL database for cloud sync.

#### Independent — Remote

**Freelance Frontend Developer**
_March 2025 – June 2026_

- Built and shipped a client website for a school counselor — services section, blog layout, testimonials, and a working contact form — in plain HTML, CSS, and vanilla JavaScript.
- Built two self-initiated landing pages (Mekatron, a 3D robot showcase; a scroll-driven creative portfolio) using React and Next.js to practice animation and scroll-based interaction.
- Live projects and source code: [github.com/alfzilham](https://github.com/alfzilham)

#### Imam Travel — Aceh, Indonesia

**Freelance Graphic Designer**
_June 2025 – March 2026_

- Designed posters and promotional materials for Umrah and international tour packages using Adobe Photoshop and CorelDRAW.
- Completed 20+ paid design projects across a range of client promotional needs.

#### Independent — Aceh, Indonesia

**Freelance Calligrapher**
_2023 – Present_

- Completed 500+ paid custom calligraphy commissions based on individual client requests.
- Specializes in Naskh script and Mushaf illumination decoration.
- Won 10+ calligraphy competitions at local/school, sub-district, district, and provincial levels.

### Leadership & Activities

#### OSMID — Student Organization of Dayah Jeumala Amal — Pidie Jaya, Aceh, Indonesia

**Member, Events & Design Division**
_2025 – 2026_

- Planned and ran school-wide events as part of the events and design division.
- Designed posters and digital banners for organizational events.
- Designed and installed hand-lettered Arabic vocabulary boards (mufradat) on plywood across the dayah campus.
- Reinstalled Windows and resolved hardware issues on the organization secretary's computer.

#### KPMN 2024 — Kemah Pramuka Madrasah Nasional — Cibubur, Jakarta, Indonesia

**Aceh Provincial Representative**
_2024_

- Selected to represent Aceh Province at the national KPMN competition in Cibubur, Jakarta.

#### Flood Relief Volunteer — Pidie Jaya, Aceh, Indonesia

**Community Volunteer**
_2026_

- Assisted flood victims in Pidie Jaya as part of a community disaster relief effort.

### Education

#### Universitas Syiah Kuala — Aceh, Indonesia

**Bachelor's, Computer Engineering (Teknik Komputer)**
_2026 – Present_

#### MAS Jeumala Amal — Pidie Jaya, Aceh, Indonesia

**High School Diploma, Science (IPA)**
_July 2024 – May 2026_

#### SMK Darul Ihsan — Aceh Besar, Aceh, Indonesia

**Computer & Network Engineering**
_1 year; transferred to MAS Jeumala Amal • 2023 – 2024_

#### MTsS Darul Ihsan — Aceh Besar, Aceh, Indonesia

**Junior High School Diploma**
_Graduated 2023_

### Skills

**Fullstack Development:** HTML, CSS, JavaScript, TypeScript, React, Next.js, Node.js, Express.js, PHP, Tailwind CSS, Bootstrap, MongoDB, PostgreSQL (Neon), MySQL, SQL, REST API, Git, GitHub, Docker, Vercel

**AI Integration & Tools:** Python, Claude API, OpenRouter API, MCP, Claude Code, n8n, AWS Bedrock, Google Vertex AI

**Design Tools:** Adobe Photoshop, Lightroom, CorelDRAW, Canva

**Spoken Languages:** Indonesian (Native), English (Intermediate), Arabic (Intermediate)

## Contact

- **WhatsApp:** [+62 852-1389-6460](https://wa.me/6285213896460)
- **Email:** alfizilham@gmail.com
- **GitHub:** [github.com/alfzilham](https://github.com/alfzilham)
- **LinkedIn:** [linkedin.com/in/alfiz-ilham093a2](https://www.linkedin.com/in/alfiz-ilham093a2/)
- **Instagram:** [@alfzilham](https://www.instagram.com/alfzilham/)

## License

Copyright (c) 2026 Alfiz Ilham. All Rights Reserved.
