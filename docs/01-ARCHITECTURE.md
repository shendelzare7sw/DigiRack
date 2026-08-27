# Arsitektur

## Model bisnis

Digital Hook bukan marketplace multi-seller. Satu akun `admin` memiliki toko ber-slug `digihook`, sekaligus mengelola katalog, stok, pesanan, pengiriman, ulasan, pembeli, dan verifikasi KTP. Pengguna umum memakai role `buyer`.

Namespace controller `Seller` masih dipakai pada beberapa operasi katalog/pesanan untuk memanfaatkan implementasi lama, tetapi hanya diekspos melalui rute `admin.*`; namespace tersebut bukan role publik.

## Role dan middleware

| Role | Akses |
|---|---|
| `admin` | `/admin/*`, operasional toko dan administrasi |
| `buyer` | katalog, profil, alamat, keranjang, checkout, pesanan, ulasan |

Tidak ada role switching. `/dashboard` mengarahkan pengguna ke panel sesuai `users.role`. Berkas rute aktif adalah `web.php`, `auth.php`, `buyer.php`, dan `admin.php`; `seller.php` tidak dimuat.

## Keamanan penting

- Dokumen KTP disimpan di disk privat `local`, bukan `public`.
- `legal_name` dan `nik` dienkripsi oleh cast model; `nik_hash` dipakai untuk mencegah satu NIK pada beberapa akun.
- Dokumen hanya dapat dibuka pemiliknya atau admin melalui controller berotorisasi.
- Checkout memeriksa status KTP dan cakupan alamat lagi di server.
