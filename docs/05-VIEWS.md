# 05 — View, Layout, Komponen & Navigasi

[← Indeks](00-README.md)

Konvensi Blade: nama view `a.b.c` = file `resources/views/a/b/c.blade.php`. Pemetaan route→view ada di [02-ROUTES-MAP.md](02-ROUTES-MAP.md).

---

## Layout

| Layout | File | Dipakai untuk |
|--------|------|---------------|
| **app** | [layouts/app.blade.php](../resources/views/layouts/app.blade.php) | Semua halaman authenticated **dan** publik (buyer, seller, admin, guest browsing). Memuat navbar dinamis (`@include('layouts.navigation')`), footer, komponen `<x-toast>` & `<x-confirm-modal>`, dan interceptor AJAX global untuk cart/wishlist. |
| **guest** | [layouts/guest.blade.php](../resources/views/layouts/guest.blade.php) | Halaman auth (login, register, OTP, reset password, recovery). Kartu terpusat, **tanpa navbar**. |
| **print-layout** | [components/print-layout.blade.php](../resources/views/components/print-layout.blade.php) | Dokumen cetak: invoice, label kirim, laporan. Props: `title`, `subtitle`, `docLabel`, `backUrl`, `watermark`. |
| **navigation** | [layouts/navigation.blade.php](../resources/views/layouts/navigation.blade.php) | Partial navbar yang berubah sesuai auth & `active_role`. |

> Catatan: project memakai pola komponen layout Blade (`<x-app-layout>`/slot) maupun `@extends`. Saat mengedit halaman, ikuti pola yang sudah ada di file view tersebut.

---

## Peta view per folder

