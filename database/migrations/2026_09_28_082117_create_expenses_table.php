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
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('expenses_user_id_foreign');
            $table->string('Pay_Method_Name');
            $table->string('Reasonforspendingmoney');
            $table->double('Theـamountـpaid', 8, 2)->default(0);
            $table->timestamps();
            $table->bigInteger('branchs_id')->default(1);
            $table->unsignedBigInteger('reasonId_id')->index('expenses_reasonid_id_foreign');
            $table->bigInteger('expensesAvt')->default(0);
            $table->text('notes')->nullable();
            $table->text('attachments')->nullable();
            $table->bigInteger('Transaction_id')->default(0);
            $table->integer('type')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
