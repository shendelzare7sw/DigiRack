# AGENTS.md — Panduan AI Agent (Digital Hook)

Digital Hook adalah toko online lokal Laravel 11 (Blade, Alpine.js, Tailwind v3) untuk komponen komputer, laptop baru/second, periferal, jaringan ringan, dan aksesori digital.

## Baca dahulu

1. [docs/02-ROUTES-MAP.md](docs/02-ROUTES-MAP.md) untuk route → controller → view.
2. [docs/03-FLOWS.md](docs/03-FLOWS.md) untuk KTP, checkout, pembayaran, dan pengiriman.
3. [docs/04-MODELS.md](docs/04-MODELS.md) untuk data dan status.
4. [docs/06-INTEGRATIONS.md](docs/06-INTEGRATIONS.md) untuk Midtrans dan kurir.

## Fakta wajib

- Role hanya string `admin` atau `buyer`; tidak ada role switching.
- Admin juga pemilik/operator satu-satunya toko Digital Hook dengan slug `digihook`.
- Beberapa controller/view masih berada di namespace/folder `Seller` sebagai kode operasional warisan, tetapi hanya diakses melalui rute `admin.*`.
- Buyer wajib berstatus KTP `verified` sebelum checkout. Dokumen ada di disk privat `local`.
- Alamat wajib termasuk whitelist `config/digitalhook.php`.
- Pengiriman hanya `digital_hook_sameday` oleh kurir toko; tidak ada RajaOngkir/ekspedisi eksternal pada alur aktif.
- Stok dikurangi saat pembayaran Midtrans sukses, bukan saat order dibuat.
- Tidak ada storefront publik, moderasi/ban toko, ulasan toko, wallet/payout seller, atau kurir per toko.

## Konvensi

- Bahasa komunikasi dan UI: Bahasa Indonesia. Identifier/komentar kode: English-style.
- Jangan commit `.env` atau kredensial.
- Percayai kode bila dokumen berbeda, lalu perbarui dokumentasi.
- Verifikasi dengan `php artisan route:list`, `php artisan test`, dan `npm run build`.
