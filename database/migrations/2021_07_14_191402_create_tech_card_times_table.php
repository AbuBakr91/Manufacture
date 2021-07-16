<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechCardTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tech_card_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->float('statistical_time')->nullable();
            $table->float('dynamic_time');
            $table->foreign('card_id')->references('id')->on('technical_cards');
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
        Schema::dropIfExists('tech_card_times');
    }
}
