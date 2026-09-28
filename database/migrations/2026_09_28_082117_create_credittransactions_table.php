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
        Schema::create('credittransactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('credittransactions_user_id_foreign');
            $table->unsignedBigInteger('customer_id');
            $table->double('recive_amount', 8, 2)->default(0);
            $table->string('pay_method');
            $table->string('Pay_Method_Name');
            $table->timestamps();
            $table->unsignedBigInteger('branchs_id')->index('credittransactions_branchs_id_foreign');
            $table->string('note')->default('-');
            $table->double('currentblance', 20, 2)->default(0);
            $table->text('attachments')->nullable();
            $table->integer('orginal_type')->default(0);
            $table->integer('orginal_id')->nullable();
            $table->bigInteger('dely_record')->default(0);
            $table->double('debtor', 8, 2)->default(0);
            $table->double('creditor', 8, 2)->default(0);
            $table->integer('vat')->default(0);
            $table->text('name')->nullable();
            $table->text('tax')->nullable();
            $table->integer('decument_id')->default(0);
            $table->integer('type_decument')->default(1);
            $table->integer('save')->default(1);
            $table->bigInteger('parent_dely_record')->nullable();
            $table->integer('Opening_entry')->default(0);
            $table->integer('parent_Opening_entry')->default(0);
            $table->date('date_export')->nullable();
            $table->bigInteger('sent_serf_count')->default(0);
            $table->bigInteger('sent_abd_count')->default(0);
            $table->bigInteger('cost_center')->nullable()->default(0);
            $table->text('type')->nullable();
            $table->bigInteger('Transactions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credittransactions');
    }
};
