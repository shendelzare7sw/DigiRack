# Model dan Status

| Model | Fungsi penting |
|---|---|
| `User` | role `admin/buyer`, profil, relasi KTP dan alamat |
| `IdentityVerification` | data KTP privat, status review, reviewer, waktu pengajuan |
| `Store` | etalase tunggal Digital Hook (`digihook`) |
| `Address` | alamat pembeli, termasuk `province`, `city`, `district`, koordinat opsional |
| `Category`, `Product` | katalog komputer dan aksesori digital |
| `Cart`, `Wishlist` | pilihan buyer |
| `Order`, `OrderItem` | transaksi, snapshot harga/alamat/kurir, status pembayaran dan pemenuhan |
| `Review`, `StoreReview` | ulasan produk dan pelayanan toko |
| `ShipmentEvent` | histori pengiriman kurir toko |
| `SystemSetting` | Midtrans, kontak toko, dan durasi auto-complete |

Status KTP: `pending`, `verified`, `rejected`.

Status order utama: `pending_payment`, `processing`, `ready_to_ship`, `shipped`, `completed`, `cancelled`, `cancellation_requested`. Status pembayaran tetap disinkronkan oleh `MidtransService`.

Tabel wallet/payout warisan masih dapat ada untuk kompatibilitas data lama, tetapi tidak digunakan oleh alur atau rute aktif Digital Hook.
