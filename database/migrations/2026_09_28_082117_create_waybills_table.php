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
        Schema::create('waybills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('waybill_no', 50)->unique();
            $table->date('date')->nullable();
            $table->string('date_hijri', 50)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->index('waybills_customer_id_foreign');
            $table->string('customer_name')->nullable();
            $table->string('destination_city')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable()->index('waybills_driver_id_foreign');
            $table->string('driver_name')->nullable();
            $table->string('driver_license_number', 100)->nullable();
            $table->string('driver_license_issue_date', 50)->nullable();
            $table->unsignedBigInteger('truck_id')->nullable()->index('waybills_truck_id_foreign');
            $table->string('owner_name')->nullable();
            $table->string('plate_number', 100)->nullable();
            $table->string('plate_region', 150)->nullable();
            $table->string('operation_license_number', 100)->nullable();
            $table->string('operation_license_issuer', 150)->nullable();
            $table->string('truck_type', 150)->nullable();
            $table->string('total_load', 100)->nullable();
            $table->date('departure_date')->nullable();
            $table->string('fare_paid_by')->nullable();
            $table->string('delivery_within')->nullable();
            $table->text('notes')->nullable();
            $table->double('total_fare')->default(0);
            $table->unsignedBigInteger('branchs_id')->nullable()->index('waybills_branchs_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('waybills_user_id_foreign');
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
        Schema::dropIfExists('waybills');
    }
};
