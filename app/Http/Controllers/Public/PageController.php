<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function downloadApp()
    {
        return view('public.pages.info', [
            'title' => 'Download App DigiRack',
            'eyebrow' => 'Mobile App',
            'heading' => 'Aplikasi mobile DigiRack sedang dalam pengembangan.',
            'description' => 'Kami sedang menyiapkan pengalaman belanja dan pengelolaan toko yang lebih ringkas melalui aplikasi Android dan iOS.',
            'accent' => 'blue',
            'primaryAction' => ['label' => 'Belanja via Website', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Kembali ke Beranda', 'url' => route('home')],
            'highlights' => [
                ['icon' => 'device-phone-mobile', 'title' => 'Coming Soon', 'body' => 'Aplikasi mobile belum tersedia untuk diunduh. Semua fitur utama tetap bisa digunakan dari website.'],
                ['icon' => 'bell', 'title' => 'Notifikasi Lebih Praktis', 'body' => 'Rencana pengembangan mencakup notifikasi pesanan, status pembayaran, dan aktivitas toko.'],
                ['icon' => 'shopping-cart', 'title' => 'Belanja Lebih Cepat', 'body' => 'Kami ingin membuat pencarian produk IT, checkout, dan tracking pesanan lebih nyaman di ponsel.'],
            ],
            'sections' => [
                ['title' => 'Sementara ini gunakan website', 'body' => 'DigiRack versi website sudah mendukung katalog produk, checkout, wishlist, order tracking, buka toko, dan dashboard seller. Simpan halaman DigiRack di layar utama browser ponsel Anda agar aksesnya terasa seperti aplikasi.'],
            ],
        ]);
    }

    public function about()
    {
        return view('public.pages.info', [
            'title' => 'Tentang DigiRack',
            'eyebrow' => 'Tentang Kami',
            'heading' => 'Marketplace untuk kebutuhan infrastruktur IT enterprise.',
            'description' => 'DigiRack membantu pembeli menemukan produk jaringan, server, rack, kabel, wireless, dan perangkat pendukung dari toko yang terkurasi.',
            'accent' => 'navy',
            'primaryAction' => ['label' => 'Jelajahi Produk', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Mulai Berjualan', 'url' => auth()->check() ? (auth()->user()->store ? route('seller.dashboard') : route('seller.register.form')) : route('register')],
            'highlights' => [
                ['icon' => 'shield-check', 'title' => 'Toko Terkurasi', 'body' => 'Seller melewati proses verifikasi agar transaksi lebih jelas dan dapat dipertanggungjawabkan.'],
                ['icon' => 'server-stack', 'title' => 'Fokus Infrastruktur IT', 'body' => 'Kategori diarahkan untuk kebutuhan jaringan, server, tools, wireless, dan perangkat pendukung enterprise.'],
                ['icon' => 'wallet', 'title' => 'Transaksi Terkendali', 'body' => 'Pembayaran, status pesanan, dan pencairan seller dibuat dalam alur yang mudah dilacak.'],
            ],
            'sections' => [
                ['title' => 'Untuk pembeli', 'body' => 'DigiRack memberi ruang pencarian produk yang lebih spesifik untuk kebutuhan teknis, dengan detail toko, kategori, wishlist, dan riwayat pesanan.'],
                ['title' => 'Untuk seller', 'body' => 'Seller dapat membuka toko, mengelola produk, kurir, pesanan, dan wallet dalam satu dashboard setelah toko lolos verifikasi admin.'],
            ],
        ]);
    }

    public function selling()
    {
        return view('public.pages.info', [
            'title' => 'Mulai Berjualan',
            'eyebrow' => 'Seller Center',
            'heading' => 'Buka toko IT Anda dan jangkau pembeli yang lebih relevan.',
            'description' => 'DigiRack dirancang untuk seller produk infrastruktur IT, mulai dari perangkat jaringan, server, kabel, tools, hingga aksesoris rack.',
            'accent' => 'orange',
            'primaryAction' => ['label' => 'Daftarkan Toko', 'url' => auth()->check() ? (auth()->user()->store ? route('seller.dashboard') : route('seller.register.form')) : route('register')],
            'secondaryAction' => ['label' => 'Lihat Syarat Toko', 'url' => '#syarat'],
            'highlights' => [
                ['icon' => 'identification', 'title' => 'Verifikasi Identitas', 'body' => 'Seller wajib upload ID card/KTP saat membuka toko agar proses approval lebih aman.'],
                ['icon' => 'cube', 'title' => 'Kelola Produk', 'body' => 'Setelah disetujui admin, seller bisa menambah produk, stok, foto, spesifikasi, dan status produk.'],
                ['icon' => 'banknotes', 'title' => 'Wallet Seller', 'body' => 'Dana pesanan dicairkan ke wallet seller sesuai status pesanan dan aturan platform.'],
            ],
            'sections' => [
                ['title' => 'Langkah membuka toko', 'body' => 'Buat akun, verifikasi email, isi profil toko, upload dokumen identitas, lalu tunggu approval admin. Selama menunggu, dashboard seller bisa dibuka tetapi menu operasional terkunci.'],
                ['title' => 'Syarat awal seller', 'body' => 'Gunakan data toko yang jelas, siapkan dokumen identitas, lengkapi informasi rekening, dan pastikan produk yang dijual sesuai kategori DigiRack.', 'id' => 'syarat'],
            ],
        ]);
    }

    public function b2b()
    {
        return view('public.pages.info', [
            'title' => 'Mitra B2B',
            'eyebrow' => 'Kemitraan',
            'heading' => 'Ruang kolaborasi untuk pengadaan dan distribusi IT.',
            'description' => 'Halaman ini disiapkan untuk kebutuhan organisasi, reseller, distributor, system integrator, dan tim procurement yang membutuhkan transaksi lebih terencana.',
            'accent' => 'green',
            'primaryAction' => ['label' => 'Jelajahi Katalog', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Buka Toko B2B', 'url' => auth()->check() ? (auth()->user()->store ? route('seller.dashboard') : route('seller.register.form')) : route('register')],
            'highlights' => [
                ['icon' => 'building-office-2', 'title' => 'Procurement', 'body' => 'Cocok untuk pencarian perangkat jaringan dan infrastruktur IT dalam kebutuhan kantor atau proyek.'],
                ['icon' => 'user-group', 'title' => 'Reseller & Integrator', 'body' => 'Mitra dapat memakai DigiRack sebagai kanal tambahan untuk menjangkau pembeli yang lebih teknis.'],
                ['icon' => 'clipboard-document-list', 'title' => 'Katalog Terstruktur', 'body' => 'Produk dikelompokkan berdasarkan kategori dan spesifikasi agar proses pembandingan lebih mudah.'],
            ],
            'sections' => [
                ['title' => 'Rekomendasi isi program B2B', 'body' => 'Ke depan, halaman ini bisa memuat permintaan quotation, harga grosir, kebutuhan proyek, verifikasi perusahaan, dan jalur komunikasi khusus untuk pembelian bernilai besar.'],
                ['title' => 'Status saat ini', 'body' => 'Fitur B2B khusus masih tahap pengembangan. Untuk sementara, pembeli dapat menggunakan katalog reguler dan seller dapat membuka toko melalui alur verifikasi yang tersedia.'],
            ],
        ]);
    }

    public function promos()
    {
        return view('public.pages.info', [
            'title' => 'Promo Spesial',
            'eyebrow' => 'Promo',
            'heading' => 'Temukan penawaran menarik untuk kebutuhan IT Anda.',
            'description' => 'Promo DigiRack disiapkan untuk membantu pembeli mendapatkan perangkat pilihan dengan harga lebih menarik pada periode tertentu.',
            'accent' => 'red',
            'primaryAction' => ['label' => 'Lihat Produk Promo', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Cek Flash Sale', 'url' => route('home') . '#kategori'],
            'highlights' => [
                ['icon' => 'fire', 'title' => 'Flash Sale', 'body' => 'Produk tertentu bisa masuk program diskon berbatas waktu saat promo aktif.'],
                ['icon' => 'tag', 'title' => 'Kategori Pilihan', 'body' => 'Promo dapat diarahkan untuk kategori populer seperti jaringan, kabel, wireless, server, dan tools.'],
                ['icon' => 'bell-alert', 'title' => 'Pantau Notifikasi', 'body' => 'Login untuk melihat notifikasi terkait pesanan dan pembaruan penting dari DigiRack.'],
            ],
            'sections' => [
                ['title' => 'Cara memanfaatkan promo', 'body' => 'Pantau halaman beranda dan katalog produk. Jika flash sale aktif, produk akan tampil dengan label dan harga promo sesuai periode yang ditentukan admin.'],
                ['title' => 'Catatan promo', 'body' => 'Ketersediaan promo bergantung pada stok, jadwal, dan kebijakan toko atau admin platform.'],
            ],
        ]);
    }

    public function help()
    {
        return view('public.pages.info', [
            'title' => 'Pusat Bantuan',
            'eyebrow' => 'Bantuan',
            'heading' => 'Panduan singkat untuk pembeli, seller, dan pemulihan akun.',
            'description' => 'Temukan arahan cepat untuk login, belanja, membuka toko, pesanan, pembayaran, dan pemulihan akun DigiRack.',
            'accent' => 'gray',
            'primaryAction' => ['label' => 'Pemulihan Akun', 'url' => route('user.recovery.form')],
            'secondaryAction' => ['label' => 'Lihat Katalog', 'url' => route('products.index')],
            'highlights' => [
                ['icon' => 'key', 'title' => 'Akses Akun', 'body' => 'Gunakan fitur lupa sandi atau pemulihan akun jika email tidak tersedia atau reset gagal terkirim.'],
                ['icon' => 'shopping-bag', 'title' => 'Belanja & Pesanan', 'body' => 'Pembeli dapat melihat status pembayaran, pengiriman, pembatalan, dan konfirmasi pesanan dari dashboard buyer.'],
                ['icon' => 'building-storefront', 'title' => 'Buka Toko', 'body' => 'Seller wajib verifikasi email dan menunggu approval admin sebelum menu operasional aktif.'],
            ],
            'sections' => [
                ['title' => 'Masalah login', 'body' => 'Jika lupa password, gunakan halaman pemulihan akun. Jika email gagal atau tidak tersedia, tiket akan diteruskan ke Customer Service untuk verifikasi manual.'],
                ['title' => 'Masalah pesanan', 'body' => 'Cek halaman riwayat pesanan untuk melihat status pembayaran, pengiriman, permintaan batal, dan konfirmasi barang diterima.'],
                ['title' => 'Masalah seller', 'body' => 'Pastikan email sudah terverifikasi, dokumen identitas sudah diunggah, dan toko sudah disetujui admin agar menu seller aktif.'],
            ],
        ]);
    }
}
