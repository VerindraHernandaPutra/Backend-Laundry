<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id_user');
            $table->string('nama');
            $table->string('username');
            $table->text('password');
            $table->enum('role', ['admin', 'kasir', 'owner']);
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();

            
            $table->foreign('id_outlet')->references('id_outlet')->on('outlet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
