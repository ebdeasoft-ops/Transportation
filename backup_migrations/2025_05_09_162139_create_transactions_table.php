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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger( 'branchs_id' )->unsigned();
            $table->foreign('branchs_id')->references('id')->on('branchs')->onDelete('cascade');
            $table->string('loading');
            $table->string('unloading');
            $table->unsignedDouble  ('price')->default(0);
            $table->string('truck_data');
            $table->string('invoice_number', 50);
            $table->string('Ext', 50);
            $table->string('Daily', 50);
            $table->string('mantob_name', 50);
            $table->bigInteger( 'user_id' )->unsigned();
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
        Schema::dropIfExists('transactions');
    }
};
