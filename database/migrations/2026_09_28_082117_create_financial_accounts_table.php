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
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->comment('جدول الشجرة المحاسبية العامة');
            $table->bigInteger('id', true);
            $table->string('name', 225);
            $table->text('name_en')->nullable();
            $table->integer('account_type');
            $table->boolean('is_parent')->default(false);
            $table->bigInteger('parent_account_number')->nullable();
            $table->bigInteger('account_number');
            $table->tinyInteger('start_balance_status')->comment('e 1-credit -2 debit 3-balanced');
            $table->decimal('start_balance', 10)->comment('دائن او مدين او متزن اول المدة');
            $table->decimal('current_balance', 15)->default(0);
            $table->bigInteger('other_table_FK')->nullable();
            $table->string('notes', 225)->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->boolean('active')->default(true)->comment('هل مفعل');
            $table->integer('com_code');
            $table->date('date');
            $table->bigInteger('orginal_id')->nullable();
            $table->integer('orginal_type')->nullable();
            $table->bigInteger('orginal_supplier')->nullable();
            $table->double('debtor_opening')->default(0);
            $table->double('creditor_opening')->default(0);
            $table->double('creditor_current', 15, 2)->default(0);
            $table->double('debtor_current', 15, 2)->default(0);
            $table->double('creditor_end')->default(0);
            $table->double('debtor_end')->default(0);
            $table->integer('branchs_id')->nullable();
            $table->integer('candidate_branch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_accounts');
    }
};
