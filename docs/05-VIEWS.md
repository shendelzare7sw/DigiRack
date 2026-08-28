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
