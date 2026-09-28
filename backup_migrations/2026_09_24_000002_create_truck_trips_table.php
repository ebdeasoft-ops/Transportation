<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * حركة الشاحنات (الأحمال)
 * php artisan migrate --path=database/migrations/2026_09_24_000002_create_truck_trips_table.php
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('waybill_trucks') && !Schema::hasColumn('waybill_trucks', 'current_region')) {
            Schema::table('waybill_trucks', function (Blueprint $table) {
                $table->string('current_region', 100)->nullable();   // مكان الشاحنة الحالي (لما تكون فاضية)
                $table->string('current_city', 150)->nullable();
            });
        }

        if (!Schema::hasTable('truck_trips')) {
            Schema::create('truck_trips', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('truck_id')->unsigned();
                $table->bigInteger('driver_id')->unsigned()->nullable();
                $table->string('driver_name')->nullable();
                $table->string('from_region', 100);
                $table->string('from_city', 150)->nullable();
                $table->string('to_region', 100);
                $table->string('to_city', 150)->nullable();
                $table->string('load_type')->nullable();          // نوع التحميل
                $table->string('load_weight', 100)->nullable();
                $table->string('customer_name')->nullable();
                $table->string('waybill_no', 50)->nullable();
                $table->dateTime('loading_at')->nullable();        // معاد التحميل
                $table->dateTime('expected_unloading_at')->nullable(); // معاد التنزيل المتوقع
                $table->dateTime('unloaded_at')->nullable();       // معاد التنزيل الفعلي
                $table->tinyInteger('status')->default(1);         // 1 = محمّلة ، 2 = تم التفريغ
                $table->text('notes')->nullable();
                $table->text('unload_notes')->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->bigInteger('unloaded_by')->unsigned()->nullable();
                $table->bigInteger('branchs_id')->unsigned()->nullable();
                $table->timestamps();
                $table->index(['truck_id', 'status']);
                $table->index('loading_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('truck_trips');
        if (Schema::hasTable('waybill_trucks') && Schema::hasColumn('waybill_trucks', 'current_region')) {
            Schema::table('waybill_trucks', function (Blueprint $table) {
                $table->dropColumn(['current_region', 'current_city']);
            });
        }
    }
};
