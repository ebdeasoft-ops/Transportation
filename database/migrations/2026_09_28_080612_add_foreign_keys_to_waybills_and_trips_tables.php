<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. waybill_drivers
        Schema::table('waybill_drivers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 2. waybill_trucks
        Schema::table('waybill_trucks', function (Blueprint $table) {
            $table->foreign('default_driver_id')->references('id')->on('waybill_drivers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. waybill_customers
        Schema::table('waybill_customers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 4. waybills
        Schema::table('waybills', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('waybill_customers')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('waybill_drivers')->onDelete('set null');
            $table->foreign('truck_id')->references('id')->on('waybill_trucks')->onDelete('set null');
            $table->foreign('branchs_id')->references('id')->on('branchs')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 5. waybill_items
        Schema::table('waybill_items', function (Blueprint $table) {
            $table->foreign('waybill_id')->references('id')->on('waybills')->onDelete('cascade');
        });

        // 6. truck_trips
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->foreign('truck_id')->references('id')->on('waybill_trucks')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('waybill_drivers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('unloaded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('branchs_id')->references('id')->on('branchs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->dropForeign(['truck_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['unloaded_by']);
            $table->dropForeign(['branchs_id']);
        });

        Schema::table('waybill_items', function (Blueprint $table) {
            $table->dropForeign(['waybill_id']);
        });

        Schema::table('waybills', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['truck_id']);
            $table->dropForeign(['branchs_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('waybill_customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('waybill_trucks', function (Blueprint $table) {
            $table->dropForeign(['default_driver_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('waybill_drivers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
