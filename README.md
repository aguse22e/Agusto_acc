# Accounting

Aplikasi akuntansi berbasis web (versi arsip/legacy) — pencatatan transaksi kas, bank, dan memorial yang bermuara ke jurnal umum, buku besar, neraca saldo, dan laporan laba rugi.

> Ini adalah versi terbaru untuk upload ulang dari arsip lama (Juni 2025). Versi pengembangan yang lebih baru ada di repo `accounting_`.

## Teknologi

- Laravel 12, PHP 8.2+ (teruji jalan di PHP 8.4 dan 8.5)
- MySQL / MariaDB (database `laravelaccoun`)
- Laravel Breeze (Bootstrap), Laravel Mix, Bootstrap 5.1
- Template admin Admindek (aset di `public/assets`)

## Fitur

- Manajemen user (level Administrator / Viewer), customer, dan supplier
- Master data: Kode Induk, Kode Akun (chart of accounts), Kunci Transaksi
- Transaksi Kas, Transaksi Bank, dan Memorial — masing-masing dengan detail dan laporan/cetak
- Jurnal umum gabungan (Kas / Bank / Memorial), Buku Besar, Neraca Saldo, Laba Rugi
- Soft delete dengan trash, restore, dan hapus permanen pada data master & transaksi
- Penomoran transaksi otomatis, log aktivitas user

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
# atur koneksi database di .env, lalu:
php artisan migrate --seed
# atau impor dump contoh: laravelaccoun.sql
npm install && npm run dev
php artisan serve
```

Akun awal dibuat oleh `database/seeders/UserSeeder.php` (Administrator).
**Wajib ganti password default seeder segera setelah login pertama.**

## Perbaikan logika akuntansi pada versi terbaru ini

1. **Referensi saldo normal akun** — semua laporan (Buku Besar, Neraca Saldo, Laba Rugi, termasuk export) sekarang membaca kolom `tipe` dari akun itu sendiri (tabel `kode_akun`), bukan dari `kode_induk` yang sudah tidak memiliki kolom tersebut. Sebelumnya nilai itu selalu kosong sehingga semua akun diperlakukan sebagai saldo normal Kredit dan saldo akun Debit (Kas, aset, beban) terbalik.
2. **Rumus saldo awal Buku Besar untuk akun saldo normal Kredit** — sekarang `total sisi Kredit − total sisi Debit`. Sebelumnya kedua sisi dijumlahkan, sehingga saldo awal hutang/modal/pendapatan selalu terlalu besar.

3. **Sinkronisasi tipe jurnal saat edit transaksi** — pada Transaksi Kas, Bank, dan Memorial, mengganti tipe Masuk/Keluar saat edit sekarang memperbarui sisi Debit/Kredit semua baris jurnal terkait (baris lama maupun detail baru). Sebelumnya header berubah tetapi jurnal tetap memakai tipe lama, sehingga laporan tidak lagi sesuai dokumen sumbernya. Pada jalur yang sama di Memorial, nilai tipe `'Debet'` yang tidak valid untuk enum jurnal juga dibetulkan menjadi `'Debit'`.

4. **User Activity dijadikan read-only yang benar** — sebelumnya method `create/store/show/edit/update/destroy` hanya stub kosong. Sekarang: route dibatasi `->only(['index', 'show'])`, stub mati dihapus, halaman detail log (`show`) diimplementasikan dengan tombol Lihat di daftar, dan query daftar diperbaiki agar `id` yang terpilih adalah id log (bukan id user). Log aktivitas adalah jejak audit, jadi memang sengaja tidak bisa dibuat/diubah/dihapus manual — catatan hanya ditulis otomatis oleh controller transaksi.

## Catatan keamanan (penting)

- File `.env` dan folder `vendor/` tidak disertakan dalam repo ini — jangan pernah meng-commit keduanya.
- `APP_KEY` dari arsip lama sudah pernah terpublikasi; jangan gunakan kembali, selalu generate yang baru (`php artisan key:generate`).
- Dump `laravelaccoun.sql` di repo ini sudah dibersihkan dari data user dan log aktivitas; isinya hanya skema + data contoh master/transaksi.
- Alur lupa-password custom pada versi legacy ini belum memakai token verifikasi, dan pembatasan role Viewer baru sebatas tampilan menu. **Jangan deploy ke produksi sebelum kedua hal ini diperbaiki** — gunakan versi yang lebih baru di repo `accounting_` untuk pengembangan aktif.
5. **Jurnal transaksi yang dibuang ke sampah tidak lagi terhitung di laporan** — sebelumnya menghapus transaksi (soft delete) hanya menyembunyikan header-nya, sedangkan baris jurnalnya tetap dihitung Buku Besar, Neraca Saldo, dan Laba Rugi sampai transaksi dihapus permanen. Sekarang tabel `jurnal` memiliki soft delete (`deleted_at`, migration `2026_11_02_000001`): memindah transaksi ke sampah ikut men-soft-delete jurnalnya, memulihkannya ikut memulihkan jurnalnya, dan hapus permanen memakai `forceDelete`. View `view_laba_rugi` dibuat ulang dengan filter `deleted_at IS NULL`, query `DB::table('jurnal')` di Neraca Saldo diberi `whereNull('jurnal.deleted_at')`, dan dump SQL diperbarui mengikuti.
6. **Modul baru: Laporan Posisi Keuangan (Neraca)** di menu General Ledger → Neraca (`general-ledger/neraca`, lengkap dengan export Excel). Sebelumnya laporan keuangan hanya Neraca Saldo dan Laba Rugi, padahal SAK mensyaratkan Laporan Posisi Keuangan. Saldo dihitung kumulatif per tanggal yang dipilih dari jurnal (akun 1xxx Aset, 2xxx Liabilitas, 3xxx Ekuitas), dan Laba (Rugi) Tahun Berjalan (akun 4xxx dikurangi 5xxx–6xxx) disajikan sebagai bagian dari Ekuitas. Halaman menampilkan status Seimbang/Tidak Seimbang beserta selisihnya. Catatan: kelengkapan laporan dilanjutkan pada perbaikan 7–9 di bawah.
7. **Modul baru: Laporan Arus Kas** di menu General Ledger → Arus Kas (`general-ledger/arus-kas`, metode langsung, lengkap dengan export Excel). Akun kas/setara kas dikenali dari nama akun berawalan "Kas"/"Bank" (konvensi aplikasi sendiri). Setiap jurnal yang salah satu sisinya akun kas adalah arus kas; transfer antar akun kas diabaikan. Klasifikasi dari akun lawan: Operasi (pendapatan/beban/pajak dan liabilitas), Investasi (aset non-kas), Pendanaan (ekuitas dan liabilitas yang namanya mengandung "Bank", mis. pinjaman bank). Laporan merekonsiliasi kenaikan bersih kas ke saldo kas awal–akhir periode dan menampilkan status kecocokannya.
8. **Modul baru: Laporan Perubahan Ekuitas** di menu General Ledger → Perubahan Ekuitas (`general-ledger/perubahan-ekuitas`, lengkap dengan export Excel): saldo awal, perubahan periode, dan saldo akhir per akun ekuitas (3xxx), ditambah baris Akumulasi Laba Periode Lalu dan Laba (Rugi) Periode Berjalan. Total Ekuitas Akhir laporan ini sama dengan total Ekuitas + Laba Tahun Berjalan pada Neraca per tanggal yang sama (terverifikasi lewat simulasi konsistensi antar-laporan).
9. **Modul baru: Tutup Buku (Jurnal Penutup)** di menu General Ledger → Tutup Buku (`general-ledger/tutup-buku`): menghitung saldo kumulatif akun nominal sampai tanggal tutup, menampilkan pratinjau, lalu membuat bukti Memorial penutup (saldo Kredit ditutup via BMM/Debit, saldo Debit via BMK/Kredit, dikelompokkan menurut sisi saldo sebenarnya) ke akun Laba Ditahan (3xxx) yang dipilih, lengkap dengan catatan User Activity — persis pola MemorialController::store. Sesudahnya saldo akun nominal menjadi nol, laba berpindah ke Laba Ditahan, dan Neraca tetap seimbang. Menjalankan dua kali pada tanggal yang sama aman (tidak ada saldo = tidak ada voucher baru). Pembatalan dilakukan dengan menghapus memorial penutup dari modul Memorial.
10. **Sisa CRUD dilengkapi** — Supplier dan Customer kini memiliki halaman detail (`show`) beserta tombol Lihat di daftar (sebelumnya route detail terdaftar tapi method-nya tidak ada sehingga selalu error), dan route Kunci Transaksi dibatasi `->only(['index', 'edit', 'update'])` karena method create/store/show/destroy-nya hanya stub kosong yang tidak dipakai UI (data berasal dari seeder), mengikuti pola User Activity pada perbaikan 4.
Catatan SAK yang masih terbuka setelah perbaikan 1–10: Catatan atas Laporan Keuangan (CALK) belum tersedia karena sifatnya naratif/tekstual (kebijakan akuntansi, rincian akun) dan belum ada tempat penyimpanannya di skema saat ini; klasifikasi arus kas untuk liabilitas mengikuti heuristik nama akun (mengandung "Bank" = pendanaan) sehingga akun pinjaman dengan nama lain perlu dinamai konsisten agar terklasifikasi benar.
11. **Modul baru: Kepegawaian — Data Karyawan, Master Shift, dan Jadwal Kerja** (menu Kepegawaian; migration `2026_11_02_000002`, tabel juga ditambahkan ke dump SQL). Analisa kebutuhannya: (a) akuntansi beban gaji membutuhkan data dasar karyawan (identitas, jabatan/departemen, status Tetap/Kontrak/Harian/Magang, tanggal masuk, NPWP, rekening bank, dan gaji pokok) — disimpan sebagai master `karyawan` dengan kode otomatis KRY-0001 dst., soft delete + tempat sampah seperti master lain, dan karyawan yang masih memiliki riwayat jadwal tidak dapat dihapus permanen; (b) jam kerja diatur lewat master `shift` (nama + jam masuk/keluar, terisi awal Pagi 07.00–15.00, Siang 15.00–23.00, Malam 23.00–07.00) agar jadwal tidak mengetik jam berulang kali; (c) jadwal dan kehadiran disatukan dalam `jadwal_kerja` — satu karyawan satu entri per tanggal (unique), status bergerak dari Terjadwal menjadi Hadir/Izin/Sakit/Cuti/Alpa/Libur — sehingga satu tabel melayani perencanaan sekaligus realisasi. Fitur jadwal: matriks bulanan karyawan x tanggal (klik sel untuk mengubah entri), rekap kehadiran per karyawan per bulan sebagai dasar hari kerja efektif untuk penggajian, generate massal per rentang tanggal dengan pola Semua Hari / Senin–Jumat / Senin–Sabtu (tanggal yang sudah terisi dilewati, tidak ditimpa; generate ulang aman), CRUD entri manual, dan tempat sampah dengan penjagaan duplikat saat restore. Karyawan nonaktif tidak ikut matriks dan generate. Jurnal beban gaji sendiri tetap dibuat lewat Memorial berdasarkan rekap + gaji pokok, karena tarif dan komponen gaji di luar gaji pokok (lembur, potongan) belum dimodelkan — itu batas modul ini saat ini.
12. **Kerangka Customer & Supplier dilengkapi mengikuti standar aplikasi akuntansi (model Accurate)** (migration `2026_11_02_000003`, dump SQL diperbarui). Sebelumnya kedua master ini hanya berisi nama, alamat, dan nomor HP — padahal kolom saldo `piutang`/`hutang` sudah ada di skema sejak awal namun tidak pernah ditampilkan. Sekarang: kode otomatis CUST-0001/SUPP-0001, kontak person, telepon kantor, email, kota/provinsi/kode pos, NPWP, termin pembayaran (hari) sebagai dasar umur piutang/hutang, saldo awal piutang/hutang (kolom lama yang kini tampil dan dapat diisi), akun piutang/hutang default yang merujuk ke chart of accounts, dan catatan. Khusus customer ditambah kategori (Umum/Reseller/Distributor) dan limit kredit; khusus supplier ditambah bank dan nomor rekening tujuan pembayaran. Daftar kini menampilkan kode, kontak, kota, termin, dan saldo awal; halaman detail menampilkan seluruh kerangka.

13. **Upgrade framework: Laravel 8.75 → Laravel 12 (PHP 7.3/8.0 → PHP 8.2+)** — dikerjakan hati-hati agar tidak ada fitur yang rusak, dan diverifikasi dengan menjalankan aplikasinya (bukan hanya membaca kode): seluruh migration berjalan dari kosong, seeding berhasil, 179 route terdaftar, halaman login tampil, dan seluruh test bawaan lulus (20 test, 64 assertion — termasuk test khusus yang membuka 30 halaman utama sebagai Administrator dan menulis-membaca data Customer). Penyesuaian yang dilakukan: (a) composer.json diperbarui (framework ^12.0, Sanctum ^4.0, PHPUnit ^11.5, Ignition diganti paket penerusnya, paket CORS lama dibuang karena sudah bawaan framework — middleware Kernel diarahkan ke HandleCors bawaan); (b) properti `$dates` yang sudah tidak didukung framework baru dihapus dari 12 model (tanggal soft-delete tetap ditangani trait SoftDeletes; tanggal lahir/masuk karyawan dan tanggal jadwal dipindah ke `$casts`); (c) phpunit.xml dimutakhirkan ke skema PHPUnit 11; (d) konstanta SSL MySQL di config/database.php dibuat sadar versi agar tidak memunculkan peringatan deprecated di PHP 8.5. Perbaikan ikut-sertakan yang ditemukan selama pengujian jalan: (e) folder view pagination terbitan (`resources/views/vendor/pagination`, 8 file) yang hilang dari paket sebelumnya dikembalikan dari arsip asal — tanpa file ini semua halaman daftar yang memakai pagination error; (f) 11 komponen Blade pelengkap halaman auth (application-logo, auth-card, auth-validation-errors, auth-session-status, button, input, label, dropdown, dropdown-link, nav-link, responsive-nav-link) ditambahkan — tanpanya halaman lupa password/daftar/konfirmasi gagal tampil; (g) UserFactory dilengkapi kolom username dan level agar sesuai skema users; (h) registrasi publik kini mengisi username (diturunkan dari email) dan level Viewer, karena kedua kolom itu NOT NULL — sebelumnya registrasi bisa gagal di database yang ketat; (i) tiga migration dibuat sadar driver tanpa mengubah perilaku di MySQL/MariaDB (ALTER ENUM khusus MySQL dilewati di driver lain; view laba rugi memakai MONTH/YEAR di MySQL dan padanan strftime di SQLite) dan enum level pada migration awal users langsung memuat 'Viewer', sehingga migration juga berjalan utuh di SQLite untuk keperluan pengujian. Catatan: build aset front-end masih memakai Laravel Mix seperti sebelumnya (file hasil compile di public/ tidak berubah); pemindahan ke Vite tidak termasuk cakupan upgrade ini.
