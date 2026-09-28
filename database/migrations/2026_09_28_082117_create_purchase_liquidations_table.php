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
        Schema::create('purchase_liquidations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branchs_id');
            $table->unsignedBigInteger('user_id');
            $table->double('price_filtering')->unsigned()->default(0);
            $table->string('note_detaials', 1000);
            $table->string('attachments', 1000);
            $table->timestamps();
            $table->bigInteger('Transactions_id')->nullable();
            $table->text('invoice_number')->nullable();
            $table->integer('save')->default(0);
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_liquidations');
    }
};
