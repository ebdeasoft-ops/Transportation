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
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('order_details_product_id_foreign');
            $table->unsignedBigInteger('order_owner')->index('order_details_order_owner_foreign');
            $table->string('product_name');
            $table->decimal('purchasingـprice')->default(0);
            $table->double('numberofpice')->default(0);
            $table->timestamps();
            $table->double('Added_Value', 8, 3)->default(0);
            $table->double('returns_purchase', 8, 2)->default(0);
            $table->integer('save')->default(0);
            $table->double('reamingQuantity')->default(0);
            $table->text('unit')->nullable();
            $table->double('sale_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
