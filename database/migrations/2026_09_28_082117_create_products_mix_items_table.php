<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_mix_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('products_mix_id')->default(1);
            $table->double('quantity')->unsigned()->default(0);
            $table->double('cost')->unsigned()->default(0);
            $table->bigInteger('product_id')->default(1);
            $table->string('note')->default('-');
            $table->timestamps();
            $table->double('Added_Value')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_mix_items');
    }
};
