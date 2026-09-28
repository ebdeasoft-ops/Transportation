<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Covenant_liquidation extends Model
{
    use HasFactory;
        protected $fillable = [
        'branchs_id',
        'user_id',
        'price_filtering',
        'note_detaials',
        'save',
        'status',
        'type',
        'owner_confirm',
        'currentblance',
        'id_trasction',
        'manager',
        'manager_check'
     
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
