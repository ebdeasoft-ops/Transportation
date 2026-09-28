<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transport_invoice_item extends Model
{
    protected $table = 'transport_invoice_items';

    protected $fillable = [
        'transport_invoice_id', 'truck_trip_id', 'description', 'trip_date', 'plate_number',
        'route_from', 'route_to', 'waybill_no', 'qty', 'unit_price', 'amount',
    ];

    protected $casts = ['trip_date' => 'datetime'];

    public function invoice() { return $this->belongsTo(transport_invoice::class, 'transport_invoice_id'); }
    public function trip()    { return $this->belongsTo(truck_trip::class, 'truck_trip_id'); }
}
