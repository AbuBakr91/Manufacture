<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dep_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->bigInteger('count');
            $table->bigInteger('user_count');
            $table->unsignedBigInteger('card_id');
            $table->boolean('in_work');
            $table->foreign('card_id')->references('id')->on('technical_cards');
            $table->foreign('dep_id')->references('id')->on('departments');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('task_orders');
    }
}
