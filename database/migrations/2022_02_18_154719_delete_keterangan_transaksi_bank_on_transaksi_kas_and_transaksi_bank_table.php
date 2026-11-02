<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteKeteranganTransaksiBankOnTransaksiKasAndTransaksiBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('transaksi_bank', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
