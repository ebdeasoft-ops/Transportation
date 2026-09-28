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
        Schema::create('offer_price_to_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('offer_price_to_customers_customer_id_foreign');
            $table->bigInteger('branchs_id')->default(1);
            $table->timestamps();
            $table->text('notes')->nullable();
            $table->double('discount')->default(0);
            $table->integer('numbershowstatus')->default(0);
            $table->double('converted')->default(500);
            $table->double('waiting')->default(300);
            $table->text('note1')->nullable();
            $table->text('note2')->nullable();
            $table->text('note3')->nullable();
            $table->text('note4')->nullable();
            $table->text('note5')->nullable();
            $table->text('note6')->nullable();
            $table->integer('payment_per_day')->default(0);
            $table->text('note7')->nullable();
            $table->text('note8')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_price_to_customers');
    }
};
