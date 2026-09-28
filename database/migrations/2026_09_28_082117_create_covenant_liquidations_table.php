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
        Schema::create('covenant_liquidations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branchs_id');
            $table->unsignedBigInteger('user_id');
            $table->double('price_filtering')->unsigned()->default(0);
            $table->string('note_detaials', 1000);
            $table->timestamps();
            $table->integer('save')->default(0);
            $table->integer('status')->default(0);
            $table->integer('type')->default(1);
            $table->integer('owner_confirm')->default(0);
            $table->double('currentblance')->default(0);
            $table->integer('id_trasction')->default(0);
            $table->bigInteger('manager')->nullable();
            $table->integer('manager_check')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('covenant_liquidations');
    }
};
