<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodigosPostalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codigos_postales', function (Blueprint $table) {
            $table->id();
            $table->string('cp',5);
            $table->string('asentamiento',250);
            $table->string('tipo_asentamiento',250);
            $table->string('municipio',250);
            $table->string('estado',250);
            $table->string('ciudad',250);
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
        Schema::dropIfExists('codigos_postales');
    }
}
