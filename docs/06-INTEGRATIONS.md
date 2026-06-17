# 06 — Integrasi Eksternal & Konfigurasi

[← Indeks](00-README.md)

Semua integrasi mengambil kredensial dari **`SystemSetting`** (dikelola admin via `admin.settings.*`) dengan **fallback ke `.env`**. Pola umum: `SystemSetting::val('key', env('KEY'))`.

---

## 1. Midtrans (pembayaran) — [MidtransService](../app/Services/MidtransService.php)

| Method | Fungsi |
|--------|--------|
| `boot()` | Set `Config` SDK dari setting; return `false` jika server key kosong. |
| `applyTransactionStatus($order, $status, $fraud)` | **Inti.** Map status Midtrans → order, lalu jalankan side-effect **tepat sekali** di tepi `unpaid → paid`: set `processing`, `stok--`, `sold_count++`, kirim `OrderNotification` ke seller & buyer. Pakai `lockForUpdate` agar aman dari race condition. |
| `syncByReference($ref)` | Query status ke Midtrans dan terapkan ke semua order ber-`payment_reference` sama. Dipakai sebagai **fallback** webhook. |
| `mapTransactionStatus()` (private) | `settlement`/`capture+accept`→`paid`; `cancel/deny/expire/failure`→`cancelled`; `pending`→`pending_payment`. |

**Pemanggil:**
- Webhook `POST /api/midtrans/callback` → [PaymentCallbackController@midtransCallback](../app/Http/Controllers/Api/PaymentCallbackController.php) (verifikasi **signature** `sha512(order_id+status_code+gross_amount+serverKey)`; route ini **CSRF-exempt**).
- Fallback saat buyer membuka order: [Buyer\OrderController@index/show](../app/Http/Controllers/Buyer/OrderController.php).
- Pembuatan Snap token saat checkout: [CheckoutController@process](../app/Http/Controllers/Buyer/CheckoutController.php#L291) (`Snap::getSnapToken`). `order_id` Midtrans = `payment_reference` order.

**Setting:** `midtrans_server_key`, `midtrans_is_production` (`'true'`/`'false'` string), (`midtrans_client_key`, `midtrans_merchant_id` di `.env`).

**Setup webhook:** arahkan Notification URL Midtrans ke `https://DOMAIN/api/midtrans/callback`.

---

## 2. Midtrans IRIS (payout) — [IrisService](../app/Services/IrisService.php)

| Method | Fungsi |
|--------|--------|
| `createPayout($data)` | POST `{baseUrl}/payouts` (Basic Auth approver key). Body: `beneficiary_name/account/bank`, `amount` (string), `notes`. Tanpa API key → **dummy success** (`DUMMY-IRIS-*`) untuk dev. |

- `baseUrl`: produksi `https://app.midtrans.com/iris/api/v1`, sandbox `https://app.sandbox.midtrans.com/iris/api/v1`.
- **Pemanggil:** [Admin\PayoutController@approve](../app/Http/Controllers/Admin/PayoutController.php#L20). Sukses → payout `completed` + simpan `iris_reference_no`.
- **Setting:** `midtrans_iris_api_key` (approver key), `midtrans_is_production`.

---

## 3. Ongkir — RajaOngkir / Komerce

Dua jalur (penting dipahami):

```mermaid
flowchart LR
    AJAX[AJAX /api/ongkir/calculate] --> OC[OngkirController] --> SM[ShippingManager]
    SM -->|legacy| LP[LegacyRajaOngkirProvider]
    SM -->|komerce| KP[KomerceShippingProvider]
    CK[CheckoutController@process] -->|inject langsung| RO[RajaOngkirService]
```

- **[ShippingManager](../app/Services/Shipping/ShippingManager.php)**: pilih provider via setting `shipping_provider_mode` (`legacy` default / `komerce`). Dipakai oleh **endpoint AJAX** [OngkirController@calculate](../app/Http/Controllers/Api/OngkirController.php) (`POST /api/ongkir/calculate`, kurir `jne/pos/tiki`).
- **[RajaOngkirService](../app/Services/RajaOngkirService.php)**: klien RajaOngkir starter (`/cost`). Tanpa API key atau saat gagal → **dummy flat Rp25.000**. Dipakai **langsung** (di-inject) oleh [CheckoutController@process](../app/Http/Controllers/Buyer/CheckoutController.php#L225) untuk menghitung ongkir final saat membuat order.
- **[ShippingProvider](../app/Services/Shipping/ShippingProvider.php)** (interface): `getCost($origin,$destination,$weight,$courier): array` — selalu mengembalikan struktur format RajaOngkir (`rajaongkir.results[0].costs[...]`).
- **Setting:** `rajaongkir_api_key`, `shipping_provider_mode`.

> Kurir internal toko (`StoreCourier`, kode `toko_*`) **tidak** lewat API ongkir — harganya tetap dari `store_couriers.price`.

---

## 4. Notifikasi (channel `database`) — [app/Notifications/](../app/Notifications/)

| Notification | Kapan dikirim |
|--------------|---------------|
| `OrderNotification` | Bayar sukses, dikirim, sampai, batal, retur, dana cair/payout, balas review (dipakai paling luas). |
| `StoreStatusNotification` | Pengajuan toko, identitas dikirim, verified/unverified/rejected, ban/restore. |
| `ReviewNotification` | Seller membalas review produk. |
| `CustomVerifyEmail`, `CustomResetPassword` | Email verifikasi & reset password kustom. |

Notifikasi tampil di dropdown navbar; dibaca via `notifications.read` / `notifications.markAllRead`.

---

## Ringkasan kredensial

| Setting key | Fallback env | Untuk |
|-------------|--------------|-------|
| `midtrans_server_key` | `MIDTRANS_SERVER_KEY` | Snap + verifikasi webhook |
| `midtrans_is_production` | `MIDTRANS_IS_PRODUCTION` | Mode prod/sandbox (string `'true'`) |
| `midtrans_iris_api_key` | `MIDTRANS_IRIS_API_KEY` | Payout IRIS |
| `rajaongkir_api_key` | `RAJAONGKIR_API_KEY` | Ongkir |
| `shipping_provider_mode` | — | `legacy` / `komerce` |

[← Indeks](00-README.md)
