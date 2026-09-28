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
        Schema::table('waybills', function (Blueprint $table) {
            $table->foreign(['truck_id'])->references(['id'])->on('waybill_trucks')->onDelete('SET NULL');
            $table->foreign(['branchs_id'])->references(['id'])->on('branchs')->onDelete('SET NULL');
            $table->foreign(['driver_id'])->references(['id'])->on('waybill_drivers')->onDelete('SET NULL');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['customer_id'])->references(['id'])->on('waybill_customers')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropForeign('waybills_truck_id_foreign');
            $table->dropForeign('waybills_branchs_id_foreign');
            $table->dropForeign('waybills_driver_id_foreign');
            $table->dropForeign('waybills_user_id_foreign');
            $table->dropForeign('waybills_customer_id_foreign');
        });
    }
};
