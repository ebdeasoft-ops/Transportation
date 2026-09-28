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

    /** مناطق المملكة العربية السعودية الإدارية الـ 13 */
    const REGIONS = [
        'الرياض', 'مكة المكرمة', 'المدينة المنورة', 'القصيم', 'المنطقة الشرقية', 'عسير', 'تبوك',
        'حائل', 'الحدود الشمالية', 'جازان', 'نجران', 'الباحة', 'الجوف',
    ];

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
        'ownership', 'invoice_number', 'reference_no', 'price', 'attachment', 'customer_account_id', 'unload_attachment',
    ];

    protected $casts = [
        'loading_at' => 'datetime',
        'expected_unloading_at' => 'datetime',
        'unloaded_at' => 'datetime',
    ];

    public function truck()  { return $this->belongsTo(waybill_truck::class, 'truck_id'); }
    public function driver() { return $this->belongsTo(waybill_driver::class, 'driver_id'); }
    public function user()   { return $this->belongsTo(User::class, 'user_id'); }

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
