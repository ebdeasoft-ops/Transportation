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
        Schema::create('delivery_product_to_the_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_from');
            $table->unsignedBigInteger('branch_to');
            $table->unsignedBigInteger('user_from');
            $table->unsignedBigInteger('product_id')->index('delivery_product_to_the_customers_product_id_foreign');
            $table->unsignedBigInteger('invoice_id')->index('delivery_product_to_the_customers_invoice_id_foreign');
            $table->bigInteger('quantity')->default(0);
            $table->bigInteger('status')->default(0);
            $table->bigInteger('user_delivery')->default(0);
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
        Schema::dropIfExists('delivery_product_to_the_customers');
    }
};
