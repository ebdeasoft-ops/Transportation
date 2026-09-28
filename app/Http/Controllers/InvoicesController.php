<?php

namespace App\Http\Controllers;
use PDF;
use Storage;
use App\Models\products_mix_items;
use Illuminate\Support\Facades\Response;

use App\Models\return_sales;
use App\Models\invoices;
use App\Models\financial_accounts;
use App\Models\sales;
use Illuminate\Http\Request;
use App\Models\Avt;
use App\Models\Delivery_product_to_the_customer;
use App\Models\products;
use App\Models\customers;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Models\offer_price_to_customer;
use App\Models\resource_purchases;
use App\Models\supllier;
use App\Models\temp_sales;
use App\Models\temp_invoice;
use App\Models\offer_price_to_customer_items;
use App\Models\credittransactions;
use App\Models\transactiontosuplliers;
use App\Services\Zatca\QRCode;
use  App\Models\system_setting;
use App\Models\settings;
use Ramsey\Uuid\Uuid;
use App\Services\Zatca\ZatcaConfig;
use DOMDocument;
use App\Services\Zatca\QRCodeString;
use Hassanhelfi\NumberToArabic\NumToArabic;
use App\Services\Zatca\Invoice\Client;
use App\Services\Zatca\Invoice\Supplier;
use App\Services\Zatca\Invoice\Delivery;
use App\Services\Zatca\Invoice\PaymentType;
use App\Services\Zatca\Invoice\PIH;
use App\Services\Zatca\Invoice\ReturnReason;
use App\Services\Zatca\Invoice\BillingReference;
use App\Services\Zatca\Invoice\AdditionalDocumentReference;
use App\Services\Zatca\Invoice\LegalMonetaryTotal;
use App\Services\Zatca\Invoice\TaxesTotal;
use App\Services\Zatca\Invoice\TaxSubtotal;
use App\Services\Zatca\Invoice\LineTaxCategory;
use App\Services\Zatca\Invoice\InvoiceLine;
use App\Services\Zatca\Invoice\AllowanceCharge;
use App\Services\Zatca\Invoice\InvoiceGenerator;
class NumberToWord{
    public $and = ' و';
    public function __construct()
    {
        
    }
    
    
    
    
    
            
        
        
        
    public function convert($number)
    {
        $_number = '';


        if(strlen($number) <= 2){
            $_number.= $this->towLength($number);
        }elseif (strlen($number) == 3) {
            $_number.= $this->threewwLength($number);
        }elseif (strlen($number) == 4) {
            $_number.= $this->moreThanThreeLength(substr($number,0,1),4).$this->and.$this->threewwLength(substr($number,1,3),true);
        }elseif (strlen($number) == 5) {
            $_number.= $this->moreThanThreeLength(substr($number,0,2),5).$this->and.$this->threewwLength(substr($number,2,3),true);
        }elseif (strlen($number) == 6) {
            $_number.= $this->moreThanThreeLength(substr($number,0,3),6).$this->and.$this->threewwLength(substr($number,3,3),false);
        }elseif (strlen($number) == 7) {
            $_number.= $this->moreThanThreeLength(substr($number,0,1),7).$this->and.$this->threewwLength(substr($number,1,3),true,$this->nameByDiget(6)).$this->and.$this->threewwLength(substr($number,4,3));
        }

        return trim($_number,$this->and);
    }
    public function oneLength($number)
    {
        if($number <= 20){
           return $this->forOneToTwntteyNew($number);
        }
    }
    /** Formation tow diget */
    private function towLength($number){
        $_number = null;
        if($number <= 20){
            $_number = $this->forOneToTwntteyNew($number);
        }else{
            if(substr($number,1,1) > 0){
                $_number .= $this->digetWithAl(substr($number,1,1)).$this->and; 
            }
            $_number .= $this->betwwenTwentyAndHundred(substr($number,0,1)); 
        }
        return $_number;
    }
    /** Formation three diget */
    private function threewwLength($number,$removeAnd = false,$text=null){
        $_number = null;
        $firstNumber = substr($number,0,1);
        if($firstNumber > 0){
            $_number.=$this->moreTahnHundred($firstNumber);
        }
        
        $scoundNumber = substr($number,1,2);

        if($scoundNumber < 20 && $scoundNumber > 0){
            if(!$removeAnd){
                $_number .= $this->and;
            }
            $_number .= $this->forOneToTwntteyNew((int)$scoundNumber);
        }else{
            if(substr($scoundNumber,1,1) > 0){
                $_number .= $this->and.$this->forOneToTwntteyNew(substr($scoundNumber,1,1)); 
            }
            if(substr($scoundNumber,0,1) > 0){
                $_number .= $this->and.$this->betwwenTwentyAndHundred(substr($scoundNumber,0,1),''); 
            }
            $_number.=' '.$text;
        }
        return $_number;
    }
    /** Formating 4 diget */
    private function moreThanThreeLength($number,$length=3)
    {
        $_number = '';
         switch ($length) {
             case 3:
            {
                if($number < 10 && $number > 0){
                    $_number .= $this->moreTahnThunsand($number);   
                }
            }
            break;
            case 4:
            {
                if($number <= 10 && $number > 0){
                    $_number .= $this->forOneToTwntteyNew($number).' '.$this->nameByDiget(4);   
                }
            }
            break;
            case 6:
            {
                 
                if($number <= 100 && $number > 0){
                    $_number .= $this->threewwLength($number,true ).' '.$this->nameByDiget(6); 
                }
            }
            break;
            case 7:
            {
                 
                if($number <= 10 && $number > 0){
                    if($number == 1 || $number == 2){
                        $_number .= $this->moreMilone($number);
                    }else{
                        $_number .= $this->oneLength($number).' '.$this->nameByDiget(7); 
                    }
                      
                }
            }
            break;
             default:
                return $_number;
                 break;
         }
         return $_number ;
    }
   
    private function lessTanTwenty($diget)
    {
        $numbers = [
            1=>'الأولي',
            2=>'الثانية',
            3=>'الثالثة',
            4=>'الرابعة',
            5=>'الخامسة',
            6=>'السادسة',
            7=>'السابعة',
            8=>'الثامنة',
            9=>'التاسعة',
            10=>'العاشرة',
            11=>'الحادية عشر',
            12=>'الثانية عشر',
            13=>'الثالثة عشر',
            14=>'الرابعة عشر',
            15=>'الخامسة عشر',
            16=>'السادسة عشر',
            17=>'السابعة عشر',
            18=>'الثامنة عشر',
            19=>'التاسعة عشر',
            20=>'العشرون',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function betwwenTwentyAndHundred($diget,$al='ال')
    {
        $numbers = [
         
            2=>$al.'عشرون',
            3=>$al.'ثلاثون',
            4=>$al.'اربعون',
            5=>$al.'خمسون',
            6=>$al.'ستون',
            7=>$al.'سبعون',
            8=>$al.'ثمانون',
            9=>$al.'تسعون',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function digetWithAl($diget)
    {
        $numbers = [
            1=>'الواحدة',
            2=>'الثانية',
            3=>'الثالثة',
            4=>'الرابعة',
            5=>'الخامسة',
            6=>'السادسة',
            7=>'السابعة',
            8=>'الثامنة',
            9=>'التاسعة',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }

    private function primaryBigNumber($diget)
    {
        $numbers = [
            100=>'مائة',
            200 =>'مائتين',
            1000 =>'الف',
            2000=>'الفين',
            3000=>'ثلاثة الاف',
            4000=>'اربعة الاف',
            5000=>'خمسة الاف',
            6000=>'ستة الاف',
            1000000=>'مليون',
            1000000000=>'مليار'
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function moreMilone($diget)
    {
        $numbers = [
            1=>'مليون',
            2 =>'مليونان',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function moreTahnHundred($diget)
    {
        $numbers = [
            1=>'مائة',
            2 =>'مائتان',
            3 =>'ثلاثمائة',
            4=>'اربعمائة',
            5=>'خمسمائة',
            6=>'ستمائة',
            7=>'سبعمائة',
            8=>'ثمانيمائة',
            9=>'تسعمائة',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function moreTahnThunsand($diget)
    {
        $numbers = [
            1=>'الف',
            2 =>'الفان',
            3 =>'ثلاثة',
            4=>'أربعة',
            5=>'خمسة',
            6=>'ستة',
            7=>'سبعة',
            8=>'ثمان',
            9=>'تسعة',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function forOneToTwnttey($diget)
    {
        $numbers = [
            1=>'واحد',
            2=>'اثنين',
            3=>'ثلاثة',
            4=>'أربعة',
            5=>'خمسة',
            6=>'ستة',
            7=>'سبعة',
            8=>'ثمانية',
            9=>'تسعة',
            10=>'عشرة',
            11=>'الحادي عشر',
            12=>'الثاني عشر',
            13=>'الثالث عشر',
            14=>'الرابع عشر',
            15=>'الخامس عشر',
            16=>'السادس عشر',
            17=>'السابع عشر',
            18=>'الثامن عشر',
            19=>'التاسع عشر',
            20=>'العشرون',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function forOneToTwntteyNew($diget)
    {
        $numbers = [
            1=>'واحد',
            2=>'اثنان',
            3=>'ثلاثة',
            4=>'أربعة',
            5=>'خمسة',
            6=>'ستة',
            7=>'سبعة',
            8=>'ثمانية',
            9=>'تسعة',
            10=>'عشرة',
            11=>'أحد عشر',
            12=>'إثنا عشر',
            13=>'ثلاثة عشر',
            14=>'أربعة عشر',
            15=>'خمسة عشر',
            16=>'ستة عشر',
            17=>'سبعة عشر',
            18=>'ثمانية عشر',
            19=>'تسعة عشر',
            20=>'عشرون',
        ];
        if(array_key_exists($diget,$numbers)){
            return $numbers[$diget];
        }
        return $diget;
    }
    private function nameByDiget($digetLength)
    {
        $numbers = [
            3=>'مائة',
            6=>'الف',
            4=>'الأف',
            7 =>'ملايين',
            10 =>'مليارات',
            13=>'بلايين',
        ];
        if(array_key_exists($digetLength,$numbers)){
            return $numbers[$digetLength];
        }
        return $digetLength;
    }
}


class InvoicesController extends Controller
{
    
        
    public function updatepaymentconfirmpayment_in_quotation($invoiceId, $cashamount, $bankamount, $creaditamount, $Bank_transfer, $paymentMethod)
    {
      
        $cashamount ?? 0;
        $bankamount ?? 0;
        $creaditamount ?? 0;
        $Bank_transfer ?? 0;
            $total_cost=0;

        $invoice = offer_price_to_customer::find($invoiceId);
        $avtSaleRate = Avt::find(1);
        $avtSaleRate = $avtSaleRate->AVT;
        $totalpricePurchases =0;
$totaldiscount =0;
$count=0;
                   foreach (offer_price_to_customer_items::where('order_id',$invoiceId)->get() as $item)
{
                   $totalpricePurchases += ($item->PriceWithoudTax * $item->quantity);
                   $totaldiscount += $item->discount;
                   $count+= $item->quantity;
}
$offer_price_to_customer=offer_price_to_customer::find($invoiceId);


                
$totalvat=round((round($totalpricePurchases,2)- round($totaldiscount+$offer_price_to_customer->discount,2))* $avtSaleRate,2);

$total_invoice_on_invoice=$offer_price_to_customer->discount+$totaldiscount;

$customerId='';
        if ($invoice == null) {
            return [0];
        }{
            $customerId=$invoice->customer_id;
          $confirminvoice=  invoices::create(
                [
                    'save' => 1,
                    'customer_id' => $invoice->customer_id,
                    'user_id' => Auth()->user()->id,
                    'Price' => $totalpricePurchases ,
                    'Added_Value' => ($totalvat),
                    'Pay' => $paymentMethod,
                    'status' => Auth()->user()->branchs_id == $invoice->branchs_id ? 0 : 1,
                    'branchs_id' => Auth()->User()->branch->id,
                    'discountOnProduct' => $totaldiscount,
                    'discount' =>$total_invoice_on_invoice,
                    'Number_of_Quantity' =>$count,
                    'note' => $invoice->notes??'-',
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'morepayment_way' => 1,
                    'cashamount' => $cashamount,
                    'bankamount' => $bankamount,
                    'creaditamount' => $creaditamount,
                    'Bank_transfer' => $Bank_transfer,
                    'Pay' => $paymentMethod,
                    'issue_date' => substr(\Carbon\Carbon::now()->addHours(3), 0, 10),
                    'issue_time' => substr(\Carbon\Carbon::now()->addHours(3), 12),
                ]
            );
          
            }
            foreach (offer_price_to_customer_items::where('order_id', $invoiceId)->get() as $sale) {
                $productdata = products::find($sale->product_id);
                $total_cost+=$productdata->PriceWithoudTax*$sale->quantity;
                if (Auth()->user()->branchs_id == $productdata->branchs_id) {
                 sales::create([
                        'save' => 1,
                        'product_id' => $sale->product_id,
                        'invoice_id' => $confirminvoice->id,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $sale->discount,
                        'Added_Value' => ($sale->PriceWithoudTax*$avtSaleRate) ,
                        'Unit_Price' => $sale->PriceWithoudTax,
                        'reamingQuantity' =>0,
                        'quantity' => $sale->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'unit' => $sale->unit,

    ]);



   if($productdata->products_mix==0){
    products::where('id', $sale->product_id)->Update([

        'numberofpice' => $productdata->numberofpice - $sale->quantity,
    ]);
   }
   else{
foreach(products_mix_items::where('products_mix_id',$productdata->products_mix)->get() as $itemmix){
    $product_chect_mix= products::find( $itemmix->product_id);

    products::where('id', $itemmix->product_id)->Update([

        'numberofpice' => $product_chect_mix->numberofpice - $itemmix->quantity,
    ]);
}



   }
                  



                }   else {
sales::create([
    'save' => 1,
    'product_id' => $sale->product_id,
    'invoice_id' => $confirminvoice->id,
    'branch_id' => Auth()->User()->branch->id,
    'Discount_Value' => $sale->discount,
    'Added_Value' => ($sale->PriceWithoudTax*$avtSaleRate) ,
    'Unit_Price' => $sale->PriceWithoudTax,
    'reamingQuantity' =>0,
    'quantity' => $sale->quantity,
    'created_at' => \Carbon\Carbon::now()->addHours(3),
    'unit' => $sale->unit,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
    ]);
                   
                }
            }

       if($cashamount){

  $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance+$cashamount,
           'debtor_current'=>$financial_accounts->debtor_current+ $cashamount,

       ]
       );
             credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' => $cashamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$cashamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$cashamount,
              

            ]
        );
  
         
    $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance+$cashamount,
           'debtor_current'=>$financial_accounts->debtor_current+ $cashamount,

       ]
       );           credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $cashamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$cashamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$cashamount,
              

            ]
        );
  
           
           
       }
       
      if($Bank_transfer+$bankamount){ 
  $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$Bank_transfer+$bankamount,
         'debtor_current'=>$financial_accounts->debtor_current+ $Bank_transfer+$bankamount,

     ]
     ); 
     
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' => $Bank_transfer+$bankamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$Bank_transfer+$bankamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$Bank_transfer+$bankamount,
              

            ]
        );
  
             $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$Bank_transfer+$bankamount,
         'debtor_current'=>$financial_accounts->debtor_current+ $Bank_transfer+$bankamount,

       ]
       ); 
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $Bank_transfer+$bankamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$Bank_transfer+$bankamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$Bank_transfer+$bankamount,
              

            ]
        );
    
           
           
       }

// new addition 2024-12-9


$total_value=$Bank_transfer+$creaditamount+$bankamount+$cashamount;




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+($total_value-($total_value*100/115))),
        //  'current_balance'=> $financial_accounts->current_balance+$total_value-($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+$total_value-($total_value*100/115),

     ]
     ); 
                     $customerdata = customers::find($customerId);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $total_value-($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+($total_value-($total_value*100/115))),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_value-($total_value*100/115),
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );


                $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_value-($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+$total_value-($total_value*100/115),

       ]
       ); 


 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_value-($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_value-($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_value-($total_value*100/115),
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );






  $financial_accounts= financial_accounts::find(112);
    financial_accounts::find(112)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+($total_value*100/115),
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

     ]
     ); 
credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  112,
                'recive_amount' => ($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> ($total_value*100/115),
                'debtor'=>0

            ]
        );


               $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+($total_value*100/115),
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

       ]
       ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> ($total_value*100/115),
                'debtor'=>0

            ]
        );







  $financial_accounts= financial_accounts::find(183);
    financial_accounts::find(183)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost,
         'debtor_current'=>$financial_accounts->debtor_current+$total_cost,

     ]
     ); 
      credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  183,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost

            ]
        );


             $financial_accounts= financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_cost,
         'debtor_current'=>$financial_accounts->debtor_current+$total_cost,

       ]
       ); 


 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost

            ]
        );





  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_cost,
         'creditor_current'=>$financial_accounts->creditor_current+ $total_cost,

     ]
     ); 
      credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost,
                'debtor'=>0

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance-$total_cost,
        'creditor_current'=>$financial_accounts->creditor_current+ $total_cost,

       ]
       ); 
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost,
                'debtor'=>0

            ]
        );



