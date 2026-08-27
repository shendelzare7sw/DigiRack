<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function downloadApp()
    {
        return view('public.pages.info', [
            'title' => 'Download App Digital Hook',
            'eyebrow' => 'Mobile App',
            'heading' => 'Aplikasi mobile Digital Hook sedang dalam pengembangan.',
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
                ['title' => 'Sementara ini gunakan website', 'body' => 'Digital Hook versi website sudah mendukung katalog, verifikasi KTP, checkout, wishlist, dan pelacakan pesanan. Simpan halaman ini di layar utama browser ponsel agar aksesnya terasa seperti aplikasi.'],
            ],
        ]);
    }

    public function about()
    {
        return view('public.pages.info', [
            'title' => 'Tentang Digital Hook',
            'eyebrow' => 'Tentang Kami',
            'heading' => 'Perangkat digital yang dekat, praktis, dan cepat sampai.',
            'description' => 'Digital Hook menyediakan komponen komputer, laptop second, periferal, kabel, dan aksesori digital untuk pembeli di Tangerang Raya.',
            'accent' => 'navy',
            'primaryAction' => ['label' => 'Jelajahi Produk', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Cek Wilayah Pengantaran', 'url' => route('pages.help')],
            'highlights' => [
                ['icon' => 'shield-check', 'title' => 'Pembeli Terverifikasi', 'body' => 'Verifikasi KTP membantu mengurangi transaksi fiktif dan melindungi operasional kurir toko.'],
                ['icon' => 'computer-desktop', 'title' => 'Kebutuhan Komputer', 'body' => 'Katalog berfokus pada komponen PC, laptop, periferal, jaringan rumahan, dan aksesori digital umum.'],
                ['icon' => 'truck', 'title' => 'Kurir Same-day', 'body' => 'Pesanan diantar langsung oleh kurir Digital Hook pada wilayah Tangerang Raya yang terjangkau.'],
            ],
            'sections' => [
                ['title' => 'Belanja lebih terarah', 'body' => 'Cari produk berdasarkan kategori, kondisi baru atau second, simpan wishlist, dan pantau pesanan dalam satu akun.'],
                ['title' => 'Cakupan lokal', 'body' => 'Alamat harus dipilih dari kota dan kecamatan yang tersedia. Jika wilayah tidak muncul, layanan pengantaran belum tersedia.'],
            ],
        ]);
    }

    public function b2b()
    {
        return view('public.pages.info', [
            'title' => 'Wilayah Pengantaran',
            'eyebrow' => 'Kurir Same-day',
            'heading' => 'Pengantaran lokal untuk Tangerang Raya.',
            'description' => 'Digital Hook hanya menerima alamat pada kota dan kecamatan yang tersedia agar pesanan dapat diantar langsung oleh kurir toko pada hari yang sama.',
            'accent' => 'green',
            'primaryAction' => ['label' => 'Jelajahi Katalog', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Kelola Alamat', 'url' => auth()->check() ? route('profile.edit').'#address-section' : route('register')],
            'highlights' => [
                ['icon' => 'map-pin', 'title' => 'Kota Tangerang', 'body' => 'Seluruh 13 kecamatan Kota Tangerang masuk cakupan awal.'],
                ['icon' => 'map-pin', 'title' => 'Tangerang Selatan', 'body' => 'Seluruh 7 kecamatan Kota Tangerang Selatan masuk cakupan awal.'],
                ['icon' => 'map-pin', 'title' => 'Kabupaten Tangerang', 'body' => 'Cakupan awal difokuskan pada kecamatan urban terdekat seperti Kelapa Dua, Curug, Pagedangan, Cisauk, Cikupa, dan sekitarnya.'],
            ],
            'sections' => [
                ['title' => 'Cara sistem menentukan jangkauan', 'body' => 'Saat menambah alamat, pembeli memilih provinsi, kota/kabupaten, lalu kecamatan. Kecamatan yang tidak tersedia berarti belum terjangkau.'],
                ['title' => 'Batas waktu same-day', 'body' => 'Pesanan sebelum pukul '.config('digitalhook.order_cutoff').' diprioritaskan untuk pengantaran hari yang sama. Kondisi operasional dan konfirmasi admin tetap berlaku.'],
            ],
        ]);
    }

    public function promos()
    {
        return view('public.pages.info', [
            'title' => 'Promo Spesial',
            'eyebrow' => 'Promo',
            'heading' => 'Temukan penawaran menarik untuk kebutuhan IT Anda.',
            'description' => 'Promo Digital Hook disiapkan untuk membantu pembeli mendapatkan perangkat pilihan dengan harga lebih menarik pada periode tertentu.',
            'accent' => 'red',
            'primaryAction' => ['label' => 'Lihat Produk Promo', 'url' => route('products.index')],
            'secondaryAction' => ['label' => 'Cek Flash Sale', 'url' => route('home') . '#kategori'],
            'highlights' => [
                ['icon' => 'fire', 'title' => 'Flash Sale', 'body' => 'Produk tertentu bisa masuk program diskon berbatas waktu saat promo aktif.'],
                ['icon' => 'tag', 'title' => 'Kategori Pilihan', 'body' => 'Promo dapat diarahkan untuk kategori populer seperti jaringan, kabel, wireless, server, dan tools.'],
                ['icon' => 'bell-alert', 'title' => 'Pantau Notifikasi', 'body' => 'Login untuk melihat notifikasi terkait pesanan dan pembaruan penting dari Digital Hook.'],
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
            'heading' => 'Panduan belanja, verifikasi, dan pemulihan akun.',
            'description' => 'Temukan arahan cepat untuk login, verifikasi KTP, alamat terjangkau, pesanan, pembayaran, dan pemulihan akun Digital Hook.',
            'accent' => 'gray',
            'primaryAction' => ['label' => 'Pemulihan Akun', 'url' => route('user.recovery.form')],
            'secondaryAction' => ['label' => 'Lihat Katalog', 'url' => route('products.index')],
            'highlights' => [
                ['icon' => 'key', 'title' => 'Akses Akun', 'body' => 'Gunakan fitur lupa sandi atau pemulihan akun jika email tidak tersedia atau reset gagal terkirim.'],
                ['icon' => 'shopping-bag', 'title' => 'Belanja & Pesanan', 'body' => 'Pembeli dapat melihat status pembayaran, pengiriman, pembatalan, dan konfirmasi pesanan dari dashboard buyer.'],
                ['icon' => 'identification', 'title' => 'Verifikasi KTP', 'body' => 'Pembeli wajib mengirim KTP dan menunggu persetujuan admin sebelum menggunakan checkout.'],
            ],
            'sections' => [
                ['title' => 'Masalah login', 'body' => 'Jika lupa password, gunakan halaman pemulihan akun. Jika email gagal atau tidak tersedia, tiket akan diteruskan ke Customer Service untuk verifikasi manual.'],
                ['title' => 'Masalah pesanan', 'body' => 'Cek halaman riwayat pesanan untuk melihat status pembayaran, pengiriman, permintaan batal, dan konfirmasi barang diterima.'],
                ['title' => 'Alamat tidak tersedia', 'body' => 'Digital Hook hanya melayani kecamatan yang muncul pada dropdown alamat. Wilayah di luar daftar belum dapat diproses oleh kurir same-day toko.'],
            ],
        ]);
    }

    public function privacy()
    {
        return view('public.pages.legal', [
            'title' => 'Kebijakan Privasi',
            'eyebrow' => 'Privasi',
            'heading' => 'Cara Digital Hook mengelola dan melindungi data pengguna.',
            'description' => 'Kebijakan ini menjelaskan data yang dikumpulkan, alasan pemrosesan, penggunaan data, dan pilihan pengguna saat memakai Digital Hook.',
            'updatedAt' => '17 Mei 2026',
            'sections' => [
                [
                    'title' => 'Data yang kami kumpulkan',
                    'items' => [
                        'Data akun seperti nama, email, nomor telepon, password terenkripsi, dan status verifikasi.',
                        'Data alamat pengiriman, kontak penerima, dan catatan pesanan yang diberikan saat checkout.',
                        'Data verifikasi pembeli berupa nama sesuai KTP, NIK, foto KTP, status pemeriksaan, dan catatan admin.',
                        'Data pembayaran seperti metode pembayaran, payment reference, status transaksi, dan informasi callback dari Midtrans. Digital Hook tidak menyimpan detail kartu pembayaran.',
                        'Data teknis seperti aktivitas login, notifikasi, session, dan log aplikasi yang dibutuhkan untuk keamanan dan pemecahan masalah.',
                    ],
                ],
                [
                    'title' => 'Penggunaan data',
                    'items' => [
                        'Membuat dan mengamankan akun pengguna, termasuk OTP pendaftaran dan verifikasi email.',
                        'Memproses checkout, pembayaran, invoice, pengiriman, pembatalan, dan konfirmasi penerimaan barang.',
                        'Memverifikasi identitas pembeli untuk membantu mencegah transaksi fiktif dan penyalahgunaan layanan kurir toko.',
                        'Mengirim notifikasi penting terkait akun, pesanan, pembayaran, pengiriman, pemulihan akun, dan aktivitas toko.',
                        'Meningkatkan keamanan, mencegah penyalahgunaan, dan memastikan operasional platform berjalan stabil.',
                    ],
                ],
                [
                    'title' => 'Berbagi data dengan pihak terkait',
                    'items' => [
                        'Data transaksi pembayaran diproses melalui Midtrans sesuai kebutuhan pembayaran dan callback status transaksi.',
                        'Data pengiriman dapat dilihat pembeli serta admin/kurir toko yang menangani pesanan terkait.',
                        'Data dapat dibagikan kepada penyedia layanan teknis, email, hosting, atau pihak yang membantu operasional platform dengan batas kebutuhan layanan.',
                        'Digital Hook dapat membuka data jika diwajibkan oleh hukum, permintaan otoritas yang sah, atau untuk melindungi hak dan keamanan platform.',
                    ],
                ],
                [
                    'title' => 'Hak pengguna dan keamanan',
                    'items' => [
                        'Pengguna dapat memperbarui profil, alamat, dan informasi akun melalui menu yang tersedia.',
                        'Permintaan pemulihan akun dapat diajukan melalui halaman pemulihan akun jika akses email atau password bermasalah.',
                        'Digital Hook menerapkan pembatasan akses berbasis role dan menyimpan file upload di storage aplikasi.',
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
            'heading' => 'Aturan penggunaan Digital Hook untuk pembeli dan admin toko.',
            'description' => 'Dengan menggunakan Digital Hook, pengguna dianggap memahami dan menyetujui aturan transaksi, pengiriman, pembayaran, serta pengelolaan akun di platform.',
            'updatedAt' => '17 Mei 2026',
            'sections' => [
                [
                    'title' => 'Akun dan akses',
                    'items' => [
                        'Pengguna wajib memberikan data yang benar saat registrasi dan menjaga kerahasiaan akses akun.',
                        'Registrasi membutuhkan verifikasi OTP email sebelum akun aktif sepenuhnya.',
                        'Pembeli wajib menyelesaikan verifikasi KTP dan menunggu persetujuan admin sebelum checkout.',
                        'Digital Hook dapat membatasi, menonaktifkan, atau meninjau akun jika ditemukan penyalahgunaan, data tidak valid, atau pelanggaran aturan.',
                    ],
                ],
                [
                    'title' => 'Produk dan katalog',
                    'items' => [
                        'Admin Digital Hook bertanggung jawab atas informasi produk, foto, harga, stok, spesifikasi, kondisi barang, dan kelayakan produk.',
                        'Katalog berfokus pada komponen komputer, laptop, periferal, jaringan rumahan, kabel, dan aksesori digital.',
                        'Kondisi barang baru atau second harus dicantumkan dengan jelas pada halaman produk.',
                    ],
                ],
                [
                    'title' => 'Pembayaran dan pesanan',
                    'items' => [
                        'Pembayaran diproses melalui Midtrans atau metode yang disediakan platform.',
                        'Status pembayaran mengikuti hasil sinkronisasi sistem dan callback pembayaran yang valid.',
                        'Pesanan yang sudah dibayar akan masuk ke proses admin toko, kecuali ada pembatalan sesuai alur yang tersedia.',
                        'Invoice dibuat otomatis berdasarkan data pesanan dan dapat digunakan sebagai dokumen transaksi di platform.',
                    ],
                ],
                [
                    'title' => 'Pengiriman, penerimaan, dan pencairan dana',
                    'items' => [
                        'Pengiriman hanya menggunakan Kurir Digital Hook pada wilayah kota/kecamatan yang tersedia.',
                        'Admin dan kurir toko bertanggung jawab memastikan barang sampai ke alamat pembeli.',
                        'Admin wajib mengunggah bukti foto ketika menandai paket sudah sampai di alamat.',
                        'Pembeli harus memeriksa barang sebelum menekan konfirmasi diterima karena konfirmasi akan memfinalisasi pesanan.',
                        'Jika buyer tidak melakukan konfirmasi setelah paket tercatat sampai melewati durasi auto-complete yang ditentukan admin, sistem dapat menyelesaikan pesanan otomatis.',
                    ],
                ],
                [
                    'title' => 'Pembatalan dan sengketa',
                    'items' => [
                        'Buyer dapat membatalkan pesanan sebelum pembayaran atau mengajukan pembatalan saat pesanan sudah diproses.',
                        'Pembatalan setelah pembayaran dapat memerlukan persetujuan admin toko sesuai status pesanan.',
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
                'Digital Hook' => [
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Tentang Digital Hook', 'url' => route('pages.about')],
                    ['label' => 'Download App', 'url' => route('pages.download-app')],
                    ['label' => 'Pusat Bantuan', 'url' => route('pages.help')],
                ],
                'Belanja' => [
                    ['label' => 'Katalog Produk', 'url' => route('products.index')],
                    ['label' => 'Promo Spesial', 'url' => route('pages.promos')],
                    ['label' => 'Dashboard Buyer', 'url' => auth()->check() ? route('buyer.dashboard') : route('login')],
                    ['label' => 'Riwayat Pesanan', 'url' => auth()->check() ? route('buyer.orders.index') : route('login')],
                ],
                'Layanan Lokal' => [
                    ['label' => 'Wilayah Pengantaran', 'url' => route('pages.b2b')],
                    ['label' => 'Verifikasi KTP', 'url' => auth()->check() ? route('profile.identity.edit') : route('login')],
                    ['label' => 'Kurir Same-day', 'url' => route('pages.help')],
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
