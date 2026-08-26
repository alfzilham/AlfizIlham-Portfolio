# Portfolio AI Chatbot — Design Spec

**Tanggal:** 2026-08-26
**Status:** Disetujui, siap masuk tahap implementation plan
**Pairs with:** `README.md`, `ARCHITECTURE.md`, `CONTEXT.md` (portfolio existing)

---

## 1. Ringkasan

AI chatbot yang di-embed di website portfolio (althentic.dev) untuk menjawab pertanyaan pengunjung tentang Alfiz Ilham (profil, skill, pengalaman, project, certificate) secara real-time, berbasis RAG (Retrieval-Augmented Generation). Ditujukan untuk visitor yang malas membaca full page dan lebih suka bertanya langsung.

Dibangun sebagai ekstensi dari backend PHP MVC yang sudah ada (bukan service terpisah), memanfaatkan admin panel existing (`AdminController`) sebagai satu-satunya jalur input data project/certificate.

## 2. Tujuan & Non-Tujuan

**Tujuan:**

- Visitor bisa tanya bebas tentang Alfiz dan dapat jawaban akurat berbasis data nyata (CV + project + certificate)
- Data project/certificate baru otomatis tersedia untuk chatbot begitu di-save lewat admin panel — tanpa langkah manual tambahan
- Auto-detect bahasa pertanyaan (EN/ID)

**Non-tujuan (di luar scope v1):**

- Tidak ada conversation memory antar sesi/reload
- Tidak ada admin UI baru — reuse admin panel existing
- Tidak ada re-embed otomatis untuk CV (CV statis, di-update manual jika berubah)
- Tidak menjawab pertanyaan di luar topik Alfiz secara mendalam (hanya redirect sopan)

## 3. Arsitektur

Pendekatan: **Thin Backend** — semua logic RAG (embedding, retrieval, prompt building, call OpenRouter) berada di PHP, menyatu dengan MVC existing. NeonDB (Postgres + pgvector) dipakai sebagai vector store, terpisah dari SQLite (yang tetap jadi source of truth untuk data project/certificate).

### 3.1 Komponen Baru

| Komponen                               | Lokasi               | Fungsi                                                                                                      |
| -------------------------------------- | -------------------- | ----------------------------------------------------------------------------------------------------------- |
| `ChatController`                       | `app/Controllers/`   | Handle `POST /api/chat` — terima pertanyaan visitor, jalankan RAG, panggil OpenRouter chat completion       |
| `EmbeddingService`                     | `app/Services/`      | Wrapper untuk `POST https://openrouter.ai/api/v1/embeddings`                                                |
| `NeonSyncService`                      | `app/Services/`      | Generate embedding + upsert/delete row di NeonDB, dipanggil dari `AdminController` setelah commit ke SQLite |
| `RagService`                           | `app/Services/`      | Query NeonDB (cosine similarity via pgvector) untuk top-N context relevan                                   |
| Static knowledge (CV + profil ringkas) | Config/system prompt | Base context yang selalu disertakan (tidak di-embed — kecil & jarang berubah)                               |

### 3.2 Data Flow

**A. Sync (trigger real-time dari admin panel):**

```
AdminController::createCard() / updateCard() / deleteCard()
   → (setelah commit ke SQLite seperti biasa, tidak mengubah alur existing)
   → NeonSyncService::sync($item)
       → EmbeddingService::embed($text)   [gabungan title + description + metadata relevan]
       → UPSERT ke tabel knowledge_chunks di NeonDB
   → delete: DELETE row NeonDB WHERE source_type + source_id cocok
```

**B. Chat (saat visitor bertanya):**

```
Visitor kirim pertanyaan → POST /api/chat
   → EmbeddingService::embed(pertanyaan)
   → RagService::search(embedding, top_k=5) → ambil context dari NeonDB
   → Bangun prompt: [system prompt + CV statis + context RAG hasil retrieval] + pertanyaan visitor
   → Call OpenRouter chat completion (model utama, dari env)
       → jika gagal / rate-limited → fallback ke model kedua (dari env)
       → jika kedua gagal → kembalikan pesan error ramah ke visitor
   → Return jawaban ke frontend
```

## 4. Skema Database — NeonDB (baru, terpisah dari SQLite)

