# CLAUDE.md

Project: **DigiRack** — marketplace multi-seller Laravel 11 (Blade + Alpine.js + Tailwind v3), role admin/seller/buyer.

Dokumentasi lengkap & netral ada di **[AGENTS.md](AGENTS.md)** dan folder **[docs/](docs/00-README.md)** — baca itu dulu daripada scan ulang codebase.

- Peta halaman → controller → view: [docs/02-ROUTES-MAP.md](docs/02-ROUTES-MAP.md)
- Alur bisnis (state machine): [docs/03-FLOWS.md](docs/03-FLOWS.md)
- Model & enum status: [docs/04-MODELS.md](docs/04-MODELS.md)
- View & navigasi: [docs/05-VIEWS.md](docs/05-VIEWS.md)
- Integrasi (Midtrans/IRIS/ongkir): [docs/06-INTEGRATIONS.md](docs/06-INTEGRATIONS.md)
- Arsitektur, role, middleware: [docs/01-ARCHITECTURE.md](docs/01-ARCHITECTURE.md)

Aturan singkat:
- Balas user dalam **Bahasa Indonesia**; identifier/komentar kode tetap bahasa kode.
- Role = string di `users.role` + `session('active_role')` (bukan Spatie).
- Jangan commit rahasia; kredensial via admin Settings (`SystemSetting`) + fallback `.env`.
- Jika dokumen tak cocok dengan kode, percayai kode lalu perbarui `docs/`.
