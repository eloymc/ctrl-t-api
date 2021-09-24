<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->nullable();
            $table->foreignId('id_rol')->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id')->on('users')->nullable();
            $table->foreign('id_rol')->references('id')->on('roles')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_roles');
    }
}