// end addition



 
     
     
            if ($creaditamount != 0 || $creaditamount != null) {
                $customerdata = customers::find($customerId);

                $updateCustomer = customers::where('id', $customerId)->update(
                    [
                        'Balance' => $customerdata->Balance + ($creaditamount)
                    ]
                );
  
  
     $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$customerId)->first();
   
         financial_accounts::where('orginal_type',1)->where('orginal_id',$customerId)->update(
     [
         'current_balance'=>$financial_accounts->current_balance+($creaditamount),
         'debtor_current'=>$financial_accounts->debtor_current+ $creaditamount,

     ]
     ); 
     
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $creaditamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$creaditamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$creaditamount

            ]
        );


            }
            $updateCustomer = customers::find($customerId);
            invoices::find($confirminvoice->id)->update(
                [
                    'currentblance' =>$updateCustomer->Balance,

                ]
            );


            $saleData = sales::where("invoice_id", $confirminvoice->id)->where('quantity', '!=', 0)->get();
            $InvoiceData = invoices::find($confirminvoice->id);
      $totAL=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

                     $totAL= number_format($totAL, 2);
    
    
            list($whole, $decimal) = explode('.',str_replace(",","",$totAL));
             $numberToWord = new NumberToWord();
             $check=str_split($decimal);
             if($check[0]=="0"){
               $decimal =(int)$check[1] ;
             }
             else{
            $decimal =$decimal ;
     
             }
          $setting=system_setting::find(1);
                 $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;
    
             $data = [
                $setting->name_ar,
                $setting->Tax,
                (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
                number_format(($Total_Amount),2,'.',''),
                number_format( (($Total_Amount*100/(100+($avtSaleRate*100)))) * $avtSaleRate,2,'.',''),
            ];
            $data[] = '';
            $data[] ='';
            $data[] = '';
            $data[] = '';
        
            $data = [
                "invoicetotal_price" =>  number_format(($Total_Amount*100/(100+($avtSaleRate*100))),2,'.',''),
                "invoicetotal_addedvalue" =>  number_format( (($Total_Amount*100/(100+($avtSaleRate*100)))) * $avtSaleRate,2,'.',''),
                "invoicetotal_discount" => $InvoiceData->discount,
                'salesData' => $saleData,
                'invoiceData' =>  $InvoiceData,
             'totatextlriyales'=>NumToArabic::number2Word(round((int)$whole,2)) .'  ريال',
                'totatextlrihalala'=>$decimal!='00'?NumToArabic::number2Word(round((int)$decimal,2)). '   هللة':'فقط', 
    
            ];
    
            return  view('products.printInvoicesToClient', compact('data'));
      


        }

    



  function sent_to_zatca_return_items($request)
    {
        // - Then Call Invoice required data from database depend on your query statment and required company id

        $setting = settings::find(1);
    
        $previous_invoice=null;
        $invoice = invoices::find($request);


        
        if($invoice->document_type == 'standard' ){
            if(is_null($invoice->customer->name) || is_null($invoice->customer->postcode) || is_null($invoice->customer->address) ||is_null( $invoice->customer->sub_city)||
            is_null( $invoice->customer->plot_identification) || is_null($invoice->customer->building_number) ||is_null( $invoice->customer->street_name)|| is_null($invoice->customer->tax_no)||strlen($invoice->customer->tax_no)!=15)
          {
            return "  \n Please enter the full national address and tax number information. Thank you يرجل ادخال بيانات العنوان الوطني و الرقم الضريبيي كاملا وشكرا";

          }
        
           }
        $invprevious =$setting ->previous_hash_invoice ;

if($invprevious==null){
    $previous_invoice='X+zrZv/IbzjZUnhsbWlsecLbwjndTpG0ZynXOif7V+k=';

}else{
    $previous_invoice= $invprevious;
}
$myuuid = Uuid::uuid4();

$created_at=\Carbon\Carbon::now()->addHours(3);
invoices::find($request)->update(
       [
        'issue_date_return' => substr($created_at, 0, 10),
       'issue_time_return' => substr($created_at, 11),
       'uuid'=>$myuuid
       ]
   );

        $invoice = invoices::find($request);
        $avtSaleRate = Avt::find(1);
        $tax_value_rate=$avtSaleRate->AVT;

        $rat_tax=$tax_value_rate*100;
        
        $itemTaxCategory = (new LineTaxCategory())
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
        $total_withot_tax_sum=0;
        $tax_sum=0;
        $total_with_tax_sum=0;
        $invoiceLines=[];


    
        foreach (return_sales::where("invoice_id", $request)->where('return_quantity', '!=', 0)->where('send_zatca', 0)->get() as $item) {
                return_sales::find($item->id)->update(['send_zatca'=>1]);
                $price_each_element_withoud_tax=number_format(($item->return_Unit_Price-($item->discountvalue/$item->return_quantity)),2,'.','');
                $temp=($item->return_Unit_Price-($item->discountvalue/$item->return_quantity))*$item->return_quantity;
                $total_withot_tax=number_format($temp,2,'.','');
                $temp=(($item->return_Unit_Price-($item->discountvalue/$item->return_quantity))*$tax_value_rate)*$item->return_quantity;
                $tax=number_format($temp,2,'.','');
                $totlal_element=$total_withot_tax+$tax;

                $total_with_tax=number_format($totlal_element,2,'.','');
                $total_withot_tax_sum=$total_withot_tax_sum+$total_withot_tax*1;
                $tax_sum=$tax_sum+$tax;
                $total_with_tax_sum=$total_with_tax_sum+$total_with_tax;
                $invoiceLines[] =(new InvoiceLine())
                ->setLineID($item->product_id)
                ->setLineName($item->productData->product_name)
                ->setLineCurrency('SAR')
                ->setLinePrice(number_format($price_each_element_withoud_tax,2,'.',''))
                ->setLineQuantity($item->return_quantity)
                ->setLineSubTotal($total_withot_tax)
                ->setLineTaxTotal($tax)
                ->setLineNetTotal($total_with_tax)
                ->setLineTaxCategories($itemTaxCategory)
                ->setLineDiscountReason( 'Discount on product')
                ->setLineDiscountAmount(0)
                ->getElement();
        }



        $total_withot_tax_sum=number_format($total_withot_tax_sum,2,'.','');
        $tax_sum=number_format($tax_sum,2,'.','');;
        $total_with_tax_sum=number_format($total_with_tax_sum,2,'.','');




  
   
// clients data


$client = (new Client())
->setVatNumber($invoice->customer->tax_no)
->setStreetName($invoice->customer->street_name)
->setBuildingNumber($invoice->customer->building_number)
->setPlotIdentification( $invoice->customer->plot_identification)
->setSubDivisionName($invoice->customer->sub_city)
->setCityName($invoice->customer->address)
->setPostalNumber($invoice->customer->postcode)
->setCountryName('SA')
->setClientName($invoice->customer->name);


        $supplier = (new Supplier())
        ->setCrn( $setting->crn)
        ->setStreetName( $setting->street_name)
        ->setBuildingNumber($setting->building_number)
        ->setPlotIdentification($setting->plot_identification)
        ->setSubDivisionName($setting->region)
        ->setCityName($setting->city)
        ->setPostalNumber($setting->postal_number)
        ->setCountryName('SA')
        ->setVatNumber($setting->trn)
        ->setVatName( $setting->name);

        $delivery = (new Delivery())
        ->setDeliveryDateTime( $invoice->issue_date);
    
        $paymentType = (new PaymentType())
        ->setPaymentType('10');
    
        $returnReason = (new ReturnReason())
        ->setReturnReason('Sales returns');
    
        $previous_hash = (new PIH())
        ->setPIH($previous_invoice);  // note this value it from step 3 , 4
         $billingReference = (new BillingReference())
        ->setBillingReference($request); // note this used when type credit or debit this value of parent invoice id
    
        $additionalDocumentReference = (new AdditionalDocumentReference())
        ->setInvoiceID(  $setting->invoices_count+1); // note this value it from step 1
    
        $legalMonetaryTotal = (new LegalMonetaryTotal())
        ->setTotalCurrency('SAR')
        ->setLineExtensionAmount(  $total_withot_tax_sum)
        ->setTaxExclusiveAmount(  $total_withot_tax_sum)
        ->setTaxInclusiveAmount($total_with_tax_sum)
        ->setAllowanceTotalAmount(0)
        ->setPrepaidAmount(0)
        ->setPayableAmount($total_with_tax_sum);
       
        $taxesTotal = (new TaxesTotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxTotal($tax_sum);
      
        $taxSubtotal = (new TaxSubtotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxableAmount(  $total_withot_tax_sum)
        ->setTaxAmount($tax_sum)
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
    
    
        $allowanceCharge = (new AllowanceCharge())
        ->setAllowanceChargeCurrency('SAR')
        ->setAllowanceChargeIndex('1')
        ->setAllowanceChargeAmount(0)
        ->setAllowanceChargeTaxCategory('S')
        ->setAllowanceChargeTaxPercentage($rat_tax)
        ->getElement();
        $response = (new InvoiceGenerator())
        ->setZatcaEnv($setting->is_production?'core':'simulation')
        ->setZatcaLang('en')
        ->setInvoiceNumber($invoice->NOTICE_Number)
        ->setInvoiceUuid($myuuid) // this value from step 6
        ->setInvoiceIssueDate($invoice->issue_date_return)
        ->setInvoiceIssueTime($invoice->issue_time_return)
        ->setInvoiceType(($invoice->document_type == 'simplified') ? '0200000' : '0100000',"381")
        ->setInvoiceCurrencyCode('SAR')
        ->setInvoiceTaxCurrencyCode('SAR')
        ->setInvoiceBillingReference($billingReference) // use this when document type is credit or debit
        ->setInvoiceAdditionalDocumentReference($additionalDocumentReference)
        ->setInvoicePIH($previous_hash)
        ->setInvoiceSupplier($supplier)
        ->setInvoiceClient($client)
        ->setInvoiceDelivery($delivery)
        ->setInvoicePaymentType($paymentType)
        ->setInvoiceReturnReason($returnReason) //use this when document type is credit or debit
        ->setInvoiceLegalMonetaryTotal($legalMonetaryTotal)
        ->setInvoiceTaxesTotal($taxesTotal)
        ->setInvoiceTaxSubTotal($taxSubtotal)
        ->setInvoiceAllowanceCharges($allowanceCharge)
        ->setInvoiceLines(...$invoiceLines)
        ->setCertificateEncoded($setting->production_certificate)
        ->setPrivateKeyEncoded( $setting->private_key)
        ->setCertificateSecret($setting->production_secret)
        ->sendDocument(true); // when you use production certifiacte for (simulation , core) dont forget set sendDocument(true)


        if ($response['success']) {
  settings::find(1)->update(['previous_hash_invoice'=>$response['hash'],
                                       'invoices_count'=> $setting->invoices_count+1
                                      ]);
            invoices::find($request)->update(
                [
                    'qr_zatca_return' => \Carbon\Carbon::now(),
                    'sent_to_zatca_status_return' => "PASS",
                    'xmltags_return' =>$response['xml'],
                    'xml_return'=>$invoice->document_type == 'simplified'?NULL:$response['response']->clearedInvoice

                ]
            );
            return 1;

        } else {

            return '|||'.$response['response']->reportingStatus.'   ||| ERROR MESSAGE    :-   '.$response['response']->validationResults->errorMessages[0]->message;
        }
    }
  
    function sent_to_zatca($request)
    {
        // - Then Call Invoice required data from database depend on your query statment and required company id

        $setting = settings::find(1);
       
        ### Zatca Integration have two steps second : Send Invoices to zatca Step example :
        // - Add below line to start of controller file which used
        $previous_invoice=null;
        $invoice = invoices::find($request);
        $invprevious =$setting ->previous_hash_invoice ;

if($invprevious==null){
    $previous_invoice='X+zrZv/IbzjZUnhsbWlsecLbwjndTpG0ZynXOif7V+k=';

}else{
    $previous_invoice= $invprevious;
}
        $myuuid = Uuid::uuid4();

        invoices::find($request)->update(
            [
                'invoice_counter' =>  $setting->invoices_count+1,
                'invoice_number' => $invoice->id,
                'invoiceUUid' =>$myuuid ,
                'document_type' => $invoice->customer_id == 1? 'simplified' : 'standard',
                'invoice_type' => "388", //  "388" NORMAL INVOICE , "383"  DEBIT_NOTE , "381" CREDIT_NOTE
                'issue_date' => substr($invoice->created_at, 0, 10),
                'issue_time' => substr($invoice->created_at, 11),
            ]
        );

        $invoice = invoices::find($request);




        
        if($invoice->document_type == 'standard' ){
            if(is_null($invoice->customer->name) || is_null($invoice->customer->postcode) || is_null($invoice->customer->address) ||is_null( $invoice->customer->sub_city)||
            is_null( $invoice->customer->plot_identification) || is_null($invoice->customer->building_number) ||is_null( $invoice->customer->street_name)|| is_null($invoice->customer->tax_no)||strlen($invoice->customer->tax_no)!=15)
          {
            return "  \n Please enter the full national address and tax number information. Thank you يرجل ادخال بيانات العنوان الوطني و الرقم الضريبيي كاملا وشكرا";

          }
        
           }
        $avtSaleRate = Avt::find(1);
        $tax_value_rate=$avtSaleRate->AVT;

        $rat_tax=$tax_value_rate*100;
        
        $itemTaxCategory = (new LineTaxCategory())
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
        $total_withot_tax_sum=0;
        $tax_sum=0;
        $total_with_tax_sum=0;
        $invoiceLines=[];


    
        foreach (sales::where("invoice_id", $request)->where('quantity', '!=', 0)->get() as $item) {
                $price_each_element_withoud_tax=number_format(($item->Unit_Price-($item->Discount_Value/$item->quantity)),2,'.','');
                $temp=($item->Unit_Price-($item->Discount_Value/$item->quantity))*$item->quantity;
                $total_withot_tax=number_format($temp,2,'.','');
                $temp=(($item->Unit_Price-($item->Discount_Value/$item->quantity))*$tax_value_rate)*$item->quantity;
                $tax=number_format($temp,2,'.','');
                $totlal_element=$total_withot_tax+$tax;




                $total_with_tax=number_format($totlal_element,2,'.','');
                $total_withot_tax_sum=$total_withot_tax_sum+$total_withot_tax*1;
                $tax_sum=$tax_sum+$tax;
                $total_with_tax_sum=$total_with_tax_sum+$total_with_tax;
                $invoiceLines[] =(new InvoiceLine())
                ->setLineID($item->product_id)
                ->setLineName($item->productData->product_name)
                ->setLineCurrency('SAR')
                ->setLinePrice(number_format($price_each_element_withoud_tax,2,'.',''))
                ->setLineQuantity($item->quantity)
                ->setLineSubTotal($total_withot_tax)
                ->setLineTaxTotal($tax)
                ->setLineNetTotal($total_with_tax)
                ->setLineTaxCategories($itemTaxCategory)
                ->setLineDiscountReason( 'Discount on product')
                ->setLineDiscountAmount(0)
                ->getElement();
        }
//  return $invoiceLines;
        $total_withot_tax_sum=number_format($total_withot_tax_sum,2,'.','');
        $tax_sum=number_format($tax_sum,2,'.','');;
        $total_with_tax_sum=number_format($total_with_tax_sum,2,'.','');



        // - If Invoice type is standard invoice (B2B) you must provide full buyer information as below :

  
   
// clients data
        $client = (new Client())
        ->setVatNumber($invoice->customer->tax_no)
        ->setStreetName($invoice->customer->street_name)
        ->setBuildingNumber($invoice->customer->building_number)
        ->setPlotIdentification( $invoice->customer->plot_identification)
        ->setSubDivisionName($invoice->customer->sub_city)
        ->setCityName($invoice->customer->address)
        ->setPostalNumber($invoice->customer->postcode)
        ->setCountryName('SA')
        ->setClientName($invoice->customer->name);
    

    //  return $client->getElement();
        $supplier = (new Supplier())
        ->setCrn( $setting->crn)
        ->setStreetName( $setting->street_name)
        ->setBuildingNumber($setting->building_number)
        ->setPlotIdentification($setting->plot_identification)
        ->setSubDivisionName($setting->region)
        ->setCityName($setting->city)
        ->setPostalNumber($setting->postal_number)
        ->setCountryName('SA')
        ->setVatNumber($setting->trn)
        ->setVatName( $setting->name);
        // return $supplier->getElement();

        $delivery = (new Delivery())
        ->setDeliveryDateTime( $invoice->issue_date);
    
        $paymentType = (new PaymentType())
        ->setPaymentType('10');
    
        $returnReason = (new ReturnReason())
        ->setReturnReason('SET_RETURN_REASON');
    
        $previous_hash = (new PIH())
        ->setPIH($previous_invoice);  // note this value it from step 3 , 4
        // $billingReference = (new BillingReference())
        // ->setBillingReference('23'); // note this used when type credit or debit this value of parent invoice id
    
        $additionalDocumentReference = (new AdditionalDocumentReference())
            ->setInvoiceID(  $setting->invoices_count+1); // note this value it from step 1
        // ->setInvoiceID( $request); // note this value it from step 1

        $legalMonetaryTotal = (new LegalMonetaryTotal())
        ->setTotalCurrency('SAR')
        ->setLineExtensionAmount(  $total_withot_tax_sum)
        ->setTaxExclusiveAmount(  $total_withot_tax_sum)
        ->setTaxInclusiveAmount($total_with_tax_sum)
        ->setAllowanceTotalAmount(0)
        ->setPrepaidAmount(0)
        ->setPayableAmount($total_with_tax_sum);
       
        $taxesTotal = (new TaxesTotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxTotal($tax_sum);
      
        $taxSubtotal = (new TaxSubtotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxableAmount(  $total_withot_tax_sum)
        ->setTaxAmount($tax_sum)
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
    
    
        $allowanceCharge = (new AllowanceCharge())
        ->setAllowanceChargeCurrency('SAR')
        ->setAllowanceChargeIndex('1')
        ->setAllowanceChargeAmount(0)
        ->setAllowanceChargeTaxCategory('S')
        ->setAllowanceChargeTaxPercentage($rat_tax)
        ->getElement();
        $response = (new InvoiceGenerator())
        ->setZatcaEnv($setting->is_production?'core':'simulation')
        ->setZatcaLang('en')
        ->setInvoiceNumber($request)
        ->setInvoiceUuid($invoice->invoiceUUid) // this value from step 6
        ->setInvoiceIssueDate($invoice->issue_date)
        ->setInvoiceIssueTime($invoice->issue_time)
        ->setInvoiceType(($invoice->document_type == 'simplified') ? '0200000' : '0100000',$invoice->invoice_type)
        ->setInvoiceCurrencyCode('SAR')
        ->setInvoiceTaxCurrencyCode('SAR')
        //->setInvoiceBillingReference($billingReference)  use this when document type is credit or debit
        ->setInvoiceAdditionalDocumentReference($additionalDocumentReference)
        ->setInvoicePIH($previous_hash)
        ->setInvoiceSupplier($supplier)
        ->setInvoiceClient($client)
        ->setInvoiceDelivery($delivery)
        ->setInvoicePaymentType($paymentType)
        //->setInvoiceReturnReason($returnReason) use this when document type is credit or debit
        ->setInvoiceLegalMonetaryTotal($legalMonetaryTotal)
        ->setInvoiceTaxesTotal($taxesTotal)
        ->setInvoiceTaxSubTotal($taxSubtotal)
        ->setInvoiceAllowanceCharges($allowanceCharge)
        ->setInvoiceLines(...$invoiceLines)
        ->setCertificateEncoded($setting->production_certificate)
        ->setPrivateKeyEncoded( $setting->private_key)
        ->setCertificateSecret($setting->production_secret)
        ->sendDocument(true); // when you use production certifiacte for (simulation , core) dont forget set sendDocument(true)
      
        //   return $response;
            
            
        if ($response['success']) {
             settings::find(1)->update(['previous_hash_invoice'=>$response['hash'],
                                       'invoices_count'=> $setting->invoices_count+1
                                      ]);
            invoices::find($request)->update(
                [
                    'signing_time' => \Carbon\Carbon::now(),
                    'hash' => $response['hash'],
                    'xml' => $response['xml'],
                    'sent_to_zatca_status' => "PASS",
                    'sent_to_zatca' => 1,
                    'clearedInvoice'=>$invoice->document_type == 'simplified'?NULL:$response['response']->clearedInvoice

                ]
            );
        
            return 1;
        }  else {

           return '|||'.$response['response']->reportingStatus.'   ||| ERROR MESSAGE    :-   '.$response['response']->validationResults->errorMessages[0]->message;
        }
    }


  




    function sendzatca_fromsale($request)
    {
        // - Then Call Invoice required data from database depend on your query statment and required company id

        $setting = settings::find(1);
       
        ### Zatca Integration have two steps second : Send Invoices to zatca Step example :
        // - Add below line to start of controller file which used
        $previous_invoice=null;
        $invoice = invoices::find($request);
        $invprevious =$setting ->previous_hash_invoice ;

if($invprevious==null){
    $previous_invoice='X+zrZv/IbzjZUnhsbWlsecLbwjndTpG0ZynXOif7V+k=';

}
else{
    $previous_invoice= $invprevious;
}

        $myuuid = Uuid::uuid4();

        invoices::find($request)->update(
            [
                'invoice_counter' =>  $setting->invoices_count+1,
                'invoice_number' => $invoice->id,
                'invoiceUUid' => $myuuid,
                'document_type' => $invoice->customer_id == 1  ? 'simplified' : 'standard',
                'invoice_type' => "388", //  "388" NORMAL INVOICE , "383"  DEBIT_NOTE , "381" CREDIT_NOTE
                'issue_date' => substr($invoice->created_at, 0, 10),
                'issue_time' => substr($invoice->created_at, 11),
            ]
        );


        $invoice = invoices::find($request);

        if($invoice->document_type == 'standard' ){
            if(is_null($invoice->customer->name) || is_null($invoice->customer->postcode) || is_null($invoice->customer->address) ||is_null( $invoice->customer->sub_city)||
            is_null( $invoice->customer->plot_identification) || is_null($invoice->customer->building_number) ||is_null( $invoice->customer->street_name)|| is_null($invoice->customer->tax_no)||strlen($invoice->customer->tax_no)!=15)
          {
            return "  \n Please enter the full national address and tax number information. Thank you يرجل ادخال بيانات العنوان الوطني و الرقم الضريبيي كاملا وشكرا";

          }
        
           }
        $avtSaleRate = Avt::find(1);
        $tax_value_rate=$avtSaleRate->AVT;

        $rat_tax=$tax_value_rate*100;
        
        $itemTaxCategory = (new LineTaxCategory())
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
        $total_withot_tax_sum=0;
        $tax_sum=0;
        $total_with_tax_sum=0;
        $invoiceLines=[];


    
        foreach (sales::where("invoice_id", $request)->where('quantity', '!=', 0)->get() as $item) {
                $price_each_element_withoud_tax=number_format(($item->Unit_Price-($item->Discount_Value/$item->quantity)),2,'.','');
                $temp=($item->Unit_Price-($item->Discount_Value/$item->quantity))*$item->quantity;
                $total_withot_tax=number_format($temp,2,'.','');
                $temp=(($item->Unit_Price-($item->Discount_Value/$item->quantity))*$tax_value_rate)*$item->quantity;
                $tax=number_format($temp,2,'.','');
                $totlal_element=$total_withot_tax+$tax;

              
        


                $total_with_tax=number_format($totlal_element,2,'.','');
                $total_withot_tax_sum=$total_withot_tax_sum+$total_withot_tax*1;
                $tax_sum=$tax_sum+$tax;
                $total_with_tax_sum=$total_with_tax_sum+$total_with_tax;
                $invoiceLines[] =(new InvoiceLine())
                ->setLineID($item->product_id)
                ->setLineName($item->productData->product_name)
                ->setLineCurrency('SAR')
                ->setLinePrice(number_format($price_each_element_withoud_tax,2,'.',''))
                ->setLineQuantity($item->quantity)
                ->setLineSubTotal($total_withot_tax)
                ->setLineTaxTotal($tax)
                ->setLineNetTotal($total_with_tax)
                ->setLineTaxCategories($itemTaxCategory)
                ->setLineDiscountReason( 'Discount on product')
                ->setLineDiscountAmount(0)
                ->getElement();
        }
//  return $invoiceLines;
        $total_withot_tax_sum=number_format($total_withot_tax_sum,2,'.','');
        $tax_sum=number_format($tax_sum,2,'.','');;
        $total_with_tax_sum=number_format($total_with_tax_sum,2,'.','');



        // - If Invoice type is standard invoice (B2B) you must provide full buyer information as below :

  
   
// clients data
$client = (new Client())
->setVatNumber($invoice->customer->tax_no)
->setStreetName($invoice->customer->street_name)
->setBuildingNumber($invoice->customer->building_number)
->setPlotIdentification( $invoice->customer->plot_identification)
->setSubDivisionName($invoice->customer->sub_city)
->setCityName($invoice->customer->address)
->setPostalNumber($invoice->customer->postcode)
->setCountryName('SA')
->setClientName($invoice->customer->name);


    //  return $client->getElement();
        $supplier = (new Supplier())
        ->setCrn( $setting->crn)
        ->setStreetName( $setting->street_name)
        ->setBuildingNumber($setting->building_number)
        ->setPlotIdentification($setting->plot_identification)
        ->setSubDivisionName($setting->region)
        ->setCityName($setting->city)
        ->setPostalNumber($setting->postal_number)
        ->setCountryName('SA')
        ->setVatNumber($setting->trn)
        ->setVatName( $setting->name);
        // return $supplier->getElement();

        $delivery = (new Delivery())
        ->setDeliveryDateTime( $invoice->issue_date);
    
        $paymentType = (new PaymentType())
        ->setPaymentType('10');
    
        $returnReason = (new ReturnReason())
        ->setReturnReason('SET_RETURN_REASON');
    
        $previous_hash = (new PIH())
        ->setPIH($previous_invoice);  // note this value it from step 3 , 4
        // $billingReference = (new BillingReference())
        // ->setBillingReference('23'); // note this used when type credit or debit this value of parent invoice id
    
        $additionalDocumentReference = (new AdditionalDocumentReference())
        ->setInvoiceID(  $setting->invoices_count+1); // note this value it from step 1
    
        $legalMonetaryTotal = (new LegalMonetaryTotal())
        ->setTotalCurrency('SAR')
        ->setLineExtensionAmount(  $total_withot_tax_sum)
        ->setTaxExclusiveAmount(  $total_withot_tax_sum)
        ->setTaxInclusiveAmount($total_with_tax_sum)
        ->setAllowanceTotalAmount(0)
        ->setPrepaidAmount(0)
        ->setPayableAmount($total_with_tax_sum);
       
        $taxesTotal = (new TaxesTotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxTotal($tax_sum);
      
        $taxSubtotal = (new TaxSubtotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxableAmount(  $total_withot_tax_sum)
        ->setTaxAmount($tax_sum)
        ->setTaxCategory('S')
        ->setTaxPercentage($rat_tax)
        ->getElement();
    
    
        $allowanceCharge = (new AllowanceCharge())
        ->setAllowanceChargeCurrency('SAR')
        ->setAllowanceChargeIndex('1')
        ->setAllowanceChargeAmount(0)
        ->setAllowanceChargeTaxCategory('S')
        ->setAllowanceChargeTaxPercentage($rat_tax)
        ->getElement();
        $response = (new InvoiceGenerator())
        ->setZatcaEnv($setting->is_production?'core':'simulation')
        ->setZatcaLang('en')
        ->setInvoiceNumber($request)
        ->setInvoiceUuid($invoice->invoiceUUid) // this value from step 6
        ->setInvoiceIssueDate($invoice->issue_date)
        ->setInvoiceIssueTime($invoice->issue_time)
        ->setInvoiceType(($invoice->document_type == 'simplified') ? '0200000' : '0100000',$invoice->invoice_type)
        ->setInvoiceCurrencyCode('SAR')
        ->setInvoiceTaxCurrencyCode('SAR')
        //->setInvoiceBillingReference($billingReference)  use this when document type is credit or debit
        ->setInvoiceAdditionalDocumentReference($additionalDocumentReference)
        ->setInvoicePIH($previous_hash)
        ->setInvoiceSupplier($supplier)
        ->setInvoiceClient($client)
        ->setInvoiceDelivery($delivery)
        ->setInvoicePaymentType($paymentType)
        //->setInvoiceReturnReason($returnReason) use this when document type is credit or debit
        ->setInvoiceLegalMonetaryTotal($legalMonetaryTotal)
        ->setInvoiceTaxesTotal($taxesTotal)
        ->setInvoiceTaxSubTotal($taxSubtotal)
        ->setInvoiceAllowanceCharges($allowanceCharge)
        ->setInvoiceLines(...$invoiceLines)
        ->setCertificateEncoded($setting->production_certificate)
        ->setPrivateKeyEncoded( $setting->private_key)
        ->setCertificateSecret($setting->production_secret)
        ->sendDocument(true); // when you use production certifiacte for (simulation , core) dont forget set sendDocument(true)
        //   return $response;
            if ($response['success']) {
  settings::find(1)->update(['previous_hash_invoice'=>$response['hash'],
                                       'invoices_count'=> $setting->invoices_count+1
                                      ]);
                invoices::find($request)->update(
                    [
                        'signing_time' => \Carbon\Carbon::now(),
                        'hash' => $response['hash'],
                        'xml' => $response['xml'],
                        'sent_to_zatca_status' => "PASS",
                        'sent_to_zatca' => 1,
                        'clearedInvoice'=>$invoice->document_type == 'simplified'?NULL:$response['response']->clearedInvoice
    
                    ]
            );
        
            return 1;
        }  else {

           return '|||'.$response['response']->reportingStatus.'   ||| ERROR MESSAGE    :-   '.$response['response']->validationResults->errorMessages[0]->message;
        }
    }


    function dwonloadxml($id)
    {
        // return $id;
        $invoice = invoices::find($id);
        $xml = new DOMDocument;
        $xml->loadXML(base64_decode( $invoice->clearedInvoice??$invoice->xml,true));
        $xml->formatOutput = true;
        $namefile = "invoice_" . $invoice->id . '_' . date("Y_m_d") . 'T' . date("h_i") . ".xml";
        $xml->save('result.xml');
        $xml = file_get_contents(public_path('result.xml'));
        $filepath = public_path('result.xml');
        $headers = [
            'Content-Type' => 'application/xml',
        ];
        $respons = Response::download($filepath, $namefile, $headers);
        // unlink($namefile);

        return  $respons;
    }
 
    
 
    public function updateinvoicebyidforsaleupdate($invoiceNumber){
                   
                                $InvoiceData = invoices::find($invoiceNumber);

        
      
        //return $product;
        $allProdctsD = [];
        $i = 0;
        if($InvoiceData!=null){
                              $products = sales::where('invoice_id', $invoiceNumber)->get();

            
               if ($InvoiceData->Pay == 'Credit') {
            $customerdata = customers::find($InvoiceData->customer_id);
            $avt = Avt::find(1);
            $saleavt = $avt->AVT;
            $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - (($InvoiceData->Price - $InvoiceData->discount) + (($InvoiceData->Price - $InvoiceData->discount) * $saleavt))
                ]
            );
       
     
            
         
        }
              invoices::find($invoiceNumber)->update([
                        'save'=>0
                        
                        ]);
   $Invoice = temp_invoice::create(
                    [
                        'customer_id' => $InvoiceData->customer_id ?? 1,
                        'user_id' => Auth()->user()->id,
                        'Price' => 0,
                        'Added_Value' =>0,
                        'Pay' => 'Cash',
                        'status' => 0,
                        'branchs_id' => Auth()->User()->branch->id,
                        'discountOnProduct' => 0,
                        'discount' => 0,
                        'Number_of_Quantity' => 0,
                        'note' => '-',
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'update_invoice'=>$invoiceNumber
                    ]
                );
                
                $totalprice=0;
                $totaladdedvalue=0;
                $totalquantity=0;
                $totaldiscount=0;
                $avtSaleRate = Avt::find(1);

                foreach($products as $item){
                    
                $totalprice=$totalprice+($item->Unit_Price*$item->quantity);
                $totaladdedvalue=$totaladdedvalue+(($item->quantity) * $item->Added_Value);
                $totalquantity=$totalquantity+$item->quantity;
                $totaldiscount=$totaldiscount+$item->Discount_Value; 
                
                
                     $updateProduct=products::find($item->product_id);
                     products::find($item->product_id)->update([
                         'numberofpice'=>$updateProduct->numberofpice   +$item->quantity
                         ]);
                        $productSales = temp_sales::create(
                    [
                        'product_id' => $item->product_id,
                        'invoice_id' =>  $Invoice->id,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $item->Discount_Value,
                        'Added_Value' => ($item->Added_Value) ,
                        'Unit_Price' => $item->Unit_Price,
                        'quantity' => $item->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
                }
                
                sales::where('invoice_id', $invoiceNumber)->update([
                    'quantity'=>0
                    
                    ]);
                 temp_invoice::find($Invoice->id)->update(
                    [
                     
                        'Price' => $totalprice,
                        'Added_Value' =>$totaladdedvalue,
                        'discountOnProduct' => $totaldiscount,
                        'discount' => $totaldiscount+$InvoiceData->discount,
                        'Number_of_Quantity' => $totalquantity,
                      
                    ]
                );
        $products = temp_sales::where('invoice_id', $Invoice->id)->get();
        //return $product;
        $allProdctsD = [];
        $i = 0;
        foreach ($products as $product) {
            $i++;
            $updateProduct = products::find($product->product_id);
               

            
            
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'Discount_Value' => $product->Discount_Value,
                'reamingquantity' => $updateProduct->numberofpice,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
           
        }

        $InvoiceData = temp_invoice::find( $Invoice->id);

        $customer = customers::find($InvoiceData->customer_id);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price-$InvoiceData->discount ,
            "invoicetotal_addedvalue" => ( $InvoiceData->Price-$InvoiceData->discount)* $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'invoice_number' => $Invoice->id,
            'pay' => $InvoiceData->pay,
            'customer' => $customer,
            'product' => $allProdctsD,
            "invoice_id" => $Invoice->id
        ];


     
        return ($data);
}
return 0;
       

        }
        
    
    
    
    
      public function generate_pdf_customer_list() {
        

 
        $data = [

        ];

         $tran = ['data'=>$data];
        // Get the current date and time.
        $dateTime = now();

        // Generate a unique filename.
    //   return view('pdf.salereturn', compact('data'));

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.customer_list', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "customer_list"."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );


       
        
    }
    
  public function generate_return_sale_pdf  ($request) {
        

        $saleData = return_sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);
        $data = [

            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
        ];

         $tran = ['data'=>$data];
        // Get the current date and time.
        $dateTime = now();

        // Generate a unique filename.
    //   return view('pdf.salereturn', compact('data'));

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.salereturn', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "Invoice_No_".$request."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );


       
        
    }
    
    
    function ConvertToHEX($value)
    {
      return pack("H*", sprintf("%02X", $value));
    }
    public function  generate_pdf($request) {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = sales::where("invoice_id", $request)->where('quantity', '!=', 0)->get();
        $InvoiceData = invoices::find($request);
  $totAL=round(($InvoiceData->Price - $InvoiceData->discount)+(($InvoiceData->Price - $InvoiceData->discount)*$avtSaleRate->AVT),2);
                 $totAL= number_format($totAL, 2);


        list($whole, $decimal) = explode('.',str_replace(",","",$totAL));
         $numberToWord = new NumberToWord();
         $check=str_split($decimal);
         if($check[0]=="0"){
           $decimal =(int)$check[1] ;
         }
         else{
        $decimal =$decimal ;
 
         }
      $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
        $data = [
            "invoicetotal_price" =>  number_format(($Total_Amount*100/(100+($avtSaleRate->AVT*100))),2,'.',''),
            "invoicetotal_addedvalue" =>  number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
             'totatextlriyales'=>$this->convertNumber(round((int)$whole,2)),
            'totatextlrihalala'=>$decimal!='00'?$this->convertNumber(round((int)$decimal,2)):'zero', 

        ];
       
        $tran = ['data'=>$data];
        // Get the current date and time.
        $dateTime = now();

        // Generate a unique filename.
        //  return view('pdf.translation', compact('data'));

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.translation', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "Invoice_No_".$request."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );


       

    
    }




    
    function convertNumber($num = false)
{
    $num = str_replace(array(',', ''), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ( $hundreds == 1 ? '' : '' ) . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' and ' . $list1[$tens] . ' ' : '' );
        } elseif ($tens >= 20) {
            $tens = (int)($tens / 10);
            $tens = ' and ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    $words = implode(' ',  $words);
    $words = preg_replace('/^\s\b(and)/', '', $words );
    $words = trim($words);
    $words = ucfirst($words);
    $words = $words;
    return $words;
}
    
     public function reciptprinter(Request $request)
     {
         //
         //  return $request;
         app()->setLocale(LaravelLocalization::getCurrentLocale());
 
         if ($request->show_invoice_number == null) {
             $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);
             session()->flash('nodataprint', '');
 
             return view('products.sales', compact('products'));
         }
         app()->setLocale(LaravelLocalization::getCurrentLocale());
         $avtSaleRate = Avt::find(1);
 
 
         $saleData = sales::where("invoice_id", $request->show_invoice_number)->where('quantity', '!=', 0)->get();
         $InvoiceData = invoices::find($request->show_invoice_number);
         $data = [
             "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
             "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
             "invoicetotal_discount" => $InvoiceData->discount,
             'salesData' => $saleData,
             'invoiceData' =>  $InvoiceData,
         ];
 
 
 
         // $saleData= sales::where("invoice_id",$invoicesid)->get();
         // $InvoiceData=invoices::find($invoicesid);
         // $data=[
         //     'salesData'=>$saleData ,
         //     'invoiceData'=>  $InvoiceData,
         // ];
         //return $data;
         return  view('products.reciptprinter', compact('data'));
     }
 
        public function updatepaymentconfirmpaymentpurchases($invoiceId, $cashamount, $bankamount, $creaditamount, $Bank_transfer, $paymentMethod)
    {

        $invoice = resource_purchases::where('orderId', $invoiceId)->first();
        if ($invoiceId == null) {
            return [0];
        } else {
            $oldpayment = 0;
            $oldpayment_parent = 0;
            if ($invoice->Pay_Method_Name == 'Shabka' || $invoice->Pay_Method_Name == 'Bank_transfer') {
                $oldpayment = 4;

                $financial_accounts = financial_accounts::find(4);
                financial_accounts::find(4)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance + ($cashamount + $bankamount + $creaditamount + $Bank_transfer),
                        'creditor_current' => $financial_accounts->creditor_current - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

                    ]
                );
                $financial_accounts = financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->first();
                if ($financial_accounts) {

                    $oldpayment_parent = $financial_accounts->id;

                    financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->update(
                        [
                            'current_balance' => $financial_accounts->current_balance + ($cashamount + $bankamount + $creaditamount + $Bank_transfer),
                            'creditor_current' => $financial_accounts->creditor_current - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

                        ]
                    );
                }
            }

            if ($invoice->Pay_Method_Name == 'Cash') {
                $oldpayment = 5;

                $financial_accounts = financial_accounts::find(5);
                financial_accounts::find(5)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance + (($cashamount + $bankamount + $creaditamount + $Bank_transfer)),
                        'creditor_current' => $financial_accounts->creditor_current - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

                    ]
                );
                $financial_accounts = financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->first();
                if ($financial_accounts) {

                    $oldpayment_parent = $financial_accounts->id;
                    financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->update(
                        [
                            'current_balance' => $financial_accounts->current_balance + (($cashamount + $bankamount + $creaditamount + $Bank_transfer)),
                            'creditor_current' => $financial_accounts->creditor_current - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

                        ]
                    );
                }

            }
            if ($invoice->Pay_Method_Name == 'Credit') {
                $customerdata = supllier::find($invoice->suplier_id);

                $updateCustomer = supllier::where('id', $invoice->suplier_id)->update(
                    [
                        'In_debt' => $customerdata->In_debt - ($cashamount + $bankamount + $creaditamount + $Bank_transfer)
                    ]
                );
                $financial_accounts = financial_accounts::where('orginal_type', 2)->where('orginal_id', $invoice->suplier_id)->first();
                $oldpayment = $financial_accounts->id;

                financial_accounts::where('orginal_type', 2)->where('orginal_id', $invoice->suplier_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),
                        'creditor_current' => $financial_accounts->creditor_current - ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

                    ]
                );

                credittransactions::where('note', '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $financial_accounts->id)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 0,
                        'recive_amount' => 0,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'currentblance' => 0,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => 0

                    ]
                );

            }








            if ($cashamount) {


                $financial_accounts = financial_accounts::find(5);
                financial_accounts::find(5)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $cashamount,
                        'creditor_current' => $financial_accounts->creditor_current + $cashamount,

                    ]
                );
                credittransactions::where('note', '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 5,
                        'recive_amount' => $cashamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'debtor' => 0,
                        'creditor' => $cashamount,


                    ]
                );

                $financial_accounts = financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->first();
                financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $cashamount,
                        'creditor_current' => $financial_accounts->creditor_current + $cashamount,

                    ]
                );
    if ($oldpayment_parent == 0) {
                    credittransactions::create(
                        [
                            // 'attachments'=>$the_file_path_2??'-',
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $cashamount,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => $paymentMethod,
                            'note' => '  فاتورة مشتريات رقم :' . (string) $invoiceId,
                            'currentblance' => $financial_accounts->current_balance + $cashamount,
                            'Pay_Method_Name' => $paymentMethod,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => 0,
                            'debtor' => 0,
                            'creditor' => $cashamount

                        ]
                    );
                }else

