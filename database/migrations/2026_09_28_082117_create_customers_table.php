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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->default('');
            $table->string('comp_name')->default('');
            $table->timestamps();
            $table->string('address')->nullable();
            $table->string('notes')->default('لا يوجد ملاحظات');
            $table->decimal('Limit_credit')->default(10000);
            $table->decimal('Balance')->default(0);
            $table->integer('grace_period_in_days')->default(30);
            $table->bigInteger('tax_no')->nullable();
            $table->bigInteger('mantob_account_id')->nullable();
            $table->double('opeing_blance')->default(0);
            $table->text('postcode')->nullable();
            $table->text('sub_city')->nullable();
            $table->text('street_name')->nullable();
            $table->text('building_number')->nullable();
            $table->text('plot_identification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
