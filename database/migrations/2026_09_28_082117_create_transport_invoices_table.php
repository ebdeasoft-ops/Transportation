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
        Schema::create('transport_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_no')->unique();
            $table->unsignedBigInteger('customer_account_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_vat', 30)->nullable();
            $table->string('customer_cr', 30)->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->dateTime('issue_date')->index();
            $table->date('supply_from')->nullable();
            $table->date('supply_to')->nullable();
            $table->decimal('subtotal', 15)->default(0);
            $table->decimal('discount', 15)->default(0);
            $table->decimal('taxable', 15)->default(0);
            $table->decimal('vat_rate', 6, 4)->default(0.15);
            $table->decimal('vat_amount', 15)->default(0);
            $table->decimal('total', 15)->default(0);
            $table->tinyInteger('prices_include_vat')->default(0);
            $table->string('po_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('posted')->default(0);
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('branchs_id')->nullable();
            $table->timestamps();
            $table->string('vat_category', 2)->default('S');
            $table->string('vat_exempt_code', 30)->nullable();
            $table->string('vat_exempt_reason')->nullable();

            $table->index(['customer_account_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_invoices');
    }
};
