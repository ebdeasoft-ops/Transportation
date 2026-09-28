<?php

namespace App\Exports;

use App\Models\Covenant_liquidation;
use App\Models\shipments_details;
use App\Models\purchase_liquidation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Avt;

class liquidation implements FromCollection
{
    
    
    public $id;


    // Constructor with 4 parameters
    public function __construct($id) {
        $this->id = $id;
 
    }
    
    
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=[];
                $Covenant_liquidation = Covenant_liquidation::where('id', $this->id)->first();
                     $data[]=[
                   "no_liquidation"=>  __('home.no_liquidation') ,
                   "username"=>__('users.username'),
                   "date"=> __('home.date'),
                   "branch"=> __('home.branch'),
                   "status_liquidation"=> __('home.status_liquidation'),
                   "TOTAL"=> __('home.total'),

                   ];
                          $data[]=[
                   "no_liquidation"=> $Covenant_liquidation->id ,
                   "username"=> $Covenant_liquidation->user->name,
                   "date"=>  $Covenant_liquidation->created_at ,
                   "branch"=> $Covenant_liquidation->branch->name ,
                   "status_liquidation"=>__('home.confirm_done'),
                   "TOTAL"=> $Covenant_liquidation->price_filtering ,

                   ];
                   
if($Covenant_liquidation->type==1){
    $shipments_details=shipments_details::where('Transactions_id',$this->id)->get();
       $data[]=[
                   "no_liquidation"=>  __('home.no_liquidation') ,
                   "username"=>__('home.loading'),
                   "date"=> __('home.Unloading') ,
                   "branch"=> __('home.truck_no'),
                   "status_liquidation"=>__('home.invoice_no') ,
                   "polica_number"=> __('home.polica_number') ,
                   "PRICE_SHIPMENT"=>__('home.PRICE_SHIPMENT') ,
                   "delay"=>__('home.delay'),
                   "ext"=> __('home.ext')  ,
                   "TOTAL"=> __('home.total'),
                   "notesClient"=> __('home.notesClient') ,
                   "date_shipment"=> __('home.date'),

                   ];
     foreach ($shipments_details as $item){
         
         
                 $data[]=[
                   "no_liquidation"=>  $item->Transactions_id,
                   "username"=>$item->loading,
                   "date"=>$item->unloading,
                   "branch"=> $item->truck_data,
                   "status_liquidation"=>$item->invoice_number ,
                   "polica_number"=>$item->polica_number ,
                   "PRICE_SHIPMENT"=>$item->price_shipment ,
                   "delay"=>$item->Daily,
                   "ext"=> $item->Ext  ,
                   "TOTAL"=>$item->Ext+$item->Daily+$item->price_shipment ,
                   "notesClient"=> $item->note_detaials ,
                   "date_shipment"=>$item->date,

                   ];
         
     }
    
}else{
    $purchase_liquidation=purchase_liquidation::where('Transactions_id',$this->id)->get();
           $data[]=[
                   "no_liquidation"=>  __('home.Invoice_no') ,
                   "username"=>__('home.total'),
                   "date"=> __('home.notesClient') ,
        

                   ];
     foreach ($purchase_liquidation as $item){
         
         
                 $data[]=[
                   "Invoice_no"=>  $item->invoice_number ,
                   "total"=>$item->price_filtering,
                   "notesClient"=> $item->note_detaials ,
              

                   ];
         
     }
    
}
        
 
        return collect($data);
    }
 
}