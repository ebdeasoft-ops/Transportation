<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بيضيف الجداول/الأعمدة الناقصة لوحده (بدل ما تشغّل migrate أو ملفات SQL بإيدك)
 * - فواتير النقل الضريبية
 * - الموارد البشرية (الحضور - الإجازات - العقود - العهد - نهاية الخدمة - إعدادات HR)
 * بيشتغل مرة واحدة لكل طلب وبيتشيك بسرعة لو كل حاجة موجودة.
 */
class AppSchema
{
    private static $transportDone = false;
    private static $hrDone = false;

    private static $regionsDone = false;

    // ================= المناطق =================
    public static function regions()
    {
        if (self::$regionsDone) return;
        self::$regionsDone = true;
        if (Schema::hasTable('truck_regions')) return;
        Schema::create('truck_regions', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100)->unique();
            $t->integer('sort')->default(0);
            $t->tinyInteger('active')->default(1);
            $t->timestamps();
        });
        $now = now();
        $rows = [];
        foreach (\App\Models\truck_trip::REGIONS as $i => $r) {
            $rows[] = ['name' => $r, 'sort' => $i + 1, 'active' => 1, 'created_at' => $now, 'updated_at' => $now];
        }
        \Illuminate\Support\Facades\DB::table('truck_regions')->insert($rows);
    }

    private static $expensesDone = false;

    // ================= مصروفات الشاحنات =================
    public static function truckExpenses()
    {
        if (self::$expensesDone) return;
        self::$expensesDone = true;
        try {
            if (!Schema::hasTable('truck_expenses')) {
                Schema::create('truck_expenses', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('truck_id')->index();
                    $t->unsignedBigInteger('truck_trip_id')->nullable();
                    $t->date('expense_date')->index();
                    $t->string('type', 50);
                    $t->decimal('amount', 15, 2)->default(0);
                    $t->string('description')->nullable();
                    $t->string('vendor')->nullable();
                    $t->string('attachment')->nullable();
                    $t->unsignedBigInteger('expense_account_id')->nullable();
                    $t->unsignedBigInteger('pay_account_id')->nullable();
                    $t->tinyInteger('posted')->default(0);
                    $t->unsignedBigInteger('user_id')->nullable();
                    $t->unsignedBigInteger('branchs_id')->nullable();
                    $t->timestamps();
                });
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ================= فواتير النقل =================
    public static function transport()
    {
        if (self::$transportDone) return;
        self::$transportDone = true;
        try {
            if (!Schema::hasTable('transport_invoices')) {
                Schema::create('transport_invoices', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('invoice_no')->unique();
                    $t->unsignedBigInteger('customer_account_id')->nullable();
                    $t->string('customer_name')->nullable();
                    $t->string('customer_vat', 30)->nullable();
                    $t->string('customer_cr', 30)->nullable();
                    $t->string('customer_address')->nullable();
                    $t->string('customer_phone', 30)->nullable();
                    $t->dateTime('issue_date');
                    $t->date('supply_from')->nullable();
                    $t->date('supply_to')->nullable();
                    $t->decimal('subtotal', 15, 2)->default(0);
                    $t->decimal('discount', 15, 2)->default(0);
                    $t->decimal('taxable', 15, 2)->default(0);
                    $t->decimal('vat_rate', 6, 4)->default(0.15);
                    $t->decimal('vat_amount', 15, 2)->default(0);
                    $t->decimal('total', 15, 2)->default(0);
                    $t->tinyInteger('prices_include_vat')->default(0);
                    $t->string('po_number', 100)->nullable();
                    $t->text('notes')->nullable();
                    $t->tinyInteger('status')->default(1); // 1 سارية - 2 ملغاة
                    $t->tinyInteger('posted')->default(0);  // اترحّلت للحسابات؟
                    $t->dateTime('cancelled_at')->nullable();
                    $t->string('cancel_reason')->nullable();
                    $t->unsignedBigInteger('cancelled_by')->nullable();
                    $t->unsignedBigInteger('user_id')->nullable();
                    $t->unsignedBigInteger('branchs_id')->nullable();
                    $t->timestamps();
                    $t->index(['customer_account_id', 'status']);
                    $t->index('issue_date');
                });
            }
            if (!Schema::hasTable('transport_invoice_items')) {
                Schema::create('transport_invoice_items', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('transport_invoice_id');
                    $t->unsignedBigInteger('truck_trip_id')->nullable();
                    $t->string('description');
                    $t->dateTime('trip_date')->nullable();
                    $t->string('plate_number', 100)->nullable();
                    $t->string('route_from')->nullable();
                    $t->string('route_to')->nullable();
                    $t->string('waybill_no', 100)->nullable();
                    $t->decimal('qty', 12, 2)->default(1);
                    $t->decimal('unit_price', 15, 2)->default(0);
                    $t->decimal('amount', 15, 2)->default(0);  // قبل الضريبة
                    $t->timestamps();
                    $t->index('transport_invoice_id');
                    $t->index('truck_trip_id');
                });
            }
            // نوع الضريبة: S = أساسية 15% | Z = نسبة صفرية (+ سبب الإعفاء)
            if (!Schema::hasColumn('transport_invoices', 'vat_category')) {
                Schema::table('transport_invoices', function (Blueprint $t) {
                    $t->string('vat_category', 2)->default('S');
                    $t->string('vat_exempt_code', 30)->nullable();
                    $t->string('vat_exempt_reason')->nullable();
                });
            }
            if (Schema::hasTable('truck_trips') && !Schema::hasColumn('truck_trips', 'transport_invoice_id')) {
                Schema::table('truck_trips', function (Blueprint $t) {
                    $t->unsignedBigInteger('transport_invoice_id')->nullable()->index();
                });
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ================= الموارد البشرية =================
    public static function hr()
    {
        if (self::$hrDone) return;
        self::$hrDone = true;
        try {
            if (Schema::hasTable('employees')) {
                foreach (['housing_allowance', 'transportation_allowance', 'other_allowances'] as $c) {
                    if (!Schema::hasColumn('employees', $c)) {
                        Schema::table('employees', function (Blueprint $t) use ($c) { $t->decimal($c, 10, 2)->nullable()->default(0); });
                    }
                }
                if (!Schema::hasColumn('employees', 'total_leave_days')) {
                    Schema::table('employees', function (Blueprint $t) { $t->double('total_leave_days')->default(21); });
                }
            }
            if (!Schema::hasTable('attendances')) {
                Schema::create('attendances', function (Blueprint $t) {
                    $t->id();
                    $t->string('type', 20)->default('normal');
                    $t->unsignedBigInteger('employee_id')->index();
                    $t->date('date');
                    $t->time('check_in')->nullable();
                    $t->time('check_out')->nullable();
                    $t->string('status')->default('present');
                    $t->decimal('discount_amount', 10, 2)->default(0);
                    $t->timestamps();
                });
            } elseif (!Schema::hasColumn('attendances', 'discount_amount')) {
                Schema::table('attendances', function (Blueprint $t) { $t->decimal('discount_amount', 10, 2)->default(0); });
            }
            if (!Schema::hasTable('leaves')) {
                Schema::create('leaves', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('employee_id')->index();
                    $t->enum('leave_type', ['annual', 'casual', 'sick', 'unpaid', 'unauthorized'])->default('annual');
                    $t->date('start_date');
                    $t->date('end_date');
                    $t->integer('days_count');
                    $t->decimal('deduction_amount', 10, 2)->default(0);
                    $t->text('reason')->nullable();
                    $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                    $t->timestamps();
                });
            }
            if (!Schema::hasTable('contracts')) {
                Schema::create('contracts', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('employee_id')->index();
                    $t->string('contract_type');
                    $t->date('start_date');
                    $t->date('end_date');
                    $t->decimal('basic_salary', 10, 2);
                    $t->date('iqama_expiry_date')->nullable();
                    $t->date('work_permit_expiry_date')->nullable();
                    $t->timestamps();
                });
            }
            if (!Schema::hasTable('custodies')) {
                Schema::create('custodies', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('employee_id')->index();
                    $t->string('item_name');
                    $t->string('serial_number')->nullable();
                    $t->date('delivery_date');
                    $t->date('return_date')->nullable();
                    $t->enum('status', ['delivered', 'returned'])->default('delivered');
                    $t->text('notes')->nullable();
                    $t->timestamps();
                });
            }
            if (!Schema::hasTable('end_of_services')) {
                Schema::create('end_of_services', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('employee_id')->index();
                    $t->date('join_date');
                    $t->date('end_date');
                    $t->decimal('service_years', 8, 2);
                    $t->decimal('basic_salary', 10, 2);
                    $t->enum('reason', ['resignation', 'termination']);
                    $t->decimal('reward_amount', 10, 2);
                    $t->text('notes')->nullable();
                    $t->timestamps();
                });
            }
            if (!Schema::hasTable('hr_settings')) {
                Schema::create('hr_settings', function (Blueprint $t) {
                    $t->id();
                    $t->time('official_check_in')->default('08:00:00');
                    $t->time('official_check_out')->default('16:00:00');
                    $t->integer('grace_period_minutes')->default(15);
                    $t->string('weekend_days')->default('friday');
                    $t->double('overtime_hour_rate')->default(0);
                    $t->timestamps();
                });
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
