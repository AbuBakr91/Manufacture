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
            $table->dateTime('pause_finish')->nullable();
            $table->unsignedBigInteger('work_id');
            $table->foreign('work_id')->references('id')->on('performing_tasks')->onDelete('cascade');
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
