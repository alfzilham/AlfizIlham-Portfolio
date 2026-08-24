# REPORT.md — Investigasi Certificate Section

Tanggal: 24 Agustus 2026
Status: Investigasi read-only, rencana fix disiapkan

---

## 1. DIVIDER DI BAWAH HEADING

**(a) File & Baris:**
- `views/sections/certificates.php:9`
- `public/assets/css/global.css:396-400`

**(b) Kode Relevan:**

```php
<!-- certificates.php:9 -->
<div class="divider"></div>
```

```css
/* global.css:396-400 */
.divider {
  width: 100%;
  height: 1px;
  background-color: var(--color-border);
}
```

**(c) Root Cause:**
Elemen `<div class="divider"></div>` diletakkan **di dalam** `.container` (baris 9), **setelah** subtitle dan **sebelum** `.certificates-grid-wrap`. CSS `.divider` membuat garis horizontal 1px se-lebar container dengan warna `--color-border`. Divider ini memang sengaja ditambahkan di revisi sebelumnya untuk memisahkan heading dari konten grid. Jika ingin dihapus, cukup hapus baris 9 di `certificates.php`.

---

## 2. BACKGROUND OPTIONWHEEL

**(a) File & Baris:**
- `public/assets/css/components.css:2603-2608`

**(b) Kode Relevan:**

```css
/* components.css:2603-2608 */
.certificates-wheel-wrap {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  background: #0a0a0a;
}
```

**(c) Root Cause:**
Background `#0a0a0a` (hampir hitam) diatur pada selector `.certificates-wheel-wrap` — ini adalah **container pembungkus** OptionWheel, bukan komponen OptionWheel itu sendiri. Tidak ada inline style background yang di-set via JS di `wheelRoot`. Jika ingin mengubah background wheel, ubah nilai `background` pada selector ini.

---

## 3. DATA DUMMY DI OPTIONWHEEL & DEPTHCAROUSEL

**(a) File & Baris:**
- `public/assets/js/main.js:2226-2236` (initCertificates)
- `public/assets/js/main.js:2227` (window.__CERTIFICATES)
- `views/sections/certificates.php:25` (JSON payload)
- `seed.php:373` (komentar — tidak ada seed data)

**(b) Kode Relevan:**

```js
// main.js:2226-2236
function initCertificates() {
  var items = window.__CERTIFICATES || [];
  var wheelRoot = document.getElementById("certOptionWheel");
  var carouselRoot = document.getElementById("certDepthCarousel");
  var gridWrap = document.querySelector(".certificates-grid-wrap");

  if (!items.length) {
    if (gridWrap) gridWrap.style.display = "none";
    return;
  }
  ...
}
```

```php
// certificates.php:25
window.__CERTIFICATES = <?= json_encode($certificates, JSON_UNESCAPED_UNICODE) ?>;
```

```php
// seed.php:373
// Certificates table (created lazily, no seed data — admin uploads manually)
```

**(c) Root Cause:**
- **Bukan hardcoded** — tidak ada array mock/fallback/sample di main.js
- **Bukan seed data baru** — seed.php saat ini tidak menghapus atau menambah baris ke tabel `certificates`
- **Data dari baris lama di database** — seed.php versi SEBELUMNYA (sebelum dihapus) pernah meng-insert 3 sample certificate (Google Cloud, AWS, Meta). Baris-baris itu **masih ada di database** karena seed.php yang baru tidak menjalankan `DELETE FROM certificates` (karena tidak ada insert baru)
- **Logika empty state benar** — `if (!items.length)` pada baris 2233 sudah benar. Masalahnya `items.length > 0` karena DB masih punya data lama

**Kesimpulan:** Data berasal dari **row lama di database** dari seed.php versi sebelumnya. Cukup jalankan `DELETE FROM certificates;` atau hapus manual via admin panel.

---

## 4. IKUT TER-SCROLL SAAT SCROLL HALAMAN

**(a) File & Baris:**
- `public/assets/js/main.js:2366-2377` (wheel listener — OptionWheel)
- `public/assets/js/main.js:2674-2685` (wheel listener — DepthCarousel)
- `public/assets/js/main.js:2280` (touchAction — OptionWheel)
- `public/assets/js/main.js:2458` (touchAction — DepthCarousel)
- `views/sections/certificates.php:12-17` (tidak ada `data-lenis-prevent`)

**(b) Kode Relevan:**

```js
// main.js:2366-2377 — OptionWheel wheel listener
wheelRoot.addEventListener("wheel", function (e) {
  e.preventDefault();
  var delta = e.deltaMode === 1 ? e.deltaY * 24 : e.deltaY;
  var step = Math.max(-1, Math.min(1, delta / owRowH));
  owTarget += step;
  owStartLoop();
  ...
}, { passive: false });

// main.js:2674-2685 — DepthCarousel wheel listener
carouselRoot.addEventListener("wheel", function (e) {
  if (items.length < 2) return;
  e.preventDefault();
  ...
}, { passive: false });

// main.js:2280 — OptionWheel touchAction
wheelRoot.style.touchAction = "none";

// main.js:2458 — DepthCarousel touchAction
carouselRoot.style.touchAction = "pan-y";
```

**(c) Root Cause Analysis:**

**Dua masalah terpisah:**

**Masalah A — DepthCarousel (`touchAction: "pan-y"`):**
DepthCarousel di-set `touch-action: pan-y` — mengizinkan scroll vertikal native. Saat user scroll halaman dan kursor di atas DepthCarousel, browser boleh melakukan scroll native. Tapi wheel listener-nya juga memanggil `e.preventDefault()` — kontradiktif.

**Masalah B — Lenis listener fire duluan:**
Lenis (smooth scroll library) mendaftarkan wheel listener di level window. Saat event wheel terjadi:
1. Lenis listener fire duluan (registered lebih dulu) → proses sebagai page scroll
2. Component listener fire → `e.preventDefault()` → sudah terlambat, Lenis sudah jalan

**Masalah C — Tidak ada `data-lenis-prevent`:**
Kedua komponen TIDAK memiliki atribut `data-lenis-prevent` di HTML. Lenis secara default memproses semua wheel events. Tanpa atribut ini, Lenis tidak tahu harus mengabaikan wheel events dari dalam komponen.

**Ringkasan:** Root cause utama adalah **Lenis wheel listener di window fire duluan** sebelum component wheel listener. Fix: tambah `data-lenis-prevent` pada `#certOptionWheel` dan `#certDepthCarousel` + sesuaikan touchAction.

---

## Ringkasan Temuan

| # | Issue | Root Cause | Fix yang Diperlukan |
|---|---|---|---|
| 1 | Divider di bawah heading | `<div class="divider">` di `certificates.php:9` | Hapus baris 9 jika tidak diinginkan |
| 2 | Background gelap wheel | CSS `.certificates-wheel-wrap { background: #0a0a0a }` di `components.css:2607` | Ubah nilai background |
| 3 | Data dummy muncul | Row lama di DB dari seed.php versi sebelumnya | `DELETE FROM certificates` atau hapus via admin panel |
| 4 | Ikut ter-scroll | Lenis fire duluan + tidak ada `data-lenis-prevent` + `touchAction: pan-y` | Tambah `data-lenis-prevent` + sesuaikan touchAction |
