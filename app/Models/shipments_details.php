<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shipments_details extends Model
{
    use HasFactory;
    
        protected $fillable = [
      'loading',
      'unloading',
      'price_shipment',
      'truck_data',
      'note',
      'invoice_number',
      'created_at',
      'updated_at',
      'attachments',
      'Ext',
      'Daily',
      'user_id',
      'date',
      'attachments_2',
      'note_detaials',
      'Transactions_id',
      'branchs_id',
      'save',
      'polica_number',
      'status',
            'date'

    ];



          public function Transactions_data()
    {
        return $this->belongsTo(Transactions_id::class,'Transactions_id');
    }     
          public function financial_accounts_data()
    {
        return $this->belongsTo(financial_accounts::class,'partner');
    }        

        public function branch()
    {
        return $this->belongsTo(branchs::class,'branchs_id');
    }
      
}
