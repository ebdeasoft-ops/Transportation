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
        Schema::table('waybill_items', function (Blueprint $table) {
            $table->foreign(['waybill_id'])->references(['id'])->on('waybills')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waybill_items', function (Blueprint $table) {
            $table->dropForeign('waybill_items_waybill_id_foreign');
        });
    }
};
