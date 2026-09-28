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
            $table->id();
            $table->bigInteger( 'branchs_id' )->unsigned();
            $table->foreign('branchs_id')->references('id')->on('branchs')->onDelete('cascade');
            $table->string('loading');
            $table->string('unloading');
            $table->unsignedDouble  ('price_shipment')->default(0);
            $table->string('truck_data');
            $table->string('invoice_number', 50);
            $table->string('Ext', 50);
            $table->string('Daily', 50);
            $table->string('attachments_2', 1000);
            $table->string('note_detaials', 1000);
            $table->bigInteger( 'user_id' )->unsigned();
            $table->bigInteger( 'Transactions_id' )->unsigned();
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
        Schema::dropIfExists('shipments_details');
    }
};
