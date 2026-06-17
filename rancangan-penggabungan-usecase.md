# Rancangan Penggabungan Use Case per Role

> **Tujuan dokumen ini**: Agar Anda bisa merevisi dan memutuskan apakah penggabungan menu-menu berikut ke dalam satu balon Use Case sudah tepat atau perlu dipecah kembali.
>
> **Aturan baca**:
> - ✅ = Menu sidebar yang digabung menjadi satu Use Case
> - Nama **Use Case** dicetak tebal
> - Menu asli sidebar ditulis persis seperti yang tertulis di kode sidebar

---

## 1. ADMIN (Level 1)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 1 | **Kelola Data Pengguna** | ✅ Manajemen User → Tenaga Pendidik |
|   |   | ✅ Manajemen User → Siswa |
|   |   | ✅ Manajemen User → Wali Murid |
| 2 | **Kelola Tiket Pemulihan Akun** | ✅ Tiket Pemulihan Akun *(tetap sendiri, alur Activity-nya unik)* |
| 3 | **Kelola Pengaturan Sistem** | ✅ Pengaturan LMS |
|   |   | ✅ Pengaturan AI |
| 4 | **Kelola Tahun Ajaran** | ✅ Tahun Ajaran *(tetap sendiri)* |
| 5 | **Kelola Data Cabang** | ✅ Manajemen Cabang *(tetap sendiri, Admin-only)* |
| 6 | **Kelola Data Kelas & Penugasan** | ✅ Data Kelas |
|   |   | ✅ Data Wali Kelas |
|   |   | ✅ Data Guru Pengajar |
|   |   | ✅ Manajemen Siswa *(penempatan siswa ke kelas)* |
| 7 | **Kelola Mata Pelajaran** | ✅ Mata Pelajaran *(tetap sendiri)* |
| 8 | **Kelola Jadwal Pelajaran** | ✅ Jadwal Pelajaran *(tetap sendiri)* |
| 9 | **Kelola Landing Page** | ✅ Landing Page *(tetap sendiri, Admin-only)* |
| 10 | **Kelola Konten Publikasi** | ✅ Kalender Akademik |
|   |   | ✅ Pengumuman |
|   |   | ✅ Flyer / Iklan |
|   |   | ✅ Kelola Berita |
| 11 | **Kelola Tagihan & Pembayaran** | ✅ Tagihan |
|   |   | ✅ Tarik Tunggakan |
|   |   | ✅ Pembayaran |
|   |   | ✅ Config Pembayaran |
| 12 | **Lihat Laporan Keuangan** | ✅ Laporan Keuangan *(tetap sendiri)* |
| 13 | **Validasi Akses Ujian dan Rapor** | ✅ Validasi Ujian & Rapor *(tetap sendiri)* |
| 14 | **Memproses Dispensasi Keuangan** | ✅ *(diajukan dari sini, diputuskan oleh Ketua)* |
| 15 | **Memproses Dispensasi Kenaikan Kelas** | ✅ Validasi Dispensasi *(diajukan dari sini, diputuskan oleh Ketua)* |
| 16 | **Kelola Pengaturan Kenaikan Kelas** | ✅ Pengaturan KKM |
|   |   | ✅ Pengaturan Kenaikan |
| 17 | **Proses Kenaikan Kelas** | ✅ Proses & Rekap *(tetap sendiri, eksekusi promosi)* |
| 18 | **Monitoring Sistem** | ✅ Monitoring → Pengguna |
|   |   | ✅ Monitoring → Wali Kelas |
|   |   | ✅ Monitoring → Guru Pengajar |
|   |   | ✅ Monitoring → Siswa |
|   |   | ✅ Monitoring → Monitoring LMS |
| 19 | **Kelola Laporan & Catatan** | ✅ Laporan |
|   |   | ✅ Catatan |

**Total Use Case Admin: 19** *(dari 36 menu asli)*

---

## 2. KETUA PKBM (Level 2)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 15 | **Memproses Dispensasi Kenaikan Kelas** | ✅ Approval Dispensasi *(Ketua menyetujui/menolak)* |
| 20 | **Validasi Rapor Tingkat Akhir** | ✅ Validasi Rapor *(tetap sendiri, alur unik: ACC/Revisi)* |
| 14 | **Memproses Dispensasi Keuangan** | ✅ Dispensasi Keuangan *(tetap sendiri)* |
| 18 | **Monitoring Sistem** | ✅ Data Pengguna |
|   |   | ✅ Data Wali Kelas |
|   |   | ✅ Data Guru Pengajar |
|   |   | ✅ Data Siswa |
|   |   | ✅ Monitoring LMS |
| 19 | **Kelola Laporan & Catatan** | ✅ Cetak Laporan |
|   |   | ✅ Kirim Catatan |

**Total Use Case Ketua: 5** *(dari 10 menu asli)*

---

