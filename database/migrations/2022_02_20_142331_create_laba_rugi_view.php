<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLabaRugiView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MONTH()/YEAR() adalah fungsi MySQL; untuk driver lain (SQLite)
        // dipakai padanannya agar view yang sama terbentuk.
        if (DB::connection()->getDriverName() === 'mysql') {
            $bulan = 'MONTH(tanggal)';
            $tahun = 'YEAR(tanggal)';
        } else {
            $bulan = "CAST(strftime('%m', tanggal) AS INTEGER)";
            $tahun = "CAST(strftime('%Y', tanggal) AS INTEGER)";
        }

        DB::statement("
        CREATE VIEW view_laba_rugi AS SELECT $bulan AS bulan, $tahun AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE kode LIKE '4%' OR kode LIKE '5%' OR kode LIKE '6%' OR lawan LIKE '4%' OR lawan LIKE '5%' OR lawan LIKE '6%' GROUP BY $bulan, $tahun, kode,lawan,tipe ORDER BY $bulan,$tahun
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_laba_rugi");
    }
}
