# Integrasi

## Midtrans Snap

`MidtransService` membuat transaksi, melakukan sinkronisasi server-to-server, dan memproses callback `/api/midtrans/callback`. Transisi sukses pembayaran memakai lock transaksi agar stok hanya berkurang sekali. Kredensial dapat disimpan melalui admin Settings dengan fallback `.env`.

## Kurir Digital Hook

Tidak ada API RajaOngkir, ekspedisi reguler, atau tracking pihak ketiga pada alur aktif. `DeliveryAreaService` membaca `config/digitalhook.php` untuk:

- daftar kota/kabupaten dan kecamatan;
- validasi alamat;
- tarif same-day;
- nama kurir dan cutoff pemesanan.

Kode kurir order adalah `digital_hook_sameday`. Bukti sampai dan histori tetap dikelola di aplikasi. Nilai tarif per kota/kabupaten dapat diubah melalui pengaturan admin dan menerima nilai `0` untuk gratis ongkir.

## Email dan notifikasi

Email digunakan untuk verifikasi akun, OTP pendaftaran, reset password, dan konfirmasi perubahan nomor telepon. Notifikasi database memberi tahu admin ketika KTP diajukan dan memberi tahu buyer ketika KTP disetujui/ditolak serta saat status pesanan berubah.

## Cloudflare Turnstile

Halaman login menampilkan widget Cloudflare Turnstile bila `TURNSTILE_ENABLED=true` dan site key serta secret key tersedia. Token selalu diverifikasi server-to-server melalui Siteverify sebelum autentikasi dijalankan. Konfigurasi yang digunakan:

- `TURNSTILE_ENABLED`
- `TURNSTILE_SITE_KEY`
- `TURNSTILE_SECRET_KEY`
- `TURNSTILE_ALLOWED_HOSTNAMES` (pisahkan lebih dari satu hostname dengan koma)

Kredensial Turnstile hanya disimpan di `.env`, bukan pada database atau pengaturan admin. Untuk pengembangan lokal, gunakan test key resmi Cloudflare dan masukkan hostname lokal pada whitelist.

## Penyimpanan dokumen

Foto produk/logo menggunakan disk `public`. KTP menggunakan disk privat `local`; jangan membuat symbolic link ke folder dokumen identitas.