## 3. WAKIL KEPALA SEKOLAH / WAKA (Level 2)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 4 | **Kelola Tahun Ajaran** | ✅ Tahun Ajaran |
| 6 | **Kelola Data Kelas & Penugasan** | ✅ Data Kelas |
|   |   | ✅ Data Wali Kelas |
|   |   | ✅ Data Guru Pengajar |
|   |   | ✅ Manajemen Siswa |
| 7 | **Kelola Mata Pelajaran** | ✅ Mata Pelajaran |
| 8 | **Kelola Jadwal Pelajaran** | ✅ Jadwal Pelajaran |
| 16 | **Kelola Pengaturan Kenaikan Kelas** | ✅ Pengaturan KKM |
|   |   | ✅ Pengaturan Kenaikan |
| 17 | **Proses Kenaikan Kelas** | ✅ Proses & Rekap |
| 18 | **Monitoring Sistem** | ✅ Monitoring Wali Kelas |
|   |   | ✅ Monitoring Guru Pengajar |
|   |   | ✅ Monitoring Siswa |
|   |   | ✅ Monitoring LMS |
| 19 | **Kelola Laporan & Catatan** | ✅ Catatan |

**Total Use Case Waka: 8** *(dari 15 menu asli)*

---

## 4. BENDAHARA (Level 2)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 11 | **Kelola Tagihan & Pembayaran** | ✅ Kelola Tagihan |
|   |   | ✅ Tarik Tunggakan |
|   |   | ✅ Kelola Pembayaran |
|   |   | ✅ Config Pembayaran |
| 13 | **Validasi Akses Ujian dan Rapor** | ✅ Validasi Ujian & Rapor |
| 15 | **Memproses Dispensasi Kenaikan Kelas** | ✅ Validasi Dispensasi *(Bendahara mengajukan)* |
| 14 | **Memproses Dispensasi Keuangan** | ✅ *(diajukan dari halaman Validasi Akses)* |
| 12 | **Lihat Laporan Keuangan** | ✅ Laporan Pembayaran |
|   |   | ✅ Rekap Tagihan |
|   |   | ✅ Siswa Belum Lunas |

**Total Use Case Bendahara: 5** *(dari 9 menu asli)*

---

## 5. SEKRETARIS (Level 3)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 10 | **Kelola Konten Publikasi** | ✅ Kalender Akademik |
|   |   | ✅ Pengumuman |
|   |   | ✅ Flyer / Iklan |
|   |   | ✅ Kelola Berita |

**Total Use Case Sekretaris: 1** *(dari 4 menu asli)*

---

## 6. WALI KELAS (Level 4)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 21 | **Kelola Presensi Siswa** | ✅ Presensi → Input Harian |
|   |   | ✅ Presensi → Validasi Izin |
|   |   | ✅ Presensi → Rekap Harian |
|   |   | ✅ Presensi → Riwayat & Edit |
| 22 | **Kelola Rapor** | ✅ Nilai Siswa *(rekap nilai untuk rapor)* |
|   |   | ✅ Kelola Rapor *(generate, kirim validasi, terbitkan)* |
|   |   | ✅ Arsip Kelas Saya |
| 23 | **Mengelola Permintaan Unduh Rapor** | ✅ Permintaan Unduh *(tetap sendiri, alur approve/reject)* |
| 20 | **Validasi Rapor Tingkat Akhir** | *(Wali tidak punya UC ini — ini milik Ketua)* |
| 24 | **Melihat Prediksi Kenaikan Kelas** | ✅ Prediksi Kenaikan *(tetap sendiri, read-only)* |
| 13 | **Validasi Akses Ujian dan Rapor** | ✅ Validasi Akses *(shared dengan Admin & Bendahara)* |
| 25 | **Melihat Informasi Akademik** | ✅ Jadwal Pelajaran *(read-only)* |
| 26 | **Membaca Catatan Monitoring** | ✅ *(tidak ada di sidebar, muncul sebagai notifikasi/badge)* |

**Total Use Case Wali Kelas: 7** *(dari 11 menu asli)*

---

## 7. GURU PENGAJAR (Level 4)

**Sidebar Dashboard (Sneat):**

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 25 | **Melihat Informasi Akademik** | ✅ Jadwal Mengajar *(read-only)* |
|   |   | ✅ Semua Kelas *(read-only, navigasi ke LMS)* |
| 27 | **Kelola Materi Pembelajaran** | ✅ Arsip LMS *(salin materi lama)* |
| 26 | **Membaca Catatan Monitoring** | ✅ Catatan Monitoring *(menerima teguran pimpinan)* |

**Sidebar LMS (per Kelas+Mapel):**

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 27 | **Kelola Materi Pembelajaran** | ✅ Materi |
| 28 | **Kelola & Koreksi Tugas** | ✅ Tugas *(buat tugas + koreksi jawaban siswa)* |
| 29 | **Kelola Ujian dan Latihan** | ✅ Latihan |
|   |   | ✅ Ujian |
| 30 | **Berpartisipasi di Forum Diskusi** | ✅ Forum Diskusi |
| 31 | **Kelola Kelas Virtual** | ✅ Kelas Virtual |
| 32 | **Input Nilai Akademik Siswa** | ✅ Nilai Siswa |

**Total Use Case Guru: 7** *(dari 11 menu asli, 2 sidebar)*

---

