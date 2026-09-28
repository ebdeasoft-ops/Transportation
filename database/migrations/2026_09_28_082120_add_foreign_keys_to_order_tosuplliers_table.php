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
        Schema::table('order_tosuplliers', function (Blueprint $table) {
            $table->foreign(['suplier_id'])->references(['id'])->on('suplliers')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_tosuplliers', function (Blueprint $table) {
            $table->dropForeign('order_tosuplliers_suplier_id_foreign');
            $table->dropForeign('order_tosuplliers_user_id_foreign');
        });
    }
};
