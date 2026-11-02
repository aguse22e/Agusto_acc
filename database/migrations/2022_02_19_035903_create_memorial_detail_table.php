<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemorialDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorial_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_memorial');
            $table->text('keterangan');
            $table->string('kode');
            $table->string('lawan');
            $table->decimal('subtotal',13,2);
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
        Schema::dropIfExists('memorial_detail');
    }
}
