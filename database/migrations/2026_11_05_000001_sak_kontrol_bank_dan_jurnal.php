<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SakKontrolBankDanJurnal extends Migration
{
    /**
     * PERBAIKAN SAK (lanjutan) — kontrol transaksi bank dan jurnal:
     * 1. kode_akun.jenis_akun: penanda sifat akun (Kas / Bank / Lainnya) agar
     *    pemilihan akun kas dan bank tidak lagi menebak dari awalan nama akun.
     *    Nilai awal diisi dari pola nama yang selama ini dipakai aplikasi.
     * 2. transaksi_bank.no_referensi: nomor bukti bank (cek, referensi transfer)
     *    sebagai jejak ke dokumen sumber.
     * 3. transaksi_bank.status_kliring + tanggal_kliring dan tabel
     *    rekonsiliasi_bank: bahan modul Rekonsiliasi Bank (saldo rekening koran
     *    per akun per periode).
     * 4. memorial.koreksi_dari: penanda jurnal memorial yang dibuat sebagai
     *    jurnal koreksi atas transaksi lain, agar koreksi yang sama tidak
     *    dibuat dua kali.
     */
    public function up()
    {
        Schema::table('kode_akun', function (Blueprint $table) {
            $table->enum('jenis_akun', ['Kas', 'Bank', 'Lainnya'])->default('Lainnya')->after('tipe');
        });

        // Isi awal penanda dari pola nama lama: "Kas Bank ..." dan "Bank ..."
        // adalah akun bank, "Kas ..." lainnya adalah akun kas.
        DB::table('kode_akun')
            ->where(function ($q) {
                $q->where('nama', 'LIKE', 'Kas Bank%')->orWhere('nama', 'LIKE', 'Bank%');
            })
            ->update(['jenis_akun' => 'Bank']);
        DB::table('kode_akun')
            ->where('nama', 'LIKE', 'Kas%')
            ->where('jenis_akun', 'Lainnya')
            ->update(['jenis_akun' => 'Kas']);

        Schema::table('transaksi_bank', function (Blueprint $table) {
            $table->string('no_referensi', 50)->nullable()->after('tipe');
            $table->enum('status_kliring', ['Belum', 'Kliring'])->default('Belum')->after('no_referensi');
            $table->date('tanggal_kliring')->nullable()->after('status_kliring');
        });

        Schema::table('memorial', function (Blueprint $table) {
            $table->string('koreksi_dari', 20)->nullable()->after('tipe');
        });

        Schema::create('rekonsiliasi_bank', function (Blueprint $table) {
            $table->id();
            $table->string('akun_kode', 15);
            $table->foreign('akun_kode')->references('kode_akun')->on('kode_akun');
            $table->date('periode');
            $table->decimal('saldo_koran', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['akun_kode', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekonsiliasi_bank');

        Schema::table('memorial', function (Blueprint $table) {
            $table->dropColumn('koreksi_dari');
        });

        Schema::table('transaksi_bank', function (Blueprint $table) {
            $table->dropColumn(['no_referensi', 'status_kliring', 'tanggal_kliring']);
        });

        Schema::table('kode_akun', function (Blueprint $table) {
            $table->dropColumn('jenis_akun');
        });
    }
}
