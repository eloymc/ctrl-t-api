<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('label',50);
            $table->integer('nivel')->default(1);
            $table->integer('id_padre')->nullable(true);
            $table->integer('orden');
            $table->string('routerLink',200)->nullable(true);
            $table->string('url',200)->nullable(true);
            $table->string('icon',150)->nullable(true);
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
        Schema::dropIfExists('menu');
    }
}
