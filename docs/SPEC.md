# SPEC.md — Alfiz Ilham Portfolio

Spesifikasi fungsional yang sesuai dengan implementasi saat ini.

## Lokalisasi dan navigasi

Bahasa yang tersedia `id` dan `en`; default dari `config/config.php` adalah `id`. `public/index.php` membaca `?lang=`, session, atau default lalu memuat `lang/id.php`/`lang/en.php`. Link `/lang/{lang}` menyimpan pilihan ke session dan mengembalikan visitor ke halaman sebelumnya.

Navbar mengarah ke anchor single-page: `#about`, `#education`, `#skills`, `#project`, `#service`, `#gallery`, `#testimonial`, `#certificate`, `#faq`, dan `#contact`. Halaman legal tersedia di `/privacy` dan `/terms`.

## Urutan dan perilaku section

Halaman utama merender intro, hero, curved marquee, about, bio stats, education, skills, project teaser, services, tech marquee, gallery, testimonials, certificates, FAQ, contact, dan closing CTA.

- Skills: tools dari SQLite; filter kategori dan pencarian nama dijalankan client-side, dengan endpoint `/api/tools` untuk data terfilter.
- Projects: project seed dikelompokkan `website`, `design`, `calligraphy`; endpoint `/api/projects?category=...` menyediakan filter. Showcase admin ditampilkan sebagai ChromaGrid, dengan lightbox, live link opsional, dan load more.
- Services: accordion interaktif dari data SQLite.
- Gallery: masonry gallery dari SQLite dengan deskripsi.
- Testimonials: data SQLite dengan rating/avatar.
- Certificates: grid client-side dari `window.__CERTIFICATES`, pinned tampil lebih dulu, load more, lightbox, credential link, dan editor controls.
- FAQ: kategori general/services/pricing/process/contact dan panel accordion yang di-render client-side.
- Contact: validasi nama, email, telepon, service, message; budget/currency dan custom timeline picker; EmailJS adalah jalur utama, `/api/contact` fallback. Peta memakai Leaflet.
- CTA: jam live dan fan cards skill.

## Sumber data

| Model | Data | Urutan/filter |
|---|---|---|
| `Project` | project portfolio | `sort_order`, `id`, kategori |
| `Tool` | skill/tool + icon | `sort_order`, nama; kategori/search |
| `Faq` | FAQ + kategori | `sort_order`, `id` |
| `Service` | service + image | `sort_order`, `id` |
| `Testimonial` | testimonial | `sort_order`, `id` |
| `Gallery` | foto aktivitas | `sort_order`, `id` |
| `ShowcaseProject` | kartu ChromaGrid editor | id terbaru |
| `Certificate` | credential | pinned, `sort_order`, id |
| `VisitorModel` | statistik visitor | count, hari ini, negara |

`PageController` mengoverlay copy localized `dynamic_content` ke FAQ, service, testimonial, dan gallery berdasarkan posisi data. Asset statis berada di `public/assets/`; upload editor disimpan sebagai WebP di `public/assets/uploads/showcase/`.

## Visitor dan chatbot

Visitor dilacak saat front controller berjalan. `VisitorService` menghindari duplikasi berbasis session, memfilter bot, lalu menyimpan IP, user agent, country, dan waktu. Country memakai nilai tersimpan untuk IP yang sama atau lookup ip-api.com dengan timeout 0.7 detik. `/api/visitor` mengembalikan `count`, `today`, dan `byCountry`.

Chatbot menerima POST JSON/form ke `/api/chat` dengan `message` 2–1000 karakter. Limit aplikasi adalah satu pertanyaan per IP per menit. Pertanyaan di-embed, dicari pada knowledge index dengan cosine similarity, lalu dijawab OpenRouter dari CV, snapshot website, fakta aggregate, dan context terpilih. Respons berbentuk `{answer, sources}`; error provider menghasilkan 429 atau 503. Tombol good/bad memakai `/api/chat/feedback` dan dapat di-toggle.

## Contact contract

Form mengirim `from_name`, `from_email`, `country_code`, `phone`, `service`, `budget`, `currency`, `timeline`, dan `message`. Fallback server memvalidasi request POST lalu `ContactService::submit()` menyimpan `contact_submissions` dan mencoba pengiriman email. Kegagalan validasi menghasilkan 422.

## Editor mode

Editor aktif setelah password diverifikasi terhadap bcrypt hash. `window.__IS_ADMIN` menentukan toolbar. Semua state-changing request menyertakan `X-CSRF-Token`.

- Showcase: create/update/delete dengan multipart image; title dan description wajib; link dinormalisasi; upload maksimal 5 MB, image umum, dimensi maksimal 8000, resize maksimal 1600, convert WebP.
- Certificate: create/update/delete/pin; field title wajib, company/credential optional.
- Bulk import: JSON project/certificate, maksimal 50 row, public HTTP(S) image wajib, host private ditolak, duplicate title dilewati, remote image diunduh dan dinormalisasi ke WebP.
- Export: JSON untuk showcase project dan certificate; endpoint memerlukan admin tetapi tidak mengubah state.

Sesudah create/update/delete project atau certificate, knowledge index ikut disinkronkan. Kegagalan sync hanya dikembalikan sebagai warning.

## Endpoint ringkas

`GET /api/tools`, `GET /api/projects`, `GET /api/visitor`, `GET /api/cards`, `POST /api/contact`, `POST /api/chat`, `POST /api/chat/feedback`, serta route admin pada bagian Editor Mode. Router juga menerima bentuk legacy `index.php?/api/...` yang digunakan JavaScript saat deployment subdirectory.

## Non-functional requirements

- PHP tanpa Composer/npm dan SQLite sebagai storage.
- Responsive CSS terpusat di `global.css`, `components.css`, dan `responsive.css`.
- CDN dependency memakai versi tetap dan SRI di layout utama.
- Session cookie hardened, CSRF, prepared statements, upload path safety, security headers, gzip/cache Apache, dan CSP report-only.
- Seed hanya CLI: `php seed.php`. Rebuild semantic index hanya CLI: `php scripts/rebuild-knowledge.php`.
