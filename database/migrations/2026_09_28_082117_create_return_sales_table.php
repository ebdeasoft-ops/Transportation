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
        Schema::create('return_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->index('return_sales_branch_id_foreign');
            $table->unsignedBigInteger('product_id')->index('return_sales_product_id_foreign');
            $table->unsignedBigInteger('invoice_id')->index('return_sales_invoice_id_foreign');
            $table->string('value')->default('empty');
            $table->double('return_Added_Value', 8, 2)->default(0);
            $table->decimal('return_Unit_Price')->default(0);
            $table->bigInteger('return_quantity')->default(0);
            $table->timestamps();
            $table->double('discountvalue')->default(0);
            $table->double('discountoninvoice')->default(0);
            $table->double('returnshabkavalue')->default(0);
            $table->integer('send_zatca')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_sales');
    }
};
