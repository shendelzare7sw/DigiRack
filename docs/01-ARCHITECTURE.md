# 01 — Arsitektur, Role & Middleware

[← Indeks](00-README.md)

## Stack

| Lapisan | Teknologi |
|---------|-----------|
| Framework | Laravel 11 (PHP 8.2+) |
| Auth scaffolding | Laravel Breeze (Blade) |
| View | Blade + komponen |
| Interaktivitas | Alpine.js (CDN/bundled), AJAX vanilla untuk cart & wishlist |
| Styling | Tailwind CSS v3 (brand color via token di `tailwind.config.js`) |
| Build | Vite 5 |
| Database | MySQL/MariaDB (prod), SQLite (lokal) |
| Pembayaran | Midtrans Snap PHP SDK |
| Payout | Midtrans IRIS (HTTP) |
| Ongkir | RajaOngkir (legacy) / Komerce — dipilih via setting |

Bahasa UI & domain: **Bahasa Indonesia**. Mata uang: **IDR** (integer rupiah, tanpa desimal di order/produk; `decimal(15,2)` di wallet).

## Struktur folder penting

```
app/
  Http/Controllers/
    Public/    -> halaman publik (katalog, storefront, pages, lokasi)
    Buyer/     -> cart, wishlist, checkout, order, review, return
    Seller/    -> registrasi toko, produk, order, shipment, return, wallet, kurir
    Admin/     -> dashboard, user, store, produk(moderasi), order, shipment,
                  return, kategori, banner, flash sale, payout, setting, fee, recovery
    Api/       -> PaymentCallbackController (webhook), OngkirController
    Auth/      -> Breeze + OTP register + recovery (user & admin)
  Http/Middleware/  -> RoleMiddleware, EnsureSellerApproved, EnforceActiveBuyerRole
  Models/           -> 25 model (lihat 04-MODELS.md)
  Services/         -> MidtransService, IrisService, RajaOngkirService, Shipping/*
  Notifications/    -> OrderNotification, StoreStatusNotification, ReviewNotification, dll.
  Console/Commands/ -> AutoCompleteOrders (orders:auto-complete)
resources/views/    -> layouts/, components/, + folder per role (lihat 05-VIEWS.md)
routes/             -> web, auth, buyer, seller, admin, console
database/migrations -> skema (rujukan enum di 04-MODELS.md)
```

## Sistem Role (inti yang wajib dipahami)

DigiRack **tidak** memakai Spatie/permission package. Role adalah **string** di kolom `users.role` dengan tiga nilai: `admin`, `seller`, `buyer` (default `buyer`). Helper di [User](../app/Models/User.php#L87): `isAdmin()`, `isSeller()`, `isBuyer()`.

### Konteks aktif: `session('active_role')`

Satu user bisa berfungsi sebagai buyer **dan** seller. Konteks "sedang berperan sebagai apa" disimpan di **session**, bukan di DB:

- Saat **login**, `active_role` di-set: `admin` → `admin`, selain itu → `buyer` (lihat `AuthenticatedSessionController`).
- **Switch role** lewat [RoleSwitchController@switchRole](../app/Http/Controllers/RoleSwitchController.php#L11) (route `GET /switch-role/{role}`):
  - `seller` → harus punya `store`; kalau belum, diarahkan ke form registrasi toko.
  - `admin` → hanya untuk user yang benar-benar `role === 'admin'`.
  - `buyer` → bebas.
- Navbar & dashboard merender versi UI sesuai `active_role` (lihat 05-VIEWS.md).

### Kapan role berubah jadi `seller`?

Bukan saat registrasi toko. User membuat toko (status `pending`) tapi `role` tetap `buyer`. Role di-upgrade ke `seller` **hanya saat admin memverifikasi toko**:

- [Admin\StoreController@toggleVerification](../app/Http/Controllers/Admin/StoreController.php#L50): set `is_verified=true`, `verification_status='approved'`, lalu `user->role='seller'`.
- Jika verifikasi dicabut / toko di-`reject`: `user->role` dikembalikan ke `buyer`.

## Middleware kustom

Didaftarkan di [bootstrap/app.php](../bootstrap/app.php#L18):

```php
$middleware->alias([
    'role'           => RoleMiddleware::class,
    'seller.approved'=> EnsureSellerApproved::class,
]);
$middleware->validateCsrfTokens(except: ['api/midtrans/callback']); // webhook bebas CSRF
```

| Middleware | Alias | Logika | Gagal → |
|-----------|-------|--------|---------|
| [RoleMiddleware](../app/Http/Middleware/RoleMiddleware.php) | `role:a,b` | Cek `user->role` ada di daftar role (mendukung `role:seller,admin`). | `abort(403)` |
| [EnsureSellerApproved](../app/Http/Middleware/EnsureSellerApproved.php) | `seller.approved` | Admin bypass. Selain itu: harus punya `store`, dan `store->isApproved()` (`verification_status==='approved' && is_verified`). | belum punya toko → `seller.register.form`; belum approved → `seller.dashboard` (dengan pesan) |
| [EnforceActiveBuyerRole](../app/Http/Middleware/EnforceActiveBuyerRole.php) | (dipakai langsung di grup buyer) | Admin **dilarang** akses rute buyer (403/redirect ke `admin.dashboard`). Jika seller menyentuh rute buyer, `active_role` otomatis di-set `buyer`. | Admin → redirect/403 |

`Store::isApproved()` = `verification_status === 'approved' && is_verified === true` (lihat [Store](../app/Models/Store.php)).

## Pembagian file route & middleware grup

| File | Prefix | Middleware grup | Isi |
|------|--------|-----------------|-----|
| [routes/web.php](../routes/web.php) | — | sebagian `auth` | Home, halaman publik (pages), API publik (lokasi, ongkir, webhook midtrans), katalog produk publik, storefront publik, recovery user, profil + alamat + switch-role + notifikasi (di grup `auth`). |
| [routes/auth.php](../routes/auth.php) | — | `guest` / `auth` | Login, register + **OTP** (`register/otp*`), forgot/reset password, verifikasi email, confirm password, logout. |
| [routes/buyer.php](../routes/buyer.php) | `/buyer` | `auth, verified, role:buyer,seller,admin, EnforceActiveBuyerRole` | Dashboard, cart, wishlist, order, return, checkout, review produk & toko. |
| [routes/seller.php](../routes/seller.php) | `/seller` | Grup 1: `auth, verified` (register/identity/dashboard). Grup 2: `auth, verified, role:seller,admin, seller.approved` (store, produk, order, shipment, return, wallet, kurir). | Manajemen penjualan. |
| [routes/admin.php](../routes/admin.php) | `/admin` | `auth, role:admin` | Seluruh panel admin. |

`web.php` me-`require` keempat file lainnya di bagian akhir.

### Smart dashboard redirect

`GET /dashboard` (di `web.php`, grup `auth`) mengarahkan berdasarkan role/active_role:
`admin` → `admin.dashboard`, `seller` → `seller.dashboard`, selain itu → `buyer.dashboard`.

## Command terjadwal

- `php artisan orders:auto-complete` ([Console/Commands/AutoCompleteOrders](../app/Console/Commands/AutoCompleteOrders.php)): meng-`complete` order yang sudah `delivered_at` melewati `auto_complete_hours` (SystemSetting; `0` = nonaktif). Dihitung sejak `delivered_at`, bukan `shipped_at`. Lihat efek dana di [03-FLOWS.md](03-FLOWS.md#flow-wallet--payout).

[← Indeks](00-README.md) · [Lanjut: Peta Route →](02-ROUTES-MAP.md)
