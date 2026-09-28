<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ملكية الشاحنة (مؤسسة / إيجار خارجي) + بيانات الفاتورة والمرفق للحمولة
 * php artisan migrate --path=database/migrations/2026_09_26_000003_add_ownership_and_invoice_to_trips.php
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('waybill_trucks', 'ownership')) {
            Schema::table('waybill_trucks', function (Blueprint $table) {
                $table->string('ownership', 20)->nullable();   // own = خاص بالمؤسسة ، external = إيجار خارجي
            });
        }
        Schema::table('truck_trips', function (Blueprint $table) {
            if (!Schema::hasColumn('truck_trips', 'ownership'))      $table->string('ownership', 20)->nullable();
            if (!Schema::hasColumn('truck_trips', 'invoice_number')) $table->string('invoice_number', 100)->nullable();
            if (!Schema::hasColumn('truck_trips', 'reference_no'))   $table->string('reference_no', 100)->nullable();
            if (!Schema::hasColumn('truck_trips', 'price'))          $table->double('price')->default(0);
            if (!Schema::hasColumn('truck_trips', 'attachment'))     $table->string('attachment')->nullable();
        });
    }

    public function down()
    {
        Schema::table('truck_trips', function (Blueprint $table) {
            $table->dropColumn(['ownership', 'invoice_number', 'reference_no', 'price', 'attachment']);
        });
        Schema::table('waybill_trucks', function (Blueprint $table) {
            $table->dropColumn('ownership');
        });
    }
};