{
                credittransactions::where('note', '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $cashamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'debtor' => 0,
                        'creditor' => $cashamount,


                    ]
                );
            }

            }

            if ($Bank_transfer + $bankamount) {


                $financial_accounts = financial_accounts::find(4);
                financial_accounts::find(4)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $Bank_transfer - $bankamount,
                        'creditor_current' => $financial_accounts->creditor_current + $Bank_transfer - $bankamount,

                    ]
                );
                credittransactions::where('note', '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 4,
                        'recive_amount' => $Bank_transfer + $bankamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'debtor' => 0,
                        'creditor' => $Bank_transfer + $bankamount,


                    ]
                );
                $financial_accounts = financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->first();
                financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $Bank_transfer - $bankamount,
                        'creditor_current' => $financial_accounts->creditor_current + $Bank_transfer + $bankamount,

                    ]
                );
        if ($oldpayment_parent == 0) {
                    credittransactions::create(
                        [
                            // 'attachments'=>$the_file_path_2??'-',
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $Bank_transfer + $bankamount,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => $paymentMethod,
                            'note' => '  فاتورة مشتريات رقم :' . (string) $invoiceId,
                            'currentblance' => $financial_accounts->current_balance + $Bank_transfer + $bankamount,
                            'Pay_Method_Name' => $paymentMethod,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => 0,
                            'debtor' => 0,
                            'creditor' => $Bank_transfer + $bankamount

                        ]
                    );
                }else{
                credittransactions::where('note', operator: '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $Bank_transfer + $bankamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'debtor' => 0,
                        'creditor' => $Bank_transfer + $bankamount,


                    ]
                );

            }
            }
            if ($creaditamount != 0 || $creaditamount != null) {
                $customerdata = supllier::find($invoice->suplier_id);


                credittransactions::where('note', operator: '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->delete();
                if ($oldpayment_parent != 0) {
                    credittransactions::where('note', operator: '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->delete();

                }
                $updateCustomer = supllier::where('id', $invoice->suplier_id)->update(
                    [
                        'In_debt' => $customerdata->In_debt + ($creaditamount)
                    ]
                );

                $financial_accounts = financial_accounts::where('orginal_type', 2)->where('orginal_id', $invoice->suplier_id)->first();
                credittransactions::where('note', operator: '  فاتورة مشتريات رقم :' . (string) $invoiceId)->where('customer_id', $financial_accounts->id)->delete();

                financial_accounts::where('orginal_type', 2)->where('orginal_id', $invoice->suplier_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance + ($creaditamount),
                        'creditor_current' => $financial_accounts->creditor_current + $creaditamount,

                    ]
                );

                credittransactions::create(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $creaditamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'note' => '  فاتورة مشتريات رقم :' . (string) $invoiceId,
                        'currentblance' => $financial_accounts->current_balance + $creaditamount,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'debtor' => 0,
                        'creditor' => $creaditamount

                    ]
                );

            }


            resource_purchases::where('orderId', $invoiceId)->update(
                [

                    'Pay_Method_Name' => $paymentMethod
                ]
            );




        }
        $data = resource_purchases::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices_purchases', compact('data'));
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function PreviousQuotes(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

      $data= offer_price_to_customer::orderby('id','desc')->paginate(30);


        return view('PreviousQuotes', compact('data'));

    }
    public function searchPreviousQuotes($id){
        $data= offer_price_to_customer::where('id',$id)->paginate(30);
        
        
                return view('searchPreviousQuotes', compact('data'));
        
            }
     
         public function getquotebycustomer($id){
        $data= offer_price_to_customer::where('customer_id',$id)->paginate(30);
        
        
                return view('searchPreviousQuotes', compact('data'));
        
            }   
     
        public function updateinvoicebyid($invoiceNumber){

$quoute=offer_price_to_customer::find($invoiceNumber);
if($quoute!=null){
   $Invoice = temp_invoice::create(
                    [
                        'customer_id' => $quoute->customer_id ?? 1,
                        'user_id' => Auth()->user()->id,
                        'Price' => 0,
                        'Added_Value' =>0,
                        'Pay' => 'Cash',
                        'status' => 0,
                        'branchs_id' => Auth()->User()->branch->id,
                        'discountOnProduct' => 0,
                        'discount' => 0,
                        'Number_of_Quantity' => 0,
                        'note' => $quoute->notes??'-',
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
                
                $totalprice=0;
                $totaladdedvalue=0;
                $totalquantity=0;
                $totaldiscount=0;
                         $avtSaleRate = Avt::find(1);

                foreach(offer_price_to_customer_items::where('order_id',$invoiceNumber)->get() as $item){
                    
                $totalprice=$totalprice+($item->PriceWithoudTax*$item->quantity);
                $totaladdedvalue=$totaladdedvalue+(($item->PriceWithoudTax*$item->quantity) * $avtSaleRate->AVT);
                $totalquantity=$totalquantity+$item->quantity;
                $totaldiscount=$totaldiscount+$item->discount; 
                
                
                     $updateProduct=products::find($item->product_id);
                        $productSales = temp_sales::create(
                    [
                        'product_id' => $item->product_id,
                        'invoice_id' =>  $Invoice->id,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $item->discount,
                        'Added_Value' => ($item->PriceWithoudTax) * $avtSaleRate->AVT,
                        'Unit_Price' => $item->PriceWithoudTax,
                        'reamingQuantity' => $updateProduct->numberofpice - $item->quantity,
                        'quantity' => $item->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
                }
                 temp_invoice::find($Invoice->id)->update(
                    [
                     
                        'Price' => $totalprice,
                        'Added_Value' =>$totaladdedvalue,
                        'discountOnProduct' => $totaldiscount,
                        'discount' => $totaldiscount+$quoute->discount,
                        'Number_of_Quantity' => $totalquantity,
                      
                    ]
                );
        $products = temp_sales::where('invoice_id', $Invoice->id)->get();
        //return $product;
        $allProdctsD = [];
        $i = 0;
        foreach ($products as $product) {
            $i++;
            $updateProduct = products::find($product->product_id);
               

            
            
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'Discount_Value' => $product->Discount_Value,
                'reamingquantity' => $updateProduct->numberofpice,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
           
        }
   sales::where('invoice_id', $invoiceNumber)->update([
                    'quantity' =>0
                    ]);

        $InvoiceData = temp_invoice::find( $Invoice->id);

        $customer = customers::find($InvoiceData->customer_id);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price-$InvoiceData->discount ,
            "invoicetotal_addedvalue" => ( $InvoiceData->Price-$InvoiceData->discount)* $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'invoice_number' => $Invoice->id,
            'pay' => $InvoiceData->pay,
            'customer' => $customer,
            'product' => $allProdctsD,
            "invoice_id" => $invoiceNumber
        ];


     
        return ($data);
}
return 0;

    }
    
     
     
    
    public function index()
    {
        //
        $data = [];
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('products.salesreturned', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getproductbyCode($request)
    {
        $updateProduct = products::where('Product_Code', $request)->first();
        return $updateProduct;
    }

    public function updatecustomerDataRecipt(Request $request)
    {

        $invoices = invoices::where('id', $request->id)->first();


        if ($invoices->Pay == 'Credit') {
            $customerdata = customers::find($invoices->customer_id);
            $avt = Avt::find(1);
            $saleavt = $avt->AVT;
            $updateCustomer = customers::where('id', $invoices->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
                ]
            );
            $customerdatanew = customers::find($request->customerId);

            $updateCustomer = customers::where('id', $request->customerId)->update(
                [
                    'Balance' => $customerdatanew->Balance + (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
                ]
            );
        }
        $InvoiceData = invoices::where('id', $request->id)->update(
            ['customer_id' => $request->customerId]
        );
        $data = invoices::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('status', 1)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }
    public function updatecustomerDataInvoice(Request $request)
    {

        $invoices = invoices::where('id', $request->id)->first();


        if ($invoices->Pay == 'Credit') {
            $customerdata = customers::find($invoices->customer_id);
            $avt = Avt::find(1);
            $saleavt = $avt->AVT;
            $updateCustomer = customers::where('id', $invoices->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
                ]
            );
            $customerdatanew = customers::find($request->customerId);

            $updateCustomer = customers::where('id', $request->customerId)->update(
                [
                    'Balance' => $customerdatanew->Balance + (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
                ]
            );
        }
        $InvoiceData = invoices::where('id', $request->id)->update(
            ['customer_id' => $request->customerId]
        );
        $data = invoices::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('status', 0)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }

    public function getByCode(Request $request)
    {
        //
        $updateProduct = products::where('Product_Code', $request->Code)->first();

        //return $updateProduct;
        $avtSaleRate = Avt::find(1);

        if ($updateProduct->numberofpice >= $request->quantity) {
            if (Auth()->user()->branchs_id == $updateProduct->branchs_id) {
                // products::where('Product_Code', $request->Code)->Update([
                //     'numberofpice' => $updateProduct->numberofpice - $request->quantity,
                //     'numberـofـsales' => $updateProduct->numberـofـsales + $request->quantity
                // ]);
            }
            $invoiceNumber = $request->invoice_number;
            if ($request->invoice_number == null) {
                // return ($request->pay);

                $Invoice = temp_invoice::create(
                    [
                        'customer_id' => $request->clientnamesearch ?? 1,
                        'user_id' => Auth()->user()->id,
                        'Price' => ($updateProduct->sale_price) * $request->quantity,
                        'Added_Value' => (($updateProduct->sale_price) * $avtSaleRate->AVT) * $request->quantity,
                        'Pay' => $request->pay,
                        'status' => Auth()->user()->branchs_id == $updateProduct->branchs_id ? 0 : 1,
                        'branchs_id' => Auth()->User()->branch->id,
                        'discountOnProduct' => 0,
                        'discount' => 0,
                        'Number_of_Quantity' => $request->quantity,
                        'note' => $request->note,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );

                $invoiceNumber = $Invoice->id;
            } else {

                $InvoiceData = temp_invoice::find($invoiceNumber);
                $Invoice = temp_invoice::where('id',  $invoiceNumber)->Update(
                    [
                        'discount' => 0,
                        'Price' => $InvoiceData->Price + (($updateProduct->sale_price) * $request->quantity),
                        'Added_Value' => $InvoiceData->Added_Value + ((($updateProduct->sale_price) * $avtSaleRate->AVT) * $request->quantity),
                        'Number_of_Quantity' => $InvoiceData->Number_of_Quantity + $request->quantity,
                        'note' => $request->note,
                        'status' => $InvoiceData->status != 1 &  Auth()->user()->branchs_id == $updateProduct->branchs_id ? 0 : 1,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
            }
            $checksale = temp_sales::where('invoice_id', $invoiceNumber)->where('product_id', $updateProduct->id)->first();

            if ($checksale != null) {

                temp_sales::where('invoice_id', $invoiceNumber)->where('product_id', $updateProduct->id)->update(
                    [
                        'quantity' =>  $checksale->quantity + 1
                    ]
                );
            } else {
                $productSales = temp_sales::create(
                    [
                        'product_id' => $updateProduct->id,
                        'invoice_id' => $invoiceNumber,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => 0,
                        'Added_Value' => ($updateProduct->sale_price) * $avtSaleRate->AVT,
                        'Unit_Price' => $updateProduct->sale_price,
                        'quantity' => $request->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
            }
        } else {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'عدم وجود مخزون من هذه المنتج' : 'Out of stock of this product';

            session()->flash('delete',  $message);
            $data = ["notfount"];

            // return view('products.sales',compact('data'));
            return $data;
        }


        // if ($request->pay == "Credit") {
        //     $customerdata = customers::find($request->clientnamesearch);

        //     $updateCustomer = customers::where('id', $request->clientnamesearch)->update(
        //         [
        //             'Balance' => $customerdata->Balance + ((($request->product_price * $request->quantity) - $request->product_price_after_dis) + ((($request->quantity * $request->product_price) - $request->product_price_after_dis) * $avtSaleRate->AVT))
        //         ]
        //     );
        // }
        $products = temp_sales::where('invoice_id', $invoiceNumber)->get();
        //return $product;
        $allProdctsD = [];
        $i = 0;
        foreach ($products as $product) {
            $updateProduct = products::find($product->product_id);

            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'reamingquantity' => $updateProduct->numberofpice - $request->quantity,
                'Discount_Value' => $product->Discount_Value,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
        }


        $customer = customers::find($request->clientnamesearch);
        $InvoiceData = temp_invoice::find($invoiceNumber);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'invoice_number' => $invoiceNumber,
            'pay' => $request->pay,
            'customer' => $customer,
            'product' => $allProdctsD,
            "invoice_id" => $invoiceNumber
        ];

        // if (Auth()->user()->branchs_id != $updateProduct->branchs_id) {
        //     Delivery_product_to_the_customer::create(
        //         [
        //             'branch_from' => Auth()->user()->branchs_id,
        //             'branch_to' => $updateProduct->branchs_id,
        //             'user_from' => Auth()->user()->id,
        //             'product_id' => $updateProduct->id,
        //             'invoice_id' => $invoiceNumber,
        //             'quantity' => $request->quantity,
        //             'status' => 0,
        //             'created_at' => \Carbon\Carbon::now()->addHours(3),
        //         ]
        //     );
        // }
        return ($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function saveInvoice($invoiceId)
    {
        $avtSaleRate = Avt::find(1);

        $invoice = invoices::find($invoiceId);
        if ($invoice == null) {
            return [0];
        } else {
            invoices::find($invoiceId)->update(
                [
                    'save' => 1
                ]
            );
            sales::where('invoice_id', $invoiceId)->update(
                [
                    'save' => 1

                ]

            );
            // sales::where('invoice_id', $invoiceId)->where('quantity',0)->delete();
            foreach (sales::where('invoice_id', $invoiceId)->get() as $sale) {
                $productdata = products::find($sale->product_id);
                if (Auth()->user()->branchs_id == $productdata->branchs_id) {

                    products::where('id', $sale->product_id)->Update([

                        'numberofpice' => $productdata->numberofpice - $sale->quantity,
                    ]);
                }
            }
            return [1];
        }
    }

    public function updatepaymentconfirmpaymentReciept($invoiceId, $cashamount, $bankamount, $creaditamount, $Bank_transfer, $paymentMethod)
    {


        $invoice = invoices::find($invoiceId);
        if ($invoice == null) {
            return [0];
        } else {
            if ($invoice->Pay == 'Credit') {
                $customerdata = customers::find($invoice->customer_id);

                $updateCustomer = customers::where('id', $invoice->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance - ($cashamount + $bankamount + $creaditamount + $Bank_transfer)
                    ]
                );
            }
            invoices::find($invoiceId)->update(
                [
                    'save' => 1,
                    'morepayment_way' => 1,
                    'cashamount' => $cashamount,
                    'bankamount' => $bankamount,
                    'creaditamount' => $creaditamount,
                    'Bank_transfer' => $Bank_transfer,
                    'Pay' => $paymentMethod
                ]
            );



            if ($creaditamount != 0 || $creaditamount != null) {
                $customerdata = customers::find($invoice->customer_id);

                $updateCustomer = customers::where('id', $invoice->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance + ($creaditamount)
                    ]
                );
            }
        }
        $data = invoices::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('status', 1)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }

    public function updatepaymentconfirmpayment($invoiceId, $cashamount, $bankamount, $creaditamount, $Bank_transfer, $paymentMethod,$another_bank)
    {


        $invoice = invoices::find($invoiceId);
        if ($invoice == null) {
            return [0];
        } else {
            
            
           $oldpayment = 0;
           $oldpayment_parent = 0; 
              if ($invoice->Pay == 'Cash') {
                $oldpayment = 5;

    $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$invoice->cashamount,
           'debtor_current'=>$financial_accounts->debtor_current-$invoice->cashamount,

       ]
       ); 
       
           $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
                               $oldpayment_parent = $financial_accounts->id;

    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
          'current_balance'=> $financial_accounts->current_balance-$invoice->cashamount,
           'debtor_current'=>$financial_accounts->debtor_current-$invoice->cashamount,

       ]
       ); 
       
        }
                   if ($invoice->Pay == 'Shabka' || $invoice->Pay == 'Bank_transfer') {
                $oldpayment = 4;

  $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$invoice->Bank_transfer-$invoice->bankamount,
         'debtor_current'=>$financial_accounts->debtor_current-$invoice->Bank_transfer-$invoice->bankamount,

     ]
     ); 

                $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
                                    $oldpayment_parent = $financial_accounts->id;

    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance-$invoice->Bank_transfer-$invoice->bankamount,
         'debtor_current'=>$financial_accounts->debtor_current-$invoice->Bank_transfer-$invoice->bankamount,

       ]
       ); 
     
     
                   }
            
            if ($invoice->Pay == 'Credit') {
                $customerdata = customers::find($invoice->customer_id);

                $updateCustomer = customers::where('id', $invoice->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance - ($cashamount + $bankamount + $creaditamount + $Bank_transfer)
                    ]
                );
                
                
                 $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$invoice->customer_id)->first();
   
         financial_accounts::where('orginal_type',1)->where('orginal_id',$invoice->customer_id)->update(
     [
         'current_balance'=>$financial_accounts->current_balance- ($cashamount + $bankamount + $creaditamount + $Bank_transfer),
         'debtor_current'=>$financial_accounts->debtor_current- ($cashamount + $bankamount + $creaditamount + $Bank_transfer),

     ]
     ); 
     
          credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $financial_accounts->id)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 0,
                        'recive_amount' => 0,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'currentblance' => 0,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => 0

                    ]
                );
            }
            
            
            
            
            
            


            if ($cashamount) {


                $financial_accounts = financial_accounts::find(5);
                financial_accounts::find(5)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $cashamount,
                        'debtor_current' => $financial_accounts->debtor_current + $cashamount,

                    ]
                );
                credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 5,
                        'recive_amount' => $cashamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => $cashamount,


                    ]
                );

                $financial_accounts = financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->first();
                financial_accounts::where('parent_account_number', 5)->where('branchs_id', Auth()->user()->branchs_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $cashamount,
                        'debtor_current' => $financial_accounts->debtor_current + $cashamount,

                    ]
                );
         if ($oldpayment_parent == 0) {
                    credittransactions::create(
                        [
                            // 'attachments'=>$the_file_path_2??'-',
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $cashamount,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => $paymentMethod,
                            'note' => '  فاتورة مبيعات رقم :' . (string) $invoiceId,
                            'currentblance' => $financial_accounts->current_balance + $cashamount,
                            'Pay_Method_Name' => $paymentMethod,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => 0,
                            'creditor' => 0,
                            'debtor' => $cashamount

                        ]
                    );
                }else

