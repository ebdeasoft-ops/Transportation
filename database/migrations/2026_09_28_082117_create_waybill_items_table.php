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
        Schema::create('waybill_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('waybill_id')->index();
            $table->string('sender_name')->nullable();
            $table->double('fare')->default(0);
            $table->string('receiver_name')->nullable();
            $table->string('goods_type')->nullable();
            $table->string('goods_weight', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waybill_items');
    }
};
