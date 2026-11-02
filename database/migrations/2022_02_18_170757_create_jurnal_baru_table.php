<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalBaruTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->enum('jenis_transaksi',['Kas','Bank','Memorial']);
            $table->string('kode_transaksi',20);
            $table->text('keterangan');
            $table->string('kode',15);
            $table->string('lawan',15);
            $table->enum('tipe',['Debit','Kredit']);
            $table->decimal('nominal',13,2);
            $table->integer('id_detail')->nullable();
            $table->timestamps();

            $table->foreign('kode')->references('kode_akun')->on('kode_akun');
            $table->foreign('lawan')->references('kode_akun')->on('kode_akun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnal_baru');
    }
}
