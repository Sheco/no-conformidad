<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('nombre');
        });

        Schema::create('user_departamentos', function (Blueprint $table) {
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('departamento_id');
          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('departamento_id')->references('id')->on('departamentos');
          $table->primary(['user_id', 'departamento_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('user_departamentos');
    }
}