{
                credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $cashamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => $cashamount,


                    ]
                );
            }

            }

            if ($Bank_transfer + $bankamount) {


                $financial_accounts = financial_accounts::find(4);
                financial_accounts::find(4)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $Bank_transfer - $bankamount,
                        'debtor_current' => $financial_accounts->debtor_current + $Bank_transfer - $bankamount,

                    ]
                );
                credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 4,
                        'recive_amount' => +$Bank_transfer + $bankamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => +$Bank_transfer + $bankamount,


                    ]
                );
                
                  if($another_bank==4){

         $financial_accounts = financial_accounts::where('id',1157)->first();
                financial_accounts::where('id',1157)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $Bank_transfer - $bankamount,
                        'debtor_current' => $financial_accounts->debtor_current + $Bank_transfer + $bankamount,

                    ]
                );
        if ($oldpayment_parent == 0) {
                    credittransactions::create(
                        [
                            // 'attachments'=>$the_file_path_2??'-',
                            'user_id' => Auth()->user()->id,
                            'customer_id' => 1157,
                            'recive_amount' => $Bank_transfer + $bankamount,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => $paymentMethod,
                            'note' => '  فاتورة مبيعات رقم :' . (string) $invoiceId,
                            'currentblance' => $financial_accounts->current_balance + $Bank_transfer + $bankamount,
                            'Pay_Method_Name' => $paymentMethod,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => 0,
                            'creditor' => 0,
                            'debtor' => $Bank_transfer + $bankamount

                        ]
                    );
                }else{
                credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 1157,
                        'recive_amount' => $Bank_transfer + $bankamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => $Bank_transfer + $bankamount,


                    ]
                );

            }

}else{
    
             $financial_accounts = financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->first();
                financial_accounts::where('parent_account_number', 4)->where('branchs_id', Auth()->user()->branchs_id)->update(
                    [
                        'current_balance' => $financial_accounts->current_balance - $Bank_transfer - $bankamount,
                        'debtor_current' => $financial_accounts->debtor_current + $Bank_transfer + $bankamount,

                    ]
                );
        if ($oldpayment_parent == 0) {
                    credittransactions::create(
                        [
                            // 'attachments'=>$the_file_path_2??'-',
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $Bank_transfer + $bankamount,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => $paymentMethod,
                            'note' => '  فاتورة مبيعات رقم :' . (string) $invoiceId,
                            'currentblance' => $financial_accounts->current_balance + $Bank_transfer + $bankamount,
                            'Pay_Method_Name' => $paymentMethod,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => 0,
                            'creditor' => 0,
                            'debtor' => $Bank_transfer + $bankamount

                        ]
                    );
                }else{
                credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $Bank_transfer + $bankamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'Pay_Method_Name' => $paymentMethod,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => $Bank_transfer + $bankamount,


                    ]
                );

            }
    
}
       
            }
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            invoices::find($invoiceId)->update(
                [
                    'save' => 1,
                    'morepayment_way' => 1,
                    'cashamount' => $cashamount,
                    'bankamount' => $bankamount,
                    'creaditamount' => $creaditamount,
                    'Bank_transfer' => $Bank_transfer,
                    'Pay' => $paymentMethod
                ]
            );



            if ($creaditamount != 0 || $creaditamount != null) {
                
                   credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment_parent)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 0,
                        'recive_amount' => 0,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'currentblance' => 0,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => 0

                    ]
                );
                   credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $oldpayment)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 0,
                        'recive_amount' => 0,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'currentblance' => 0,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => 0

                    ]
                );
                    $customerdata = customers::find($invoice->customer_id);
                                 $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$invoice->customer_id)->first();

                           credittransactions::where('note', '  فاتورة مبيعات رقم :' . (string) $invoiceId)->where('customer_id', $financial_accounts->id)->update(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => 0,
                        'recive_amount' => 0,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'currentblance' => 0,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => 0

                    ]
                );
            
   financial_accounts::where('orginal_type',1)->where('orginal_id',$invoice->customer_id)->update(
     [
         'current_balance'=>$financial_accounts->current_balance+$creaditamount,
         'debtor_current'=>$financial_accounts->debtor_current+$creaditamount,

     ]
     ); 
                $updateCustomer = customers::where('id', $invoice->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance + ($creaditamount)
                    ]
                );
            }
        }
        
        
                credittransactions::create(
                    [
                        // 'attachments'=>$the_file_path_2??'-',
                        'user_id' => Auth()->user()->id,
                        'customer_id' => $financial_accounts->id,
                        'recive_amount' => $creaditamount,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'pay_method' => $paymentMethod,
                        'note' => '  فاتورة مبيعات رقم :' . (string) $invoiceId,
                        'currentblance' => $financial_accounts->current_balance + $creaditamount,
                        'Pay_Method_Name' => $paymentMethod,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        'orginal_id' => 0,
                        'creditor' => 0,
                        'debtor' => $creaditamount

                    ]
                );

        
        $data = invoices::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('status', 0)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }

    public function confirmpaymentconfirmpayment($invoiceId, $cashamount, $bankamount, $creaditamount, $Bank_transfer, $paymentMethod,$customerId,$date2,$date12,$another_bank,$p_o,$shippingfee)
    {
        $cashamount ?? 0;
        $bankamount ?? 0;
        $creaditamount ?? 0;
        $Bank_transfer ?? 0;
            $total_cost=0;
        $invoice = temp_invoice::find($invoiceId);
        if ($invoice == null) {
            return [0];
        }else {
            if($invoice->update_invoice){
                
                 invoices::find($invoice->update_invoice)->update(
                [
                    'save' => 1,
                    'customer_id' => $customerId,
                    'user_id' => Auth()->user()->id,
                    'Price' => ($invoice->Price) ,
                    'Added_Value' => ($invoice->Added_Value),
                    'Pay' => $invoice->Pay,
                    'status' => Auth()->user()->branchs_id == $invoice->branchs_id ? 0 : 1,
                    'branchs_id' => Auth()->User()->branch->id,
                    'discountOnProduct' => $invoice->discountOnProduct,
                    'discount' =>$invoice->discount,
                    'Number_of_Quantity' => $invoice->Number_of_Quantity,
                    'note' => $invoice->note,
                   'created_at'  =>$date12!='0'?$date12.' '.substr(\Carbon\Carbon::now()->addHours(3), 12):\Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'morepayment_way' => 1,
                    'cashamount' => $cashamount,
                    'bankamount' => $bankamount,
                    'creaditamount' => $creaditamount,
                    'Bank_transfer' => $Bank_transfer,
                    'Pay' => $paymentMethod,
                    'issue_date' => substr(\Carbon\Carbon::now()->addHours(3), 0, 10),
                    'issue_time' => substr(\Carbon\Carbon::now()->addHours(3), 12),
                    
                ]
            );
                      $confirminvoice=  invoices::find($invoice->update_invoice);

            }
            else{
          $confirminvoice=  invoices::create(
                [
                    'save' => 1,
                    'customer_id' => $customerId,
                    'user_id' => Auth()->user()->id,
                    'Price' => ($invoice->Price) ,
                    'Added_Value' => ($invoice->Added_Value),
                    'Pay' => $invoice->Pay,
                    'status' => Auth()->user()->branchs_id == $invoice->branchs_id ? 0 : 1,
                    'branchs_id' => Auth()->User()->branch->id,
                    'discountOnProduct' => $invoice->discountOnProduct,
                    'discount' =>$invoice->discount,
                    'Number_of_Quantity' => $invoice->Number_of_Quantity,
                    'note' => $invoice->note,
                   'created_at'  =>$date12!='0'?$date12.' '.substr(\Carbon\Carbon::now()->addHours(3), 12):\Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'morepayment_way' => 1,
                    'cashamount' => $cashamount,
                    'bankamount' => $bankamount,
                    'creaditamount' => $creaditamount,
                    'Bank_transfer' => $Bank_transfer,
                    'Pay' => $paymentMethod,
                    'issue_date' => substr(\Carbon\Carbon::now()->addHours(3), 0, 10),
                    'issue_time' => substr(\Carbon\Carbon::now()->addHours(3), 12),
                    'p_o'=>$p_o,
                    'shippingfee'=>$shippingfee,

                ]
            );
          
            }
            foreach (temp_sales::where('invoice_id', $invoiceId)->get() as $sale) {
                $productdata = products::find($sale->product_id);
                $total_cost+=$productdata->purchasingـprice*$sale->quantity;
                if (Auth()->user()->branchs_id == $productdata->branchs_id) {
sales::create([
                        'save' => 1,
                        'product_id' => $sale->product_id,
                        'invoice_id' => $confirminvoice->id,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $sale->Discount_Value,
                        'Added_Value' => ($sale->Added_Value) ,
                        'Unit_Price' => $sale->Unit_Price,
                        'reamingQuantity' => $sale->reamingQuantity,
                        'quantity' => $sale->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
    ]);



    products::where('id', $sale->product_id)->Update([

        'numberofpice' => $productdata->numberofpice - $sale->quantity,
    ]);
   

                  



                }   else {
                    
                       invoices::find($confirminvoice->id)->update([
                         'status' =>1
                         ]);
sales::create([
                        'save' => 1,
                        'product_id' => $sale->product_id,
                        'invoice_id' => $confirminvoice->id,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $sale->Discount_Value,
                        'Added_Value' => ($sale->Added_Value) ,
                        'Unit_Price' => $sale->Unit_Price,
                        'reamingQuantity' => $sale->reamingQuantity,
                        'quantity' => $sale->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
    ]);
                    Delivery_product_to_the_customer::create(
                        [
                            'branch_from' => Auth()->user()->branchs_id,
                            'branch_to' => $productdata->branchs_id,
                            'user_from' => Auth()->user()->id,
                            'product_id' => $productdata->id,
                            'invoice_id' =>$confirminvoice->id,
                            'quantity' => $sale->quantity,
                            'status' => 0,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                        ]
                    );
                }
            }

       if($cashamount){

  $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance+$cashamount,
           'debtor_current'=>$financial_accounts->debtor_current+ $cashamount,

       ]
       );
             credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' => $cashamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$cashamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$cashamount,
              

            ]
        );
  
         
    $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance+$cashamount,
           'debtor_current'=>$financial_accounts->debtor_current+ $cashamount,

       ]
       );           credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $cashamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$cashamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$cashamount,
              

            ]
        );
  
           
           
       }
       
      if($Bank_transfer+$bankamount){ 
  $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$Bank_transfer+$bankamount,
         'debtor_current'=>$financial_accounts->debtor_current+ $Bank_transfer+$bankamount,

     ]
     ); 
     
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' => $Bank_transfer+$bankamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$Bank_transfer+$bankamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$Bank_transfer+$bankamount,
              

            ]
        );
  if($another_bank==5){
      
      
             $financial_accounts= financial_accounts::where('id',1157)->first();
    financial_accounts::where('id',1157)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$Bank_transfer+$bankamount,
         'debtor_current'=>$financial_accounts->debtor_current+ $Bank_transfer+$bankamount,

       ]
       ); 
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>1157,
                'recive_amount' => $Bank_transfer+$bankamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$Bank_transfer+$bankamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$Bank_transfer+$bankamount,
              

            ]
        );
  }
  else{
      
  
             $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$Bank_transfer+$bankamount,
         'debtor_current'=>$financial_accounts->debtor_current+ $Bank_transfer+$bankamount,

       ]
       ); 
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $Bank_transfer+$bankamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$Bank_transfer+$bankamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$Bank_transfer+$bankamount,
              

            ]
        );
    
      }  
           
       }

