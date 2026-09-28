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
        Schema::create('temp_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id');
            $table->unsignedBigInteger('user_id')->index('temp_invoices_user_id_foreign');
            $table->unsignedBigInteger('branchs_id')->index('temp_invoices_branch_id_foreign');
            $table->decimal('Price')->default(0);
            $table->decimal('Added_Value')->default(0);
            $table->decimal('Number_of_Quantity')->default(0);
            $table->string('note')->default('-');
            $table->string('Pay')->default('-');
            $table->integer('status')->default(0);
            $table->double('discountOnInvoice')->default(0);
            $table->double('discount')->default(0);
            $table->double('creaditamount')->default(0);
            $table->double('bankamount')->default(0);
            $table->double('cashamount')->default(0);
            $table->double('Bank_transfer')->default(0);
            $table->integer('save')->default(0);
            $table->integer('morepayment_way')->default(0);
            $table->double('discountOnProduct')->default(0);
            $table->timestamps();
            $table->bigInteger('update_invoice')->default(0);
            $table->text('p_o')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_invoices');
    }
};