### `welcome.blade.php` (home)
Hero + 6 kategori aktif + carousel flash sale (countdown Alpine.js) + grid produk acak. Data dari closure `home` di [web.php](../routes/web.php#L16).

### `products/` (katalog publik)
| File | Halaman | Data |
|------|---------|------|
| `products/index` | Katalog + filter | produk paginate, kategori, `wishlistIds` |
| `products/show` | Detail produk | produk, galeri, review, produk terkait |
| `products/reviews` | Daftar ulasan produk | review + filter rating/media |
| `products/partials/filter-sidebar` | Sidebar filter (reusable) | kategori, range harga, rating, kondisi |

### `public/` (halaman publik lain)
`public/pages/info` (about/selling/b2b/help/download/promo), `public/pages/legal` (privacy/terms), `public/pages/sitemap`, `public/seller/storefront` (toko publik), `public/seller/reviews` (ulasan toko publik).

### `buyer/`
`buyer/dashboard`, `buyer/cart/index`, `buyer/wishlist/index`, `buyer/orders/{index,show,invoice}` (invoice pakai print-layout), `buyer/checkout/{index,payment,manual-transfer}`, `buyer/returns/create`, `buyer/reviews/edit`, `buyer/store-reviews/edit`.

### `seller/`
`seller/dashboard`, `seller/register`, `seller/identity`, `seller/store/index`, `seller/products/{index,create,edit,report}`, `seller/orders/{index,show,report}`, `seller/shipments/{index,label}`, `seller/returns/index`, `seller/wallet/index`, `seller/couriers/index`. (`report` & `label` pakai print-layout.)

### `admin/`
`admin/dashboard`, `admin/users/{index,show}`, `admin/stores/{index,show}`, `admin/products/{index,show}`, `admin/orders/{index,show,report}`, `admin/shipments/index`, `admin/returns/index`, `admin/categories/index`, `admin/banners/index`, `admin/flash-sales/index`, `admin/payouts/index`, `admin/transaction_fees/index`, `admin/recovery-tickets/index`, `admin/settings/index`.

### `auth/`
`login`, `register`, `verify-otp`, `verify-email`, `forgot-password`, `reset-password`, `confirm-password`, `user-recovery`, `admin-recovery`.

### `profile/`
`profile/edit` + partials: `update-profile-information-form`, `update-password-form`, `delete-user-form`, `address-management`, `address-modal`.

---

## Komponen Blade reusable ([components/](../resources/views/components/))

| Komponen | Kegunaan |
|----------|----------|
| `<x-product-card :product :wishlisted>` | Kartu produk (katalog, flash sale, rekomendasi, storefront). |
| `<x-star-rating :value>` | Bintang rating. |
| `<x-badge :color :size>` | Label status inline (navy/orange/green/red/yellow/gray). |
| `<x-toast>` | Notifikasi pojok; dipicu event JS `show-toast` (dipakai oleh interceptor AJAX). |
| `<x-confirm-modal>` | Dialog konfirmasi global; dipicu event `open-confirm-modal` (mis. logout, hapus). |
| `<x-modal :name>` | Modal generik. |
| `<x-empty-state>` | Placeholder saat data kosong. |
| `<x-breadcrumb :items>` | Jejak navigasi. |
| `<x-shipment-timeline :order>` | Timeline `ShipmentEvent` (detail order). |
| `<x-print-layout>` | Lihat tabel Layout. |
| `<x-icon name>` | SVG Heroicons by name. |
| `<x-dropdown>` | Menu dropdown (navbar). |
| Helper Breeze | `application-logo`, `auth-session-status`, `primary/secondary/danger-button`, `text-input`, `input-label`, `input-error`, `nav-link`, `responsive-nav-link`, `dropdown-link`. |

---

## Struktur menu navigasi (dari `navigation.blade.php`)

Navbar merender versi berbeda sesuai status auth & `active_role`:

- **Guest:** Logo, search, bell (prompt login), cart (→ login), tombol Login/Register; menu mobile berisi info DigiRack.
- **Buyer:** + notifikasi (8 terbaru), cart (badge), wishlist (badge, sembunyi di mobile), dropdown profil (Toko Saya → daftar/switch seller, Dashboard, Profil, Logout).
- **Seller** (`active_role=seller`): dropdown profil menampilkan **role switcher** (Pembeli/Seller). Dashboard seller berisi grid menu: Kelola Produk, Tambah Produk, Pesanan Masuk, Manajemen Pengiriman, Pengembalian, Saldo/Wallet, Profil Toko, Kelola Kurir, Edit Profil. Jika toko **belum verified**, menu terkunci + box status verifikasi (upload/kirim ulang identitas).
- **Admin** (`active_role=admin`): tanpa cart/wishlist. Dashboard admin berisi grid: Kategori, Banner, Flash Sale, Kelola Toko, Kelola User, Pesanan, Pengiriman, Pengembalian, Pembayaran Seller, Fee Transaksi, Ticket Recovery, Pengaturan Sistem, Lihat Katalog.

Data navbar (di-resolve di view): notifikasi `Auth::user()->unreadNotifications`, `$cartCount`, `$wishlistCount`, `$activeRole`, `$sellerEntryUrl` (= `switch.role seller` jika punya toko, else `seller.register.form`).

---

## Pola Alpine.js penting

- **Navbar**: `{ mobileMenuOpen, searchOpen, notifOpen }` — hamburger, toggle search mobile, dropdown notifikasi.
- **Cart & Wishlist**: aksi via **AJAX** (tanpa reload). Interceptor global di `app.blade.php` menampilkan `<x-toast>` dari respons JSON dan memperbarui badge.
- **Detail produk** (`products/show`): galeri + lightbox + qty + add-to-cart/wishlist async.
- **Welcome**: `countdownTimer()` untuk hitung mundur flash sale.
- **Checkout/Filter/Modal**: toggle state lokal (`x-data`) untuk pilih kurir, buka filter, buka modal alamat.

[← Indeks](00-README.md) · [Lanjut: Integrasi Eksternal →](06-INTEGRATIONS.md)
