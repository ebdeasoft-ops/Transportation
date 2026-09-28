<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waybill_truck extends Model
{
    use HasFactory;
    protected $table = 'waybill_trucks';
    protected $fillable = ['plate_number','plate_region','owner_name','operation_license_number','operation_license_issuer','truck_type','total_load','default_driver_id','notes','user_id','current_region','current_city','ownership',
        'insurance_company','insurance_policy_no','insurance_start','insurance_expiry','insurance_value',
        'istimara_no','istimara_expiry'];

    protected $casts = [
        'insurance_start'  => 'date',
        'insurance_expiry' => 'date',
        'istimara_expiry'  => 'date',
    ];

    /** حالة الوثيقة: expired منتهية / soon باقي 30 يوم أو أقل / ok سارية / null مش متسجلة */
    public static function docStatus($date)
    {
        if (!$date) return null;
        $today = \Carbon\Carbon::now('Asia/Riyadh')->startOfDay();
        $d = \Carbon\Carbon::parse($date->format('Y-m-d'), 'Asia/Riyadh')->startOfDay();
        if ($d->lt($today)) return 'expired';
        if ($today->diffInDays($d) <= 30) return 'soon';
        return 'ok';
    }

    public function getInsuranceStatusAttribute() { return self::docStatus($this->insurance_expiry); }
    public function getIstimaraStatusAttribute()  { return self::docStatus($this->istimara_expiry); }

    /** أسوأ حالة بين التأمين والاستمارة (للتنبيه على الكارت) */
    public function getDocsAlertAttribute()
    {
        $s = [$this->insurance_status, $this->istimara_status];
        if (in_array('expired', $s, true)) return 'expired';
        if (in_array('soon', $s, true)) return 'soon';
        return null;
    }

    /** الحمولة الحالية (لو الشاحنة محمّلة) */
    public function activeTrip()
    {
        return $this->hasOne(truck_trip::class, 'truck_id')->where('status', truck_trip::LOADED)->latest('id');
    }

    public function default_driver()
    {
        return $this->belongsTo(waybill_driver::class, 'default_driver_id');
    }
}
