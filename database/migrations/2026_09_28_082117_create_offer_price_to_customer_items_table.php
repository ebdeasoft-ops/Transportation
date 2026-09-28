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
        Schema::create('offer_price_to_customer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('offer_price_to_customer_items_product_id_foreign');
            $table->unsignedBigInteger('order_id')->index('offer_price_to_customer_items_order_id_foreign');
            $table->double('quantity')->default(0);
            $table->timestamps();
            $table->double('PriceWithoudTax', 8, 2)->default(0);
            $table->double('discount', 8, 2)->default(0);
            $table->text('truck_type')->nullable();
            $table->text('Unloading')->nullable();
            $table->text('loading')->nullable();
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
        Schema::dropIfExists('offer_price_to_customer_items');
    }
};
