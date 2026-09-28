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
        Schema::create('stock_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->default(0);
            $table->bigInteger('branchs_id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->string('product_name')->default('-');
            $table->double('productdecrease', 8, 2)->default(0);
            $table->double('productincrease', 8, 2)->default(0);
            $table->timestamps();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_updates');
    }
};
