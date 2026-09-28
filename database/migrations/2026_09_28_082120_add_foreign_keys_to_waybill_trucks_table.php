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
        Schema::table('waybill_trucks', function (Blueprint $table) {
            $table->foreign(['default_driver_id'])->references(['id'])->on('waybill_drivers')->onDelete('SET NULL');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waybill_trucks', function (Blueprint $table) {
            $table->dropForeign('waybill_trucks_default_driver_id_foreign');
            $table->dropForeign('waybill_trucks_user_id_foreign');
        });
    }
};