```sql
CREATE EXTENSION IF NOT EXISTS vector;

CREATE TABLE knowledge_chunks (
  id SERIAL PRIMARY KEY,
  source_type TEXT NOT NULL,      -- 'project' | 'certificate'
  source_id INTEGER NOT NULL,     -- id dari SQLite showcase_projects / certificates
  content TEXT NOT NULL,          -- teks gabungan yang di-embed
  embedding VECTOR(1024),         -- dimensi mengikuti bge-m3 (primary); lihat §7.2 soal provider berbeda
  embedding_provider TEXT NOT NULL DEFAULT 'huggingface',  -- 'huggingface' | 'openrouter' — lihat §7.2
  metadata JSONB,                 -- link, image, credential_id, dll untuk ditampilkan di jawaban
  updated_at TIMESTAMP DEFAULT now(),
  UNIQUE(source_type, source_id)
);

CREATE INDEX ON knowledge_chunks USING ivfflat (embedding vector_cosine_ops);
```

SQLite (`showcase_projects`, `certificates`) tetap menjadi source of truth. NeonDB murni sebagai index vektor turunan — bisa di-rebuild kapan saja dari SQLite jika perlu.

## 5. API Endpoint Baru

| Method | Endpoint    | Controller              | Deskripsi                                                  |
| ------ | ----------- | ----------------------- | ---------------------------------------------------------- |
| POST   | `/api/chat` | `ChatController::ask()` | Terima pertanyaan visitor, jalankan RAG, return jawaban AI |

Endpoint sync (`NeonSyncService`) tidak diekspos sebagai route publik — dipanggil langsung sebagai method call dari dalam `AdminController` yang sudah session-gated, bukan lewat HTTP webhook eksternal.

## 6. Behavior AI (System Prompt)

- **Persona:** Asisten yang menjelaskan Alfiz Ilham — profil, skill, pengalaman, project, certificate — berdasarkan data yang tersedia
- **Sumber kebenaran:** Hanya CV statis + hasil retrieval NeonDB. Dilarang mengarang project, pengalaman, atau klaim yang tidak ada di data
- **Bahasa:** Auto-detect dari bahasa pertanyaan visitor (EN/ID, mengikuti bahasa yang dipakai visitor, bukan toggle website)
- **Off-topic handling:** Boleh menjawab santai untuk pertanyaan di luar topik, tapi tetap arahkan balik ke topik tentang Alfiz
- **Statefulness:** Tidak menyimpan riwayat percakapan antar reload halaman — tiap sesi chat dimulai bersih

## 7. Model & Fallback

### 7.1 Chat

| Peran               | Env var                                                                           | Catatan                                                                                                                                                                                                                                                                                                                   |
| ------------------- | --------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Chat model utama    | `OPENROUTER_CHAT_MODEL` (default: `openrouter/free`)                              | Router bawaan OpenRouter yang otomatis memilih model gratis yang tersedia — menghindari masalah "model delisted" karena rotasinya ditangani OpenRouter sendiri, bukan hardcoded di kode                                                                                                                                   |
| Chat model fallback | `OPENROUTER_CHAT_MODEL_FALLBACK` (default: `dots-studio/dots3-note-preview:free`) | Model `:free` spesifik sebagai jaring pengaman jika router utama gagal/timeout. Dipakai otomatis saat model utama gagal / kena rate limit (limit umum model gratis: ~20 req/menit, ~200 req/hari). Verifikasi ulang ketersediaannya di openrouter.ai/models sebelum implementasi, karena daftar model gratis berubah-ubah |

Jika kedua model chat gagal, `ChatController` mengembalikan pesan error yang ramah ke visitor (bukan raw error).

### 7.2 Embedding

Dipilih untuk menghemat limit: provider gratis sebagai primary, provider berbayar-murah sebagai fallback (bukan cadangan model dalam satu provider yang sama).

| Peran              | Env var                                                                 | Catatan                                                                                                                                                              |
| ------------------ | ----------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Embedding primary  | `HF_EMBEDDING_MODEL` (default: `BAAI/bge-m3`)                           | Hugging Face Inference API, gratis. Multilingual (cocok EN/ID). Free tier: ~ratusan request/jam, kemungkinan cold start 10-30 detik pada model yang jarang dipanggil |
| Embedding fallback | `OPENROUTER_EMBEDDING_MODEL` (default: `openai/text-embedding-3-small`) | Dipakai otomatis jika Hugging Face gagal/timeout/rate-limited. Berbayar per token (murah), praktis tanpa downtime                                                    |

