<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waybill extends Model
{
    use HasFactory;
    protected $table = 'waybills';
    protected $fillable = [
        'waybill_no','date','date_hijri',
        'customer_id','customer_name','destination_city',
        'driver_id','driver_name','driver_license_number','driver_license_issue_date',
        'truck_id','owner_name','plate_number','plate_region','operation_license_number',
        'operation_license_issuer','truck_type','total_load',
        'departure_date','fare_paid_by','delivery_within','notes',
        'total_fare','branchs_id','user_id'
    ];

    public function items()
    {
        return $this->hasMany(waybill_item::class, 'waybill_id');
    }
    public function customer()
    {
        return $this->belongsTo(waybill_customer::class, 'customer_id');
    }
    public function driver()
    {
        return $this->belongsTo(waybill_driver::class, 'driver_id');
    }
    public function truck()
    {
        return $this->belongsTo(waybill_truck::class, 'truck_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
