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
        Schema::create('waybill_trucks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plate_number', 100);
            $table->string('plate_region', 150)->nullable();
            $table->string('owner_name')->nullable();
            $table->string('operation_license_number', 100)->nullable();
            $table->string('operation_license_issuer', 150)->nullable();
            $table->string('truck_type', 150)->nullable();
            $table->string('total_load', 100)->nullable();
            $table->unsignedBigInteger('default_driver_id')->nullable()->index('waybill_trucks_default_driver_id_foreign');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('waybill_trucks_user_id_foreign');
            $table->timestamps();
            $table->string('current_region', 100)->nullable();
            $table->string('current_city', 150)->nullable();
            $table->string('ownership', 20)->nullable();
            $table->string('insurance_company', 150)->nullable();
            $table->string('insurance_policy_no', 100)->nullable();
            $table->string('istimara_no', 100)->nullable();
            $table->date('insurance_start')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('istimara_expiry')->nullable();
            $table->double('insurance_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waybill_trucks');
    }
};
