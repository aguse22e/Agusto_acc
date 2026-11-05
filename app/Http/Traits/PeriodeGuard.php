<?php

namespace App\Http\Traits;

use App\Models\KodeAkun;
use App\Models\KunciTransaksi;

trait PeriodeGuard
{
    /**
     * PERBAIKAN SAK — penegakan kunci periode dan pemeriksaan akun posting.
     *
     * Kunci Transaksi menyimpan tanggal_mulai_kunci per jenis transaksi
     * ('Kas' untuk Transaksi Kas, 'Bank Memorial' untuk Transaksi Bank dan
     * Memorial). Semantikanya: transaksi bertanggal SEBELUM tanggal mulai
     * kunci berada pada periode yang sudah ditutup, sehingga tidak boleh
     * ditambah, diubah, dipindah ke sampah, atau dipulihkan. Sebelum
     * perbaikan ini tanggal kunci hanya disimpan dan tidak pernah diperiksa.
     */
    protected function tanggalMulaiKunci($jenis)
    {
        $baris = KunciTransaksi::where('jenis_transaksi', $jenis)->first();
        return $baris ? $baris->tanggal_mulai_kunci : null;
    }

    protected function tanggalTerkunci($jenis, $tanggal)
    {
        $kunci = $this->tanggalMulaiKunci($jenis);
        if (!$kunci || !$tanggal) {
            return false;
        }
        return date('Y-m-d', strtotime($tanggal)) < date('Y-m-d', strtotime($kunci));
    }

    protected function pesanTerkunci($jenis, $tanggal)
    {
        return 'Tanggal ' . date('d-m-Y', strtotime($tanggal)) . ' berada pada periode yang sudah dikunci (kunci '
            . $jenis . ' berlaku mulai ' . date('d-m-Y', strtotime($this->tanggalMulaiKunci($jenis))) . '). '
            . 'Perubahan pada periode tertutup harus melalui Jurnal Koreksi.';
    }

    /**
     * PERBAIKAN SAK — edit dan hapus langsung hanya untuk transaksi pada
     * periode berjalan (bulan dan tahun ini). Transaksi periode lalu yang
     * keliru dibetulkan lewat Jurnal Koreksi agar jurnal asalnya tetap utuh
     * sebagai jejak audit.
     */
    protected function diPeriodeBerjalan($tanggal)
    {
        return date('Y-m', strtotime($tanggal)) === date('Y-m');
    }

    protected function pesanBukanPeriodeBerjalan($tanggal)
    {
        return 'Transaksi bertanggal ' . date('d-m-Y', strtotime($tanggal)) . ' bukan periode berjalan, '
            . 'sehingga tidak dapat diubah atau dihapus langsung. Gunakan tombol Jurnal Koreksi pada halaman detail transaksi.';
    }

    /**
     * Pemeriksaan akun posting di sisi server: akun harus ada dan belum
     * terhapus lunak (akun aktif), dan bila $jenisHarus diisi, penanda
     * jenis_akunnya harus cocok (akun utama Transaksi Bank harus berjenis
     * Bank, akun utama Transaksi Kas harus berjenis Kas). Mengembalikan
     * pesan kesalahan, atau null bila akun lolos pemeriksaan.
     */
    protected function periksaAkunPosting($kode, $jenisHarus = null)
    {
        $akun = KodeAkun::find($kode);
        if (!$akun) {
            return 'Akun dengan kode ' . $kode . ' tidak ditemukan atau sudah nonaktif.';
        }
        if ($jenisHarus !== null && $akun->jenis_akun !== $jenisHarus) {
            return 'Akun ' . $akun->kode_akun . ' (' . $akun->nama . ') bukan akun berjenis ' . $jenisHarus . '. '
                . 'Periksa penanda Jenis Akun pada data Kode Akun.';
        }
        return null;
    }
}
