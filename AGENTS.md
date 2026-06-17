# AGENTS.md — Panduan untuk AI Agent (DigiRack)

> Pintu masuk netral untuk **semua** AI coding agent (Claude Code, Cursor, Copilot, dll.). Dokumentasi lengkap & kanonik ada di folder [`docs/`](docs/00-README.md).

## Apa ini

DigiRack = **marketplace multi-seller** Laravel 11 (Blade + Alpine.js + Tailwind v3) untuk produk IT/jaringan. Tiga role dalam satu aplikasi: **admin / seller / buyer**.

## Baca ini dulu (lompat cepat)

1. **Cari file penanggung jawab sebuah halaman/menu** → [docs/02-ROUTES-MAP.md](docs/02-ROUTES-MAP.md) (tabel `route → controller → view → data flow`).
2. **Pahami proses bisnis** (checkout, bayar, kirim, retur, payout, onboarding) → [docs/03-FLOWS.md](docs/03-FLOWS.md) (state-machine + diagram).
3. **Struktur data & enum status** → [docs/04-MODELS.md](docs/04-MODELS.md).
4. **Tampilan, komponen, navigasi** → [docs/05-VIEWS.md](docs/05-VIEWS.md).
5. **Pembayaran/payout/ongkir** → [docs/06-INTEGRATIONS.md](docs/06-INTEGRATIONS.md).
6. **Role & middleware & routing** → [docs/01-ARCHITECTURE.md](docs/01-ARCHITECTURE.md).

Indeks penuh: [docs/00-README.md](docs/00-README.md). Setup & deploy: [README.md](README.md).

## Fakta wajib tahu

- **Role = string** di `users.role` (`admin/seller/buyer`), bukan Spatie. Konteks aktif di `session('active_role')`.
- Role naik ke `seller` **hanya saat admin verifikasi toko** (`Admin\StoreController@toggleVerification`).
- Route dipecah per role: `routes/{web,auth,buyer,seller,admin}.php`.
- Bayar via **Midtrans Snap**; status disinkron lewat webhook `/api/midtrans/callback` + fallback server-to-server.
- **Stok dikurangi saat pembayaran sukses**, bukan saat order dibuat.
- Dana seller masuk **Wallet** saat order `completed`, cair via **payout** (admin approve → IRIS).
- Pengiriman tanpa API tracking: resi manual + kurir internal toko (`toko_*`); histori di `ShipmentEvent`.

## Konvensi kerja

- Bahasa komunikasi & UI: **Bahasa Indonesia**. Identifier/komentar kode: bahasa kode (English-style).
- Jangan commit rahasia (`.env`, key Midtrans/SMTP, DB). Kredensial dikelola via admin Settings (`SystemSetting`) dengan fallback `.env`.
- Cek kebenaran ke kode dulu sebelum mengandalkan dokumen; jika dokumen sudah tidak cocok, **percayai kode** lalu perbarui `docs/`.
- Perintah berguna: `php artisan route:list`, `php artisan test`, `php artisan orders:auto-complete`.
