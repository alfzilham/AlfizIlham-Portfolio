# CONTEXT.md — Alfiz Ilham Portfolio

## Ringkasan

Alfiz Ilham adalah mahasiswa Teknik Komputer Universitas Syiah Kuala di Banda Aceh yang memosisikan diri sebagai **Fullstack Developer, AI-Integrated Apps**. Website ini adalah portfolio single-page yang juga memuat layanan, karya desain dan kaligrafi, kredensial, contact flow, serta assistant AI yang menjawab berdasarkan data portfolio.

## Tujuan produk

1. Mengubah pengunjung menjadi calon klien melalui CTA, service list, FAQ, contact form, WhatsApp, email, dan map Banda Aceh.
2. Menunjukkan kemampuan fullstack, AI integration, automation, design, dan calligraphy melalui data serta karya yang dapat dijelajahi.
3. Memberi recruiter dan calon klien jawaban cepat melalui chatbot grounded tanpa mengarang fakta.
4. Menjaga konten yang berubah—showcase project dan certificate—dapat dikelola melalui editor mode tanpa CMS eksternal.

## Audiens

- UMKM dan bisnis kecil/menengah yang memerlukan website, aplikasi, automasi, atau integrasi AI.
- Recruiter/hiring manager untuk posisi software, AI, atau fullstack junior.
- Klien commission kaligrafi Naskh.
- Komunitas akademik dan organisasi di Aceh.

## Positioning dan tone

Identitas visual monochrome, tegas, minimal, dan kontras tinggi. Mascot 3D, marquee, kartu interaktif, dan motion memberi sisi personal; copy tetap profesional dan berbasis bukti. Developer, designer, AI builder, dan calligrapher dipresentasikan sebagai satu identitas multidisiplin.

Default bahasa UI adalah Indonesia, dengan English sebagai pilihan penuh melalui session toggle. Copy data berulang dapat dilokalkan lewat `dynamic_content` di dictionary bahasa.

## Domain konten

| Domain | Source | Fungsi |
|---|---|---|
| Website/design/calligraphy | tabel `projects` + folder image | project teaser dan filter kategori |
| Showcase terkurasi | `showcase_projects` + `uploads/showcase` | ChromaGrid, CRUD editor, chatbot knowledge |
| Skills | tabel `tools` + icon SVG | kategori/search dan tech marquee |
| Services | tabel `services` | accordion layanan |
| Aktivitas | tabel `gallery` + `assets/image/gallery` | masonry gallery |
| Social proof | `testimonials` | testimonial/rating/avatar |
| Credentials | `certificates` | pinned card, credential link, editor |
| Profile authority | `public/assets/cv/Alfiz_Ilham_CV.md` | sumber chatbot |

## Batasan yang disengaja

- PHP MVC vanilla, tanpa framework, Composer, npm, bundler, atau server API terpisah.
- SQLite dipilih agar deployment ringan dan sesuai hosting sederhana; `seed.php` hanya dapat dijalankan dari CLI.
- Halaman utama tetap single-page, sedangkan privacy/terms adalah page entry point terpisah.
- Contact memakai EmailJS dari browser dengan fallback PHP; konfigurasi provider dan AI dibaca dari config/env.
- Chatbot hanya boleh menyampaikan fakta dari CV, snapshot website, database, dan retrieval context. Ia memiliki rate limit dan graceful error.
- Editor bukan CMS publik: login session, CSRF, validasi upload, WebP normalization, dan remote-image SSRF protection wajib dipertahankan.

## Source of truth

Perilaku aktual ditentukan oleh `public/index.php`, controller/service/model, dan template di `views/`. Database SQLite adalah source of truth untuk record runtime. `graphify-out/2026-09-01/GRAPH_REPORT.md` membantu navigasi dependency, tetapi dapat stale setelah perubahan dan bukan source code. Jalankan Graphify update setelah perubahan code bila graph diperlukan kembali.
