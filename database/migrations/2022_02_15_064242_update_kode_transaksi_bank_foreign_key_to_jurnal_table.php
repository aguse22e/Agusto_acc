<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateKodeTransaksiBankForeignKeyToJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->foreign('kode_transaksi_bank')->references('kode_transaksi_bank')->on('transaksi_bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jurnal', function (Blueprint $table) {
            //
        });
    }
}
