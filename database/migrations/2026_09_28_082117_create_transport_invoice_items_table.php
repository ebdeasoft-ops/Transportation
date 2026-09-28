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
        Schema::create('transport_invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transport_invoice_id')->index();
            $table->unsignedBigInteger('truck_trip_id')->nullable()->index();
            $table->string('description');
            $table->dateTime('trip_date')->nullable();
            $table->string('plate_number', 100)->nullable();
            $table->string('route_from')->nullable();
            $table->string('route_to')->nullable();
            $table->string('waybill_no', 100)->nullable();
            $table->decimal('qty', 12)->default(1);
            $table->decimal('unit_price', 15)->default(0);
            $table->decimal('amount', 15)->default(0);
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
        Schema::dropIfExists('transport_invoice_items');
    }
};
