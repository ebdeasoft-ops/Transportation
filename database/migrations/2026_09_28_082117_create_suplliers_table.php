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
        Schema::create('suplliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->string('location');
            $table->string('email')->default('');
            $table->string('comp_name')->default('');
            $table->timestamps();
            $table->double('In_debt', 8, 2)->default(0);
            $table->bigInteger('TaxـNumber')->default(0);
            $table->bigInteger('mantob_account_id')->nullable();
            $table->double('opeing_blance')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suplliers');
    }
};
