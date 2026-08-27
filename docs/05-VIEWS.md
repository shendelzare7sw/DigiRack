# View dan Navigasi

Desain Blade, Alpine.js, dan Tailwind lama dipertahankan. Branding aktif menggunakan nama Digital Hook dan aset `public/images/digital-hook-logo.png`.

## Navigasi buyer

- Katalog produk komputer dan digital
- Wishlist, keranjang, pesanan
- Profil, alamat, dan Verifikasi KTP
- Tentang Digital Hook dan Wilayah Pengantaran

## Navigasi admin

- Dashboard operasional
- Profil toko
- Produk, kategori, promo/banner
- Pesanan dan balasan ulasan
- Pengguna serta review KTP
- Pengaturan Midtrans dan kontak toko

Form alamat memakai dropdown Provinsi → Kota/Kabupaten → Kecamatan. Data dropdown berasal dari `LocationController`, sedangkan validasi final dilakukan `DeliveryAreaService`.
