<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndicadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('valor');
            $table->integer('escala');
            $table->string('query');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('unidad')->unsigned();
            $table->foreign('unidad')->references('id')->on('unidades');
            $table->integer('responsable')->unsigned();
            $table->foreign('responsable')->references('id')->on('users');
            $table->integer('subgrupo_id')->unsigned();
            $table->foreign('subgrupo_id')->references('id')->on('subgrupos');
            $table->integer('create_id')->unsigned();
            $table->foreign('create_id')->references('id')->on('users');
            $table->integer('update_id')->unsigned();
            $table->foreign('update_id')->references('id')->on('users');
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
        Schema::drop('indicadors');
    }
}
