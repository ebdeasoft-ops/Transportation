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
        Schema::create('resource_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('orderId');
            $table->unsignedBigInteger('branchs_id')->index('resource_purchases_branchs_id_foreign');
            $table->unsignedBigInteger('suplier_id')->index('resource_purchases_suplier_id_foreign');
            $table->double('In_debt', 8, 2)->default(0);
            $table->string('Pay_Method_Name');
            $table->timestamps();
            $table->string('notes')->default('لا توجد ملاحظات');
            $table->double('recoveredـpieces', 8, 2)->default(0);
            $table->decimal('Other expenses')->default(0);
            $table->decimal('shipping fee')->default(0);
            $table->text('purchase_invoice_no')->nullable();
            $table->text('Purchase_invoice_number')->nullable();
            $table->integer('save')->default(0);
            $table->double('discount')->default(0);
            $table->text('attachments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_purchases');
    }
};
