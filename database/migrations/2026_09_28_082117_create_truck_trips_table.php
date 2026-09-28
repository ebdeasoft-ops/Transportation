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
        Schema::create('truck_trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('driver_id')->nullable()->index('truck_trips_driver_id_foreign');
            $table->string('driver_name')->nullable();
            $table->string('from_region', 100);
            $table->string('from_city', 150)->nullable();
            $table->string('to_region', 100);
            $table->string('to_city', 150)->nullable();
            $table->string('load_type')->nullable();
            $table->string('load_weight', 100)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('waybill_no', 50)->nullable();
            $table->dateTime('loading_at')->nullable()->index();
            $table->dateTime('expected_unloading_at')->nullable();
            $table->dateTime('unloaded_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->text('unload_notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('truck_trips_user_id_foreign');
            $table->unsignedBigInteger('unloaded_by')->nullable()->index('truck_trips_unloaded_by_foreign');
            $table->unsignedBigInteger('branchs_id')->nullable()->index('truck_trips_branchs_id_foreign');
            $table->timestamps();
            $table->string('ownership', 20)->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->double('price')->default(0);
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('customer_account_id')->nullable();
            $table->unsignedBigInteger('transport_invoice_id')->nullable()->index();
            $table->string('unload_attachment')->nullable();

            $table->index(['truck_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('truck_trips');
    }
};
