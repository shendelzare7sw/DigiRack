# DigiRack

DigiRack adalah marketplace untuk produk infrastruktur IT, jaringan, server, dan perangkat enterprise. Aplikasi ini dibangun dengan Laravel 11, Blade, Tailwind CSS, Alpine.js, Vite, dan integrasi Midtrans untuk pembayaran.

Project ini mendukung alur buyer, seller, dan admin dalam satu aplikasi: katalog produk, toko seller, checkout, pembayaran, pengiriman, bukti paket sampai, auto-complete order, wallet seller, payout, serta pengaturan operasional platform.

## Fitur Utama

- Marketplace produk IT: katalog, detail produk, galeri gambar, kategori, banner, flash sale, wishlist, dan cart.
- Role buyer, seller, dan admin dengan dashboard masing-masing.
- Registrasi akun dengan OTP email 6 digit, masa berlaku, batas percobaan, dan resend cooldown.
- Seller onboarding dengan data toko, dokumen identitas, approval admin, dan storefront publik.
- CRUD produk seller, laporan produk, dan proteksi agar seller tidak membeli produk sendiri.
- Checkout buyer dengan Midtrans Snap, status sinkron dari callback dan dari halaman order.
- Riwayat order buyer dan seller, invoice cetak/unduh, pembatalan order, dan notifikasi.
- Pengiriman fleksibel tanpa API tracking: kurir toko, ekspedisi reguler, input resi manual, dan bukti foto paket sampai.
- Upload multiple foto bukti paket sampai, preview sebelum upload, thumbnail di buyer/seller, dan preview besar saat diklik.
- Auto-complete order setelah paket tercatat sampai dan buyer tidak konfirmasi dalam durasi yang ditentukan admin.
- Wallet seller, perhitungan dana bersih, fee platform per item, dan request payout.
- Admin panel untuk user, toko, produk, order, kategori, banner, flash sale, payout, recovery ticket, transaction fee, dan system settings.

## Stack

- PHP 8.2+
- Laravel 11
- Laravel Breeze
- MySQL/MariaDB atau SQLite untuk lokal
- Tailwind CSS 3
- Alpine.js
- Vite 5
- Midtrans PHP SDK
- PHPUnit

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- Database MySQL/MariaDB untuk production
- Mail transport aktif untuk OTP email
- Web server yang mengarah ke folder `public`

## Setup Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Atur database di `.env`. Untuk Laragon/MySQL biasanya:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digirack
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
php artisan storage:link
```

Jalankan aplikasi:

```bash
php artisan serve
npm run dev
```

Untuk build asset production:

```bash
npm run build
```

## Environment Penting

Contoh variabel yang biasanya perlu diatur:

```env
APP_NAME=DigiRack
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Jakarta

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@domain.test
MAIL_FROM_NAME="Helpdesk DigiRack"

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_MERCHANT_ID=
MIDTRANS_IRIS_API_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Catatan: konfigurasi Midtrans juga bisa dikelola dari menu admin `Settings`, sehingga nilai database dapat menggantikan fallback dari `.env`.

## Alur Pembayaran Midtrans

1. Buyer checkout dan memilih pembayaran Midtrans.
2. Sistem membuat payment reference dan payment token.
3. Buyer membayar lewat Snap.
4. Status order diperbarui melalui callback `/api/midtrans/callback`.
5. Halaman `buyer/orders` dan detail order juga melakukan sinkronisasi status berdasarkan payment reference.
6. Jika pembayaran sukses, order masuk ke status diproses dan seller dapat mengirim paket.

Pastikan URL callback Midtrans mengarah ke:

```text
https://domain-anda.com/api/midtrans/callback
```

## Alur Pengiriman dan Dana Seller

1. Seller menandai order sudah dikirim.
2. Untuk kurir reguler, seller mengisi nomor resi manual.
3. Untuk kurir toko, sistem memakai penanda internal `KURIR-TOKO`.
4. Saat paket sudah benar-benar sampai di alamat, seller wajib upload foto bukti sampai.
5. Buyer melihat bukti foto di detail order dan dapat konfirmasi pesanan diterima.
6. Dana dicairkan ke wallet seller setelah buyer konfirmasi atau setelah auto-complete melewati batas waktu.

Auto-complete dihitung sejak `delivered_at`, yaitu saat paket tercatat sampai, bukan sejak `shipped_at`.

## Cron Production

Command auto-complete:

```bash
php artisan orders:auto-complete
```

Cron yang disarankan:

```cron
* * * * * cd /www/wwwroot/digirack && php artisan schedule:run >> /dev/null 2>&1
```

Jika scheduler belum memanggil command ini di environment tertentu, jalankan langsung tiap beberapa menit:

```cron
*/5 * * * * cd /www/wwwroot/digirack && php artisan orders:auto-complete >> /dev/null 2>&1
```

Durasi auto-complete dapat diatur admin melalui `auto_complete_hours` di menu Settings. Nilai `0` menonaktifkan auto-complete.

## Deploy Production

Setelah pull perubahan terbaru:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika build gagal karena permission pada `public/build`, perbaiki ownership folder yang perlu ditulis oleh user web server:

```bash
sudo chown -R www:www public/build storage bootstrap/cache
sudo -u www npm run build
```

Sesuaikan user `www:www` dengan user web server production Anda.

## Testing

Jalankan semua test:

```bash
php artisan test
```

Test penting yang sudah mencakup flow kritis:

- OTP registration
- Midtrans payment status sync
- Buyer/seller role switching
- Proteksi pembelian produk sendiri
- Product gallery
- Auto-complete order
- Bukti foto pengiriman multiple image

## Struktur Folder Penting

- `app/Http/Controllers/Buyer` - alur buyer, cart, checkout, order.
- `app/Http/Controllers/Seller` - toko, produk, order, kurir, wallet seller.
- `app/Http/Controllers/Admin` - panel admin dan pengaturan platform.
- `app/Services/MidtransService.php` - integrasi status pembayaran Midtrans.
- `app/Console/Commands/AutoCompleteOrders.php` - command auto-complete order.
- `resources/views` - Blade UI untuk public, buyer, seller, admin, auth, dan layout.
- `routes/buyer.php`, `routes/seller.php`, `routes/admin.php` - routing per role.
- `database/migrations` - skema database.
- `database/seeders` - data awal kategori, lokasi, user, toko, produk, banner, flash sale, review.

## Catatan Keamanan

- Jangan commit `.env`, key Midtrans, credential SMTP, atau credential database.
- Gunakan mode Sandbox Midtrans saat testing, lalu ubah ke Production hanya setelah callback dan payment flow terverifikasi.
- Pastikan `APP_DEBUG=false` di production.
- Pastikan folder `storage` dan `bootstrap/cache` writable oleh web server.
- Gunakan HTTPS di production, terutama untuk callback Midtrans, login, OTP, dan upload bukti pengiriman.

## Developer Credit

Developed by: Tabah Ujianto & Yayan Wahyudi
