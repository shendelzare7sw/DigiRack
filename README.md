# Digital Hook

Digital Hook adalah toko online single-store untuk komponen komputer, laptop baru/second, periferal, perangkat jaringan ringan, dan aksesori digital. Layanan ditujukan untuk Tangerang Raya dan menggunakan kurir toko same-day.

## Teknologi

- Laravel 11, PHP 8.2+
- Blade, Alpine.js, Tailwind CSS v3, Vite
- MySQL/MariaDB untuk produksi, SQLite untuk pengujian
- Midtrans Snap untuk pembayaran

## Aturan utama

- Hanya ada role `admin` dan `buyer`. Admin sekaligus pemilik dan operator toko Digital Hook.
- Pembeli wajib mengirim KTP dan memperoleh persetujuan admin sebelum checkout.
- Alamat hanya dapat disimpan jika kota dan kecamatannya tercantum di `config/digitalhook.php`.
- Pengiriman hanya memakai `Kurir Digital Hook Same Day`; tidak ada ekspedisi atau API ongkir eksternal.
- Stok berkurang saat pembayaran sukses. Pembayaran masuk langsung ke merchant Digital Hook; tidak ada wallet atau payout seller.

## Menjalankan proyek

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

Untuk production, isi kredensial Midtrans melalui panel admin atau `.env`, atur `APP_URL`, dan jalankan scheduler untuk `php artisan orders:auto-complete`.

## Verifikasi

```bash
php artisan test
npm run build
php artisan route:list
```

Dokumentasi pengembang dimulai dari [docs/00-README.md](docs/00-README.md).
