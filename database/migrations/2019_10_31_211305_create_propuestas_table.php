<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->datetime('fecha_entrega')->nullable();

            $table->unsignedBigInteger('documento_id');
            $table->unsignedBigInteger('responsable_usr_id');
            $table->unsignedBigInteger('retro_usr_id')->nullable();
            $table->boolean('status')->nullable();
            $table->string('descripcion');
            $table->string('retro')->nullable();

            $table->foreign('documento_id')->references('id')->on('documentos');
            $table->foreign('retro_usr_id')->references('id')->on('users');
            $table->foreign('responsable_usr_id')->references('id')->on('users');

            $table->index('documento_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propuestas');
    }
}
