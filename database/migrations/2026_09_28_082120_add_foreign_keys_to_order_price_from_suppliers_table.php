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
        Schema::table('order_price_from_suppliers', function (Blueprint $table) {
            $table->foreign(['suplier_id'])->references(['id'])->on('suplliers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_price_from_suppliers', function (Blueprint $table) {
            $table->dropForeign('order_price_from_suppliers_suplier_id_foreign');
        });
    }
};
