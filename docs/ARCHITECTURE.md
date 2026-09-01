# ARCHITECTURE.md — Alfiz Ilham Portfolio

Dokumentasi arsitektur yang mengikuti source code saat ini. Project adalah aplikasi portfolio PHP vanilla tanpa framework dan tanpa build step.

## Stack dan entry point

- PHP 8.x, PDO SQLite, dan Apache/XAMPP.
- HTML/PHP views, CSS vanilla, dan satu bundle JavaScript `public/assets/js/main.js`.
- CDN: Leaflet 1.9.4, Lucide 1.34.0, Lenis 1.3.26, GSAP 3.15.0, Chart.js 4.5.1, Bootstrap Icons, Google Fonts.
- WebGL circular gallery memakai OGL melalui dynamic import dari `esm.sh`.
- Root `index.php` mendelegasikan ke `public/index.php`. Root `.htaccess` menyembunyikan `/public`, me-rewrite asset, page legal, dan `/api/*`.
- `public/privacy.php` dan `public/terms.php` memakai layout legal yang sama dan mendukung `?lang=en|id`.

## Struktur dan tanggung jawab

```text
bootstrap.php                  constants, autoloader, env, helpers, class loading
public/index.php               front controller, session, locale, visitor tracking, routes
app/Core/                      Router, Request, Database singleton, View renderer
app/Controllers/               PageController, ApiController, ChatController, AdminController
app/Models/                    SQLite access untuk content, admin data, dan knowledge index
app/Services/                  contact, visitor, HTTP, embedding, RAG, knowledge indexing
views/layouts/main.php         shell halaman utama, modal admin, lightbox, CDN scripts
views/sections/                section halaman single-page
views/partials/                navbar, footer, chatbot
lang/                          dictionary EN/ID termasuk dynamic_content
config/                        konfigurasi site dan `.env` lokal
data/database.sqlite           database runtime
scripts/rebuild-knowledge.php  rebuild index semantic dari project/certificate
```

## Request flow

```text
HTTP request
  -> .htaccess / public/index.php
  -> bootstrap.php
  -> secure_session_start() + locale + VisitorService::track()
  -> Router::dispatch(resolve_route_uri())
  -> controller
  -> model/service -> PDO SQLite
  -> json_response() atau View::render()
```

`Router` mendukung GET, POST, DELETE, route parameter `{id}`, pretty URL, dan format legacy `index.php?/api/...`. `Request` membaca query, form, JSON body, IP, dan user agent. `View` melakukan output buffering lalu me-render layout/section/partial.

## Halaman utama

`PageController::index()` mengambil FAQ, service, testimonial, gallery, project, showcase, tool, certificate, dan visitor count. Baris FAQ/service/testimonial/gallery kemudian dioverlay dengan `dynamic_content` dari dictionary locale berdasarkan `sort_order` atau `id`.

Urutan section di `views/layouts/main.php` adalah: intro, hero, curved marquee, about, bio stats, education, skills, projects teaser, services, tech marquee, gallery, testimonials, certificates, FAQ, contact, dan closing CTA. Data server diteruskan ke JavaScript melalui `window.__LANG`, `window.__LANG_DATA`, `window.__IS_ADMIN`, `window.__CSRF_TOKEN`, `window.__CERTIFICATES`, dan konfigurasi EmailJS.

## API dan controller

| Method | Path | Handler | Kegunaan |
|---|---|---|---|
| GET | `/` | `PageController::index` | Render portfolio |
| POST | `/api/contact` | `ApiController::contact` | Fallback contact ke database/email service |
| GET | `/api/visitor` | `ApiController::visitorCount` | Total, hari ini, dan negara |
| GET | `/api/tools` | `ApiController::tools` | Filter category/search |
| GET | `/api/projects` | `ApiController::projects` | Filter category |
| POST | `/api/chat` | `ChatController::ask` | Tanya chatbot grounded |
| POST | `/api/chat/feedback` | `ChatController::feedback` | Simpan/toggle good/bad |
| GET | `/api/cards` | `AdminController::listCards` | Showcase public |
| POST | `/api/admin/login` | `AdminController::login` | Login editor |
| POST | `/api/admin/logout` | `AdminController::logout` | Logout editor |
| GET | `/api/admin/session` | `AdminController::session` | Cek session admin |
| POST/DELETE | `/api/admin/cards[/{id}]` | `AdminController` | CRUD showcase |
| GET/POST/DELETE | `/api/admin/certificates[/{id}]` | `AdminController` | CRUD certificate |
| POST | `/api/admin/certificates/{id}/pin` | `AdminController` | Toggle pinned certificate |
| POST | `/api/admin/{certificates,projects}/bulk-import` | `AdminController` | Import JSON, maksimal 50 row |
| GET | `/api/admin/{certificates,projects}/export` | `AdminController` | Export JSON |
| GET | `/lang/{lang}` | closure | Simpan locale lalu redirect |

