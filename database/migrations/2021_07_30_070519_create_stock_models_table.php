<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_name_id');
            $table->bigInteger('stock');
            $table->bigInteger('reserve');
            $table->bigInteger('sellQuantity')->nullable();
            $table->bigInteger('returnQuantity')->nullable();
            $table->foreign('product_name_id')->references('id')->on('product_names');
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
        Schema::dropIfExists('stock_models');
    }
}
