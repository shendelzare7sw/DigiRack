# DigiRack — Dokumentasi untuk AI Agent

> Dokumentasi ini ditulis agar **AI agent mana pun** (Claude Code, Cursor, Copilot, dll.) maupun developer baru bisa memahami project DigiRack dengan cepat tanpa harus scan ulang seluruh codebase. Sumber kebenaran kanonik ada di folder `docs/` ini.

DigiRack adalah **marketplace multi-seller** (Laravel 11 + Blade + Alpine.js + Tailwind v3) untuk produk infrastruktur IT/jaringan/server. Satu aplikasi melayani tiga peran: **admin**, **seller**, **buyer**.

---

## Cara pakai dokumen ini (alur lompat cepat)

Saat ada masalah/perubahan di satu halaman, ikuti urutan ini:

1. **Tahu nama halaman/menu?** → buka **[02-ROUTES-MAP.md](02-ROUTES-MAP.md)**, cari di tabel role yang sesuai. Tiap baris memberi: `route name` → `URI` → `Controller@method` → `View` → "data mengalir ke mana".
2. **Perlu paham proses bisnisnya** (checkout, bayar, kirim, retur, payout)? → buka **[03-FLOWS.md](03-FLOWS.md)**. Ada diagram state-machine + controller pemicu tiap transisi status.
3. **Perlu paham struktur data / status enum**? → buka **[04-MODELS.md](04-MODELS.md)**.
4. **Perlu paham tampilan / komponen / menu navigasi**? → buka **[05-VIEWS.md](05-VIEWS.md)**.
5. **Menyentuh pembayaran / payout / ongkir**? → buka **[06-INTEGRATIONS.md](06-INTEGRATIONS.md)**.
6. **Belum paham role / middleware / cara routing dipecah**? → buka **[01-ARCHITECTURE.md](01-ARCHITECTURE.md)**.

> Contoh: "Halaman checkout error." → 02 cari `buyer.checkout.*` → ketemu `Buyer\CheckoutController@process` render `buyer.checkout.payment` → 03 baca **Flow Checkout & Pembayaran** untuk paham urutan create order + Snap token.

---

## Peta file dokumentasi

| File | Isi singkat |
|------|-------------|
| [01-ARCHITECTURE.md](01-ARCHITECTURE.md) | Stack & versi, struktur folder, **sistem 3 role + `active_role`**, middleware kustom (`role`, `seller.approved`, `EnforceActiveBuyerRole`), pembagian file route, `bootstrap/app.php`. |
| [02-ROUTES-MAP.md](02-ROUTES-MAP.md) | **Inti.** Tabel `route → controller → view → data flow` untuk Publik, Auth, Buyer, Seller, Admin, Profile, API/webhook. |
| [03-FLOWS.md](03-FLOWS.md) | State-machine + diagram **Mermaid**: checkout/bayar, lifecycle order, pengiriman, retur, wallet & payout, onboarding seller. |
| [04-MODELS.md](04-MODELS.md) | 25 model: relasi Eloquent, field penting, `$casts`, dan **nilai enum status** (dengan rujukan migration). |
| [05-VIEWS.md](05-VIEWS.md) | Layout, peta view per role, komponen Blade reusable, struktur menu navigasi, pola Alpine.js. |
| [06-INTEGRATIONS.md](06-INTEGRATIONS.md) | Integrasi eksternal: Midtrans (bayar), Midtrans IRIS (payout), RajaOngkir/Komerce (ongkir), `SystemSetting` config. |

Pintu masuk netral untuk agent ada di root: **[../AGENTS.md](../AGENTS.md)**. Setup/instalasi/deploy ada di **[../README.md](../README.md)**.

---

## Ringkasan 30 detik

- **Role disimpan sebagai string** di `users.role` (`admin` / `seller` / `buyer`) — **bukan** Spatie permission. Konteks aktif disimpan di `session('active_role')`.
- Satu user bisa jadi buyer **dan** seller: role di-upgrade ke `seller` **otomatis saat admin memverifikasi toko** ([Admin\StoreController@toggleVerification](../app/Http/Controllers/Admin/StoreController.php#L50)).
- Route dipecah per role: [routes/web.php](../routes/web.php) (publik+profil), [auth.php](../routes/auth.php), [buyer.php](../routes/buyer.php), [seller.php](../routes/seller.php), [admin.php](../routes/admin.php).
- Pembayaran lewat **Midtrans Snap**; status order disinkron via webhook `/api/midtrans/callback` **dan** fallback server-to-server saat buyer membuka halaman order.
- Dana seller masuk **Wallet** saat order `completed`, lalu dicairkan via **payout** (admin approve → Midtrans IRIS).
- Pengiriman **tanpa API tracking**: resi diinput manual; ada juga kurir internal toko (`toko_*`). Histori tersimpan di `ShipmentEvent`.

> **Catatan akurasi:** Dokumen ini mencerminkan kode pada commit branch `feature/shipping-management` (Mei 2026). Jika nama method/route/kolom yang disebut di sini tidak lagi cocok dengan kode, **percayai kode**, lalu perbarui dokumen ini.
