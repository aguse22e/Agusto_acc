<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToJurnalAndFixLabaRugiView extends Migration
{
    /**
     * PERBAIKAN SAK — jurnal transaksi yang dipindah ke sampah tidak boleh
     * lagi terhitung di laporan. Tabel jurnal diberi soft delete, dan view
     * laba rugi dibuat ulang agar mengabaikan jurnal yang terhapus.
     */
    public function up()
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->softDeletes();
        });

        DB::statement("DROP VIEW IF EXISTS view_laba_rugi");
        DB::statement("
        CREATE VIEW view_laba_rugi AS SELECT MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE deleted_at IS NULL AND (kode LIKE '4%' OR kode LIKE '5%' OR kode LIKE '6%' OR lawan LIKE '4%' OR lawan LIKE '5%' OR lawan LIKE '6%') GROUP BY MONTH(tanggal), YEAR(tanggal), kode,lawan,tipe ORDER BY MONTH(tanggal),YEAR(tanggal)
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_laba_rugi");
        DB::statement("
        CREATE VIEW view_laba_rugi AS SELECT MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE kode LIKE '4%' OR kode LIKE '5%' OR kode LIKE '6%' OR lawan LIKE '4%' OR lawan LIKE '5%' OR lawan LIKE '6%' GROUP BY MONTH(tanggal), YEAR(tanggal), kode,lawan,tipe ORDER BY MONTH(tanggal),YEAR(tanggal)
        ");

        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