// new addition 2024-12-9


$total_value=$Bank_transfer+$creaditamount+$bankamount+$cashamount;




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+($total_value-($total_value*100/115))),
        //  'current_balance'=> $financial_accounts->current_balance+$total_value-($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+$total_value-($total_value*100/115),

     ]
     ); 
                     $customerdata = customers::find($customerId);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $total_value-($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+($total_value-($total_value*100/115))),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_value-($total_value*100/115),
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );


                $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_value-($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+$total_value-($total_value*100/115),

       ]
       ); 


 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_value-($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_value-($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_value-($total_value*100/115),
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );






  $financial_accounts= financial_accounts::find(112);
    financial_accounts::find(112)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+($total_value*100/115),
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

     ]
     ); 
credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  112,
                'recive_amount' => ($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> ($total_value*100/115),
                'debtor'=>0

            ]
        );


               $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+($total_value*100/115),
         'creditor_current'=>$financial_accounts->creditor_current+($total_value*100/115),
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

       ]
       ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_value*100/115),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+($total_value*100/115),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> ($total_value*100/115),
                'debtor'=>0

            ]
        );







  $financial_accounts= financial_accounts::find(183);
    financial_accounts::find(183)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost,
         'debtor_current'=>$financial_accounts->debtor_current+$total_cost,

     ]
     ); 
      credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  183,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost

            ]
        );


             $financial_accounts= financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_cost,
         'debtor_current'=>$financial_accounts->debtor_current+$total_cost,

       ]
       ); 


 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost

            ]
        );





  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_cost,
         'creditor_current'=>$financial_accounts->creditor_current+ $total_cost,

     ]
     ); 
      credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost,
                'debtor'=>0

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance-$total_cost,
        'creditor_current'=>$financial_accounts->creditor_current+ $total_cost,

       ]
       ); 
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost,
                'debtor'=>0

            ]
        );



