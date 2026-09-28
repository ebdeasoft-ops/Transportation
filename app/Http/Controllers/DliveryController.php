<?php

namespace App\Http\Controllers;

use App\Models\dlivery;
use App\Models\products;
use App\Models\dlivery_items;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Models\Avt;
use App\Models\invoices;
use App\Models\sales;
use App\Models\financial_accounts;
use App\Models\credittransactions;

use App\Models\customers;

class DliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        return view('products.deliver_to_anoter_supplier');
        //
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






    public function print_delivery_invoice(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avtSaleRate = Avt::find(1);

        $saleData = sales::where("invoice_id", $request->OrderNoprint)->get();
        $InvoiceData = invoices::find($request->OrderNoprint);
        $customerId= $InvoiceData->customer_id;
        $dlivery=dlivery::where('to_dlivery_id', $customerId)->first();
        // return $saleData;
        // return $dlivery;
        foreach( $saleData as $item){
            $dlivery=dlivery::where('to_dlivery_id', $customerId)->first();

            $total_price_withod_tax=($item->Unit_Price *  $item->quantity)-$item->Discount_Value;
             $result=  dlivery::where('to_dlivery_id', $customerId)->update(
                [
                    'blance' => $dlivery->blance - (($total_price_withod_tax) + ($total_price_withod_tax * $avtSaleRate->AVT)),
                    'number_items' => $dlivery->number_items - $item->quantity,
                    'last_payment' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            dlivery_items::where('states', 0)->where('supplier_id', $customerId)->where('product_id',  $item->product_id)->where('created_at', $item->created_at)->update(['states'=>1]);
           
         
            
        }




  sales::where("invoice_id", $request->OrderNoprint)->update(['save' => 1,]);
  $totAL=round(($InvoiceData->Price - $InvoiceData->discount)+(($InvoiceData->Price - $InvoiceData->discount)*$avtSaleRate->AVT),2);

$total_value=$totAL;

$creaditamount=0;
$Bank_transfer=0;
$bankamount=0;
$cashamount=0;
if($request->pay_sale=='Cash'){
    $cashamount=$total_value;
}elseif($request->pay_sale=='Bank_transfer' || $request->pay_sale=='Shabka'){
$Bank_transfer=$total_value;

}
else{
    $creaditamount=$total_value;
   
    
}

$INVOICE_ID=$InvoiceData->id;
//*******************************************************************************************
$paymentMethod=$request->pay_sale;
$total_cost=($total_value*100/115);


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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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
                'note' =>  '  فاتورة مبيعات رقم :'.(string) $INVOICE_ID,
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









































//*******************************************************************************************
$cash=0;
$shabka=0;
$credit=0;
$bank_tranfer=0;
  if ($request->pay_sale== 'Cash'){
$cash=$totAL;
    }
    elseif ($request->pay_sale=='Bank_transfer'){
$bank_tranfer=$totAL;
}
    elseif ($request->pay_sale=='Shabka'){
$shabka=$totAL;
    }else{
     $credit=$totAL;
                                                           $payment= '-';
}
$bank_transfer=0;
        invoices::find($request->OrderNoprint)->update([
        'save' => 1, 
        'Pay' =>$request->pay_sale,
              'cashamount' => $cash,
                    'bankamount' => $shabka,
                    'creaditamount' => $credit,
                    'Bank_transfer' => $bank_tranfer,
]);
      
$InvoiceData=invoices::find($request->OrderNoprint);

$data = [
   
   "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
   "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
   "invoicetotal_discount" => $InvoiceData->discount,
   'salesData' => $saleData,
   'invoiceData' =>  $InvoiceData,
   'totatextlriyales'=>'',
   'totatextlrihalala'=>'', 

];

        //   return $data;
        return  view('products.printInvoicesReturnToClientRecentSales', compact('data'));
        //
    }
    public function confirmdelivery()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        return view('products.confirmdelivery');
        //
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getitems($id)
    {
        //
        $ListProducts = [];
        $count = 0;
        $supplier_delivery = dlivery::where('to_dlivery_id', $id)->first();
        $itemsRequest = dlivery_items::where('states', 0)->where('supplier_id', $id)->get();
        $avtSaleRate = Avt::find(1);

        $confirminvoice =  invoices::create(
            [
                'save' => 0,
                'customer_id' =>  $supplier_delivery->to_dlivery_id,
                'user_id' => Auth()->user()->id,
                'Price' => $supplier_delivery->blance * 100 / 115,
                'Added_Value' => ($supplier_delivery->blance * 100 / 115) * $avtSaleRate->AVT,
                'Pay' => 'Cash',
                'status' =>  0,
                'branchs_id' => Auth()->User()->branch->id,
                'discountOnProduct' => 0,
                'discount' => 0,
                'Number_of_Quantity' => $supplier_delivery->number_items,
                'note' => $supplier_delivery->note ?? '-',
                'created_at' => \Carbon\Carbon::now()->addHours(3),
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
                'morepayment_way' => 1,
                'cashamount' => $supplier_delivery->blance,
                'bankamount' => 0,
                'creaditamount' => 0,
                'Bank_transfer' => 0,
            ]
        );
        $discountonproduct = 0;
        foreach ($itemsRequest as $item) {
            $discountonproduct += $item->discount;
           $sales= sales::create([
                'save' => 1,
                'product_id' => $item->productData->id,
                'invoice_id' => $confirminvoice->id,
                'branch_id' => Auth()->User()->branch->id,
                'Discount_Value' => $item->discount,
                'Added_Value' => $item->Added_value,
                'Unit_Price' => $item->cost,
                'reamingQuantity' => 0,
                'quantity' => $item->quantity,
                'created_at' => $item->created_at,
            ]);
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $item->productData->Product_Code,
                "productName" => $item->productData->product_name,
                "sale_price" => $item->cost,
                "discount" => $item->discount,
                "order_id" =>  $confirminvoice->id,
                "quantity" => $item->quantity,
                "added_value" => $item->Added_value,
                'id' => $sales->id,
                'invoiceId'=> $confirminvoice->id


            ];
        }
        invoices::find($confirminvoice->id)->update(
            [
                'discountOnProduct' => $discountonproduct,
                'discount' =>  $discountonproduct,
            ]
        );
        return $ListProducts;
    }




    public function deleteitemdeliveryconfirm($id)
    {
        //
        $ListProducts = [];
        $count = 0;
        $avtSaleRate = Avt::find(1);
         $salesdelete=sales::find($id);
         $datainvoice=invoices::find($salesdelete->invoice_id);
         $totalprice=$datainvoice->Price -($salesdelete->quantity*$salesdelete->Unit_Price)-$salesdelete->Discount_Value;
        $confirminvoice =  invoices::find($salesdelete->invoice_id)->update(
            [
             
                'Price' =>  $totalprice,
                'Added_Value' => ( $totalprice) * $avtSaleRate->AVT,
                'discountOnProduct' =>$datainvoice->discountOnProduct -$salesdelete->Discount_Value,
                'discount' => $datainvoice->discount -$salesdelete->Discount_Value,
                'Number_of_Quantity' => $datainvoice->Number_of_Quantity-$salesdelete->quantity,
                'cashamount' => $totalprice +(($totalprice) * $avtSaleRate->AVT),
             
            ]
        );
         sales::find($id)->delete();

        $discountonproduct = 0;
        $itemsRequest=sales::where('invoice_id',$salesdelete->invoice_id)->get();
        foreach ($itemsRequest as $item) {
            $discountonproduct += $item->discount;
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $item->productData->Product_Code,
                "productName" => $item->productData->product_name,
                "sale_price" => $item->Unit_Price,
                "discount" => $item->Discount_Value,
                "order_id" =>$salesdelete->invoice_id,
                "quantity" => $item->quantity,
                "added_value" => $item->Added_Value,
                'id' => $item->id,
                'invoiceId'=> $datainvoice->id


            ];
        }
      
     
        return $ListProducts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $supplier_delivery = dlivery::where('to_dlivery_id', $request->clientnamesearch)->first();
        if ($supplier_delivery == null) {
            dlivery::create(
                [
                    'to_dlivery_id' => $request->clientnamesearch,
                    'supplier_id' => $request->clientnamesearch,
                    'blance' => 0,
                    'number_items' => 0,
                    'last_payment' => \Carbon\Carbon::now()->addHours(3),
                    'note' => $request->notes,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
        }
        $avtSaleRate = Avt::find(1);
        $total_price_withod_tax = ($request->saleprice * $request->quentity) - $request->discount;
        $dlivery = dlivery::where('to_dlivery_id', $request->clientnamesearch)->first();
        dlivery::where('to_dlivery_id', $request->clientnamesearch)->update(
            [
                'blance' => $dlivery->blance + (($total_price_withod_tax) + ($total_price_withod_tax * $avtSaleRate->AVT)),
                'number_items' => $dlivery->number_items + $request->quentity,
                'last_payment' => \Carbon\Carbon::now()->addHours(3),
                'note' => $request->notes,
                'created_at' => \Carbon\Carbon::now()->addHours(3),
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );
        $productdata = products::find($request->productNo);
        dlivery_items::create([
            'to_dlivery_id' => $request->clientnamesearch,
            'product_id' => $request->productNo,
            'product_name' => '-',
            'quantity' => $request->quentity,
            'Added_value' => ($request->saleprice * $avtSaleRate->AVT),
            'states' => 0,
            'cost' => $request->saleprice,
            'supplier_id' => $request->clientnamesearch,
            'discount' => $request->discount,
            'created_at' => \Carbon\Carbon::now()->addHours(3),
            'updated_at' => \Carbon\Carbon::now()->addHours(3),
        ]);

        products::find($request->productNo)->update([
            'numberofpice' => $productdata->numberofpice - $request->quentity
        ]);

        $ListProducts = [];
        $count = 0;
        $itemsRequest = dlivery_items::where('states', 0)->where('supplier_id', $request->clientnamesearch)->get();
        foreach ($itemsRequest as $item) {
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $item->productData->Product_Code,
                "productName" => $item->productData->product_name,
                "sale_price" => $item->cost,
                "discount" => $item->discount,
                "order_id" => $item->supplier_id,
                "quantity" => $item->quantity,
                "added_value" => $item->Added_value,
                'id' => $item->id,


            ];
        }
        return $ListProducts;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\dlivery  $dlivery
     * @return \Illuminate\Http\Response
     */
    public function show(dlivery $dlivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\dlivery  $dlivery
     * @return \Illuminate\Http\Response
     */
    public function print_delivery_to_anoter_supplier(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $dlivery_items = dlivery_items::where('supplier_id', $request->OrderNoprint)->where('states', 0)->get();
        $supplier = dlivery::where('to_dlivery_id', $request->OrderNoprint)->first();

        $data = [
            'items' => $dlivery_items,
            'supplier' => $supplier
        ];
        return view('products.print_order_perice_to_dlivery', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\dlivery  $dlivery
     * @return \Illuminate\Http\Response
     */
    public function updateproductallDatadelivery(Request $request)
    {
        //
        $avtSaleRate = Avt::find(1);
        $dlivery_items = dlivery_items::find($request->id);
        $total_price_withod_tax = ($dlivery_items->cost * $dlivery_items->quantity) - $dlivery_items->discount;
        $dlivery = dlivery::where('to_dlivery_id', $dlivery_items->supplier_id)->first();
        dlivery::where('to_dlivery_id', $dlivery_items->to_dlivery_id)->update(
            [
                'blance' => $dlivery->blance - (($total_price_withod_tax) + ($total_price_withod_tax * $avtSaleRate->AVT)),
                'number_items' => $dlivery->number_items - $dlivery_items->quantity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );
        $total_price_withod_tax = ($request->price * $request->quentity) - $request->discount;

        dlivery::where('to_dlivery_id', $dlivery_items->to_dlivery_id)->update(
            [
                'blance' => $dlivery->blance + (($total_price_withod_tax) + ($total_price_withod_tax * $avtSaleRate->AVT)),
                'number_items' => $dlivery->number_items + $request->quentity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );
        $productdata = products::find($dlivery_items->product_id);
        products::find($dlivery_items->product_id)->update([
            'numberofpice' => $productdata->numberofpice + $dlivery_items->quantity
        ]);
        products::find($dlivery_items->product_id)->update([
            'numberofpice' => $productdata->numberofpice + $request->quentity
        ]);
        dlivery_items::find($request->id)->update(
            [
                'quantity' => $request->quentity,
                'Added_value' => ($request->price * $avtSaleRate->AVT),
                'cost' => $request->price,
                'discount' => $request->discount,
            ]
        );
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\dlivery  $dlivery
     * @return \Illuminate\Http\Response
     */
    public function getcustomerproductsdelivery($dliveryID)
    {
        $dlivery = dlivery::where('to_dlivery_id', $dliveryID)->first();
        $itemsRequest = dlivery_items::where('states', 0)->where('supplier_id', $dliveryID)->get();
        $count=0;
        foreach ($itemsRequest as $item) {
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $item->productData->Product_Code,
                "productName" => $item->productData->product_name,
                "sale_price" => $item->cost,
                "discount" => $item->discount,
                "order_id" => $item->supplier_id,
                "quantity" => $item->quantity,
                "added_value" => $item->Added_value,
                'id' => $item->id,


            ];
        }
        return $ListProducts;
    }
    public function deleteitemdelivery($dliveryID)
    {
        //
        $avtSaleRate = Avt::find(1);
        $dlivery_items = dlivery_items::find($dliveryID);
        $total_price_withod_tax = ($dlivery_items->cost * $dlivery_items->quantity) - $dlivery_items->discount;
        $dlivery = dlivery::where('to_dlivery_id', $dlivery_items->supplier_id)->first();
        dlivery::where('to_dlivery_id', $dlivery_items->supplier_id)->update(
            [
                'blance' => $dlivery->blance - (($total_price_withod_tax) + ($total_price_withod_tax * $avtSaleRate->AVT)),
                'number_items' => $dlivery->number_items - $dlivery_items->quantity,
                'updated_at' => \Carbon\Carbon::now()->addHours(3),
            ]
        );
        $productdata = products::find($dlivery_items->product_id);
        products::find($dlivery_items->product_id)->update([
            'numberofpice' => $productdata->numberofpice + $dlivery_items->quantity
        ]);
        dlivery_items::find($dliveryID)->delete();
        $ListProducts = [];
        $count = 0;
        $itemsRequest = dlivery_items::where('states', 0)->where('supplier_id', $dlivery_items->supplier_id)->get();
        foreach ($itemsRequest as $item) {
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $item->productData->Product_Code,
                "productName" => $item->productData->product_name,
                "sale_price" => $item->cost,
                "discount" => $item->discount,
                "order_id" => $item->supplier_id,
                "quantity" => $item->quantity,
                "added_value" => $item->Added_value,
                'id' => $item->id,


            ];
        }
        return $ListProducts;
    }
}
