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
        Schema::table('credittransactions', function (Blueprint $table) {
            $table->foreign(['branchs_id'])->references(['id'])->on('branchs')->onDelete('CASCADE');
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
        Schema::table('credittransactions', function (Blueprint $table) {
            $table->dropForeign('credittransactions_branchs_id_foreign');
            $table->dropForeign('credittransactions_user_id_foreign');
        });
    }
};