// end addition



 
     
     
            if ($creaditamount != 0 || $creaditamount != null) {
                $customerdata = customers::find($customerId);

                $updateCustomer = customers::where('id', $customerId)->update(
                    [
                        'Balance' => $customerdata->Balance + ($creaditamount)
                    ]
                );
  
  
     $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$customerId)->first();
   
         financial_accounts::where('orginal_type',1)->where('orginal_id',$customerId)->update(
     [
         'current_balance'=>$financial_accounts->current_balance+($creaditamount),
         'debtor_current'=>$financial_accounts->debtor_current+ $creaditamount,

     ]
     ); 
     
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $creaditamount,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $confirminvoice->id,
                'currentblance'=>$financial_accounts->current_balance+$creaditamount,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$creaditamount

            ]
        );


            }
            $updateCustomer = customers::find($customerId);
            invoices::find($confirminvoice->id)->update(
                [
                    'currentblance' =>$updateCustomer->Balance,

                ]
            );
        }
        return $confirminvoice->id;
    }


  public function get_all_customer(){
     return  customers::get();
  }
  public function update_customer_name($id,$name){
       customers::find($id)->update(['comp_name'=>$name]);
       return 1;
  }




    public function store(Request $request)
    {
        //

        // return Auth()->User()->branch->id;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avtSaleRate = Avt::find(1);
        //return $avtSaleRate->AVT;
        $updateProduct = products::find($request->productNo);

        //return $updateProduct;


        // if ($updateProduct->numberofpice >= $request->quantity) {
        if (true) {

            if (Auth()->user()->branchs_id == $updateProduct->branchs_id) {
                // products::where('id', $request->productNo)->Update([
                //     'numberofpice' => $updateProduct->numberofpice - $request->quantity,
                //     'numberـofـsales' => $updateProduct->numberـofـsales + $request->quantity
                // ]);
            }
            $invoiceNumber = $request->invoice_number;
            if ($request->invoice_number == null) {
                // return ($request->pay);

                $Invoice = temp_invoice::create(
                    [
                        'customer_id' => $request->clientnamesearch ?? 1,
                        'user_id' => Auth()->user()->id,
                        'Price' => ($request->product_price) * $request->quantity,
                        'Added_Value' => (($request->product_price) * $avtSaleRate->AVT) * $request->quantity,
                        'Pay' => $request->pay,
                        'status' => Auth()->user()->branchs_id == $updateProduct->branchs_id ? 0 : 1,
                        'branchs_id' => Auth()->User()->branch->id,
                        'discountOnProduct' => $request->product_price_after_dis,
                        'discount' => $request->product_price_after_dis,
                        'Number_of_Quantity' => $request->quantity,
                        'note' => $request->note,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                               'p_o'=>$request->p_o,
                    ]
                );

                $invoiceNumber = $Invoice->id;
            } else {

                $InvoiceData = temp_invoice::find($invoiceNumber);
                $Invoice = temp_invoice::where('id',  $invoiceNumber)->Update(
                    [
                        'discount' => $request->product_price_after_dis,
                        'Price' => $InvoiceData->Price + (($request->product_price) * $request->quantity),
                        'Added_Value' => $InvoiceData->Added_Value + ((($request->product_price) * $avtSaleRate->AVT) * $request->quantity),
                        'Number_of_Quantity' => $InvoiceData->Number_of_Quantity + $request->quantity,
                        'discount' => $InvoiceData->discount + $request->product_price_after_dis,
                        'discountOnProduct' => $InvoiceData->discountOnProduct + $request->product_price_after_dis,
                        'note' => $request->note,
                        'status' => $InvoiceData->status != 1 &  Auth()->user()->branchs_id == $updateProduct->branchs_id ? 0 : 1,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                                                       'p_o'=>$request->p_o,

                    ]
                );
            }
            $checksale = temp_sales::where('invoice_id', $invoiceNumber)->where('product_id', $request->productNo)->first();

            if ($checksale != null) {
                $InvoiceData = temp_invoice::find($invoiceNumber);
                $Invoice = temp_invoice::where('id',  $invoiceNumber)->Update(
                    [
                        'Price' => $InvoiceData->Price - (($request->product_price) * $request->quantity) + ($checksale->Unit_Price * $request->quantity),
                        'Added_Value' => $InvoiceData->Added_Value - ((($request->product_price) * $avtSaleRate->AVT) * $request->quantity) + (($checksale->Unit_Price * $request->quantity * $avtSaleRate->AVT)),

                    ]
                );
                $enterrecent = temp_sales::where('invoice_id', $invoiceNumber)->where('product_id', $request->productNo)->first();
                temp_sales::where('invoice_id', $invoiceNumber)->where('product_id', $request->productNo)->update(
                    [
                        'quantity' => $request->quantity + $checksale->quantity,
                        'reamingQuantity' => $enterrecent->reamingQuantity - $request->quantity
                    ]
                );
            } else {
                $productSales = temp_sales::create(
                    [
                        'product_id' => $request->productNo,
                        'invoice_id' => $invoiceNumber,
                        'branch_id' => Auth()->User()->branch->id,
                        'Discount_Value' => $request->product_price_after_dis,
                        'Added_Value' => ($request->product_price) * $avtSaleRate->AVT,
                        'Unit_Price' => $request->product_price,
                        'reamingQuantity' => $updateProduct->numberofpice - $request->quantity,
                        'quantity' => $request->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
            }
        } else {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'عدم وجود مخزون من هذه المنتج' : 'Out of stock of this product';

            session()->flash('delete',  $message);
            $data = ["notfount"];

            // return view('products.sales',compact('data'));
            return $data;
        }



        $products = temp_sales::where('invoice_id', $invoiceNumber)->get();
        //return $product;
        $allProdctsD = [];
        $i = 0;
        $total_profit=0;
        foreach ($products as $product) {
            $i++;
            $updateProduct = products::find($product->product_id);
            $total_profit+=(($product->Unit_Price)-$updateProduct->purchasingـprice)*$product->quantity;

            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'Discount_Value' => $product->Discount_Value,
                'reamingquantity' => $updateProduct->numberofpice - $product->quantity,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
        }


        $customer = customers::find($request->clientnamesearch);
        $InvoiceData = temp_invoice::find($invoiceNumber);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'invoice_number' => $invoiceNumber,
            'pay' => $request->pay,
            'customer' => $customer,
            'product' => $allProdctsD,
            "invoice_id" => $invoiceNumber,
            'total_profit'=>round($total_profit*1- $InvoiceData->discount*1,2)
        ];



        return ($data);
    }


    public function printreturnInvoice($request)
    {
        //
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = return_sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);
        $data = [

            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
        ];

        return  view('products.printInvoicesToClientReturnSales', compact('data'));
    }




    public function returnsalesprinter($request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = return_sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);
          $totalprice = 0;
          $totalAddedValue = 0;
          $discount_total=0;
          foreach ($saleData as $product){
               $discount_total+=( $product->discountvalue + $product->discountoninvoice);
               $totalprice += ($product->return_Unit_Price * $product->return_quantity) - $product->discountvalue - $product->discountoninvoice;
                                 
          }
                               
                                
                             
   $data = [
            "invoicetotal_price" => round($totalprice,2),
            "invoicetotal_addedvalue" => round($totalprice,2)* $avtSaleRate->AVT,
            "invoicetotal_discount" => round($discount_total,2),
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
        ];
        return  view('products.returnsales_role', compact('data'));
    }


    public function printreturnReturnSalesInvoice($request)
    {
        //
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = return_sales::where("invoice_id", $request)->where("quantity", '!=', 0)->get();
        $InvoiceData = invoices::find($request);
        $data = [

            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
        ];

        return  view('products.printInvoicesToClientReturnSales', compact('data'));
    }
    public function showInvoiceRecent($request)
    {
        //
        //  return $request;

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = sales::where("invoice_id", $request)->where('quantity', '!=', 0)->get();
        $InvoiceData = invoices::find($request);
  $totAL=round(($InvoiceData->Price - $InvoiceData->discount)+(($InvoiceData->Price - $InvoiceData->discount)*$avtSaleRate->AVT),2);
                 $totAL= number_format($totAL, 2);


        list($whole, $decimal) = explode('.',str_replace(",","",$totAL));
         $numberToWord = new NumberToWord();
         $check=str_split($decimal);
         if($check[0]=="0"){
           $decimal =(int)$check[1] ;
         }
         else{
        $decimal =$decimal ;
 
         }
      $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
    
        $data = [
            "invoicetotal_price" =>  number_format(($Total_Amount*100/(100+($avtSaleRate->AVT*100))),2,'.',''),
            "invoicetotal_addedvalue" =>  number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
         'totatextlriyales'=>NumToArabic::number2Word(round((int)$whole,2)) .'  ريال',
            'totatextlrihalala'=>$decimal!='00'?NumToArabic::number2Word(round((int)$decimal,2)). '   هللة':'فقط', 

        ];

        return  view('products.printInvoicesReturnToClientRecentSales', compact('data'));
    }


    public function getlastprice($product_id,$customer_id)
    {
       
    
    
      $data_supplier=[];
    
foreach(invoices::where("customer_id", $customer_id)->where('save', 1)->orderby('id','desc')->get() as $invoice){

$saleData = sales::where("invoice_id", $invoice->id)->where("product_id", $product_id)->first();
if(  $saleData!=null){
         $data_supplier[]=[
                        'invoiceid'=> $invoice->id ,
                        'date'=>substr($invoice->created_at,0,10),
                        'cost'=>$saleData->Unit_Price+$saleData->Added_Value,
                        'quantity'=>$saleData->quantity,
                        ];
}
}

        return $data_supplier;
    


    }



  public function getlastprice_offer_price($product_id,$customer_id)
    {
       
    
      $data_supplier=[];
foreach(offer_price_to_customer::where("customer_id", $customer_id)->orderby('id','desc')->get() as $invoice){
$saleData = offer_price_to_customer_items::where("order_id", $invoice->id)->where("product_id", $product_id)->first();
if(  $saleData!=null){
         $data_supplier[]=[
                        'invoiceid'=> $invoice->id ,
                        'date'=>substr($invoice->created_at,0,10),
                        'cost'=>$saleData->PriceWithoudTax+round($saleData->PriceWithoudTax*0.15,2),
                        'quantity'=>$saleData->quantity,
                        ];
}
}

        return $data_supplier;
    


    }
    
    
    
    public function showRecieptRecent($request)
    {
        //
        //  return $request;


        $avtSaleRate = Avt::find(1);
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);
            $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
   
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,

            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,

        ];
        // return $InvoiceData->customer->name;
        return  view('products.printInvoicesToCustomer', compact('data'));
    }

    public function showInvoice($request)
    {
        //
        //  return $request;

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);
    $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
  
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,

        ];

        //  return $data;
        return  view('products.printInvoicesToClient', compact('data'));
    }


    public function changechustomerInInvoice($orderId, $newUserId)
    {

        $invoices = invoices::where('id', $orderId)->first();


        if ($invoices->Pay == 'Credit') {
            $customerdata = customers::find($invoices->customer_id);
            $avt = Avt::find(1);
            $saleavt = $avt->AVT;
            // $updateCustomer = customers::where('id', $invoices->customer_id)->update(
            //     [
            //         'Balance' => $customerdata->Balance - (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
            //     ]
            // );
            $customerdatanew = customers::find($newUserId);

            // $updateCustomer = customers::where('id', $newUserId)->update(
            //     [
            //         'Balance' => $customerdatanew->Balance + (($invoices->Price - $invoices->discount) + (($invoices->Price - $invoices->discount) * $saleavt))
            //     ]
            // );
        }
        $InvoiceData = temp_invoice::where('id', $orderId)->update(
            ['customer_id' => $newUserId]
        );
        return 'Done';
    }

    public function changePaymethodIninvoice($orderId, $paymentMethod)
    {
        $invoices = invoices::where('id', $orderId)->first();

        // if ($invoices->Pay == 'Credit') {
        //     $customerdata = customers::find($invoices->customer_id);

        //     $updateCustomer = customers::where('id', $invoices->customer_id)->update(
        //         [
        //             'Balance' => $customerdata->Balance - ($invoices->Price + $invoices->Added_Value - $invoices->discount)
        //         ]
        //     );
        // }
        $InvoiceData = invoices::where('id', $orderId)->update(
            ['Pay' => $paymentMethod]
        );
        $invoices = invoices::where('id', $orderId)->first();
        // if ($paymentMethod == "Credit") {
        //     $customerdata = customers::find($invoices->customer_id);

        //     $updateCustomer = customers::where('id', $invoices->customer_id)->update(
        //         [
        //             'Balance' => $customerdata->Balance + ($invoices->Price + $invoices->Added_Value - $invoices->discount)
        //         ]
        //     );
        // }
        return 'Done';
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request)
    {
        //
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        if ($request->show_invoice_number == null) {
            $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);
            session()->flash('nodataprint', '');

            return view('products.sales', compact('products'));
        }
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avtSaleRate = Avt::find(1);


        $saleData = sales::where("invoice_id", $request->show_invoice_number)->where('quantity', '!=', 0)->get();
        $InvoiceData = invoices::find($request->show_invoice_number);
        $totAL=round(($InvoiceData->Price - $InvoiceData->discount)+(($InvoiceData->Price - $InvoiceData->discount)*$avtSaleRate->AVT),2);
                 $totAL= number_format($totAL, 2);


    list($whole, $decimal) = explode('.',str_replace(",","",$totAL));
         $numberToWord = new NumberToWord();
         $check=str_split($decimal);
         if($check[0]=="0"){
           $decimal =(int)$check[1] ;
         }
         else{
        $decimal =$decimal ;
 
         }
             $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
  
        $data = [
            
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
            'totatextlriyales'=>NumToArabic::number2Word(round((int)$whole,2)) .'  ريال',
            'totatextlrihalala'=>$decimal!='00'?NumToArabic::number2Word(round((int)$decimal,2)). '   هللة':'فقط', 

        ];


        // $saleData= sales::where("invoice_id",$invoicesid)->get();
        // $InvoiceData=invoices::find($invoicesid);
        // $data=[
        //     'salesData'=>$saleData ,
        //     'invoiceData'=>  $InvoiceData,
        // ];
        return  view('products.printInvoicesToClient', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */

    public function increaseProduct(Request $request)
    {
        //
        // return  $request;
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = temp_sales::find($request->id);
        $productData = products::find($saleData->product_id);
        if (true) {


            $productSales = temp_sales::where('id', $request->id)->update(
                [
                    'quantity' => $saleData->quantity + $request->increasequantity,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );

            $productData = products::find($saleData->product_id);
            // if (Auth()->user()->branchs_id == $productData->branchs_id) {

            //     products::where('id', $saleData->product_id)->Update([
            //         'numberofpice' => $productData->numberofpice - $request->increasequantity
            //     ]);
            // }
            $InvoiceData = temp_invoice::find($saleData->invoice_id);

            $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                [

                    'Price' => round($InvoiceData->Price + ($saleData->Unit_Price * $request->increasequantity), 2),
                    'Added_Value' => round(($InvoiceData->Added_Value + ($saleData->Unit_Price * $request->increasequantity * $avtSaleRate->AV)),
                        2
                    ),
                    'Number_of_Quantity' => $InvoiceData->Number_of_Quantity + $request->increasequantity,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );


            $InvoiceData = temp_invoice::find($saleData->invoice_id);
            // if ($InvoiceData->Pay == "Credit") {
            //     $customerdata = customers::find($InvoiceData->customer_id);
            //     // return ($customerdata->Balance-(($request->return_quentity*$saleData->Unit_Price)+($request->return_quentity*$saleData->Added_Value)));
            //     $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //         [
            //             'Balance' => $customerdata->Balance + (($request->increasequantity * $saleData->Unit_Price) + ($request->increasequantity * $saleData->Added_Value)),
            //             'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //         ]
            //     );
            // }

            $products = temp_sales::where('invoice_id', $saleData->invoice_id)->get();
            $allProdctsD = [];
            $i = 0;
            foreach ($products as $product) {
                $updateProduct = products::find($product->product_id);

                $i++;
                $allProdctsD[] = [
                    'Product_Code' => $product->productData->Product_Code,
                    'product_name' => $product->productData->product_name,
                    'quantity' => $product->quantity,
                    'Unit_Price' => $product->Unit_Price,
                    'reamingquantity' => $updateProduct->numberofpice - $product->quantity,
                    'Discount_Value' => $product->Discount_Value,
                    'Added_Value' => $product->Added_Value,
                    'count' => $i,
                    'id' => $product->id
                ];
            }
            //return $product;
            $data = [
                "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
                "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
                "invoicetotal_discount" => $InvoiceData->discount,
                'product' => $allProdctsD,
                "invoice_id" => $saleData->invoice_id
            ];
            return $data;
        } else {
            return ["notfount"];
        }
        return view('products.sales', compact('data'));
    }
    public function printReceiptToStorehouse(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);
        // return $request;
        if ($request->show_invoice_number == null) {
            $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);
            // return $products;
            session()->flash('nodataprint', '');
            return view('products.Receipt', compact('products'));
        }
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = sales::where("invoice_id", $request->show_invoice_number)->where('quantity', '!=', 0)->get();
        $InvoiceData = invoices::find($request->show_invoice_number);
            $setting=system_setting::find(1);
             $Total_Amount=$InvoiceData->Bank_transfer +  $InvoiceData->creaditamount + $InvoiceData->bankamount + $InvoiceData->cashamount;

         $data = [
            $setting->name_ar,
            $setting->Tax,
            (string)$InvoiceData->issue_date . 'T' . (string)$InvoiceData->issue_time,
            number_format(($Total_Amount),2,'.',''),
            number_format( (($Total_Amount*100/(100+($avtSaleRate->AVT*100)))) * $avtSaleRate->AVT,2,'.',''),
        ];
        $data[] = '';
        $data[] ='';
        $data[] = '';
        $data[] = '';
    
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,

        ];
        // return $InvoiceData->customer->name;
        return  view('products.printInvoicesToCustomer', compact('data'));
    }




    public function editRecipt(Request $request)
    {
        //
        // return  $request;
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = sales::find($request->id);
        $productSales = sales::where('id', $request->id)->update(
            [
                'quantity' => $saleData->quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );

        $productData = products::find($saleData->product_id);
        // products::where('id', $saleData->product_id)->Update([
        //     'numberofpice' => $productData->numberofpice + $request->return_quentity
        // ]);
        $InvoiceData = invoices::find($saleData->invoice_id);

        $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
            [

                'Price' => round(($InvoiceData->Price - ($saleData->Unit_Price * $request->return_quentity)), 2),
                'Added_Value' => round(($InvoiceData->Added_Value - ($saleData->Unit_Price * $request->return_quentity * $avtSaleRate->AVT)), 2),

                'Number_of_Quantity' => $InvoiceData->Number_of_Quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );


        $InvoiceData = invoices::find($saleData->invoice_id);
        if ($InvoiceData->Pay == "Credit") {
            $customerdata = customers::find($InvoiceData->customer_id);
            // return ($customerdata->Balance-(($request->return_quentity*$saleData->Unit_Price)+($request->return_quentity*$saleData->Added_Value)));
            // $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //     [
            //         'Balance' => $customerdata->Balance - (($request->return_quentity * $saleData->Unit_Price) + ($request->return_quentity * $saleData->Added_Value)),
            //         'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //     ]
            // );
        }

        $products = sales::where('invoice_id', $saleData->invoice_id)->get();
        $allProdctsD = [];
        $i = 0;
        foreach ($products as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'Discount_Value' => $product->Discount_Value,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
        }
        //return $product;
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'product' => $allProdctsD,
            "invoice_id" => $saleData->invoice_id
        ];
        return $data;
        return view('products.sales', compact('data'));
    }


    public function returnAll(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $message = '';
        $page = $request->pagename;
        $page =   $page;
        $avtSaleRate = Avt::find(1);
        if ($request->invoice_no_delete_All == null) {

            $products = products::where('branchs_id', Auth()->user()->branchs_id)->paginate(10);
            return view($page, compact('products'));
        }
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $InvoiceData = invoices::find($request->invoice_no_delete_All);
$discount_value_invoice=$InvoiceData->discount;
$paymentMethod=$InvoiceData->Pay;

        $saleData = sales::where('invoice_id', $InvoiceData->id)->get();
        $count = count($saleData);

        //  return   $saleData;
        $total_cost_value=0;
        foreach ($saleData as $sale) {
            $updateProduct = products::find($sale->product_id);
            $total_cost_value+=$updateProduct->purchasingـprice*$sale->quantity;
            if ($updateProduct->branchs_id == $InvoiceData->branchs_id) {
                products::where('id', $sale->product_id)->Update([
                    'numberofpice' => $updateProduct->numberofpice + $sale->quantity,
                    'numberـofـsales' => $updateProduct->numberـofـsales - $sale->quantity
                ]);
                $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم عملية الاسترجاع بنجاح شكرا" : "The recovery process was successful. Thank you.";

                session()->flash('success', $message);
            } else {
                $mproduct = products::where('branchs_id', $InvoiceData->branchs_id)->where('Product_Code', $updateProduct->Product_Code)->first();
                if ($mproduct != null) {
                    products::where('branchs_id', $InvoiceData->branchs_id)->where('Product_Code', $updateProduct->Product_Code)->Update([
                        'numberofpice' => $mproduct->numberofpice + $sale->quantity,
                        'purchasingـprice' => $updateProduct->purchasingـprice,

                    ]);
                    $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم عملية الاسترجاع بنجاح شكرا" : "The recovery process was successful. Thank you.";

                    session()->flash('success', $message);
                } else {
                    $newproducts = products::create(
                        [
                            'product_name' => $updateProduct->product_name,
                            'name_en' => $updateProduct->name_en,
                            'branchs_id' => $InvoiceData->branchs_id,
                            'user_id' => Auth()->User()->id,
                            'Product_Location' => $updateProduct->Product_Location,
                            'Product_Code' => $updateProduct->Product_Code,
                            'purchasingـprice' => $updateProduct->purchasingـprice,
                            'average_cost' => $updateProduct->purchasingـprice,
                            'Status' => 1,
                            'notes' => $updateProduct->notes,
                            'unit' => $updateProduct->unit,
                            'minmum_quantity_stock_alart' => $updateProduct->minmum_quantity_stock_alart,
                        ]
                    );
                    $productname = $updateProduct->product_name;

                    $message = LaravelLocalization::getCurrentLocale() == 'ar' ?  "  تم عملية الاسترجاع . المنتج المسترجع غير مسجل لديكم مسبقا تم تسجيل  " . $productname . "  بنفس رقم المنتج  شكرا  " : "The product is not previously registered. It has been registered with a name " . $productname . " and a product number, such as the number below";
                    products::where('id', $newproducts->id)->Update([
                        'numberofpice' =>  $sale->quantity,
                    ]);
                    session()->flash('createnewproduct', $message);
                }
            }

            $invicedis = invoices::where('id',  $sale->invoice_id)->first();
  Delivery_product_to_the_customer::where('invoice_id', $sale->invoice_id)->where('product_id', $sale->product_id)->update(
                [
                    'quantity' => 0,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            
            
                        $discount_value_invoice-=$sale->Discount_Value;

            if ($count == 1) {
                if($sale->quantity>0){

                $return_sales = return_sales::create([
                    'product_id' => $sale->product_id,
                    'invoice_id' => $sale->invoice_id,
                    'branch_id' => Auth()->User()->branch->id,
                    'discountvalue' => $sale->Discount_Value,
                    'discountoninvoice' => $discount_value_invoice,
                    'return_Added_Value' => $sale->Added_Value,
                    'return_Unit_Price' => $sale->Unit_Price,
                    'return_quantity' => $sale->quantity,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),

                ]);
                }
            } else {
                
                
        
                                if($sale->quantity>0){

                $return_sales = return_sales::create([
                    'product_id' => $sale->product_id,
                    'invoice_id' => $sale->invoice_id,
                    'branch_id' => Auth()->User()->branch->id,
                    'discountvalue' => $sale->Discount_Value,
                    'return_Added_Value' => $sale->Added_Value,
                    'return_Unit_Price' => $sale->Unit_Price,
                    'return_quantity' => $sale->quantity,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),

                ]);
                                }
            }
            $mSale = sales::where('id', $sale->id)->first();


            $productSales = sales::where('id', $sale->id)->update(
                [
                    'quantity' => 0,
                    'quantityreturn' => $sale->quantity,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'Discount_Value' => 0
                ]
            );
            $count--;
        }
        $paymentMethod=$request->pay_return_sale;
        
        if ($request->pay_return_sale== "Shabka" ||$request->pay_return_sale == "Bank_transfer" ) {


            $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
       [
           'current_balance'=> $financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
            'creditor_current'=>$financial_accounts->creditor_current+ (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),

       ]
       ); 
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' =>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'debtor'=>0,
              

            ]
        );
  
         
         $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
            'current_balance'=> $financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
            'creditor_current'=>$financial_accounts->creditor_current+ (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),

       ]
       ); 
       
             credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'debtor'=>0,
              

            ]
        );
  



}



        if ($request->pay_return_sale == "Cash" ) {

            $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
            'creditor_current'=>$financial_accounts->creditor_current+ (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),

       ]
       ); 
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' =>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'debtor'=>0,
              

            ]
        );
  
         
         $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
            'current_balance'=> $financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
            'creditor_current'=>$financial_accounts->creditor_current+ (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),

       ]
       ); 
       
             credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'debtor'=>0,
              

            ]
        );
  

}

  
        if ($request->pay_return_sale == "Credit") {
            $avtSaleRate = Avt::find(1);

            $invicedis = invoices::find($request->invoice_no_delete_All);

            $customerdata = customers::find($InvoiceData->customer_id);
            $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),


                ]
            );
            
    $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->first();
   financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->update(
     [
         'current_balance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
         'creditor_current'=>$financial_accounts->creditor_current+ (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),

     ]
     ); 
     
     
             credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance- (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  (($invicedis->Price - $invicedis->discount) + (($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT)),
                'debtor'=>0,
              

            ]
        );
  
        }







