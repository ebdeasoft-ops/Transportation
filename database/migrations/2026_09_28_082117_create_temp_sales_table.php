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
        Schema::create('temp_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('temp_sales_product_id_foreign');
            $table->unsignedBigInteger('invoices_id_delete')->nullable()->index('temp_sales_invoice_id_foreign');
            $table->double('Discount_Value', 8, 2)->default(0);
            $table->unsignedBigInteger('branch_id')->index('temp_sales_branch_id_foreign');
            $table->double('Added_Value', 8, 2)->default(0);
            $table->double('reamingQuantity', 8, 2)->default(0);
            $table->double('discountreturn', 8, 2)->default(0);
            $table->double('quantityreturn', 8, 2)->default(0);
            $table->decimal('Unit_Price')->default(0);
            $table->bigInteger('quantity')->default(0);
            $table->integer('save')->default(0);
            $table->timestamps();
            $table->bigInteger('invoice_id');
            $table->text('unit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_sales');
    }
};
