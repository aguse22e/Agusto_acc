<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('jurnal_id')->unsigned();
            $table->string('kode_akun');
            $table->foreign('kode_akun')->references('kode_akun')->on('kode_akun');
            $table->decimal('debit',13,2)->nullable();
            $table->decimal('kredit',13,2)->nullable();
            $table->enum('tipe',['Debit','Kredit']);
            $table->bigInteger('id_detail_transaksi')->unsigned();

            $table->foreign('jurnal_id')->references('id')->on('jurnal');
            // $table->foreign('id_detail_transaksi')->references('id')->on('transaksi_kas_detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnal_detail');
    }
}
