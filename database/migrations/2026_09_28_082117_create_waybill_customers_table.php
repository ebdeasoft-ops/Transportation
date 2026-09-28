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
        Schema::create('waybill_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('address')->nullable();
            $table->string('tax_no', 50)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('waybill_customers_user_id_foreign');
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
        Schema::dropIfExists('waybill_customers');
    }
};
