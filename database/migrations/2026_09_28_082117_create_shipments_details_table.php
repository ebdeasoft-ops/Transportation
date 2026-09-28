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
        Schema::create('shipments_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branchs_id')->index('shipments_details_branchs_id_foreign');
            $table->string('loading');
            $table->string('unloading');
            $table->double('price_shipment')->unsigned()->default(0);
            $table->string('truck_data');
            $table->string('invoice_number', 50);
            $table->string('Ext', 50);
            $table->string('Daily', 50);
            $table->string('attachments_2', 1000);
            $table->string('note_detaials', 1000);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('Transactions_id');
            $table->timestamps();
            $table->integer('save')->default(0);
            $table->text('polica_number')->nullable();
            $table->integer('status')->default(0);
            $table->text('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments_details');
    }
};
