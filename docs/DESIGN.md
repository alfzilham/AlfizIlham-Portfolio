# DESIGN.md — Alfiz Ilham Portfolio

Panduan visual dan interaction yang tercermin pada `views/`, `public/assets/css/`, dan `public/assets/js/main.js`.

## Prinsip visual

- Monochrome: hitam/putih/abu-abu sebagai dasar, tanpa gradient dekoratif.
- Tipografi display besar dan uppercase untuk headline; Plus Jakarta Sans untuk body/UI.
- Kontras, whitespace, border, dan scale menjadi pembawa hierarchy.
- Editorial dan playful secara bersamaan: mascot, marquee, fan cards, ChromaGrid, dan motion menjadi aksen.
- Semua section harus tetap terbaca dan usable pada empat kelompok breakpoint di `responsive.css`.

## Token dan layout

Source token berada di `global.css` dan diperluas di `components.css`/`responsive.css`. Gunakan spacing berbasis `rem`, border tipis, radius kecil/menengah, dan shadow yang hemat. Layout utama memakai container lebar dengan padding horizontal responsif; jangan menambahkan warna aksen atau dependency CSS baru tanpa alasan produk.

CTA utama menggunakan tombol hitam solid; CTA sekunder memakai ghost/outline. Focus state harus terlihat. Icon UI memakai Lucide; brand/tool icon memakai asset SVG atau Bootstrap Icons.

## Komposisi halaman

```text
intro mascot
hero profile + CTA + stats
curved marquee
about + bio stats + education
skills/filter/search
project teaser (ChromaGrid + circular gallery)
services accordion
dark tech marquee
gallery masonry
testimonials
certificates grid
FAQ category sidebar + accordion
contact form + map + contact strip/newsletter
closing CTA + clock + fan cards
footer + chatbot
```

Semua section di-render oleh `views/layouts/main.php` melalui `View::section()`. Navbar/footer/chatbot adalah partial bersama. Modal editor, lightbox, timeline picker, dan bulk import overlay berada di layout atau section terkait.

## Interaction contract

- Navbar mobile membuka/menutup drawer; active section mengikuti IntersectionObserver.
- Lenis mengatur smooth scrolling. Scroll progress, reveal, fold text, stat counters, hero reveal/parallax, marquee, dan cursor/magnet effects berjalan progressively dengan fallback browser.
- Skills mengubah filter dan search tanpa reload.
- Project ChromaGrid mendukung hover spotlight, menu editor saat admin, load more, drag/keyboard pada circular gallery, dan lightbox prev/next/filmstrip.
- Services dan FAQ memakai accordion; FAQ memiliki filter kategori.
- Certificates memakai load more, lightbox, credential link, serta pin/edit/delete untuk admin.
- Contact memakai custom dropdown, validasi inline, timeline calendar + clock picker, EmailJS lalu fallback API, dan status `aria-live`.
- Chatbot memiliki panel toggle, pending indicator, rich answer, edit/resend question, feedback good/bad, dan error state.
- Editor memiliki login overlay, CRUD multipart, upload drag/drop preview, bulk JSON import, JSON export, dan logout.

## Motion rules

Motion harus komunikatif dan dapat dimatikan/diringankan bila `prefers-reduced-motion` aktif. Hindari layout thrashing: transform/opacity untuk animasi visual, observer untuk reveal/lazy behavior, dan passive listener untuk scroll/pointer saat memungkinkan. Interaksi drag harus memiliki pointer cancel/up cleanup dan keyboard alternative untuk gallery/lightbox.

## Accessibility

- Gunakan semantic section, heading order, label form, button untuk action, dan link untuk navigation.
- Semua modal/overlay memiliki `role="dialog"`, `aria-modal`, close action, serta keyboard Escape.
- Error/status memakai `aria-live`; custom dropdown menjaga selected value dan keyboard behavior.
- Gambar diberi alt yang berasal dari konten atau deskripsi; link eksternal memakai `target="_blank" rel="noopener"`.
- Kontras teks dan focus ring harus dipertahankan pada mode mobile maupun dark marquee.

## Asset rules

- Asset publik berada di `public/assets/` dengan path yang dipakai view relatif terhadap public root.
- Foto dan upload umumnya WebP; tool/brand icon berupa SVG.
- Upload editor hanya masuk `public/assets/uploads/showcase/`, di-resize maksimal 1600 px dan dikonversi WebP oleh `AdminController`.
- Jangan mengandalkan file source `app/`, `views/`, `config/`, `docs/`, atau `graphify-out/` sebagai asset publik; root `.htaccess` memang memblokirnya.

## Third-party loading

Versi CDN dan SRI didefinisikan di `views/layouts/main.php`. Leaflet dipakai contact map, Lucide untuk icon replacement, Lenis untuk scroll, GSAP untuk motion, Chart.js untuk admin/stat visualization bila digunakan, dan OGL untuk circular gallery. `main.js` tetap menjadi entry point behavior dan memakai cache-busting `filemtime`.

## Responsive requirements

Mobile harus menjaga padding navbar dari tepi viewport, memperbesar/menyesuaikan curved marquee, men-stack filter skills, menyusun ulang contact strip, menempatkan newsletter setelah contact links, mengecilkan CTA fan cards agar enam icon tidak overflow, dan membuat overlay/lightbox dapat di-scroll tanpa mengunci halaman secara salah.