// new addition 2024-12-9


$total_tax=(($invicedis->Price - $invicedis->discount) * $avtSaleRate->AVT);
$total_withoud_tax=($invicedis->Price - $invicedis->discount) ;




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=>  ($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
        //  'current_balance'=> $financial_accounts->current_balance-$total_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

     ]
     ); 
                     $customerdata = customers::find($InvoiceData->customer_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );


                     $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
       'current_balance'=> ($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );






  $financial_accounts= financial_accounts::find(112);
    financial_accounts::find(112)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

     ]
     ); 
      credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  112,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );


               $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );







  $financial_accounts= financial_accounts::find(183);
    financial_accounts::find(183)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

     ]
     ); 
     
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  183,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );


           $financial_accounts= financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );





  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

     ]
     ); 
       credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

       ]
       ); 
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );



// end addition



 
     














  $invoicedatarecent=invoices::find($request->invoice_no_delete_All);
        if($invoicedatarecent->NOTICE_Number!=0){
        $NOTICE_Number=$invoicedatarecent->NOTICE_Number;

        }
        else{
        $recentreturn=invoices::where('id','!=',$request->invoice_no_delete_All)->where('NOTICE_Number','!=',0)->orderBy('NOTICE_Number', 'DESC')->first();
        if($recentreturn){
                    $NOTICE_Number=$recentreturn->NOTICE_Number==null?0+1:$recentreturn->NOTICE_Number+1;

            
        }
        else{
             $NOTICE_Number=1;
        }
        }

        $Invoice = invoices::where('id', $request->invoice_no_delete_All)->Update(
            [

                'Price' => 0,
                'Added_Value' => 0,
                'Number_of_Quantity' => 0,
                'discountOnInvoice' => $InvoiceData->discount - $sale->Discount_Value,
                'discount' => 0,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'NOTICE_Number'=>$NOTICE_Number,
                'payment_return'=>$request->pay_return_sale,


            ]
        );



        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);
        $data = [
            'message' => $message,
        ];
        return $data;
    }



    public function updateproductallDataInvoices(Request $request)
    {
        //
        // return  $request;
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = temp_sales::find($request->id);
        $newquantity = 0;

        $productData = products::find($saleData->product_id);

        $newquantity = $request->quentity - $saleData->quantity;
        if (true) {
            // if (Auth()->user()->branchs_id == $productData->branchs_id) {

            //     products::where('id', $saleData->product_id)->Update([
            //         'numberofpice' => $productData->numberofpice + ($newquantity)
            //     ]);
            // }





            $InvoiceData = temp_invoice::find($saleData->invoice_id);
            $customerdata = customers::find($InvoiceData->customer_id);

            // if ($InvoiceData->Pay == "Credit") {

            //     // $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //     //     [
            //     //         'Balance' => $customerdata->Balance - ((($saleData->quantity * $saleData->Unit_Price) - $saleData->Discount_Value) + ((($saleData->quantity  * $saleData->Unit_Price) - $saleData->Discount_Value) * $avtSaleRate->AVT)),
            //     //         'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //     //     ]
            //     // );
            //     $customerdata = customers::find($InvoiceData->customer_id);

            //     $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //         [

            //             'Balance' => $customerdata->Balance + ((($request->quentity * $request->price)) + ((($request->quentity * $request->price) - $request->discount) * $avtSaleRate->AVT)),
            //             'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //         ]
            //     );
            // }


            $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                [

                    'Price' => $InvoiceData->Price - (($saleData->Unit_Price * $saleData->quantity)),
                    'Added_Value' => $InvoiceData->Added_Value - ((($saleData->Unit_Price * $saleData->quantity) - $saleData->Discount_Value) * $avtSaleRate->AVT),
                    'Number_of_Quantity' => $InvoiceData->Number_of_Quantity - $saleData->quantity,
                    'discount' => $InvoiceData->discount - $saleData->Discount_Value,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $InvoiceData = temp_invoice::find($saleData->invoice_id);

            $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                [

                    'Price' => $InvoiceData->Price + (($request->quentity * $request->price)),
                    'Added_Value' => $InvoiceData->Added_Value + ((($request->quentity * $request->price)) * $avtSaleRate->AVT),
                    'Number_of_Quantity' => $InvoiceData->Number_of_Quantity + $request->quentity,
                    'discount' => $InvoiceData->discount + $request->discount,
                    'discountOnProduct' => $request->discount,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );

            $InvoiceData = temp_invoice::find($saleData->invoice_id);

            // if ($InvoiceData->Number_of_Quantity == 0 && $InvoiceData->Pay == "Credit") {

            //     $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //         [
            //             'Balance' => $customerdata->Balance - $InvoiceData->discount,
            //             'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //         ]
            //     );
            // }


            if ($InvoiceData->Number_of_Quantity == 0) {
                $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                    [
                        'discount' => 0,
                    ]
                );
            }
            $productSales = temp_sales::where('id', $request->id)->update(
                [
                    'quantity' => $request->quentity,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'Discount_Value' => $request->discount,
                    'Unit_Price' => $request->price,
                    'Added_Value' => $request->avt

                ]
            );

            $products = temp_sales::where('invoice_id', $saleData->invoice_id)->get();
            $allProdctsD = [];
            $i = 0;
            foreach ($products as $product) {
                $updateProduct = products::find($product->product_id);

                $i++;
                $allProdctsD[] = [
                    'Product_Code' => $product->productData->Product_Code,
                    'product_name' => $product->productData->product_name,
                    'quantity' => $product->quantity,
                    'Unit_Price' => $product->Unit_Price,
                    'reamingquantity' => $updateProduct->numberofpice - $product->quantity,
                    'Discount_Value' => $product->Discount_Value,
                    'Added_Value' => $product->Added_Value,
                    'count' => $i,
                    'id' => $product->id
                ];
            }
            $InvoiceData = temp_invoice::find($saleData->invoice_id);

            //return $product;
            $data = [
                "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
                "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
                "invoicetotal_discount" => $InvoiceData->discount,
                'product' => $allProdctsD,
                "invoice_id" => $saleData->invoice_id
            ];
            return $data;
        } else {
            return [];
        }
    }









    public function edit(Request $request)
    {
        //
        // return  $request;
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $saleData = temp_sales::find($request->id);


        $productData = products::find($saleData->product_id);
        if (true) {

            // products::where('id', $saleData->product_id)->Update([
            //     'numberofpice' => $productData->numberofpice + $request->return_quentity
            // ]);
        }



        $InvoiceData = temp_invoice::find($saleData->invoice_id);
        // if ($InvoiceData->Pay == "Credit") {
        //     $customerdata = customers::find($InvoiceData->customer_id);
        //     // return ($customerdata->Balance-(($request->return_quentity*$saleData->Unit_Price)+($request->return_quentity*$saleData->Added_Value)));
        //     $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
        //         [
        //             'Balance' => $customerdata->Balance - ((($request->return_quentity * $saleData->Unit_Price) - $saleData->Discount_Value) + ((($request->return_quentity * $saleData->Unit_Price) - $saleData->Discount_Value) * $avtSaleRate->AVT)),
        //             'updated_at' => \Carbon\Carbon::now()->addHours(3),

        //         ]
        //     );
        // }

        $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
            [

                'Price' => round($InvoiceData->Price - (($saleData->Unit_Price * $request->return_quentity)), 2),
                'Added_Value' => round($InvoiceData->Added_Value - ((($saleData->Unit_Price * $request->return_quentity)) * $avtSaleRate->AVT), 2),
                'Number_of_Quantity' => $InvoiceData->Number_of_Quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );
        $InvoiceData = temp_invoice::find($saleData->invoice_id);

        if ($InvoiceData->Number_of_Quantity == 0 && $InvoiceData->Pay == "Credit") {
            $InvoiceData = invoices::find($saleData->invoice_id);

            // $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
            //     [
            //         'Balance' => $customerdata->Balance - $InvoiceData->discount,
            //         'updated_at' => \Carbon\Carbon::now()->addHours(3),

            //     ]
            // );
        }


        if ($InvoiceData->Number_of_Quantity == 0) {
            $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                [
                    'discount' => 0,
                ]
            );
        } else {
            $Invoice = temp_invoice::where('id',  $saleData->invoice_id)->Update(
                [
                    'discount' => $InvoiceData->discount - $saleData->Discount_Value,
                    'discountOnProduct' => $InvoiceData->discountOnProduct - $saleData->Discount_Value,
                ]
            );
        }
        $productSales = temp_sales::where('id', $request->id)->update(
            [
                'quantity' => $saleData->quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'Discount_Value' => 0
            ]
        );

        $products = temp_sales::where('invoice_id', $saleData->invoice_id)->get();
        $allProdctsD = [];
        $i = 0;
        foreach ($products as $product) {
            $i++;
            $updateProduct = products::find($product->product_id);

            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->quantity,
                'Unit_Price' => $product->Unit_Price,
                'reamingquantity' =>  $updateProduct->numberofpice - $product->quantity,
                'Discount_Value' => $product->Discount_Value,
                'Added_Value' => $product->Added_Value,
                'count' => $i,
                'id' => $product->id
            ];
        }
        $InvoiceData = temp_invoice::find($saleData->invoice_id);

        //return $product;
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'product' => $allProdctsD,
            "invoice_id" => $saleData->invoice_id
        ];
        return $data;
        return view('products.sales', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */

    public function return_sale(Request $request)
    {
        //
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $product = sales::where('invoice_id', $request->invoice_no)->where('save', 1)->get();
        if (count($product) == 0) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? '  لم يتم العثور علي فاتورة بهذة الرقم' : 'No invoice with this number was found';

            session()->flash('notfountreturnproduct', $message);
            $data = [];
            return view('products.salesreturned', compact('data'));
        } else {
            $InvoiceData = invoices::find($request->invoice_no);

            $data = [
                "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
                "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
                "invoicetotal_discount" => $InvoiceData->discount,
                'product' => $product,
                'payment' => $InvoiceData->Pay,
                "invoice_id" => $request->invoice_no
            ];
            session()->flash('foundinvoice', '   تم العثور علي فاتورة ');

            return view('products.salesreturned', compact('data'));
        }
    }

    public function update_return_Sale(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avtSaleRate = Avt::find(1);
        $returnshabkavalue = 0;
        $saleData = sales::find($request->id);
        //return $saleData;
        $updateProduct = products::find($saleData->product_id);
        $InvoiceData = invoices::find($saleData->invoice_id);
        $total_cost_value=$updateProduct->purchasingـprice*$request->return_quentity;
$paymentMethod=$InvoiceData->Pay;

        if ($updateProduct->branchs_id == $InvoiceData->branchs_id) {
            products::where('id', $saleData->product_id)->Update([
                'numberofpice' => $updateProduct->numberofpice + $request->return_quentity,
                'numberـofـsales' => $updateProduct->numberـofـsales - $request->return_quentity
            ]);
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم عملية الاسترجاع بنجاح شكرا" : "The recovery process was successful. Thank you.";
            session()->flash('success', $message);
        } else {
             Delivery_product_to_the_customer::where('invoice_id', $saleData->invoice_id)->where('product_id', $saleData->product_id)->update(
                [
                    'quantity' => $saleData->quantity - $request->return_quentity,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $mproduct = products::where('branchs_id', $InvoiceData->branchs_id)->where('Product_Code', $updateProduct->Product_Code)->first();
            if ($mproduct != null) {
                products::where('branchs_id', $InvoiceData->branchs_id)->where('Product_Code', $updateProduct->Product_Code)->Update([
                    'numberofpice' => $mproduct->numberofpice + $request->return_quentity,
                    'purchasingـprice' => $updateProduct->purchasingـprice,

                ]);
                $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم عملية الاسترجاع بنجاح شكرا" : "The recovery process was successful. Thank you.";
                session()->flash('success', $message);
            } else {
                $product = sales::where('invoice_id', $saleData->invoice_id)->get();
                $InvoiceData = invoices::where('id',  $saleData->invoice_id)->first();
                //return $product;

                $newproducts = products::create(
                    [
                        'product_name' => $updateProduct->product_name,
                        'name_en' => $updateProduct->name_en,
                        'branchs_id' => $InvoiceData->branchs_id,
                        'user_id' => Auth()->User()->id,
                        'Product_Location' => $updateProduct->Product_Location,
                        'Product_Code' => $updateProduct->Product_Code,
                        'purchasingـprice' => $updateProduct->purchasingـprice,
                        'average_cost' => $updateProduct->purchasingـprice,
                        'Status' => 1,
                        'notes' => $updateProduct->notes,
                        'unit' => $updateProduct->unit,
                        'minmum_quantity_stock_alart' => $updateProduct->minmum_quantity_stock_alart,
                    ]
                );
                $productname = $updateProduct->product_name;

                $message = LaravelLocalization::getCurrentLocale() == 'ar' ?  "  تم عملية الاسترجاع. المنتج المسترجع غير مسجل لديكم مسبقا تم تسجيل  " . $productname . "  بنفس رقم المنتج  شكرا  " : "The product is not previously registered. It has been registered with a name " . $productname . " and a product number, such as the number ";
                products::where('id', $newproducts->id)->Update([
                    'numberofpice' =>  $request->return_quentity,
                ]);
                session()->flash('createnewproduct', $message);
            }
        }



        $saleData = sales::find($request->id);



        $finddsalefordiscount = sales::find($request->id);


        $invicedis = invoices::find($saleData->invoice_id);
        if (count(sales::where('invoice_id', $saleData->invoice_id)->where('quantity', '!=', 0)->get()) == 1 &&(($finddsalefordiscount->quantity - $request->return_quentity)==0)) {
            $return_sales = return_sales::create([
                'product_id' => $saleData->product_id,
                'invoice_id' => $saleData->invoice_id,
                'branch_id' => Auth()->User()->branch->id,
                'return_Added_Value' => $saleData->Added_Value,
                'return_Unit_Price' => $saleData->Unit_Price,
                'discountvalue' => $finddsalefordiscount->Discount_Value,
                'discountoninvoice' => $invicedis->discount - $finddsalefordiscount->Discount_Value,
                'returnshabkavalue' => $returnshabkavalue ?? 0,
                'return_quantity' => $request->return_quentity,
                'created_at' => \Carbon\Carbon::now()->addHours(3),

            ]);
        } else {
            $return_sales = return_sales::create([
                'product_id' => $saleData->product_id,
                'invoice_id' => $saleData->invoice_id,
                'branch_id' => Auth()->User()->branch->id,
                'return_Added_Value' => $saleData->Added_Value,
                'return_Unit_Price' => $saleData->Unit_Price,
                'discountvalue' => $saleData->Discount_Value,
                'returnshabkavalue' => $returnshabkavalue,
                'return_quantity' => $request->return_quentity,
                'created_at' => \Carbon\Carbon::now()->addHours(3),

            ]);
        }




        sales::find($request->id)->update(
            [
                'quantity' => $finddsalefordiscount->quantity - $request->return_quentity,
                'quantityreturn' => $finddsalefordiscount->quantityreturn + $request->return_quentity,
                'Discount_Value' => 0
            ]
        );



         $invoicedatarecent=invoices::find($saleData->invoice_id);
         $NOTICE_Number=0;
        if($invoicedatarecent->NOTICE_Number!=0){
        $NOTICE_Number=$invoicedatarecent->NOTICE_Number;

        }
        else{
        $recentreturn=invoices::where('id','!=',$saleData->invoice_id)->where('NOTICE_Number','!=',0)->orderBy('NOTICE_Number', 'DESC')->first();
        $NOTICE_Number=$recentreturn==null?1:$recentreturn->NOTICE_Number+1;
        }

        //  return  $return_sales;
        $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
            [
                'Price' => round($InvoiceData->Price - (($saleData->Unit_Price * $request->return_quentity) ), 2),
                'Added_Value' => round($InvoiceData->Added_Value - ((($saleData->Unit_Price * $request->return_quentity) - $saleData->Discount_Value) * $avtSaleRate->AVT), 2),
                'Number_of_Quantity' => $InvoiceData->Number_of_Quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'NOTICE_Number'=>$NOTICE_Number

            ]
        );
        $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
            [
                'discountOnInvoice' => $InvoiceData->discount - $saleData->Discount_Value,
                'discount' => $InvoiceData->discount - $saleData->Discount_Value,
                'payment_return'=>$request->pay_return_sale
            ]
        );
        $InvoiceData = invoices::find($saleData->invoice_id);
        $customerdata = customers::find($InvoiceData->customer_id);

        if ($InvoiceData->Number_of_Quantity == 0) {
            $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
                [
                    'Price' => 0,
                    'Added_Value' => 0,
                    'discount' => 0,

                ]
            );

$total_value=(($request->return_quentity * $saleData->Unit_Price) -  $InvoiceData->discountOnInvoice);
$total_tax=((($request->return_quentity * $saleData->Unit_Price) -  $InvoiceData->discountOnInvoice) * $avtSaleRate->AVT);
$paymentMethod=$request->pay_return_sale;

        if ($request->pay_return_sale== "Shabka" ||$request->pay_return_sale == "Bank_transfer" ) {
            
            

            $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

      ] ); 
             
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
       
    $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

       ]
       ); 
       
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
        

            
            
        }

        elseif ($request->pay_return_sale == "Cash") {

            $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

      ] ); 
             
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
       
    $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

       ]
       ); 
       
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
        
}else{
                $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance-$total_tax-$total_value,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),

                    ]
                );
                
                    
    $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->first();
   financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->update(
     [
                        'current_balance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                        'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value
     ]
     ); 
     
     
     
              credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
            }
            
            
          
          
          


