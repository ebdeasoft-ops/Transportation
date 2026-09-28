<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بوليصة الشحن - السائقين / الشاحنات / العملاء / البوليصات / أصناف البوليصة
 * تشغيل هذا الملف فقط:
 * php artisan migrate --path=database/migrations/2026_09_24_000001_create_waybill_tables.php
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('waybill_drivers')) {
            Schema::create('waybill_drivers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 50)->nullable();
                $table->string('id_number', 50)->nullable();          // رقم الهوية / الإقامة
                $table->string('license_number', 100)->nullable();    // رقم رخصة القيادة
                $table->string('license_issue_date', 50)->nullable(); // تاريخ صدورها
                $table->string('nationality', 100)->nullable();
                $table->text('notes')->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('waybill_trucks')) {
            Schema::create('waybill_trucks', function (Blueprint $table) {
                $table->id();
                $table->string('plate_number', 100);                    // رقم السيارة
                $table->string('plate_region', 150)->nullable();        // جهتها
                $table->string('owner_name')->nullable();               // اسم مالك السيارة
                $table->string('operation_license_number', 100)->nullable(); // رقم رخصة التشغيل
                $table->string('operation_license_issuer', 150)->nullable(); // جهة صدورها
                $table->string('truck_type', 150)->nullable();          // نوع السيارة
                $table->string('total_load', 100)->nullable();          // الحمولة الإجمالية
                $table->bigInteger('default_driver_id')->unsigned()->nullable();
                $table->text('notes')->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('waybill_customers')) {
            Schema::create('waybill_customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 50)->nullable();
                $table->string('city', 150)->nullable();
                $table->string('address')->nullable();
                $table->string('tax_no', 50)->nullable();
                $table->text('notes')->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('waybills')) {
            Schema::create('waybills', function (Blueprint $table) {
                $table->id();
                $table->string('waybill_no', 50)->unique();
                $table->date('date')->nullable();
                $table->string('date_hijri', 50)->nullable();

                $table->bigInteger('customer_id')->unsigned()->nullable();
                $table->string('customer_name')->nullable();
                $table->string('destination_city')->nullable();

                $table->bigInteger('driver_id')->unsigned()->nullable();
                $table->string('driver_name')->nullable();
                $table->string('driver_license_number', 100)->nullable();
                $table->string('driver_license_issue_date', 50)->nullable();

                $table->bigInteger('truck_id')->unsigned()->nullable();
                $table->string('owner_name')->nullable();
                $table->string('plate_number', 100)->nullable();
                $table->string('plate_region', 150)->nullable();
                $table->string('operation_license_number', 100)->nullable();
                $table->string('operation_license_issuer', 150)->nullable();
                $table->string('truck_type', 150)->nullable();
                $table->string('total_load', 100)->nullable();

                $table->date('departure_date')->nullable();
                $table->string('fare_paid_by')->nullable();      // تدفع الأجرة من قبل
                $table->string('delivery_within')->nullable();   // يجب إيصال البضاعة خلال
                $table->text('notes')->nullable();

                $table->double('total_fare')->default(0);
                $table->bigInteger('branchs_id')->unsigned()->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('waybill_items')) {
            Schema::create('waybill_items', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('waybill_id')->unsigned();
                $table->string('sender_name')->nullable();     // الراسل - الاسم
                $table->double('fare')->default(0);            // الراسل - الأجرة
                $table->string('receiver_name')->nullable();   // اسم المرسل إليه
                $table->string('goods_type')->nullable();      // نوع البضاعة
                $table->string('goods_weight', 100)->nullable(); // وزن البضاعة
                $table->timestamps();
                $table->index('waybill_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('waybill_items');
        Schema::dropIfExists('waybills');
        Schema::dropIfExists('waybill_customers');
        Schema::dropIfExists('waybill_trucks');
        Schema::dropIfExists('waybill_drivers');
    }
};
