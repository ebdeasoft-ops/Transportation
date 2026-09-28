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
        Schema::table('offer_price_to_customer_items', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('offer_price_to_customers')->onDelete('CASCADE');
            $table->foreign(['product_id'])->references(['id'])->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_price_to_customer_items', function (Blueprint $table) {
            $table->dropForeign('offer_price_to_customer_items_order_id_foreign');
            $table->dropForeign('offer_price_to_customer_items_product_id_foreign');
        });
    }
};
