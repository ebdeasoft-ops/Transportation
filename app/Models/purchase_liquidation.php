<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_liquidation extends Model
{
    use HasFactory;

            protected $fillable = [
        'branchs_id',
        'user_id',
        'price_filtering',
        'note_detaials',
        'save',
        'status',
        'attachments',
        'Transactions_id',
        'invoice_number',
        'save',
     
    ];

        public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function branch()
    {
        return $this->belongsTo(branchs::class,'branchs_id');
    }
}
