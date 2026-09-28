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
        Schema::create('expenses_reasons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('expenses_reason')->default('مصروفات نقدية غير مسجلة');
            $table->timestamps();
            $table->bigInteger('expensesAvt')->default(0);
            $table->string('expenses_reason_en', 250)->default('-');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses_reasons');
    }
};
