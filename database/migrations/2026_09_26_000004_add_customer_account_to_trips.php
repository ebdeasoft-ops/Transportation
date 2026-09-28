<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ربط الحمولة بالعميل (من العملاء المسجلين: financial_accounts.orginal_type = 1)
 * php artisan migrate --path=database/migrations/2026_09_26_000004_add_customer_account_to_trips.php
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('truck_trips', 'customer_account_id')) {
            Schema::table('truck_trips', function (Blueprint $table) {
                $table->bigInteger('customer_account_id')->unsigned()->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->dropColumn('customer_account_id');
        });
    }
};
