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
        Schema::create('dliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('to_dlivery_id')->default(1);
            $table->double('blance')->default(0);
            $table->double('number_items')->default(0);
            $table->date('last_payment');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->bigInteger('supplier_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dliveries');
    }
};
