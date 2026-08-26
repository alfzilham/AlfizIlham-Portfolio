# CONTEXT.md — Alfiz Ilham Portfolio

Project background, goals, audience, and brand context. Read this first before ARCHITECTURE.md, SPEC.md, or DESIGN.md — it explains **why** the site is built the way it is.

---

## 1. Project Summary

A personal portfolio website for **Alfiz Ilham**, a Computer Engineering (Teknik Komputer) student at Universitas Syiah Kuala (Aceh, Indonesia) who positions himself professionally as a **"Fullstack Developer, AI-Integrated Apps"**.

The site is a single-page (`index.php`) PHP MVC portfolio combining:

- Software/web development case studies (19 website projects)
- Design work (9 pieces — posters, banners, brand assets)
- Naskh calligraphy portfolio (18 pieces, 10+ competition wins, 500+ commissions)
- A full tech-stack directory (filterable, searchable — 56 tools)
- A working contact form + embedded map (Banda Aceh)
- Community/organizational activity documentation (B&W photo gallery)

The site is bilingual (EN/ID toggle in navbar) — English is the default language.

---

## 2. Goals

| Goal | Why it matters |
|------|---------------|
| **Convert visitors into clients/leads** | Primary CTA repeats throughout ("Let's Build Something", "Start a Project", "Send Message") — this is a service-selling site, not just a resume. |
| **Establish credibility across 3 disciplines** | Alfiz isn't just a developer — the site must convincingly present dev, design, _and_ calligraphy as serious, professional skill areas (20+ design projects, 500+ commissions, 10+ competition wins). |
| **Show breadth of technical skill** | The full tech-stack grid + filterable tool directory (56 tools) exists to reassure technical clients/recruiters that stack knowledge is real and current. |
| **Feel personal and human, not templated** | Heavy use of a custom 3D mascot illustration, playful marquees, and a monochrome-but-bold visual identity — differentiates from generic portfolio templates. |
| **Local + international reach** | Contact section centers on Banda Aceh (local market, UMKM clients via althentic.dev) while the overall English-first copy and "outside Indonesia" FAQ signal openness to international clients. |

---

## 3. Target Audience

1. **Prospective clients (freelance/agency work)** — small-to-medium Indonesian businesses (UMKM) needing websites, plus potential international clients for full-stack/AI work via althentic.dev.
2. **Recruiters / hiring managers** — evaluating Alfiz for internship or junior software/AI engineering roles.
3. **Calligraphy commission clients** — a distinct audience segment interested in Naskh calligraphy work, separate from tech clients.
4. **Academic/peer network** — university peers, USK community, and organizations Alfiz has volunteered with (visible in the Gallery section).

---

## 4. Brand Personality

- **Bold, confident, minimal** — monochrome black/white palette, oversized typography, no gradients or decorative color. Confidence communicated through scale and contrast, not color.
- **Playful but professional** — the 3D mascot avatar, kinetic marquee text, and floating icon badges add personality without undermining the professional tone of the copy itself.
- **Multi-disciplinary, not scattered** — dev + AI + design + calligraphy are presented as facets of one coherent identity ("Software & AI Engineer" who also does calligraphy), not as unrelated hobbies.
- **Evidence-driven** — sections back claims with CV-supported numbers: 20+ design projects, 4+ years of calligraphy, 500+ commissions, 10+ competition wins, and the 19/9/18 project breakdown.

---

## 5. Content Domains

| Domain | Asset folder | Count |
|--------|-------------|-------|
| Website projects | `public/assets/image/projects/website/` | 19 |
| Design work | `public/assets/image/projects/design/` | 9 |
| Calligraphy | `public/assets/image/projects/calligraphy/` | 18 |
| Community/activity gallery | `public/assets/image/gallery/` | 16 |

---

## 6. Known Constraints

- **PHP MVC (vanilla)** — no frontend framework, no CSS framework beyond Bootstrap Icons (icon font only). Vanilla routing, no Composer dependencies.
- **SQLite database** — lightweight, serverless. No MySQL/PostgreSQL required. Data seeded via `seed.php` (CLI only).
- **Single page** — all sections live on `index.php`; nav links are anchor scrolls (`#about`, `#skills`, etc.).
- **Static assets, no CMS** — all project/gallery images are pre-exported `.webp` files in `public/assets/image/`.
- **Contact form** — EmailJS as primary submission method, PHP `mail()` as fallback. No dedicated email service configured yet.
- **i18n** — English complete, Indonesian complete. Toggle via PHP session.
- **Visitor counter** — real tracking via SQLite. Session-based deduplication. Bot filtering included.
- **Mobile responsiveness** — tuned per-section in `responsive.css` (4 breakpoints): navbar keeps side padding from viewport edges, the curved marquee scales up on small screens, skill filters stack instead of scrolling, contact strip pairs "Call & WhatsApp" with "Write to Us" (icons hidden, newsletter below), and the CTA fan cards scale down so all six icons stay inside small viewports.
