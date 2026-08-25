# REPORT.md — Investigasi Bug: Contact Form Custom Dropdown

Tanggal: 25 Agustus 2026
Status: Investigasi selesai, fix belum diterapkan

---

## Bug 1: Dropdown Popup Terbuka Bersamaan & Overlap

### (a) File & Baris
- `public/assets/css/components.css:2897-2910`

### (b) Kode Relevan

```css
/* components.css:2897-2910 */
.dropdown-popup {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  z-index: 100;
  background: var(--color-bg);
  border-radius: var(--neu-radius-sm);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  max-height: 240px;
  display: flex;              /* ← MASALAH: override hidden attribute */
  flex-direction: column;
}
```

HTML (contact.php:37, 60, 83):
```html
<div class="dropdown-popup" hidden>  /* hidden, tapi CSS override */
```

### (c) Root Cause
CSS `display: flex` pada `.dropdown-popup` **meng-override** atribut `hidden` di HTML. Atribut `hidden` mengatur `display: none`, tapi aturan CSS `display: flex` memiliki spesifisitas lebih tinggi → **semua popup terlihat dari awal**, bukan tersembunyi.

**Akibat:** 3 popup (country code + service + currency) semuanya terlihat sekaligus dari awal halaman dimuat → overlap dan tumpang tindih.

---

## Bug 2: Raw Translation Key "contact_search_placeholder"

### (a) File & Baris
- `views/sections/contact.php:37, 60, 83`
- `lang/en.php` — TIDAK ADA
- `lang/id.php` — TIDAK ADA

### (b) Kode Relevan

```php
<!-- contact.php:37, 60, 83 (3 tempat) -->
<input type="text" class="dropdown-search" 
       placeholder="<?= i18n::t('contact_search_placeholder') ?>" />
```

### (c) Root Cause
Key `contact_search_placeholder` **tidak pernah ditambahkan** ke `lang/en.php` maupun `lang/id.php`. Fungsi `i18n::t()` mengembalikan key sendiri sebagai fallback jika key tidak ditemukan → `"contact_search_placeholder"` tampil mentah di placeholder.

---

## Bug 3: Dropdown Popup Meluber Keluar Container

### (a) File & Baris
- `public/assets/css/components.css:2897-2910` (dropdown-popup)
- `public/assets/css/components.css:2765-2771` (contact-form)

### (b) Root Cause
**Merupakan konsekuensi langsung dari Bug 1.** Karena Bug 1 menyebabkan SEMUA popup terlihat sekaligus (3 popup × banyak item), konten melampaui batas form. Begitu Bug 1 diperbaiki (popup hidden by default), masalah overflow ini **hilang secara otomatis**.

Namun ada potensi masalah tersendiri: dropdown country code memiliki 30 item. Dengan `max-height: 240px` dan search input (±36px), popup bisa mencapai ±276px. Jika form berada di bagian bawah layar, popup bisa terpotong viewport.

---

## Dependency antar Bug

```
Bug 1 (display:flex override hidden)
  │
  ├──► Semua popup visible sekaligus
  │      │
  │      ├──► Bug 3: meluber/overlap (akibat langsung)
  │      │
  │      └──► UI berantakan (tumpang tindih 3 popup)
  │
  └──► Fix Bug 1 → Bug 3 hilang otomatis

Bug 2 (key tidak ada) — independent, tidak terkait Bug 1/3
```

---

## Rekomendasi Perbaikan

| Bug | Arah Perbaikan |
|---|---|
| **Bug 1** | Ganti `display: flex` menjadi aturan bersyarat: `.dropdown-popup:not([hidden]) { display: flex; }` agar flex hanya aktif saat popup terbuka. Biarkan `hidden` attribute bekerja untuk state default. |
| **Bug 2** | Tambah key `contact_search_placeholder` ke `lang/en.php` (`"Search..."`) dan `lang/id.php` (`"Cari..."`). |
| **Bug 3** | Fix Bug 1 → Bug 3 hilang otomatis. Safety net: `max-height: 240px` sudah ada dan memadai. |
