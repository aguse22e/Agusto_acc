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
        DB::statement($this->viewSql(true));
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_laba_rugi");
        DB::statement($this->viewSql(false));

        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * SQL pembuatan view_laba_rugi sesuai driver aktif. Sintaks MySQL
     * (MONTH/YEAR) dipertahankan persis untuk MySQL/MariaDB; driver lain
     * (SQLite) memakai strftime yang setara.
     */
    private function viewSql($denganFilterSampah)
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $bulan = 'MONTH(tanggal)';
            $tahun = 'YEAR(tanggal)';
        } else {
            $bulan = "CAST(strftime('%m', tanggal) AS INTEGER)";
            $tahun = "CAST(strftime('%Y', tanggal) AS INTEGER)";
        }

        $filter = $denganFilterSampah ? 'deleted_at IS NULL AND ' : '';

        return "
        CREATE VIEW view_laba_rugi AS SELECT $bulan AS bulan, $tahun AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE $filter(kode LIKE '4%' OR kode LIKE '5%' OR kode LIKE '6%' OR lawan LIKE '4%' OR lawan LIKE '5%' OR lawan LIKE '6%') GROUP BY $bulan, $tahun, kode,lawan,tipe ORDER BY $bulan,$tahun
        ";
    }
}
