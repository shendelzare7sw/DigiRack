# 02 — Peta Route → Controller → View → Data Flow

[← Indeks](00-README.md)

Dokumen inti untuk **lompat cepat**: temukan halaman/menu di tabel role yang sesuai, lalu lompat ke controller & view-nya. Kolom "Data mengalir ke" menjelaskan apa yang terjadi setelah request.

Singkatan: **C@m** = `Controller@method`. View `name` = path Blade (`a.b.c` = `resources/views/a/b/c.blade.php`). Banyak aksi POST/PUT/DELETE me-`redirect back` dengan flash message (toast) — ditulis "→ back".

---

## A. Publik (tanpa login) — [routes/web.php](../routes/web.php)

| Route name | HTTP URI | C@m | View / hasil | Data mengalir ke |
|------------|----------|-----|--------------|-----------------|
| `home` | GET `/` | Closure di web.php | `welcome` | Banner, kategori aktif, flash sale berlangsung, produk acak; `wishlistIds` jika login. |
| `pages.about` / `download-app` / `selling` / `b2b` / `promos` / `help` | GET `/tentang-digirack` dll | `Public\PageController@*` | `public.pages.info` | Konten statis per tipe halaman. |
| `pages.privacy` / `pages.terms` | GET `/kebijakan-privasi`, `/syarat-dan-ketentuan` | `PageController@privacy/terms` | `public.pages.legal` | Teks legal. |
| `pages.sitemap` | GET `/sitemap` | `PageController@sitemap` | `public.pages.sitemap` | Daftar tautan. |
| `products.index` | GET `/products` | `Public\ProductController@index` | `products.index` | Query produk aktif + filter (search/kategori/harga/rating/kondisi/sort), paginate 20; `wishlistIds`. |
| `products.show` | GET `/products/{slug}` | `Public\ProductController@show` | `products.show` | Produk by slug + review + produk terkait + status wishlist. |
| `products.reviews.index` | GET `/products/{slug}/reviews` | `Public\ProductController@reviews` | `products.reviews` | Review produk (filter rating/media). |
| `store.show` | GET `/toko/{slug}` | `Public\StoreController@show` | `public.seller.storefront` | Toko by slug (404 jika nonaktif & bukan admin), produk toko, 2 review. |
| `store.reviews.index` | GET `/toko/{slug}/reviews` | `Public\StoreController@reviews` | `public.seller.reviews` | Review toko + distribusi rating. |
| — | GET `/api/locations/provinces` | `Public\LocationController@getProvinces` | JSON | Dropdown alamat. |
| — | GET `/api/locations/cities/{province_id}` | `Public\LocationController@getCities` | JSON | Dropdown kota. |
| — | POST `/api/ongkir/calculate` | `Api\OngkirController@calculate` | JSON | Hitung ongkir via `ShippingManager` (lihat [06](06-INTEGRATIONS.md)). |
| — | POST `/api/midtrans/callback` | `Api\PaymentCallbackController@midtransCallback` | JSON | **Webhook Midtrans** (CSRF-exempt). Lihat [Flow Pembayaran](03-FLOWS.md#flow-checkout--pembayaran). |
| `user.recovery.form` / `.store` | GET/POST `/recovery` | `Auth\UserRecoveryController@showForm/store` | `auth.user-recovery` | Recovery akun buyer/seller via security Q/PIN → buat `RecoveryTicket`. |
| `admin.recovery.form` / `.unlock` / `.reset` | GET/POST `/admin-recovery*` | `Auth\AdminRecoveryController@*` | `auth.admin-recovery` | Recovery admin (hidden). |

---

## B. Auth & OTP — [routes/auth.php](../routes/auth.php) (+ verifikasi di auth.php)

| Route name | HTTP URI | C@m | View / hasil | Catatan |
|------------|----------|-----|--------------|---------|
| `register` | GET/POST `register` | `RegisteredUserController@create/store` | `auth.register` | POST throttle 3/menit → kirim OTP email, lalu ke notice OTP. |
| `register.otp.notice` | GET `register/otp` | `RegisteredUserController@showOtp` | `auth.verify-otp` | Tampilkan input OTP. |
| `register.otp.verify` | POST `register/otp` | `RegisteredUserController@verifyOtp` | redirect | Verifikasi OTP 6 digit (throttle 10/menit). |
| `register.otp.resend` | POST `register/otp/resend` | `RegisteredUserController@resendOtp` | → back | Resend dengan cooldown (throttle 5/menit). |
| `login` | GET/POST `login` | `AuthenticatedSessionController@create/store` | `auth.login` | Login set `session('active_role')`. |
| `password.request` / `.email` | GET/POST `forgot-password` | `PasswordResetLinkController@*` | `auth.forgot-password` | Kirim link reset. |
| `password.reset` / `.store` | GET/POST `reset-password` | `NewPasswordController@*` | `auth.reset-password` | Set password baru. |
| `verification.notice` / `.verify` / `.send` | GET/POST `verify-email*` | `Auth\EmailVerification*` | `auth.verify-email` | Verifikasi email (signed link). |
| `password.confirm` / `password.update` | GET/POST/PUT | `ConfirmablePasswordController`, `PasswordController` | `auth.confirm-password` | — |
| `logout` | POST `logout` | `AuthenticatedSessionController@destroy` | redirect home | — |

---

## C. Buyer — [routes/buyer.php](../routes/buyer.php) · prefix `/buyer` · `auth, verified, role:buyer,seller,admin, EnforceActiveBuyerRole`

> Admin diblokir dari rute ini; seller yang masuk otomatis `active_role=buyer`.

| Route name | HTTP URI | C@m | View / hasil | Data mengalir ke |
|------------|----------|-----|--------------|-----------------|
| `buyer.dashboard` | GET `/buyer/dashboard` | Closure | `buyer.dashboard` | Ringkasan buyer. |
| `buyer.cart.index` | GET `/buyer/cart` | `Buyer\CartController@index` | `buyer.cart.index` | Cart grouped per toko + total. |
| `buyer.cart.store` | POST `/buyer/cart` | `CartController@store` | → back/JSON | Tambah item (AJAX). |
| `buyer.cart.update` | PATCH `/buyer/cart/{id}` | `CartController@update` | → back/JSON | Ubah qty. |
| `buyer.cart.destroy` | DELETE `/buyer/cart/{id}` | `CartController@destroy` | → back/JSON | Hapus item. |
| `buyer.wishlist.index` | GET `/buyer/wishlist` | `Buyer\WishlistController@index` | `buyer.wishlist.index` | Produk wishlist. |
| `buyer.wishlist.toggle` | POST `/buyer/wishlist/toggle` | `WishlistController@toggle` | JSON | Toggle (AJAX, dipakai product-card). |
| `buyer.checkout.init` | POST `/buyer/checkout/init` | `Buyer\CheckoutController@init` | → `index()` | **Beli Langsung**: buat/update cart 1 item lalu render checkout. |
| `buyer.checkout.index` | POST `/buyer/checkout` | `CheckoutController@index` | `buyer.checkout.index` | Review item terpilih + alamat + opsi kurir + fee. Redirect ke profil jika belum ada alamat. |
| `buyer.checkout.process` | POST `/buyer/checkout/process` | `CheckoutController@process` | `buyer.checkout.payment` **atau** `buyer.checkout.manual-transfer` | Buat **Order per toko** (`pending_payment`), kosongkan cart, ambil Snap token. Lihat [Flow](03-FLOWS.md#flow-checkout--pembayaran). |
| `buyer.orders.upload-proof` | POST `/buyer/orders/{id}/upload-proof` | `CheckoutController@uploadProof` | → `buyer.orders.show` | Upload bukti transfer manual. |
| `buyer.orders.index` | GET `/buyer/orders` | `Buyer\OrderController@index` | `buyer.orders.index` | List order; **sinkron status Midtrans** untuk order unpaid. |
| `buyer.orders.show` | GET `/buyer/orders/{id}` | `OrderController@show` | `buyer.orders.show` | Detail + timeline; sinkron Midtrans jika unpaid. |
| `buyer.orders.invoice` | GET `/buyer/orders/{id}/invoice` | `OrderController@invoice` | `buyer.orders.invoice` (print-layout) | Invoice cetak. |
| `buyer.orders.confirm` | POST `/buyer/orders/{id}/confirm` | `OrderController@confirm` | → back | **Konfirmasi terima** → order `completed` + **kredit wallet seller**. |
| `buyer.orders.cancel` | POST `/buyer/orders/{id}/cancel` | `OrderController@cancel` | → back | Batal (jika `pending_payment` langsung `cancelled`; jika `processing` → `cancellation_requested`). |
| `buyer.returns.create` / `.store` | GET/POST `/buyer/orders/{id}/return` | `Buyer\ReturnController@create/store` | `buyer.returns.create` | Ajukan retur (syarat: sudah diterima & dalam window). |
| `buyer.reviews.edit` / `.store` | GET/POST `/buyer/reviews/{orderItem}` | `Buyer\ReviewController@edit/store` | `buyer.reviews.edit` | Review produk per item. |
| `buyer.store-reviews.edit` / `.store` | GET/POST `/buyer/store-reviews/{order}` | `Buyer\StoreReviewController@edit/store` | `buyer.store-reviews.edit` | Review toko per order. |

---

## D. Seller — [routes/seller.php](../routes/seller.php) · prefix `/seller`

### D.1 Onboarding — grup `auth, verified`

| Route name | HTTP URI | C@m | View / hasil | Catatan |
|------------|----------|-----|--------------|---------|
| `seller.register.form` / `.store` | GET/POST `/seller/register` | `SellerRegistrationController@showForm/register` | `seller.register` | Buat toko status `pending` (role belum berubah). |
| `seller.identity.form` / `.submit` | GET/POST `/seller/identity` | `SellerRegistrationController@showIdentityForm/submitIdentity` | `seller.identity` | (Re)upload dokumen identitas → antre verifikasi. |
| `seller.dashboard` | GET `/seller/dashboard` | Closure | `seller.dashboard` | Tanpa toko → redirect ke register. |

### D.2 Manajemen — grup `auth, verified, role:seller,admin, seller.approved`

| Route name | HTTP URI | C@m | View / hasil | Data mengalir ke |
|------------|----------|-----|--------------|-----------------|
| `seller.store.show` / `.update` | GET/POST `/seller/store` | `Seller\StoreProfileController@show/update` | `seller.store.index` | Profil toko (logo, banner, bank, ekspedisi). |
| `seller.products.index` | GET `/seller/products` | `Seller\ProductController@index` | `seller.products.index` | List produk toko + stats. |
| `seller.products.report` | GET `/seller/products/report` | `ProductController@report` | `seller.products.report` (print) | Laporan produk. |
| `seller.products.create` / `.store` | GET/POST `/seller/products/create` | `ProductController@create/store` | `seller.products.create` | Buat produk + upload gambar. |
| `seller.products.edit` / `.update` | GET/POST `/seller/products/{id}/edit` | `ProductController@edit/update` | `seller.products.edit` | Edit produk & galeri. |
| `seller.products.destroy` | DELETE `/seller/products/{id}` | `ProductController@destroy` | → back | Hapus produk. |
| `seller.orders.index` | GET `/seller/orders` | `Seller\OrderController@index` | `seller.orders.index` | Order masuk (filter status). |
| `seller.orders.show` | GET `/seller/orders/{id}` | `OrderController@show` | `seller.orders.show` | Detail order + aksi. |
| `seller.orders.report` | GET `/seller/orders/report` | `OrderController@report` | `seller.orders.report` (print) | Rekap omzet. |
| `seller.orders.status` | POST `/seller/orders/{id}/status` | `OrderController@updateStatus` | → back | Verifikasi bayar manual / set `shipped` (+resi) / `cancelled`. |
| `seller.orders.delivered` | POST `/seller/orders/{id}/delivered` | `OrderController@markDelivered` | → back | Tandai **sampai** + foto bukti → mulai timer auto-complete. |
| `seller.orders.cancellation` | POST `/seller/orders/{id}/cancellation` | `OrderController@resolveCancellation` | → back | Approve/Reject permintaan batal buyer. |
| `seller.orders.reviews.reply` | POST `/seller/orders/{id}/reviews/{review}/reply` | `OrderController@replyReview` | → back | Balas review produk. |
| `seller.store-reviews.reply` | POST `/seller/store-reviews/{storeReview}/reply` | `Seller\StoreReviewController@reply` | → back | Balas review toko. |
| `seller.shipments.index` | GET `/seller/shipments` | `Seller\ShipmentController@index` | `seller.shipments.index` | Tab: need_ship/shipped/delivered/completed. |
| `seller.shipments.bulk-ship` | POST `/seller/shipments/bulk-ship` | `ShipmentController@bulkShip` | → back | Kirim massal (input resi per order). |
| `seller.shipments.label` | GET `/seller/shipments/{id}/label` | `ShipmentController@label` | `seller.shipments.label` (print) | Cetak label. |
| `seller.shipments.tracking` | POST `/seller/shipments/{id}/tracking` | `ShipmentController@updateTracking` | → back | Koreksi resi (status `shipped`). |
| `seller.returns.index` | GET `/seller/returns` | `Seller\ReturnController@index` | `seller.returns.index` | Daftar pengajuan retur. |
| `seller.returns.review` | POST `/seller/returns/{id}/review` | `ReturnController@review` | → back | Approve (refund+restock) / Reject. |
| `seller.wallet.index` | GET `/seller/wallet` | `Seller\WalletController@index` | `seller.wallet.index` | Saldo + mutasi + riwayat payout. |
| `seller.wallet.payout` | POST `/seller/wallet/payout` | `WalletController@requestPayout` | → back | Ajukan pencairan (potong saldo, status `pending`). |
| `seller.couriers.index` | GET `/seller/couriers` | `Seller\CourierController@index` | `seller.couriers.index` | Kurir internal + ekspedisi reguler. |
| `seller.couriers.store` / `.toggle` / `.destroy` | POST/DELETE `/seller/couriers*` | `CourierController@store/toggleActive/destroy` | → back | CRUD kurir toko. |
| `seller.couriers.expeditions` | POST `/seller/couriers/expeditions` | `CourierController@updateExpeditions` | → back | Set ekspedisi reguler aktif (`enabled_expeditions`). |

---

## E. Admin — [routes/admin.php](../routes/admin.php) · prefix `/admin` · `auth, role:admin`

| Route name | HTTP URI | C@m | View / hasil | Data mengalir ke |
|------------|----------|-----|--------------|-----------------|
| `admin.dashboard` | GET `/admin/dashboard` | `Admin\DashboardController@index` | `admin.dashboard` | KPI + chart revenue. |
| `admin.users.index` / `.show` | GET `/admin/users{/id}` | `Admin\UserController@index/show` | `admin.users.index` / `.show` ⚠️ | Daftar & detail user. **`admin.users.show` belum ada filenya** (lihat catatan ⚠️). |
| `admin.users.toggle` | POST `/admin/users/{id}/toggle-active` | `UserController@toggleActive` | → back | Aktif/nonaktif user. |
| `admin.stores.index` / `.show` | GET `/admin/stores{/id}` | `Admin\StoreController@index/show` | `admin.stores.index` / `.show` | Daftar & detail toko + dokumen identitas. |
| `admin.stores.verify` | POST `/admin/stores/{id}/toggle-verify` | `StoreController@toggleVerification` | → back | **Verifikasi toko → upgrade role user ke `seller`.** |
| `admin.stores.reject` | POST `/admin/stores/{id}/reject` | `StoreController@reject` | → back | Tolak (role kembali `buyer`). |
| `admin.stores.toggle` | POST `/admin/stores/{id}/toggle-active` | `StoreController@toggleActive` | → back | Ban/pulihkan toko. |
| `admin.products.index` / `.show` | GET `/admin/products{/id}` | `Admin\ProductModerationController@index/show` | `admin.products.index` / `.show` ⚠️ | Moderasi produk. **`admin.products.show` belum ada filenya**. |
| `admin.products.toggle` | POST `/admin/products/{id}/toggle-status` | `ProductModerationController@toggleStatus` | → back | Aktif/nonaktif produk. |
| `admin.orders.index` / `.show` | GET `/admin/orders{/id}` | `Admin\OrderController@index/show` | `admin.orders.index` / `.show` ⚠️ | Monitor semua order. **`admin.orders.show` belum ada filenya**. |
| `admin.orders.report` | GET `/admin/orders/report` | `OrderController@report` | `admin.orders.report` (print) | Rekap global. |
| `admin.shipments.index` | GET `/admin/shipments` | `Admin\ShipmentController@index` | `admin.shipments.index` | Monitor pengiriman lintas toko. |
| `admin.shipments.tracking` / `.status` | POST `/admin/shipments/{id}/*` | `ShipmentController@overrideTracking/overrideStatus` | → back | Intervensi resi/status (catat `admin_override`). |
| `admin.returns.index` | GET `/admin/returns` | `Admin\ReturnController@index` | `admin.returns.index` | Semua retur. |
| `admin.returns.mediate` | POST `/admin/returns/{id}/mediate` | `ReturnController@mediate` | → back | **Mediasi final** (override seller). |
| `admin.categories.index` | GET `/admin/categories` | `Admin\CategoryController@index` | `admin.categories.index` | — |
| `admin.categories.store/.update/.toggle/.destroy` | POST/PUT/DELETE | `CategoryController@*` | → back | CRUD kategori. |
| `admin.banners.index` | GET `/admin/banners` | `Admin\BannerController@index` | `admin.banners.index` | — |
| `admin.banners.store/.update/.toggle/.destroy` | POST/DELETE | `BannerController@*` | → back | CRUD banner (upload gambar). |
| `admin.flash-sales.index` | GET `/admin/flash-sales` | `Admin\FlashSaleController@index` | `admin.flash-sales.index` | — |
| `admin.flash-sales.store/.update/.toggle/.destroy` | POST/PUT/DELETE | `FlashSaleController@*` | → back | CRUD flash sale. |
| `admin.payouts.index` | GET `/admin/payouts` | `Admin\PayoutController@index` | `admin.payouts.index` | Antrean pencairan. |
| `admin.payouts.approve` | POST `/admin/payouts/{id}/approve` | `PayoutController@approve` | → back | **Transfer via IRIS** → status `completed`. |
| `admin.payouts.reject` | POST `/admin/payouts/{id}/reject` | `PayoutController@reject` | → back | Tolak → kembalikan saldo wallet. |
| `admin.settings.index` / `.store` | GET/POST `/admin/settings` | `Admin\SettingController@index/store` | `admin.settings.index` | `SystemSetting` (Midtrans, ongkir, fee, auto-complete). |
| `admin.transaction_fees.index` | GET `/admin/transaction-fees` | `Admin\BuyerTransactionFeeController@index` | `admin.transaction_fees.index` | Fee pembeli. |
| `admin.transaction_fees.store/.toggle/.destroy` | POST/DELETE | `BuyerTransactionFeeController@*` | → back | CRUD fee. |
| `admin.recovery-tickets.index` | GET `/admin/recovery-tickets` | `Admin\RecoveryTicketController@index` | `admin.recovery-tickets.index` | Tiket recovery akun. |
| `admin.recovery-tickets.resend-reset/.resolve/.expire` | POST | `RecoveryTicketController@*` | → back | Tindak lanjut tiket. |

> ⚠️ **Catatan view hilang:** Controller `Admin\UserController@show`, `Admin\ProductModerationController@show`, dan `Admin\OrderController@show` memanggil `view('admin.users.show')`, `view('admin.products.show')`, `view('admin.orders.show')`, tetapi **file Blade-nya belum ada** di `resources/views/admin/...`. Membuka halaman detail tersebut akan memunculkan error "View not found". Jika perlu fitur detail, buat file view-nya (yang lain seperti `admin.stores.show` sudah ada sebagai acuan).

---

## F. Profil & lintas-role — [routes/web.php](../routes/web.php) (grup `auth`)

| Route name | HTTP URI | C@m | View / hasil | Catatan |
|------------|----------|-----|--------------|---------|
| `dashboard` | GET `/dashboard` | Closure | redirect | Smart redirect per role. |
| `profile.edit` / `.update` / `.destroy` | GET/PATCH/DELETE `/profile` | `ProfileController@edit/update/destroy` | `profile.edit` | Profil + alamat + password + hapus akun. |
| `profile.addresses.store/.update/.destroy/.set-primary` | POST/PUT/DELETE/PATCH `/profile/addresses*` | `ProfileAddressController@*` | → back | CRUD alamat. |
| `profile.phone.confirm` | GET `/profile/phone/confirm/{user}/{token}` | `ProfileController@confirmPhoneChange` | redirect | Konfirmasi ganti HP (signed). |
| `switch.role` | GET `/switch-role/{role}` | `RoleSwitchController@switchRole` | redirect | Ganti `active_role`. |
| `notifications.read` / `.markAllRead` | GET/POST `/notifications*` | `NotificationController@read/markAllRead` | redirect/JSON | Notifikasi database. |

[← Indeks](00-README.md) · [Lanjut: Alur Bisnis →](03-FLOWS.md)
