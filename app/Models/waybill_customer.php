<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waybill_customer extends Model
{
    use HasFactory;
    protected $table = 'waybill_customers';
    protected $fillable = ['name','phone','city','address','tax_no','notes','user_id'];

}
