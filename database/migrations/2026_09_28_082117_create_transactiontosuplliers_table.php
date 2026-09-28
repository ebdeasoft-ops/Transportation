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
        Schema::create('transactiontosuplliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('transactiontosuplliers_user_id_foreign');
            $table->unsignedBigInteger('suplier_id');
            $table->double('paidـamount', 8, 2)->default(0);
            $table->string('Pay_Method_Name');
            $table->timestamps();
            $table->unsignedBigInteger('branchs_id')->index('transactiontosuplliers_branchs_id_foreign');
            $table->string('note')->default('-');
            $table->double('currentblance')->default(0);
            $table->text('attachments')->nullable();
            $table->integer('orginal_type')->default(0);
            $table->bigInteger('orginal_id')->nullable();
            $table->bigInteger('dely_record')->default(0);
            $table->integer('debtor')->default(0);
            $table->integer('creditor')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactiontosuplliers');
    }
};
