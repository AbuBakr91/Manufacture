<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkWaitingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_waiting', function (Blueprint $table) {
            $table->id();
            $table->dateTime('waiting_begin');
            $table->dateTime('waiting_finish');
            $table->unsignedBigInteger('work_id');
            $table->foreign('work_id')->references('id')->on('performing_tasks');
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
        Schema::dropIfExists('work_waitings');
    }
}
