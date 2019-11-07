<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_logs', function (Blueprint $table) {
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('documento_id');
          $table->datetime('fecha');
          $table->string('mensaje');

          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('documento_id')->references('id')->on('documentos');

          $table->index('user_id');
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
      Schema::dropIfExists('documento_logs');
    }
}
