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
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->foreign(['unloaded_by'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['branchs_id'])->references(['id'])->on('branchs')->onDelete('SET NULL');
            $table->foreign(['truck_id'])->references(['id'])->on('waybill_trucks')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['driver_id'])->references(['id'])->on('waybill_drivers')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->dropForeign('truck_trips_unloaded_by_foreign');
            $table->dropForeign('truck_trips_branchs_id_foreign');
            $table->dropForeign('truck_trips_truck_id_foreign');
            $table->dropForeign('truck_trips_user_id_foreign');
            $table->dropForeign('truck_trips_driver_id_foreign');
        });
    }
};
