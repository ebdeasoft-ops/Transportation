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
        Schema::table('order_price_from_supplier_items', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('order_price_from_suppliers')->onDelete('CASCADE');
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
        Schema::table('order_price_from_supplier_items', function (Blueprint $table) {
            $table->dropForeign('order_price_from_supplier_items_order_id_foreign');
            $table->dropForeign('order_price_from_supplier_items_product_id_foreign');
        });
    }
};
