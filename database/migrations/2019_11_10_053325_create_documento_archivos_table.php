<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_archivos', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('documento_id');
          $table->string('nombre');
          $table->timestamps();  

          $table->index('documento_id');
          $table->foreign('documento_id')->references('id')->on('documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_archivos');
    }
}
