<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelacionesUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_persona')->nullable();
            $table->foreignId('id_direccion')->nullable();
            $table->foreignId('id_telefono_1')->nullable();
            $table->foreignId('id_telefono_2')->nullable();
            
            $table->foreign('id_persona')->references('id')->on('personas')->nullable();
            $table->foreign('id_direccion')->references('id')->on('direccions')->nullable();
            $table->foreign('id_telefono_1')->references('id')->on('telefonos')->nullable();
            $table->foreign('id_telefono_2')->references('id')->on('telefonos')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_persona');
            $table->dropColumn('id_direccion');
            $table->dropColumn('id_telefono_1');
            $table->dropColumn('id_telefono_2');
        });
    }
}