Endpoint admin yang mengubah state memerlukan session admin dan header `X-CSRF-Token`. Login/logout juga memerlukan CSRF; login me-regenerate session ID.

## Data dan lazy migration

Seeder `php seed.php` membuat tabel content awal: `visitors`, `contact_submissions`, `projects`, `tools`, `faqs`, `testimonials`, `services`, `gallery`, `showcase_projects`, dan `certificates`.

Model runtime dapat membuat/migrasikan tabel tambahan: `chat_feedback`, `knowledge_chunks`, `chat_rate_limits`, dan `feedback_rate_limits`. `Certificate::ensureTable()` menambahkan `company` dan `pinned` pada database lama; `Service::ensureImageColumn()` menambahkan kolom image; `ShowcaseProject::ensureTable()` memastikan tabel dan kolom link.

`Certificate::all()` mengurutkan `pinned DESC, sort_order ASC, id ASC`. `ShowcaseProject::all()` mengurutkan terbaru lebih dulu. Upload lokal dikonversi ke WebP di `public/assets/uploads/showcase/` dan path lama dihapus secara path-safe saat replacement/deletion.

## Chatbot dan knowledge index

1. `ChatController::ask()` menerima JSON/form `message` sepanjang 2–1000 karakter.
2. Rate limit per IP: maksimal satu request per menit; record lama dua jam dibersihkan.
3. `EmbeddingService` mencoba Hugging Face (`HF_TOKEN`, default `BAAI/bge-m3`, prefix `query:`/`passage:`), lalu OpenRouter embeddings sebagai fallback.
4. `RagService` hanya mengambil `knowledge_chunks` dengan provider, model, dan dimension yang sama; cosine similarity diranking di PHP dan maksimal lima hasil dikirim ke prompt.
5. OpenRouter chat memakai `OPENROUTER_CHAT_MODEL` lalu fallback model, temperature 0.2, max 450 token, dan reasoning dikecualikan.
6. Prompt menggabungkan CV statis, snapshot website, aggregate facts, dan hasil retrieval; jawaban dibersihkan dari reasoning/presentation wrapper sebelum dikirim.
7. Feedback menyimpan hash pesan, tipe, question, answer, IP; maksimal 30 feedback per IP per jam dan klik tipe yang sama melakukan toggle off.

Create/update/delete admin untuk showcase dan certificate melakukan sync/delete knowledge index. Kegagalan provider dicatat sebagai warning dan tidak membatalkan perubahan SQLite. `php scripts/rebuild-knowledge.php` membangun ulang index dari semua showcase dan certificate.

## Keamanan dan deployment

- Session memakai `HttpOnly`, `SameSite=Lax`, `Secure` saat HTTPS, serta CSRF token per session.
- Password admin hanya disimpan sebagai bcrypt hash di config.
- Upload membatasi tipe image, ukuran/dimensi, mengonversi WebP, dan folder upload mematikan eksekusi script.
- Root `.htaccess` mengembalikan 404 untuk source/config/data/docs dan memblokir file sensitif, TRACE/TRACK, serta directory listing.
- Header keamanan aktif: nosniff, SAMEORIGIN, strict referrer policy, dan CSP report-only.
- Contact mencoba EmailJS di browser lalu fallback `/api/contact`; country visitor lookup memakai cache SQLite dan fallback ip-api.com dengan timeout pendek.

Development: XAMPP Apache pada `http://localhost/alfizilham`. Tidak ada `npm install`, Composer, atau bundling; pastikan ekstensi PDO SQLite dan GD tersedia untuk admin image upload.
