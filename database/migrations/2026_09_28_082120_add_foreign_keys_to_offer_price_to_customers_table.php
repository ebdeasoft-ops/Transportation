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
        Schema::table('offer_price_to_customers', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_price_to_customers', function (Blueprint $table) {
            $table->dropForeign('offer_price_to_customers_customer_id_foreign');
        });
    }
};
