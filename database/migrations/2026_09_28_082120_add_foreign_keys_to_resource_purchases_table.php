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
        Schema::table('resource_purchases', function (Blueprint $table) {
            $table->foreign(['branchs_id'])->references(['id'])->on('branchs')->onDelete('CASCADE');
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
        Schema::table('resource_purchases', function (Blueprint $table) {
            $table->dropForeign('resource_purchases_branchs_id_foreign');
            $table->dropForeign('resource_purchases_suplier_id_foreign');
        });
    }
};
