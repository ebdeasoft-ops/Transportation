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
        Schema::create('order_tosuplliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('suplier_id')->index('order_tosuplliers_suplier_id_foreign');
            $table->unsignedBigInteger('user_id')->index('order_tosuplliers_user_id_foreign');
            $table->timestamps();
            $table->string('Limit_credit')->default('');
            $table->double('purchaseـamount', 8, 2)->default(0);
            $table->double('added_value', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_tosuplliers');
    }
};
