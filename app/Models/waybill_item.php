<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waybill_item extends Model
{
    use HasFactory;
    protected $table = 'waybill_items';
    protected $fillable = ['waybill_id','sender_name','fare','receiver_name','goods_type','goods_weight'];

    public function waybill()
    {
        return $this->belongsTo(waybill::class, 'waybill_id');
    }
}
