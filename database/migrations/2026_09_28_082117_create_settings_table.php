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
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('mobile');
            $table->bigInteger('trn')->comment('Tax Registration Number');
            $table->bigInteger('crn')->comment('Commercial Registration Number');
            $table->string('street_name');
            $table->integer('building_number');
            $table->integer('scander_number')->default(1212);
            $table->integer('plot_identification');
            $table->string('region');
            $table->string('city');
            $table->integer('postal_number');
            $table->string('egs_serial_number');
            $table->text('business_category')->nullable();
            $table->string('common_name');
            $table->string('organization_unit_name');
            $table->string('organization_name');
            $table->string('country_name')->default('SA')->comment('Country code');
            $table->string('registered_address');
            $table->string('otp');
            $table->string('email_address');
            $table->enum('invoice_type', ['1100', '0100', '1000']);
            $table->boolean('is_production')->default(false);
            $table->longText('cnf')->nullable();
            $table->longText('private_key')->nullable();
            $table->longText('public_key')->nullable();
            $table->longText('csr_request')->nullable();
            $table->longText('certificate')->nullable();
            $table->string('secret')->nullable();
            $table->string('csid')->nullable();
            $table->longText('production_certificate')->nullable();
            $table->string('production_secret')->nullable();
            $table->string('production_csid')->nullable();
            $table->unsignedBigInteger('company_id')->index('settings_company_id_foreign');
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
        Schema::dropIfExists('settings');
    }
};