## 8. ORANG TUA (Level 5)

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 33 | **Melakukan Pembayaran Tagihan** | ✅ [Anak] → Tagihan *(lihat tagihan + bayar Midtrans/manual)* |
| 34 | **Mengajukan Izin Ketidakhadiran** | ✅ *(tombol "Ajukan Izin" dari dashboard per anak)* |
| 35 | **Melihat Riwayat Presensi** | ✅ [Anak] → Presensi *(read-only kehadiran anak)* |
| 36 | **Meminta dan Mengunduh Rapor** | ✅ [Anak] → Rapor *(request download + unduh PDF)* |

**Total Use Case Orang Tua: 4** *(dari 3 menu sidebar + 1 aksi dari dashboard)*

---

## 9. SISWA (Level 6)

**Sidebar SIA:**

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 35 | **Melihat Riwayat Presensi** | ✅ Presensi *(read-only, shared dengan Orang Tua)* |
| 37 | **Melihat Nilai Pribadi** | ✅ Data Penilaian *(read-only)* |

**Sidebar LMS:**

| No | Nama Use Case | Menu Sidebar yang Digabung |
|----|--------------|---------------------------|
| 38 | **Mengikuti Pembelajaran LMS** | ✅ [Mapel] → Materi *(membaca)* |
|   |   | ✅ [Mapel] → Tugas *(mengumpulkan)* |
|   |   | ✅ [Mapel] → Ujian *(mengerjakan)* |
|   |   | ✅ [Mapel] → Latihan *(mengerjakan)* |
| 30 | **Berpartisipasi di Forum Diskusi** | ✅ [Mapel] → Forum Diskusi *(shared dengan Guru)* |
| 31 | **Kelola Kelas Virtual** | ✅ [Mapel] → Kelas Virtual *(shared dengan Guru)* |
| 25 | **Melihat Informasi Akademik** | ✅ Kalender Akademik |
|   |   | ✅ Jadwal Pelajaran |
|   |   | ✅ Daftar Guru |

**Total Use Case Siswa: 6** *(dari 9 menu asli, 2 sidebar)*

---

## Ringkasan Jumlah Use Case Unik

Setelah deduplikasi (banyak UC di-share antar role), **total Use Case unik** yang akan muncul di diagram:

| No | Nama Use Case | Aktor |
|----|--------------|-------|
| 1 | Kelola Data Pengguna | Admin |
| 2 | Kelola Tiket Pemulihan Akun | Admin |
| 3 | Kelola Pengaturan Sistem | Admin |
| 4 | Kelola Tahun Ajaran | Admin, Waka |
| 5 | Kelola Data Cabang | Admin |
| 6 | Kelola Data Kelas & Penugasan | Admin, Waka |
| 7 | Kelola Mata Pelajaran | Admin, Waka |
| 8 | Kelola Jadwal Pelajaran | Admin, Waka |
| 9 | Kelola Landing Page | Admin |
| 10 | Kelola Konten Publikasi | Admin, Sekretaris |
| 11 | Kelola Tagihan & Pembayaran | Admin, Bendahara |
| 12 | Lihat Laporan Keuangan | Admin, Bendahara |
| 13 | Validasi Akses Ujian dan Rapor | Admin, Bendahara, Wali Kelas |
| 14 | Memproses Dispensasi Keuangan | Admin, Bendahara, Ketua PKBM |
| 15 | Memproses Dispensasi Kenaikan Kelas | Admin, Bendahara, Ketua PKBM |
| 16 | Kelola Pengaturan Kenaikan Kelas | Admin, Waka |
| 17 | Proses Kenaikan Kelas | Admin, Waka |
| 18 | Monitoring Sistem | Admin, Ketua, Waka |
| 19 | Kelola Laporan & Catatan | Admin, Ketua, Waka |
| 20 | Validasi Rapor Tingkat Akhir | Ketua PKBM |
| 21 | Kelola Presensi Siswa | Wali Kelas |
| 22 | Kelola Rapor | Wali Kelas |
| 23 | Mengelola Permintaan Unduh Rapor | Wali Kelas |
| 24 | Melihat Prediksi Kenaikan Kelas | Wali Kelas |
| 25 | Melihat Informasi Akademik | Wali Kelas, Guru, Siswa |
| 26 | Membaca Catatan Monitoring | Guru, Wali Kelas |
| 27 | Kelola Materi Pembelajaran | Guru |
| 28 | Kelola & Koreksi Tugas | Guru |
| 29 | Kelola Ujian dan Latihan | Guru |
| 30 | Berpartisipasi di Forum Diskusi | Guru, Siswa |
| 31 | Kelola Kelas Virtual | Guru, Siswa |
| 32 | Input Nilai Akademik Siswa | Guru |
| 33 | Melakukan Pembayaran Tagihan | Orang Tua |
| 34 | Mengajukan Izin Ketidakhadiran | Orang Tua |
| 35 | Melihat Riwayat Presensi | Siswa, Orang Tua |
| 36 | Meminta dan Mengunduh Rapor | Orang Tua |
| 37 | Melihat Nilai Pribadi | Siswa |
| 38 | Mengikuti Pembelajaran LMS | Siswa |

**GRAND TOTAL: 38 Use Case Unik**
