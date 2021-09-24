<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direccions', function (Blueprint $table) {
            $table->id();
            $table->string('calle',100);
            $table->string('no_ext',100);
            $table->string('no_int',100);
            $table->string('colonia',100);
            $table->string('municipio',100);
            $table->string('estado',100);
            $table->string('pais',50);
            $table->string('cp',5);
            $table->text('referencia');
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
        Schema::dropIfExists('direccions');
    }
}