**Constraint penting — dimensi vektor:** `bge-m3` dan `text-embedding-3-small` punya dimensi vektor berbeda. Karena kolom `embedding VECTOR(n)` di NeonDB butuh dimensi tetap, `EmbeddingService` harus:

- Menyimpan hasil embedding dari provider manapun yang berhasil ke dimensi kolom yang sama (disarankan: standardisasi ke dimensi `bge-m3` sebagai primary, dan jika fallback OpenRouter terpakai, hasilnya di-pad/truncate atau tabel dirancang menyimpan `provider` per baris agar pencarian similarity tidak mencampur dua ruang vektor berbeda)
- **Rekomendasi implementasi:** tandai setiap baris `knowledge_chunks` dengan kolom `embedding_provider`, dan saat query chat, jalankan retrieval hanya terhadap baris dengan provider yang sama dengan yang dipakai untuk meng-embed pertanyaan saat itu. Detail teknis final ditentukan di tahap implementation plan.

Jika kedua provider embedding gagal, request chat tidak bisa lanjut ke retrieval — `ChatController` mengembalikan pesan error ramah ke visitor.

## 8. Environment Variables

```env
# --- Isi sendiri (isi di config/.env — JANGAN pernah commit nilai asli ke repo) ---
NEON_DATABASE_URL=
OPENROUTER_API_KEY=

# --- Model config chat (rotasi model gratis OpenRouter berubah-ubah; verifikasi ulang di openrouter.ai/models sebelum implementasi) ---
OPENROUTER_CHAT_MODEL=openrouter/free
OPENROUTER_CHAT_MODEL_FALLBACK=dots-studio/dots3-note-preview:free

# --- Model config embedding (primary: Hugging Face gratis, fallback: OpenRouter berbayar-murah) ---
HF_TOKEN=
HF_EMBEDDING_MODEL=BAAI/bge-m3
OPENROUTER_EMBEDDING_MODEL=openai/text-embedding-3-small

# --- Opsional, disarankan untuk keamanan ---
CHAT_API_RATE_LIMIT_PER_MINUTE=20
```

Catatan per variabel:

- `NEON_DATABASE_URL` — connection string Postgres dari NeonDB (format `postgres://user:pass@host/dbname?sslmode=require`)
- `OPENROUTER_API_KEY` — dipakai untuk chat completion (primary + fallback) dan embedding fallback
- `OPENROUTER_CHAT_MODEL` / `_FALLBACK` — isi setelah cek model `:free` yang aktif di openrouter.ai/models saat implementasi
- `HF_TOKEN` — token Hugging Face terpisah, dipakai khusus untuk embedding primary
- `HF_EMBEDDING_MODEL` — default `BAAI/bge-m3` (multilingual, gratis); bisa diganti model HF lain yang mendukung embedding
- `CHAT_API_RATE_LIMIT_PER_MINUTE` — opsional, untuk melindungi endpoint `/api/chat` dari abuse publik sebelum limit provider sendiri kena (disarankan karena endpoint ini publik-facing, beda dari endpoint admin yang sudah session-gated)

Tidak perlu webhook secret terpisah karena sync dipanggil sebagai internal method call (lihat §5), bukan HTTP endpoint publik.

## 9. Keamanan & Constraint

- `/api/chat` adalah endpoint publik (tanpa auth) — perlu rate limiting dasar per IP untuk mencegah abuse yang menghabiskan quota OpenRouter
- Tidak ada perubahan pada security hardening existing (CSRF, session, `.htaccess` rules) — endpoint baru mengikuti pola `ApiController` yang sudah ada
- NeonDB connection string dan OpenRouter API key disimpan di `config/.env` (pola yang sama seperti `config/.env.example` existing), tidak pernah di-commit ke git

## 10. Testing

- Unit: `EmbeddingService` (mock response), `RagService` (similarity search dengan data dummy)
- Integration: sync end-to-end (create/update/delete project di admin panel → cek row NeonDB berubah)
- Manual: `/api/chat` dengan pertanyaan EN dan ID, termasuk pertanyaan off-topic dan pertanyaan tentang project yang baru saja di-upload
