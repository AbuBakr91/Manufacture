<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPausedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_paused', function (Blueprint $table) {
            $table->id();
            $table->dateTime('pause_begin');
            $table->dateTime('pause_finish');
            $table->unsignedBigInteger('work_time_id');
            $table->foreign('work_time_id')->references('id')->on('work_times');
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
        Schema::dropIfExists('work_pauseds');
    }
}
