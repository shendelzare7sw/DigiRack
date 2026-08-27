# Peta Rute

| Area | Rute utama | Controller/view |
|---|---|---|
| Publik | `/`, `/products`, `/products/{slug}`, `/toko/{slug}` | `Public\*`, katalog dan etalase Digital Hook |
| Informasi | `/tentang-digital-hook`, `/wilayah-pengantaran`, `/pusat-bantuan` | `Public\PageController`, `public.pages.info` |
| Lokasi | `/api/locations/provinces`, `/cities/{province}`, `/districts/{city}` | `Public\LocationController` |
| KTP pembeli | `/profile/verifikasi-ktp`, `/profile/dokumen-identitas/{verification}` | `IdentityVerificationController`, `IdentityDocumentController` |
| Alamat | `/profile/addresses*` | `ProfileAddressController` |
| Pembelian | `/buyer/cart`, `/buyer/checkout*`, `/buyer/orders*` | `Buyer\CartController`, `CheckoutController`, `OrderController` |
| Admin toko | `/admin/store`, `/admin/products*`, `/admin/orders*` | implementasi `Seller\StoreProfileController`, `ProductController`, `OrderController` |
| Admin pengguna | `/admin/users*`, termasuk approve/reject KTP | `Admin\UserController` |
| Pembayaran | `/api/midtrans/callback` | `Api\PaymentCallbackController` |

Daftar final selalu dapat diperiksa dengan `php artisan route:list`.
