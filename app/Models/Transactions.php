<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

        protected $fillable = [
        'branchs_id' ,
        'loading',
        'unloading',
        'price',
        'truck_data',
        'note',
        'invoice_number',
        'created_at',
        'updated_at',
        'attachments',
        'Ext',
        'Daily',
        'mantob_name',
      'user_id',
      'date',
      "partner",
      'attachments_2',
      'note_detaials',
      'status',
      'Transactions',
      'mantob'
    ];



          public function financial_accounts_data()
    {
        return $this->belongsTo(financial_accounts::class,'partner');
    }        

        public function branch()
    {
        return $this->belongsTo(branchs::class,'branchs_id');
    }
        
}
