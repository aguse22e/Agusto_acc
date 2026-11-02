<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIdDetailTransaksiOnJurnalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->bigInteger('id_detail_transaksi')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->bigInteger('id_detail_transaksi')->unsigned()->change();
        });
    }
}
