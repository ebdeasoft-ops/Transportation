<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class truck_trip extends Model
{
    use HasFactory;
    protected $table = 'truck_trips';

    const LOADED   = 1;   // محمّلة
    const UNLOADED = 2;   // تم التفريغ

    /** مناطق المملكة العربية السعودية الإدارية الـ 13 (القائمة الافتراضية أول مرة) */
    const REGIONS = [
        'الرياض', 'مكة المكرمة', 'المدينة المنورة', 'القصيم', 'المنطقة الشرقية', 'عسير', 'تبوك',
        'حائل', 'الحدود الشمالية', 'جازان', 'نجران', 'الباحة', 'الجوف',
    ];

    /**
     * المناطق من جدول truck_regions (بتتضاف وتتعدل من شاشة «المناطق»)
     * $all = true: كل المناطق حتى المخفية (للتحقق من البيانات القديمة)
     */
    public static function regions($all = false)
    {
        static $cache = [];
        $k = $all ? 'all' : 'active';
        if (isset($cache[$k])) return $cache[$k];
        try {
            \App\Support\AppSchema::regions();
            $q = \Illuminate\Support\Facades\DB::table('truck_regions')->orderBy('sort')->orderBy('id');
            if (!$all) $q->where('active', 1);
            $list = $q->pluck('name')->all();
        } catch (\Throwable $e) {
            report($e);
            $list = [];
        }
        return $cache[$k] = $list ?: self::REGIONS;
    }

    /** قاعدة تحقق للمنطقة (من المناطق المسجلة) */
    public static function regionRule($required = true)
    {
        return [$required ? 'required' : 'nullable', \Illuminate\Validation\Rule::in(self::regions(true))];
    }

    /** ملكية الشاحنة */
    const OWNERSHIP = [
        'own'      => 'خاص بالمؤسسة',
        'external' => 'إيجار خارجي',
    ];

    public static function ownershipLabel($v)
    {
        return self::OWNERSHIP[$v] ?? 'غير محدد';
    }

    protected $fillable = [
        'truck_id', 'driver_id', 'driver_name', 'from_region', 'from_city', 'to_region', 'to_city',
        'load_type', 'load_weight', 'customer_name', 'waybill_no',
        'loading_at', 'expected_unloading_at', 'unloaded_at', 'status',
        'notes', 'unload_notes', 'user_id', 'unloaded_by', 'branchs_id',
        'ownership', 'invoice_number', 'reference_no', 'price', 'attachment', 'customer_account_id', 'unload_attachment', 'transport_invoice_id',
    ];

    protected $casts = [
        'loading_at' => 'datetime',
        'expected_unloading_at' => 'datetime',
        'unloaded_at' => 'datetime',
    ];

    public function truck()  { return $this->belongsTo(waybill_truck::class, 'truck_id'); }
    public function driver() { return $this->belongsTo(waybill_driver::class, 'driver_id'); }
    public function user()   { return $this->belongsTo(User::class, 'user_id'); }
    public function transport_invoice() { return $this->belongsTo(transport_invoice::class, 'transport_invoice_id'); }

    /** الوقت الحالي بتوقيت السعودية (التطبيق شغال UTC) */
    public static function nowLocal()
    {
        return \Carbon\Carbon::now('Asia/Riyadh');
    }

    /** متأخرة؟ (محمّلة وعدّى معاد التنزيل المتوقع) */
    public function getIsOverdueAttribute()
    {
        return $this->status == self::LOADED && $this->expected_unloading_at
            && $this->expected_unloading_at->format('Y-m-d H:i:s') < self::nowLocal()->format('Y-m-d H:i:s');
    }
}
