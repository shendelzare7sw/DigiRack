# View dan Navigasi

Desain Blade, Alpine.js, dan Tailwind lama dipertahankan. Branding aktif menggunakan `public/images/digital-hook-logo-white.png` untuk wordmark dan `public/images/digital-hook-icon.png` untuk favicon.

## Navigasi buyer

- Katalog produk komputer dan digital
- Wishlist, keranjang, pesanan
- Profil, alamat, dan Verifikasi KTP
- Tentang Digital Hook dan Wilayah Pengantaran

## Navigasi admin

- Dashboard operasional
- Profil bisnis Digital Hook
- Produk, kategori, promo/banner
- Pesanan dan balasan ulasan
- Pengguna serta review KTP
- Pengaturan Midtrans dan kontak bisnis

Form alamat memakai dropdown Provinsi → Kota/Kabupaten → Kecamatan. Data dropdown berasal dari `LocationController`, sedangkan validasi final dilakukan `DeliveryAreaService`.

Nomor telepon, email, dan alamat footer publik dibaca dari `SystemSetting` dan dapat diubah melalui menu Pengaturan admin. Kontak yang belum diisi tidak ditampilkan sebagai data contoh.

Semua pemanggilan paginator Laravel menggunakan override global `resources/views/vendor/pagination/tailwind.blade.php`. Versi mobile menampilkan ringkasan hasil, halaman aktif, serta tombol kembali/lanjut yang tidak overflow; versi desktop menampilkan nomor halaman lengkap dengan gaya Digital Hook.

Beranda memiliki section pretelan laptop/PC second dengan tampilan netral. Admin dapat mengubah judul, deskripsi, label tombol, status tampil, dan memilih maksimal 10 produk kondisi `used` melalui menu Pengaturan. Jika tidak ada produk yang dipilih, section otomatis menggunakan produk second aktif terbaru yang masih memiliki stok.
