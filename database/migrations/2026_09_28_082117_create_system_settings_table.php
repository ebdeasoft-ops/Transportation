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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ar')->default('empty');
            $table->string('name_en')->default('empty');
            $table->string('SR')->default('empty');
            $table->string('Tax')->default('empty');
            $table->string('logo')->default('empty');
            $table->string('address_ar')->default('empty');
            $table->string('address_en')->default('empty');
            $table->timestamps();
            $table->double('serviceCost', 8, 2)->default(0);
            $table->double('deliveryCost', 8, 2)->default(0);
            $table->text('descriptionarbic')->nullable();
            $table->text('descriptionenglish')->nullable();
            $table->double('discount_on_invoice')->default(100);
            $table->text('bank_acount_iban')->nullable();
            $table->text('bank_acount_number')->nullable();
            $table->text('bankname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};
