<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id('id');
            $table->integer('external_id');
            $table->string('mark')->nullable();
            $table->string('model')->nullable();
            $table->string('generation')->nullable();
            $table->year('year')->nullable();
            $table->integer('run')->nullable();
            $table->string('color')->nullable();
            $table->string('body-type')->nullable();
            $table->string('engine-type')->nullable();
            $table->string('transmission')->nullable();
            $table->string('gear-type')->nullable();
            $table->integer('generation_id')->nullable();
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
        Schema::dropIfExists('offers');
    }
}
