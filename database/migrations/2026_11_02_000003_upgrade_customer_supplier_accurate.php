<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeCustomerSupplierAccurate extends Migration
{
    /**
     * PENYEMPURNAAN — kerangka Customer & Supplier mengikuti standar
     * aplikasi akuntansi (model Accurate):
     *  - kode otomatis (CUST-/SUPP-) sebagai identitas tetap di dokumen
     *  - kontak lengkap: contact person, telepon kantor, email, kota,
     *    provinsi, kode pos, NPWP
     *  - termin pembayaran (hari) untuk umur piutang/hutang
     *  - customer: kategori, limit kredit, akun piutang default
     *  - supplier: bank + nomor rekening pembayaran, akun hutang default
     * Kolom piutang (customer) dan hutang (supplier) yang sudah ada sejak
     * skema awal dipakai sebagai Saldo Awal Piutang/Hutang.
     * Akun default merujuk kode_akun agar jurnal penjualan/pembelian
     * berikutnya konsisten dengan chart of accounts.
     */
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('kode_customer', 15)->nullable()->unique();
            $table->string('kontak_person', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('npwp', 25)->nullable();
            $table->unsignedSmallInteger('termin_hari')->default(0);
            $table->string('kategori', 50)->nullable();
            $table->decimal('limit_kredit', 15, 2)->default(0);
            $table->string('akun_piutang', 15)->nullable();
            $table->text('catatan')->nullable();
            $table->foreign('akun_piutang')->references('kode_akun')->on('kode_akun')->nullOnDelete();
        });

        Schema::table('supplier', function (Blueprint $table) {
            $table->string('kode_supplier', 15)->nullable()->unique();
            $table->string('kontak_person', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('npwp', 25)->nullable();
            $table->unsignedSmallInteger('termin_hari')->default(0);
            $table->string('nama_bank', 50)->nullable();
            $table->string('no_rekening', 30)->nullable();
            $table->string('akun_hutang', 15)->nullable();
            $table->text('catatan')->nullable();
            $table->foreign('akun_hutang')->references('kode_akun')->on('kode_akun')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropForeign(['akun_piutang']);
            $table->dropColumn(['kode_customer', 'kontak_person', 'telepon', 'email', 'kota', 'provinsi', 'kode_pos', 'npwp', 'termin_hari', 'kategori', 'limit_kredit', 'akun_piutang', 'catatan']);
        });

        Schema::table('supplier', function (Blueprint $table) {
            $table->dropForeign(['akun_hutang']);
            $table->dropColumn(['kode_supplier', 'kontak_person', 'telepon', 'email', 'kota', 'provinsi', 'kode_pos', 'npwp', 'termin_hari', 'nama_bank', 'no_rekening', 'akun_hutang', 'catatan']);
        });
    }
}
