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

    public function privacy()
    {
        return view('public.pages.legal', [
            'title' => 'Kebijakan Privasi',
            'eyebrow' => 'Privasi',
            'heading' => 'Cara DigiRack mengelola dan melindungi data pengguna.',
            'description' => 'Kebijakan ini menjelaskan data yang dikumpulkan, alasan pemrosesan, penggunaan data, dan pilihan pengguna saat memakai DigiRack.',
            'updatedAt' => '17 Mei 2026',
            'sections' => [
                [
                    'title' => 'Data yang kami kumpulkan',
                    'items' => [
                        'Data akun seperti nama, email, nomor telepon, password terenkripsi, dan status verifikasi.',
                        'Data alamat pengiriman, kontak penerima, dan catatan pesanan yang diberikan saat checkout.',
                        'Data toko seller seperti profil toko, dokumen identitas untuk verifikasi, kurir, rekening, produk, stok, dan riwayat transaksi.',
                        'Data pembayaran seperti metode pembayaran, payment reference, status transaksi, dan informasi callback dari Midtrans. DigiRack tidak menyimpan detail kartu pembayaran.',
                        'Data teknis seperti aktivitas login, notifikasi, session, dan log aplikasi yang dibutuhkan untuk keamanan dan pemecahan masalah.',
                    ],
                ],
                [
                    'title' => 'Penggunaan data',
                    'items' => [
                        'Membuat dan mengamankan akun pengguna, termasuk OTP pendaftaran dan verifikasi email.',
                        'Memproses checkout, pembayaran, invoice, pengiriman, pembatalan, konfirmasi penerimaan barang, wallet seller, dan payout.',
                        'Memverifikasi seller dan menjaga kualitas toko serta produk yang tampil di marketplace.',
                        'Mengirim notifikasi penting terkait akun, pesanan, pembayaran, pengiriman, pemulihan akun, dan aktivitas toko.',
                        'Meningkatkan keamanan, mencegah penyalahgunaan, dan memastikan operasional platform berjalan stabil.',
                    ],
                ],
                [
                    'title' => 'Berbagi data dengan pihak terkait',
                    'items' => [
                        'Data transaksi pembayaran diproses melalui Midtrans sesuai kebutuhan pembayaran dan callback status transaksi.',
                        'Data pengiriman dapat terlihat oleh seller dan buyer yang terlibat dalam pesanan terkait.',
                        'Data dapat dibagikan kepada penyedia layanan teknis, email, hosting, atau pihak yang membantu operasional platform dengan batas kebutuhan layanan.',
                        'DigiRack dapat membuka data jika diwajibkan oleh hukum, permintaan otoritas yang sah, atau untuk melindungi hak dan keamanan platform.',
                    ],
                ],
                [
                    'title' => 'Hak pengguna dan keamanan',
                    'items' => [
                        'Pengguna dapat memperbarui profil, alamat, dan informasi akun melalui menu yang tersedia.',
                        'Permintaan pemulihan akun dapat diajukan melalui halaman pemulihan akun jika akses email atau password bermasalah.',
                        'DigiRack menerapkan pembatasan akses berbasis role dan menyimpan file upload di storage aplikasi.',
                        'Gunakan password yang kuat, jaga akses email, dan segera hubungi pengelola jika ada aktivitas mencurigakan.',
                    ],
                ],
            ],
        ]);
    }

    public function terms()
    {
        return view('public.pages.legal', [
            'title' => 'Syarat & Ketentuan',
            'eyebrow' => 'Ketentuan Layanan',
            'heading' => 'Aturan penggunaan DigiRack untuk buyer, seller, dan admin platform.',
            'description' => 'Dengan menggunakan DigiRack, pengguna dianggap memahami dan menyetujui aturan transaksi, pengiriman, pembayaran, serta pengelolaan akun di platform.',
            'updatedAt' => '17 Mei 2026',
            'sections' => [
                [
                    'title' => 'Akun dan akses',
                    'items' => [
                        'Pengguna wajib memberikan data yang benar saat registrasi dan menjaga kerahasiaan akses akun.',
                        'Registrasi membutuhkan verifikasi OTP email sebelum akun aktif sepenuhnya.',
                        'Seller wajib melengkapi data toko dan dokumen identitas, lalu menunggu persetujuan admin sebelum berjualan.',
                        'DigiRack dapat membatasi, menonaktifkan, atau meninjau akun jika ditemukan penyalahgunaan, data tidak valid, atau pelanggaran aturan.',
                    ],
                ],
                [
                    'title' => 'Produk dan toko',
                    'items' => [
                        'Seller bertanggung jawab atas akurasi nama produk, foto, harga, stok, spesifikasi, kondisi barang, dan kelayakan produk.',
                        'Produk harus relevan dengan kategori infrastruktur IT, jaringan, server, perangkat enterprise, atau kategori lain yang tersedia di DigiRack.',
                        'Admin dapat menonaktifkan produk atau toko yang melanggar aturan, menyesatkan, ilegal, atau berisiko merugikan pengguna.',
                        'Seller tidak diperbolehkan membeli produk milik toko sendiri melalui akun yang sama.',
                    ],
                ],
                [
                    'title' => 'Pembayaran dan pesanan',
                    'items' => [
                        'Pembayaran diproses melalui Midtrans atau metode yang disediakan platform.',
                        'Status pembayaran mengikuti hasil sinkronisasi sistem dan callback pembayaran yang valid.',
                        'Pesanan yang sudah dibayar akan masuk ke proses seller, kecuali ada pembatalan sesuai alur yang tersedia.',
                        'Invoice dibuat otomatis berdasarkan data pesanan dan dapat digunakan sebagai dokumen transaksi di platform.',
                    ],
                ],
                [
                    'title' => 'Pengiriman, penerimaan, dan pencairan dana',
                    'items' => [
                        'Seller wajib mengirim barang sesuai pesanan dan mengisi resi untuk kurir reguler jika tersedia.',
                        'Untuk kurir toko, seller tetap bertanggung jawab memastikan barang sampai ke alamat buyer.',
                        'Seller wajib mengunggah bukti foto ketika menandai paket sudah sampai di alamat.',
                        'Buyer harus memeriksa barang sebelum menekan konfirmasi diterima karena konfirmasi akan memfinalisasi pesanan dan mencairkan dana ke seller.',
                        'Jika buyer tidak melakukan konfirmasi setelah paket tercatat sampai melewati durasi auto-complete yang ditentukan admin, sistem dapat menyelesaikan pesanan otomatis.',
                    ],
                ],
                [
                    'title' => 'Pembatalan dan sengketa',
                    'items' => [
                        'Buyer dapat membatalkan pesanan sebelum pembayaran atau mengajukan pembatalan saat pesanan sudah diproses.',
                        'Pembatalan setelah pembayaran dapat memerlukan persetujuan seller sesuai status pesanan.',
                        'Jika ada kendala barang, pembayaran, atau pengiriman, pengguna dianjurkan menyimpan bukti transaksi dan menghubungi pengelola platform.',
                    ],
                ],
            ],
        ]);
    }

    public function sitemap()
    {
        return view('public.pages.sitemap', [
            'groups' => [
                'DigiRack' => [
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Tentang DigiRack', 'url' => route('pages.about')],
                    ['label' => 'Download App', 'url' => route('pages.download-app')],
                    ['label' => 'Pusat Bantuan', 'url' => route('pages.help')],
                ],
                'Belanja' => [
                    ['label' => 'Katalog Produk', 'url' => route('products.index')],
                    ['label' => 'Promo Spesial', 'url' => route('pages.promos')],
                    ['label' => 'Dashboard Buyer', 'url' => auth()->check() ? route('buyer.dashboard') : route('login')],
                    ['label' => 'Riwayat Pesanan', 'url' => auth()->check() ? route('buyer.orders.index') : route('login')],
                ],
                'Seller' => [
                    ['label' => 'Mulai Berjualan', 'url' => route('pages.selling')],
                    ['label' => 'Mitra B2B', 'url' => route('pages.b2b')],
                    ['label' => 'Dashboard Seller', 'url' => auth()->check() ? route('seller.dashboard') : route('login')],
                ],
                'Akun dan Legal' => [
                    ['label' => 'Login', 'url' => route('login')],
                    ['label' => 'Registrasi', 'url' => route('register')],
                    ['label' => 'Pemulihan Akun', 'url' => route('user.recovery.form')],
                    ['label' => 'Kebijakan Privasi', 'url' => route('pages.privacy')],
                    ['label' => 'Syarat & Ketentuan', 'url' => route('pages.terms')],
                ],
            ],
        ]);
    }
}
