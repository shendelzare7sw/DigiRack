# Model dan Status

| Model | Fungsi penting |
|---|---|
| `User` | role `admin/buyer`, profil, relasi KTP dan alamat |
| `IdentityVerification` | data KTP privat, status review, reviewer, waktu pengajuan |
| `Store` | identitas internal tunggal Digital Hook (`digihook`) dan pemilik relasi produk/order; bukan storefront publik |
| `Address` | alamat pembeli, termasuk `province`, `city`, `district`, koordinat opsional |
| `Category`, `Product` | katalog komputer dan aksesori digital |
| `Cart`, `Wishlist` | pilihan buyer |
| `Order`, `OrderItem` | transaksi, snapshot harga/alamat/kurir, status pembayaran dan pemenuhan |
| `Review` | ulasan produk dari pembeli |
| `ShipmentEvent` | histori pengiriman kurir toko |
| `SystemSetting` | Midtrans, kontak toko, dan durasi auto-complete |

Status KTP: `pending`, `verified`, `rejected`.

Status order utama: `pending_payment`, `processing`, `ready_to_ship`, `shipped`, `completed`, `cancelled`, `cancellation_requested`. Status pembayaran tetap disinkronkan oleh `MidtransService`.

Tidak ada status verifikasi/ban toko, ulasan toko, wallet seller, payout, atau kurir per toko. Moderasi hanya berlaku untuk akun buyer dan verifikasi KTP buyer.
