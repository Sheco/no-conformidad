<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->timestamps();

            $table->string('folio');
            $table->datetime('fecha');
            $table->unsignedBigInteger('departamento_id')->nullable();
            $table->unsignedBigInteger('tipo_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('creador_usr_id');
            $table->unsignedBigInteger('responsable_usr_id')->nullable();
            $table->string('descripcion');

            $table->foreign('departamento_id')->references('id')->on('departamentos');
            $table->foreign('tipo_id')->references('id')->on('tipos');
            $table->foreign('status_id')->references('id')->on('status');
            $table->foreign('creador_usr_id')->references('id')->on('users');
            $table->foreign('responsable_usr_id')->references('id')->on('users');

            $table->index('status_id');
            $table->index('responsable_usr_id');
            $table->index('creador_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
