<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetailTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailtransaksi', function (Blueprint $table) {
            $table->bigIncrements('id_detailtransaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_paket');
            $table->Integer('weight');
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('id_paket')->references('id_paket')->on('paket')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detailtransaksi');
    }
}
