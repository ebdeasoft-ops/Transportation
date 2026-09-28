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
        Schema::create('convertcashbox_to_banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('from_user_id')->default(1);
            $table->double('amount')->unsigned()->default(0);
            $table->bigInteger('branchs_id')->default(1);
            $table->timestamps();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convertcashbox_to_banks');
    }
};
