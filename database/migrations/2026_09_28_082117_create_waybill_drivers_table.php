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
        Schema::create('waybill_drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('id_number', 50)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->string('license_issue_date', 50)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('waybill_drivers_user_id_foreign');
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
        Schema::dropIfExists('waybill_drivers');
    }
};
