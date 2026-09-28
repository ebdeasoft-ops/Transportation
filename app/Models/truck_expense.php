<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** مصروف على شاحنة (وقود / صيانة / إطارات / ...) */
class truck_expense extends Model
{
    protected $table = 'truck_expenses';

    const TYPES = [
        'fuel'        => 'وقود',
        'maintenance' => 'صيانة وإصلاح',
        'parts'       => 'قطع غيار',
        'tires'       => 'إطارات',
        'oil'         => 'زيوت وفلاتر',
        'wash'        => 'غسيل',
        'insurance'   => 'تأمين',
        'istimara'    => 'استمارة وتجديد رخص',
        'fees'        => 'رسوم طرق / مخالفات',
        'driver'      => 'مصاريف سائق (إعاشة / سكن)',
        'rent'        => 'إيجار شاحنة خارجية',
        'other'       => 'أخرى',
    ];

    public static function typeLabel($t) { return self::TYPES[$t] ?? $t; }

    protected $fillable = [
        'truck_id', 'truck_trip_id', 'expense_date', 'type', 'amount', 'description', 'vendor', 'attachment',
        'expense_account_id', 'pay_account_id', 'posted', 'user_id', 'branchs_id',
    ];

    protected $casts = ['expense_date' => 'date'];

    public function truck() { return $this->belongsTo(waybill_truck::class, 'truck_id'); }
    public function trip()  { return $this->belongsTo(truck_trip::class, 'truck_trip_id'); }
    public function user()  { return $this->belongsTo(User::class, 'user_id'); }
    public function payAccount() { return $this->belongsTo(financial_accounts::class, 'pay_account_id'); }
}