// new addition 2024-12-9

$total_tax=$total_tax;
$total_withoud_tax=$total_value ;




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=> ($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

     ]
     ); 
                     $customerdata = customers::find($InvoiceData->customer_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );

                     $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
       'current_balance'=> ($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );






  $financial_accounts= financial_accounts::find(112);
    financial_accounts::find(112)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

     ]
     ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  112,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
      'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

       ]
       ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );







  $financial_accounts= financial_accounts::find(183);
    financial_accounts::find(183)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

     ]
     ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  183,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );


           $financial_accounts= financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
       'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );





  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

     ]
     ); 
     credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );


              $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
    'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

       ]
       ); 
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );



// end addition
  
            
            
            
        } else {
            
            










$total_value=(($request->return_quentity * $saleData->Unit_Price) -  $saleData->Discount_Value);
$total_tax= ((($request->return_quentity * $saleData->Unit_Price) -  $saleData->Discount_Value) * $avtSaleRate->AVT);
$paymentMethod=$request->pay_return_sale;
        if ($request->pay_return_sale == "Shabka" ||$request->pay_return_sale == "Bank_transfer" ) {

            $financial_accounts= financial_accounts::find(4);
    financial_accounts::find(4)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

      ] ); 
      
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
    $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

       ]
       );    
       
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                                'user_id' => Auth()->user()->id,

                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
       
       
        
}
  
        if ( $request->pay_return_sale== "Cash" ) {

            $financial_accounts= financial_accounts::find(5);
    financial_accounts::find(5)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

      ] ); 
      
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
    $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_tax-$total_value,
          'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value

       ]
       );    
       
       
          credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                                'user_id' => Auth()->user()->id,

                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
       
       
        
}
  
            if ($InvoiceData->Pay == "Credit") {
                $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                    [
                        'Balance' => $customerdata->Balance-$total_tax-$total_value,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),

                    ]
                );
                
                    
    $financial_accounts= financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->first();
   financial_accounts::where('orginal_type',1)->where('orginal_id',$InvoiceData->customer_id)->update(
     [
                        'current_balance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                        'creditor_current'=>$financial_accounts->creditor_current+$total_tax+$total_value
     ]
     ); 
     
     
     
              credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  +$total_tax+$total_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax-$total_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $total_tax+$total_value,
                'debtor'=>0,
              

            ]
        );
  
            }
            
            
          
          
          


// new addition 2024-12-9

$total_tax=$total_tax;
$total_withoud_tax=$total_value ;




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=> ($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

     ]
     ); 
       $customerdata = customers::find($InvoiceData->customer_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>($financial_accounts->debtor_current+$total_tax)- ($financial_accounts->creditor_current),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );
                     $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance-$total_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_tax,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_tax,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->tax_no,

            ]
        );






  $financial_accounts= financial_accounts::find(112);
    financial_accounts::find(112)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

     ]
     ); 
    
    
    

        credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  112,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );

                $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
           'current_balance'=> $financial_accounts->current_balance-$total_withoud_tax,
         'debtor_current'=>$financial_accounts->debtor_current+$total_withoud_tax,
        //  'debtor_current'=>$financial_accounts->creditor_current+ $total_value,

       ]
       ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_withoud_tax,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=> $financial_accounts->current_balance-$total_withoud_tax,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$total_withoud_tax

            ]
        );







  $financial_accounts= financial_accounts::find(183);
    financial_accounts::find(183)->update(
     [
         'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

     ]
     ); 
     
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  183,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );


           $financial_accounts= financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',183)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
          'current_balance'=> $financial_accounts->current_balance-$total_cost_value,
         'creditor_current'=>$financial_accounts->creditor_current+$total_cost_value,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance-$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost_value,
                'debtor'=>0

            ]
        );





  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

     ]
     ); 
       credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );


              $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
       'current_balance'=> $financial_accounts->current_balance+$total_cost_value,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost_value,

       ]
       ); 
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مرتجع مبيعات رقم :'.(string) $InvoiceData->id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost_value

            ]
        );



// end addition
        }
        $InvoiceData = invoices::find($saleData->invoice_id);

        $productconvert = [];
        $product = sales::where('invoice_id', $saleData->invoice_id)->get();
        $InvoiceData = invoices::where('id',  $saleData->invoice_id)->first();
        //return $product;
        $i = 0;
        foreach ($product as $item) {
            $i++;
            if ($item->quantity > 0) {
                $productconvert[] = [
                    'count' => $i,
                    'Product_Code' => $item->productData->Product_Code,
                    'product_name' => $item->productData->product_name,
                    'quantity' => $item->quantity,
                    'Unit_Price' => $item->Unit_Price,
                    'Discount_Value' => $item->Discount_Value,
                    "id" => $item->id


                ];
            }
        }
        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => round(($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT, 2),
            "invoicetotal_discount" => $InvoiceData->discount,
            'total' => round(($InvoiceData->Price - $InvoiceData->discount) + ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT, 2),
            'product' => $productconvert,
            "invoice_id" => $saleData->invoice_id,
            "message" => $message
        ];
        return $data;
    }






    public function update(Request $request)
    {
        //
        //   return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avtSaleRate = Avt::find(1);

        $saleData = sales::find($request->id);
        //return $saleData;
        $updateProduct = products::find($saleData->product_id);

        products::where('id', $saleData->product_id)->Update([
            'numberofpice' => $updateProduct->numberofpice + $request->return_quentity,
            'numberـofـsales' => $updateProduct->numberـofـsales - $request->return_quentity
        ]);


        $InvoiceData = invoices::find($saleData->invoice_id);
        if ($InvoiceData->Pay == "Credit") {
            $customerdata = customers::find($InvoiceData->customer_id);
            // return ($customerdata->Balance-(($request->return_quentity*$saleData->Unit_Price)+($request->return_quentity*$saleData->Added_Value)));
            $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - ((($request->return_quentity * $saleData->Unit_Price) - $saleData->Discount_Value) + ((($request->return_quentity * $saleData->Unit_Price) - $saleData->Discount_Value) * $avtSaleRate->AVT)),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );
        }
        if ($InvoiceData->Number_of_Quantity == 0) {
            $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
                [
                    'discount' => 0,
                ]
            );
        } else {
            $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
                [
                    'discount' => $InvoiceData->discount - $saleData->Discount_Value,
                ]
            );
        }

        $return_sales = return_sales::create([
            'product_id' => $saleData->product_id,
            'invoice_id' => $saleData->invoice_id,
            'branch_id' => Auth()->User()->branch->id,
            'return_Added_Value' => $saleData->Added_Value,
            'return_Unit_Price' => $saleData->Unit_Price,
            'return_quantity' => $request->return_quentity,
            'created_at' => \Carbon\Carbon::now()->addHours(3),

        ]);
        //  return  $return_sales;
          $invoicedatarecent=invoices::find($saleData->invoice_id);
          $NOTICE_Number=0;
        if($invoicedatarecent->NOTICE_Number!=0){
        $NOTICE_Number=$invoicedatarecent->NOTICE_Number+1;

        }
      
        $Invoice = invoices::where('id',  $saleData->invoice_id)->Update(
            [

                'Price' => round($InvoiceData->Price - (($saleData->Unit_Price * $request->return_quentity)), 2),
                'Added_Value' => round($InvoiceData->Added_Value - ((($saleData->Unit_Price * $request->return_quentity)) * $avtSaleRate->AVT), 2),
                'Number_of_Quantity' => $InvoiceData->Number_of_Quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'NOTICE_Number'=>$NOTICE_Number

            ]
        );
        $productSales = sales::where('id', $request->id)->update(
            [
                'quantity' => $saleData->quantity - $request->return_quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'Discount_Value' => 0
            ]
        );
        $InvoiceData = invoices::find($saleData->invoice_id);

        if ($InvoiceData->Number_of_Quantity == 0 && $InvoiceData->Pay == "Credit") {
            $InvoiceData = invoices::find($saleData->invoice_id);

            $updateCustomer = customers::where('id', $InvoiceData->customer_id)->update(
                [
                    'Balance' => $customerdata->Balance - $InvoiceData->discount,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );
        }
        $product = sales::where('invoice_id', $saleData->invoice_id)->get();
        //return $product;


        $InvoiceData = invoices::find($saleData->invoice_id);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'product' => $product,
            "invoice_id" => $saleData->invoice_id
        ];
        return $data;
        return view('products.salesreturned', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */

    public function makeTotalDiscont($invoiceId, $discountValue)
    {
        $avtSaleRate = Avt::find(1);


        $Invoices = temp_invoice::where('id',  $invoiceId)->first();
        $totalafterdiscount = (($Invoices->Price - $Invoices->discount) + round((($Invoices->Price - $Invoices->discount) * $avtSaleRate->AVT), 2)) - $discountValue;
        $pricenew = round($totalafterdiscount * 100 / 115, 2);
        $discountvalue = ($Invoices->Price - $Invoices->discount) - $pricenew;
        if ($discountvalue)
            temp_invoice::where('id',  $invoiceId)->update([
                'discount' => $Invoices->discount + $discountvalue
            ]);
        $Invoices = temp_invoice::where('id',  $invoiceId)->first();
        return [
            'totalprice' => round(($Invoices->Price - $Invoices->discount), 2),
            'addedvalueafterdiscount' => round((($Invoices->Price - $Invoices->discount) * $avtSaleRate->AVT), 2),
            "discount" => $Invoices->discount
        ];
    }


    public function makenoteoninvoice($invoiceId, $notecontent)
    {


        $Invoices = temp_invoice::where('id',  $invoiceId)->first();
    
        if ($notecontent)
            temp_invoice::where('id',  $invoiceId)->update([
                'note' => $notecontent
            ]);
        return 1;
    }


    public function cancelInvoiceDiscont($invoiceId)
    {
        $avtSaleRate = Avt::find(1);

        $Invoices = temp_invoice::where('id',  $invoiceId)->first();


        $discountonInvoice = $Invoices->discount - $Invoices->discountOnProduct;

        temp_invoice::where('id',  $invoiceId)->update([
            'discount' => $Invoices->discount - $discountonInvoice,
        ]);
        $Invoices = temp_invoice::where('id',  $invoiceId)->first();

        return [
            'totalprice' => round(($Invoices->Price - $Invoices->discount), 2),
            'addedvalueafterdiscount' => round((($Invoices->Price - $Invoices->discount) * $avtSaleRate->AVT), 2),
            "discount" => $Invoices->discount
        ];
    }

    public function Receipt(Request $request)
    {
        //
        $avtSaleRate = Avt::find(1);

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $updateProduct = products::find($request->productNo);
        // return $updateProduct;
        if ($updateProduct->numberofpice >= 1) {
            products::where('id', $request->productNo)->Update([
                'numberofpice' => $updateProduct->numberofpice - $request->quantity,
            ]);
            $invoiceNumber = $request->invoice_number;
            if ($request->invoice_number == null) {
                $Invoice = invoices::create(
                    [
                        'customer_id' => $request->clientnamesearch ?? 1,
                        'user_id' => Auth()->user()->id,
                        'Price' => $request->product_price - $request->product_price_after_dis,
                        'Added_Value' => ($request->product_price - $request->product_price_after_dis) * $avtSaleRate->AVT,
                        'Pay' => $request->pay,
                        'Number_of_Quantity' => $request->quantity,
                        'created_at' => \Carbon\Carbon::now()->addHours(3),
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                         'issue_date' => substr(\Carbon\Carbon::now()->addHours(3), 0, 10),
                       'issue_time' => substr(\Carbon\Carbon::now()->addHours(3), 12),
                    ]
                );
                $invoiceNumber = $Invoice->id;
            } else {
                $InvoiceData = invoices::find($invoiceNumber);
                $Invoice = invoices::where('id',  $invoiceNumber)->Update(
                    [

                        'Price' => round($InvoiceData->Price + ($request->product_price - $request->product_price_after_dis), 2),
                        'Added_Value' => round(($InvoiceData->Added_Value + (($request->product_price - $request->product_price_after_dis) * $avtSaleRate->AVT)), 2),
                        'Number_of_Quantity' => $InvoiceData->Number_of_Quantity + $request->quantity,
                        'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    ]
                );
            }
            $productSales = sales::create(
                [
                    'product_id' => $request->productNo,
                    'invoice_id' => $invoiceNumber,
                    'Discount_Value' => $request->product_price_after_dis,
                    'Added_Value' => ($request->product_price - $request->product_price_after_dis) * $avtSaleRate->AVT,
                    'Unit_Price' => $request->product_price - $request->product_price_after_dis,
                    'quantity' => $request->quantity,
                    'branch_id' => Auth()->User()->branch->id,

                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
        } else {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'عدم وجود مخزون من هذه المنتج' : 'No stock of this product';

            session()->flash('delete', $message);
            $data = [
                "invoice_id" => null
            ];

            return view('products.Receipt', compact('data'));
        }
        $product = sales::where('invoice_id', $invoiceNumber)->get();
        //return $product;
        $data = [
            'product' => $product,
            "invoice_id" => $invoiceNumber
        ];

        return view('products.Receipt', compact('data'));
    }
}
