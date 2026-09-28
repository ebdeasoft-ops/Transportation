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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('email');
            $table->string('phone');
            $table->string('department');
            $table->double('salary', 8, 2)->default(0);
            $table->string('nationality');
            $table->integer('old')->default(0);
            $table->string('sex')->default('Male');
            $table->timestamps();
            $table->bigInteger('personal_identification')->default(0);
            $table->decimal('housing_allowance', 10)->nullable()->default(0);
            $table->decimal('transportation_allowance', 10)->nullable()->default(0);
            $table->decimal('other_allowances', 10)->nullable()->default(0);
            $table->double('total_leave_days')->default(21);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
