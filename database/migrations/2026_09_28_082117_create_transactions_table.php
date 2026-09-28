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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branchs_id')->nullable()->index('transactions_branchs_id_foreign');
            $table->string('loading')->nullable();
            $table->string('unloading')->nullable();
            $table->double('price')->unsigned()->default(0);
            $table->string('truck_data')->nullable();
            $table->string('invoice_number', 50)->nullable();
            $table->string('Ext', 50)->nullable();
            $table->string('Daily', 50)->nullable();
            $table->string('mantob_name', 50)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->text('attachments')->nullable();
            $table->text('note')->nullable();
            $table->date('date')->nullable();
            $table->bigInteger('partner')->nullable();
            $table->text('attachments_2')->nullable();
            $table->text('note_detaials')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('Transactions')->default(0);
            $table->bigInteger('mantob')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
