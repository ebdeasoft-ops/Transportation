<?php

namespace App\Http\Controllers;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Models\Avt;
use App\Models\acounts_type;
use App\Models\convertcashboxToBank;
use App\Models\branchs;
use App\Models\Transfer_cash_to_the_next_day;
use Illuminate\Http\Request;
use App\Models\invoices;
use App\Models\orderDetails;
use App\Models\credittransactions;
use App\Models\customers;
use App\Models\Transactions;
use App\Models\transferMoney_to_mainbranch;
use App\Models\order_price_from_supplier;
use App\Models\product_movement_another_branch;
use App\Models\product_movement_another_branch_items;
use App\Models\resource_purchases;
use App\Models\sales;
use App\Models\supllier;
use App\Models\Cash_withdrawal_from_the_bank;
use App\Models\expenses;
use App\Models\orderTosupllier;
use App\Models\return_sales;
use App\Models\offer_price_to_customer;
use  App\Models\transactiontosuplliers;
use App\Models\products;
use App\Models\cash_from__bank;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Exportproducts;
use App\Exports\supllierExport;
use App\Exports\customersExport;
use App\Exports\Low_sell_export;
use App\Exports\financial_accounts_Export;
use PDF;
use App\Models\stock_update;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\financial_accounts;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\shipments_details;
use App\Models\Covenant_liquidation;
use App\Models\purchase_liquidation;

class ReportController extends Controller
{
    //
    public function getAccountBalance($id)
{
    $start_at = date('Y-01-01');
    $end_at   = date('Y-m-d');

    $credittransactions = credittransactions::where('customer_id', $id)
        ->whereDate('created_at', '>=', $start_at)
        ->whereDate('created_at', '<=', $end_at)
        ->where('save', 1)
        ->orderBy('created_at')
        ->get();

    $credit = 0;
    $debit  = 0;

    foreach ($credittransactions as $item) {
        $credit += $item->creditor;
        $debit  += $item->debtor;
    }

    return response()->json([
        'credit'  => round($credit, 2),
        'debit'   => round($debit, 2),
    ]);
}
    public function accountStatement($supplierId, Request $request)
{
    $start_at = $request->start_at ?? date('Y-01-01');
    $end_at   = $request->end_at ?? date('Y-m-d');

    $financial_accounts = financial_accounts::find($supplierId);

    $LAST_credittransactions = credittransactions::where('customer_id', $supplierId)
        ->whereDate('created_at', '<', $start_at)
        ->where('save', 1)
        ->get();

    $credittransactions = credittransactions::where('customer_id', $supplierId)
        ->whereDate('created_at', '>=', $start_at)
        ->whereDate('created_at', '<=', $end_at)
        ->where('save', 1)
        ->where(function ($q) {
            $q->where('debtor', '!=', 0)
              ->orWhere('creditor', '!=', 0);
        })
        ->get();

    $List_dely_record = [];

    foreach ($credittransactions as $item) {

        $parent_name = credittransactions::where('note', $item->note)
            ->where('sent_abd_count', '!=', 0)
            ->orWhere('sent_serf_count', '!=', 0)
            ->where('note', $item->note)
            ->first();

        $name_parent = $parent_name == null ? '-' : $parent_name->financial_accounts_data->name;

        if ($item->type == 'Cash') {
            $payment = __('report.cash');
        } elseif ($item->type == 'Bank_transfer') {
            $payment = __('home.Bank_transfer');
        } elseif ($item->type == 'Shabka') {
            $payment = __('report.shabka');
        } else {
            $payment = '-';

            if ($item->type == null) {
                if ($item->Pay_Method_Name == 'Cash') {
                    $payment = __('report.cash');
                } elseif ($item->Pay_Method_Name == 'Bank_transfer') {
                    $payment = __('home.Bank_transfer');
                } elseif ($item->Pay_Method_Name == 'Shabka') {
                    $payment = __('report.shabka');
                } elseif ($item->Pay_Method_Name == 'Partition') {
                    $payment = __('home.Partition of the amount');
                } else {
                    $payment = __('report.credit');
                }
            }
        }

        $List_dely_record[] = [
            'id' => $item->id,
            'recive_amount' => $item->recive_amount,
            'depit' => $item->debtor,
            'credit' => $item->creditor,
            'current_blance' => $item->currentblance,
            'dely_record' => $item->dely_record,
            'date_export' => $item->date_export,
            'date' => $item->created_at,
            'note' => $item->note . '-' . '(' . $payment . ')' . '-' . $name_parent,
            'user' => $item->user->name,
            'branch' => $item->branch->name,
        ];
    }

    $credit = 0;
    $debit = 0;
    $blance = 0;

    foreach ($LAST_credittransactions as $item) {
        $credit += $item->creditor;
        $debit  += $item->debtor;
        $blance  = $item->currentblance;
    }

    return view('reports.accountStatement', compact(
        'List_dely_record',
        'financial_accounts',
        'credit',
        'debit',
        'blance',
        'start_at',
        'end_at',
        'supplierId'
    ));
}


               public function profit_and_lost()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.profit_and_lost');
    } 
    
                  public function liquidation()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.liquidation');
    } 
     
                  public function search_liquidation(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if( $request->type=='-'){
if($request->branch=='-')
{
if($request->branch!='-'&&$request->type!='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->where('type',  $request->type)->get();
}if($request->branch!='-'&&$request->type=='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->get();
}if($request->branch=='-'&&$request->type!='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->where('type',  $request->type)->get();
}else{
                    $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->get();

}
}else{
    
if($request->branch!='-'&&$request->type!='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->where('type',  $request->type)->get();
}if($request->branch!='-'&&$request->type=='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->get();
}if($request->branch=='-'&&$request->type!='-'){
                $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->where('type',  $request->type)->get();
}else{
               $data = Covenant_liquidation::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->get();

}

    
}
}else{
   
if($request->branch=='-')
{
if($request->branch!='-'&&$request->type!='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->where('type',  $request->type)->get();
}if($request->branch!='-'&&$request->type=='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->get();
}if($request->branch=='-'&&$request->type!='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->where('type',  $request->type)->get();
}else{
                    $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'save',  1)->get();

}
}else{
    
if($request->branch!='-'&&$request->type!='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->where('type',  $request->type)->get();
}if($request->branch!='-'&&$request->type=='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->get();
}if($request->branch=='-'&&$request->type!='-'){
                $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->where('type',  $request->type)->get();
}else{
               $data = Covenant_liquidation::where( 'type', $request->type)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->where( 'branchs_id',  $request->branch)->where( 'save',  1)->get();

}

    
}
 
    
}
        return view('reports.liquidation_report',compact('data'));
    } 
    
    public function shipments_report()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.shipments');
    }  
    
                   public function low_sell()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.low_sell');
    } 
    


      public function search_hipments_report(Request $request)
    {
       app()->setLocale(LaravelLocalization::getCurrentLocale());
 
if($request->branch=='-'){
            $data = Transactions::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->orderBy('id', 'desc')->get();

}else{
            $data = Transactions::where('branchs_id',$request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=',$request->end_at)->orderBy('id', 'desc')->get();

}
        return view('reports.print_shipments_report', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('branch_id', $request->branch);


    
    }


         public function low_sell_search(Request $request)
    {
       app()->setLocale(LaravelLocalization::getCurrentLocale());
 

       return Excel::download(new Low_sell_export($request->start_at,$request->end_at,$request->branch), 'Low_sell_export.xlsx');


    
    }
    
    
    
           public function cost_center()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.search_cost_center');
    }        
          public function sales_and_return()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.sales_and_return');
    }
    
          public function search_profit_and_lost(Request $request)
{
    
    
    $expense_total_value=0;
    $sales_total_value=0;
    $sales_return_total_value=0;
    $purchase_total_value=0;
    $purchase_return_total_value=0;
    
    
    
        if ( $request->branch == '-') {

            $expense= credittransactions::where('orginal_type', 3)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $sales= credittransactions::where('customer_id', 112)->where('note', 'LIKE', '%' . 'فاتورة مبيعات ' . '%')->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $sales_return= credittransactions::where('customer_id', 112)->where('note', 'LIKE', '%' . 'فاتورة مرتجع مبيعات' . '%')->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $purchase= credittransactions::where('customer_id', 181)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $purchase_return= credittransactions::where('customer_id', 181)->where('note', 'LIKE', '%' . 'مرتجع مشتريات فاتورة' . '%')->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();


        }  else {
            $expense= credittransactions::where('branchs_id', $request->branch)->where('orginal_type', 3)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
            $sales= credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'فاتورة مبيعات ' . '%')->where('customer_id',$financial_accounts->id )->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $financial_accounts= financial_accounts::where('parent_account_number',112)->where('branchs_id', Auth()->user()->branchs_id)->first();
            $sales_return= credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'فاتورة مرتجع مبيعات' . '%')->where('customer_id', $financial_accounts->id)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
            $purchase= credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'فاتورة مشتريات' . '%')->where('customer_id', $financial_accounts->id)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();
            $purchase_return= credittransactions::where('branchs_id', $request->branch)->where('customer_id', $financial_accounts->id)->where('note', 'LIKE', '%' . 'مرتجع مشتريات فاتورة' . '%')->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
             
            
            
        }
        
        foreach($expense as $item){
            $expense_total_value=$expense_total_value+$item->recive_amount;
        }
        
           foreach($sales as $item){
            $sales_total_value=$sales_total_value+$item->recive_amount;
        } 
        
             foreach($sales_return as $item){
            $sales_return_total_value=$sales_return_total_value+$item->recive_amount;
        } 
        
             foreach($purchase as $item){
            $purchase_total_value=$purchase_total_value+$item->recive_amount;
        } 
        
             foreach($purchase_return as $item){
            $purchase_return_total_value=$purchase_return_total_value+$item->recive_amount;
        } 
        
     $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', Auth()->user()->branchs_id)->first();

        
    $data=[
        'expense'=>$expense_total_value,
        'sales'=>$sales_total_value,
        'sales_return'=>$sales_return_total_value,
        'purchase'=>$purchase_total_value,
        'purchase_return'=>$purchase_return_total_value,
        'stockopining'=>$financial_accounts->debtor_opening,
        'stockclosing'=>$financial_accounts->debtor_end,
        ];
        
        // return $data;
           return view('reports.print_profit_and_lost', compact('data'))->with('start', $request->start_at)->with('end', $request->end_at)->with('branch', Auth()->user()->branchs_id);
 
}
    
    
      public function cost_center_search(Request $request)
    {
        $data=[];
       app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ( $request->cost_center == '-') {

            $data = credittransactions::where('cost_center','!=',0 )->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        }  else {
            $data = credittransactions::where('cost_center', $request->cost_center)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        }
        

  
    
    }
    
    
    
    
    
    
      
    
    public function search_sales_and_return(Request $request)
    {
       app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ( $request->branch == '-') {

            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $Invoicesreturn = return_sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }  else {
            $Invoices = invoices::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $Invoicesreturn = return_sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }
        
$data=[
    'invoices'=>$Invoices,
    'returnsales'=>$Invoicesreturn
    ];
    
    // return $data;
    
        return view('reports.print_sales_and_return', compact('data'))->with('start', $request->start_at)->with('end', $request->end_at)->with('branch_id', $request->branch);
    }
    
    
     
          public function Customer_debt_restructuring()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.search_Customer_debt_restructuring');
    }
    
          public function Supplier_debt_restructuring()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.search_Supplier_debt_restructuring');
    }
    
    
    
    
           public function search_Supplier_debt_restructuring(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data=[];
        if($request->parent_account_number=="-"){
            foreach(financial_accounts::where('orginal_type',2)->get() as $financial_accounts){
                
                
                
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
           $credittransactions=credittransactions::where('sent_serf_count', '!=',0)->where('customer_id',$financial_accounts->id)->orderBy('id','desc')->first(); 
           $lastdate=$credittransactions==NULL?'-':$credittransactions->created_at;
           $crrunt_balence=$financial_accounts->creditor_current-$financial_accounts->debtor_current;
           $invoices=resource_purchases::where('suplier_id',$financial_accounts->orginal_id)->where('Pay_Method_Name','Credit')->get();
           $f_0_t_10=0;
           $f_11_t_30=0;
           $f_31_t_60=0;
           $f_61_t_90=0;
           $f_91_t_120=0;
           $f_121_t_180=0;
           $f_181_t_00=0;
           $I=0;
           $life_creadit=0;
           $crrunt_balence_value='';
  if($crrunt_balence>0){
      $crrunt_balence_value='('.__('home.credit').')'.$crrunt_balence;
  }elseif($crrunt_balence<0){
      $crrunt_balence_value='('.__('home.debit').')'.($crrunt_balence*-1);
  }else{
    $crrunt_balence_value=  __('home.Balanced');
  }
           foreach($invoices as $invoice){
               $date1 = new DateTime($invoice->created_at);
               $date2 = new DateTime(date("Y-m-d"));
               $diff = $date1->diff($date2);

               $TOTAL=($invoice->In_debt);
                 if($I==0){
                  $life_creadit=$diff->d; 
               }
               if($diff->d >=0&&$diff->d <=10){
                $f_0_t_10+=$TOTAL;
        
               }
           
                 elseif($diff->d >=11&&$diff->d <=30){
                       
               $f_11_t_30+=$TOTAL;
        
               }
                 elseif($diff->d >=31&&$diff->d <=60){
                    
               $f_31_t_60+=$TOTAL;
           
               }
                 elseif($diff->d >=61&&$diff->d <=90){
   
               $f_61_t_90+=$TOTAL;
          
               }
                 elseif($diff->d >=91&&$diff->d <=120){
                        $f_91_t_120+=$TOTAL;
           
               }
                 elseif($diff->d >=121&&$diff->d <=180){
                      $f_121_t_180+=$TOTAL;
               }
               else{
                     $f_181_t_00+=$TOTAL; 

               }
           }
              $data[]=[
            'Acount_number'=>$financial_accounts->account_number   ,
            'name'=> $financial_accounts->name,
            'life_creadit'=>$life_creadit,
            'crrunt_balence'=>$crrunt_balence_value,
            'lastdate'=>$lastdate,
            'f_0_t_10'=>$f_0_t_10,
            'f_11_t_30'=>$f_11_t_30,
            'f_31_t_60'=>$f_31_t_60,
            'f_61_t_90'=>$f_61_t_90,
            'f_91_t_120'=>$f_91_t_120,
            'f_121_t_180'=>$f_121_t_180,
            'f_181_t_00'=>$f_181_t_00
            ];

            
        }
        
     
        }else{
            $financial_accounts=financial_accounts::find($request->parent_account_number);
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
           $credittransactions=credittransactions::where('sent_serf_count', '!=',0)->where('customer_id',$request->parent_account_number)->orderBy('id','desc')->first(); 
           $lastdate=$credittransactions->created_at;
           $crrunt_balence=$financial_accounts->creditor_current-$financial_accounts->debtor_current;
           $invoices=resource_purchases::where('suplier_id',$financial_accounts->orginal_id)->where('Pay_Method_Name','Credit')->get();
           $f_0_t_10=0;
           $f_11_t_30=0;
           $f_31_t_60=0;
           $f_61_t_90=0;
           $f_91_t_120=0;
           $f_121_t_180=0;
           $f_181_t_00=0;
           $I=0;
           $life_creadit=0;
           $crrunt_balence_value='';
  if($crrunt_balence>0){
      $crrunt_balence_value='('.__('home.credit').')'.$crrunt_balence;
  }elseif($crrunt_balence<0){
      $crrunt_balence_value='('.__('home.debit').')'.($crrunt_balence*-1);
  }else{
    $crrunt_balence_value=  __('home.Balanced');
  }
           foreach($invoices as $invoice){
               $date1 = new DateTime($invoice->created_at);
               $date2 = new DateTime(date("Y-m-d"));
               $diff = $date1->diff($date2);

               $TOTAL=($invoice->In_debt);
                 if($I==0){
                  $life_creadit=$diff->d; 
               }
               if($diff->d >=0&&$diff->d <=10){
                $f_0_t_10+=$TOTAL;
        
               }
           
                 elseif($diff->d >=11&&$diff->d <=30){
                       
               $f_11_t_30+=$TOTAL;
        
               }
                 elseif($diff->d >=31&&$diff->d <=60){
                    
               $f_31_t_60+=$TOTAL;
           
               }
                 elseif($diff->d >=61&&$diff->d <=90){
   
               $f_61_t_90+=$TOTAL;
          
               }
                 elseif($diff->d >=91&&$diff->d <=120){
                        $f_91_t_120+=$TOTAL;
           
               }
                 elseif($diff->d >=121&&$diff->d <=180){
                      $f_121_t_180+=$TOTAL;
               }
               else{
                     $f_181_t_00+=$TOTAL; 

               }
           }
        }
        
        $data[]=[
            'Acount_number'=>$financial_accounts->account_number   ,
            'name'=> $financial_accounts->name,
            'life_creadit'=>$life_creadit,
            'crrunt_balence'=>$crrunt_balence_value,
            'lastdate'=>$lastdate,
            'f_0_t_10'=>$f_0_t_10,
            'f_11_t_30'=>$f_11_t_30,
            'f_31_t_60'=>$f_31_t_60,
            'f_61_t_90'=>$f_61_t_90,
            'f_91_t_120'=>$f_91_t_120,
            'f_121_t_180'=>$f_121_t_180,
            'f_181_t_00'=>$f_181_t_00
            ];


        return view('reports.Customer_debt_restructuring', compact('data'));
    }
    
    
           public function search_Customer_debt_restructuring(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data=[];
        if($request->parent_account_number=="-"){
            foreach(financial_accounts::where('orginal_type',1)->get() as $financial_accounts){
                
                
                
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
           $credittransactions=credittransactions::where('sent_abd_count', '!=',0)->where('customer_id',$financial_accounts->id)->orderBy('id','desc')->first(); 
           $lastdate=$credittransactions==NULL?'-':$credittransactions->created_at;
           $crrunt_balence=$financial_accounts->creditor_current-$financial_accounts->debtor_current;
           $invoices=invoices::where('customer_id',$financial_accounts->orginal_id)->where('Pay','Credit')->where('save',1)->get();
           $f_0_t_10=0;
           $f_11_t_30=0;
           $f_31_t_60=0;
           $f_61_t_90=0;
           $f_91_t_120=0;
           $f_121_t_180=0;
           $f_181_t_00=0;
           $I=0;
           $life_creadit=0;
           $crrunt_balence_value='';
  if($crrunt_balence>0){
      $crrunt_balence_value='('.__('home.credit').')'.$crrunt_balence;
  }elseif($crrunt_balence<0){
      $crrunt_balence_value='('.__('home.debit').')'.($crrunt_balence*-1);
  }else{
    $crrunt_balence_value=  __('home.Balanced');
  }
  
  
           foreach($invoices as $invoice){
               $date1 = new DateTime($invoice->created_at);
               $date2 = new DateTime(date("Y-m-d"));
               $diff = $date1->diff($date2);

               $TOTAL=($invoice->Price)+($invoice->Price*$saleavt);
                 if($I==0){
                  $life_creadit=$diff->d; 
               }
               if($diff->d >=0&&$diff->d <=10){
                $f_0_t_10=+$TOTAL;
        
               }
           
                 elseif($diff->d >=11&&$diff->d <=30){
                       
               $f_11_t_30+=$TOTAL;
        
               }
                 elseif($diff->d >=31&&$diff->d <=60){
                    
               $f_31_t_60+=$TOTAL;
           
               }
                 elseif($diff->d >=61&&$diff->d <=90){
   
               $f_61_t_90+=$TOTAL;
          
               }
                 elseif($diff->d >=91&&$diff->d <=120){
                        $f_91_t_120+=$TOTAL;
           
               }
                 elseif($diff->d >=121&&$diff->d <=180){
                      $f_121_t_180+=$TOTAL;
               }
               else{
                     $f_181_t_00+=$TOTAL; 

               }
           }
              $data[]=[
            'Acount_number'=>$financial_accounts->account_number   ,
            'name'=> $financial_accounts->name,
            'life_creadit'=>$life_creadit,
            'crrunt_balence'=>$crrunt_balence_value,
            'lastdate'=>$lastdate,
            'f_0_t_10'=>$f_0_t_10,
            'f_11_t_30'=>$f_11_t_30,
            'f_31_t_60'=>$f_31_t_60,
            'f_61_t_90'=>$f_61_t_90,
            'f_91_t_120'=>$f_91_t_120,
            'f_121_t_180'=>$f_121_t_180,
            'f_181_t_00'=>$f_181_t_00
            ];

            
        }
        
     
        }else{
            $financial_accounts=financial_accounts::find($request->parent_account_number);
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
           $credittransactions=credittransactions::where('sent_abd_count', '!=',0)->where('customer_id',$request->parent_account_number)->orderBy('id','desc')->first(); 
           $lastdate=$credittransactions->created_at;
           $crrunt_balence=$financial_accounts->creditor_current-$financial_accounts->debtor_current;
           $invoices=invoices::where('customer_id',$financial_accounts->orginal_id)->where('Pay','Credit')->where('save',1)->get();
           
           $f_0_t_10=0;
           $f_11_t_30=0;
           $f_31_t_60=0;
           $f_61_t_90=0;
           $f_91_t_120=0;
           $f_121_t_180=0;
           $f_181_t_00=0;
           $I=0;
           $life_creadit=0;
           $crrunt_balence_value='';
  if($crrunt_balence>0){
      $crrunt_balence_value='('.__('home.credit').')'.$crrunt_balence;
  }elseif($crrunt_balence<0){
      $crrunt_balence_value='('.__('home.debit').')'.($crrunt_balence*-1);
  }else{
    $crrunt_balence_value=  __('home.Balanced');
  }
  $diffrent=[];
           foreach($invoices as $invoice){
               $date1 = new DateTime($invoice->created_at);
               $date2 = new DateTime(date("Y-m-d"));
               $diff = $date1->diff($date2);

               $TOTAL=($invoice->Price)+($invoice->Price*$saleavt);
                 if($I==0){
                  $life_creadit=$diff->d; 
               }
               
               $diffrent[]=$diff->d;
               if($diff->d >=0&&$diff->d <=10){
                $f_0_t_10+=$TOTAL;
        
               }
           
                 elseif($diff->d >=11&&$diff->d <=30){
                       
               $f_11_t_30+=$TOTAL;
        
               }
                 elseif($diff->d >=31&&$diff->d <=60){
                    
               $f_31_t_60+=$TOTAL;
           
               }
                 elseif($diff->d >=61&&$diff->d <=90){
   
               $f_61_t_90+=$TOTAL;
          
               }
                 elseif($diff->d >=91&&$diff->d <=120){
                        $f_91_t_120+=$TOTAL;
           
               }
                 elseif($diff->d >=121&&$diff->d <=180){
                      $f_121_t_180+=$TOTAL;
               }
               else{
                     $f_181_t_00+=$TOTAL; 

               }
           }
           
        //   return $diffrent;
               $data[]=[
            'Acount_number'=>$financial_accounts->account_number   ,
            'name'=> $financial_accounts->name,
            'life_creadit'=>$life_creadit,
            'crrunt_balence'=>$crrunt_balence_value,
            'lastdate'=>$lastdate,
            'f_0_t_10'=>$f_0_t_10,
            'f_11_t_30'=>$f_11_t_30,
            'f_31_t_60'=>$f_31_t_60,
            'f_61_t_90'=>$f_61_t_90,
            'f_91_t_120'=>$f_91_t_120,
            'f_121_t_180'=>$f_121_t_180,
            'f_181_t_00'=>$f_181_t_00
            ];
            
            // return $invoices;
        return view('reports.Customer_debt_restructuring', compact('data'));
        }
        
    
    }
    
    
    
    
          public function Daily_record_report()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Daily_record_report');
    }
    
              public function account_statement()
    {
        

        // $financial_accounts=financial_accounts::where('id','>=',1)->update(
            
        //     [
        //         'current_balance'=>0,
        //         'creditor_opening'=>0,
        //         'debtor_opening'=>0,
        //         'creditor_current'=>0,
        //         'debtor_current'=>0,
                
        //         ]
            
        //     );
        
        
    //     customers::where('id','>',1)->update(['Balance'=>0]);
    //     supllier::where('id','>',1)->update(['In_debt'=>0]);
        
      
    //      $customers=customers::get();
    //   $i=201;
    //      foreach($customers as $item){
    //       $i++;  
             
    
    
    
    //     financial_accounts::create(
    //         [
    //             'name'=>$item->name,
    //             'account_type'=>1,
    //             'parent_account_number'=>2,
    //             'account_number'=>$i,
    //             'start_balance'=>0,
    //             'current_balance'=>0,
    //             'start_balance_status'=>3,
    //             'other_table_FK'=>NULL,
    //              'notes'=>NULL
              
    //             ,'added_by'=>1,
    //             'updated_by'=>NULL,
    //             'com_code'=>1
    //             ,'date'=>\Carbon\Carbon::now()->addHours(3),
    //             'active'=>1
    //             ,'is_parent'=>0,
    //             'start_balance_status'=>3,
    //             'orginal_id'=> $item->id,
    //             'orginal_type'=>1

    //         ]
    //         );
    //      }
        
             
    //              $customers=supllier::get();
    //   $i=301;
    //      foreach($customers as $item){
    //       $i++;  
             
    
    
    
    //     financial_accounts::create(
    //         [
    //             'name'=>$item->name,
    //             'account_type'=>2,
    //             'parent_account_number'=>1,
    //             'account_number'=>$i,
    //             'start_balance'=>0,
    //             'current_balance'=>0,
    //             'start_balance_status'=>3,
    //             'other_table_FK'=>NULL,
    //              'notes'=>NULL
              
    //             ,'added_by'=>1,
    //             'updated_by'=>NULL,
    //             'com_code'=>1
    //             ,'date'=>\Carbon\Carbon::now()->addHours(3),
    //             'active'=>1
    //             ,'is_parent'=>0,
    //             'start_balance_status'=>3,
    //             'orginal_id'=> $item->id,
    //             'orginal_type'=>2

    //         ]
    //         );
    //      }
        
        

            
 
            
     
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.account_statement');
    }
    
    
             public function Supplier_account_statement()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.supplier_account_statement');
    }
                  public function search_Daily_record_report (Request $request)
{
    
            app()->setLocale(LaravelLocalization::getCurrentLocale());

    $credittransactions=credittransactions::where('dely_record','!=',0)->where('save','!=',0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
$List_dely_record=[];
$dely_record=0;

foreach($credittransactions as $item){
    $dely_record=$item->dely_record;

    $List_dely_record[]=[
            'name' => $item->financial_accounts_data->name,
            'method_pay' =>  __('home.credit') ,
            'debtor' => $item->debtor,
            'creditor' => $item->creditor,
            'dely_record'=>$item->dely_record,
            'date'=>$item->created_at,
            'note'=>$item->note,
            ];

    
}

        $dates = array();
        
        foreach ($List_dely_record as $key => $row)
        {
            $dates[$key] = strtotime($row['date']);
        }
        array_multisort($dates, SORT_ASC, $List_dely_record);

            return view('reports.print_daily_record_1', compact('List_dely_record'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('dely_record', $dely_record);

}
              public function search_Supplier_account_statement (Request $request)
    {
      app()->setLocale(LaravelLocalization::getCurrentLocale());
        $reamingamount = 0;
         $supplier=supllier::find( $request->UserId);
        $Invoices = resource_purchases::where('suplier_id', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        $returnsales = orderDetails::whereDate('updated_at', '>=', $request->start_at)->whereDate('updated_at', '<=', $request->end_at)->get();
        $credittransactions = transactiontosuplliers::where('orginal_type', 2)->where('orginal_id', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        $credittransactionsLast =transactiontosuplliers::where('orginal_type', 2)->where('orginal_id', $request->UserId)->whereDate('created_at', '<', $request->start_at)->get();
        $returnsalesdata = [];
        $dataInvoices = [];
        $totaladdedvalue = 0;
        $totalpricefinal = 0;
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
        if ($returnsales) {
            foreach ($returnsales as  $returnsale) {
                $totaladdedvalue = 0;
                $totalpricefinal = 0;
                $invoice = orderTosupllier::find($returnsale->order_owner);
                $resource_purchases = resource_purchases::where('orderId',$returnsale->order_owner)->first();
                // return $invoice->suplier_id;
                                if($invoice!=null){

                if ($invoice->suplier_id  == $request->UserId) {
                    $totaladdedvalue = (($returnsale->purchasingـprice * $returnsale->returns_purchase) ) * $saleavt;
                    $totalpricefinal = ($returnsale->purchasingـprice * $returnsale->returns_purchase) ;
                    $pays = '';
                    if($resource_purchases){
                       if ($resource_purchases->Pay_Method_Name == 'Cash') {
                    $pays = __('report.cash');
                } elseif ($resource_purchases->Pay_Method_Name == 'Shabka') {
                    $pays = __('report.shabka');
                } elseif ($resource_purchases->Pay_Method_Name == "Credit") {
                    $pays = __('report.credit');
                } elseif ($resource_purchases->Pay_Method_Name == "Bank_transfer") {
                    $pays = __('home.Bank_transfer');
                } else {
                    $pays = __('home.Partition of the amount');
                }
                    }
                    $returnsalesdata[] = $returnsale;
                    $type=0;
                    if($resource_purchases==null){
                        
                    }else{
                        $type=$resource_purchases->Pay_Method_Name == "Credit" ? 1 : 0;
                    }
                    $dataInvoices[] = [
                        'id' => $returnsale->order_owner,
                        'data' => $returnsale->updated_at,
                        'branch' => '',
                        'payment' => $pays,
                        'user' => '-',
                        'type' => 2,
                        'typepayment' =>$type,
                        'amoint' => $totaladdedvalue +  $totalpricefinal
                    ];
                }
            }
            }
        }
     
    
        $avt = Avt::find(2);
        $saleavt = $avt->AVT;
        if ($Invoices != null) {
            foreach ($Invoices as $product) {
                $pays = '';
                if ($product->Pay_Method_Name == 'Cash') {
                    $pays = __('report.cash');
                } elseif ($product->Pay_Method_Name == 'Shabka') {
                    $pays = __('report.shabka');
                } elseif ($product->Pay_Method_Name == "Credit") {
                    $pays = __('report.credit');
                } elseif ($product->Pay_Method_Name == "Bank_transfer") {
                    $pays = __('home.Bank_transfer');
                } else {
                    $pays = __('home.Partition of the amount');
                }
                $totalprice=0;
                $totalAddedvalue=0;
                 foreach (orderDetails::where('order_owner',$product->orderId)->get() as $items)
                             {   
                                $totalprice += ($items->purchasingـprice )*(( $items->numberofpice+ $items->returns_purchase));
                                $totalAddedvalue += $items->Added_Value *  ( ( $items->returns_purchase + $items->numberofpice));
                 }
                                
                                
                                
                $dataInvoices[] = [
                    'id' => $product->orderId,
                    'data' => $product->created_at,
                    'branch' => $product->branch->name,
                    'payment' => $pays,
                    'user' => "-",
                    'type' => 1,
                    'typepayment' => $product->Pay_Method_Name == "Credit" ? 1 : 0,
                    'amoint' => $totalAddedvalue+ $totalprice-$product->discount
                ];
            }

}
            foreach ($credittransactions as $product) {
                $pays = '';
                if ($product->Pay_Method_Name == 'Cash') {
                    $pays = __('report.cash');
                } elseif ($product->Pay_Method_Name == 'Shabka') {
                    $pays = __('report.shabka');
                } elseif ($product->Pay_Method_Name == "Credit") {
                    $pays = __('report.credit');
                } elseif ($product->Pay_Method_Name == "Bank_transfer") {
                    $pays = __('home.Bank_transfer');
                } else {
                    $pays = __('home.Partition of the amount');
                }
                $dataInvoices[] = [
                    'id' => $product->id,
                    'data' => $product->created_at,
                    'branch' => $product->currentblance,
                    'payment' => $pays,
                    'user' => '-',
                    'type' => 3,
                    'typepayment' => $product->Pay_Method_Name == "Credit" ? 1 : 0,
                    'amoint' => $product->paidـamount
                ];
            }

    $Invoiceslast = [];
            $Invoiceslast = resource_purchases::where('suplier_id', $request->UserId)->where('Pay_Method_Name', 'Credit')->whereDate('created_at', '<', $request->start_at)->where('save', 1)->get();
            $returnsales =  orderDetails::whereDate('created_at', '<', $request->start_at)->get();
            foreach ($credittransactionsLast as $product) {
                $reamingamount -= (double)$product->Pay_Method_Name;
            }
         
            foreach ($Invoiceslast as $item) {
                   $totalprice=0;
            $totalAddedvalue=0;
               foreach (orderDetails::where('order_owner',$item->orderId)->get() as $items)
                 {   
                                $totalprice += $items->purchasingـprice  * $items->numberofpice;
                                $totalAddedvalue += $items->Added_Value * $items->numberofpice;
                 }
            
                $reamingamount +=  $totalAddedvalue+ $totalprice-$items->discount-$item->discount;
            }



            foreach ($returnsales as  $returnsale) {
                $totaladdedvalue = 0;
                $totalpricefinal = 0;
                                $invoice = orderTosupllier::find($returnsale->order_owner);

            $resource_purchases = resource_purchases::where('orderId',$returnsale->order_owner)->get();

                if ($invoice->suplier_id   == $request->UsorderId) {
                    $totaladdedvalue = (($returnsale->purchasingـprice * $returnsale->returns_purchase) ) * $saleavt;
                    $totalpricefinal = ($returnsale->purchasingـprice * $returnsale->returns_purchase) ;
                    $reamingamount -=($totaladdedvalue+$totalpricefinal-$resource_purchases->discount);
                }
            }
        
            // return $reamingamount;
        
        $dates = array();
        foreach ($dataInvoices as $key => $row) {
            $dates[$key] = ($row['data']);
        }
        array_multisort($dates, SORT_ASC, $dataInvoices);
        $data = [$credittransactions, $dataInvoices, round($reamingamount, 2)];
        return view('reports.print_supplier_account_statement', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('customerId', $request->UserId)->with('customerName', $supplier->name);


    }
    
    
    
    public function serverDBBackup()
    {//ENTER THE RELEVANT INFO BELOW
$mysqlHostName      = env('DB_HOST');
$mysqlUserName      = env('DB_USERNAME');
$mysqlPassword      = env('DB_PASSWORD');
$DbName             = env('DB_DATABASE');
$tables             = array();
$connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
$get_all_table_query = "SHOW TABLES";
$statement = $connect->prepare($get_all_table_query);
$statement->execute();
$result = $statement->fetchAll();

$prep = "Tables_in_$DbName";
foreach ($result as $res) {
    $tables[] =  $res[$prep];
}

$output = '';
$alterStatements = [];
foreach ($tables as $table) {
    $show_table_query = "SHOW CREATE TABLE " . $table . "";
    $statement = $connect->prepare($show_table_query);
    $statement->execute();

    $show_table_result = $statement->fetchAll();
    //detached CONSTRAINT
    foreach ($show_table_result as $show_table_row) {
        $preg = 'CONSTRAINT `(.*?)` FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)';
        preg_match_all('/' . $preg . '/', $show_table_row["Create Table"], $matches, PREG_SET_ORDER);
        $createTableWithoutConstraints = preg_replace('/,?\s*' . $preg . ',?/', '', $show_table_row["Create Table"]);
        if ($matches) {
            $alterTableQuery = "ALTER TABLE `$table` ";
            foreach ($matches as $match) {
                $constraintName = $match[1];
                $columnName = $match[2];
                $referencedTable = $match[3];
                $referencedColumn = $match[4];

                $alterTableQuery .= "ADD CONSTRAINT `$constraintName` FOREIGN KEY (`$columnName`) REFERENCES `$referencedTable` (`$referencedColumn`), ";
            }
            $alterStatements[] = trim($alterTableQuery, ', ') . ';COMMIT;';
        }

        $output .= "\n\n" . $createTableWithoutConstraints . ";\n\n";
    }

    $select_query = "SELECT * FROM " . $table . "";
    $statement = $connect->prepare($select_query);
    $statement->execute();
    $total_row = $statement->rowCount();
    if(!$total_row) {
        continue;
    }
    $columns = [];
    
    for ($count = 0; $count < $statement->columnCount(); $count++) {
        $column = $statement->getColumnMeta($count);
        $columns[] = "`".$column['name'] ."`";
    }
    $values = [];
    $output .= "\nINSERT INTO $table (";
    $output .= "" . implode(", ", $columns) . ") VALUES \n";
    for ($count = 0; $count < $total_row; $count++) {
        $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
        $table_value_array = array_values($single_result);
        $rowValues = [];

        foreach ($table_value_array as $value) {
            if ($value === null) {
                $rowValues[] = "NULL";
            } elseif (is_numeric($value)) {
                $rowValues[] = $value;
            } elseif (is_array($value) || is_object($value)) {
                $jsonValue = json_encode($value);
                $rowValues[] = "'" . addslashes($jsonValue) . "'";
            } else {
                $rowValues[] = "'" . addslashes($value) . "'";
            }
        }

        $values[] = "(" . implode(", ", $rowValues) . ")";
        
    }
    $output .= implode(",\n ", $values) . ";\n";
}

$file_name = 'database_backup_on_' . date('y-m-d') . '.sql';

$file_handle = fopen($file_name, 'w+');
fwrite($file_handle, $output);
//add CONSTRAINT foreign key 

foreach ($alterStatements as $alterStatement) {
    fwrite($file_handle, $alterStatement . "\n");
}
fclose($file_handle);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($file_name));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_name));

ob_clean();
flush();
readfile($file_name);
unlink($file_name);
        
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
  public function search_product_sales_purchases(Request $request)
    {
        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $productId = $request->productNo ?? '-';
        if ($productId == '-') {
            session()->flash('notfountreturnproduct', __('home.productnotfount'));
            $Invoices = [];
            return view('reports.product_sales_purchases', compact('Invoices'))->with('branch_Id', $request->branch);
        } else {
            if ($request->branch == '-' ) {
                $sales = sales::where('product_id', $productId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
                $return_sales = return_sales::where('product_id', $productId)->where('return_quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
                $orderDetails = orderDetails::where('product_id', $productId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
                $stock_update = stock_update::where('product_id', $productId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            } else {
                $return_sales = return_sales::where('product_id', $productId)->where('branch_id', $request->branch)->where('return_quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

                $orderDetails = orderDetails::where('product_id', $productId)->where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

                $sales = sales::where('product_id', $productId)->where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
                $stock_update = stock_update::where('product_id', $productId)->where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
           
            }
        }

$count=0;
$products=[];
foreach($stock_update as $item ){
    $products[]=[
        'id'=>$item->id,
        'Product_id'=>$item->product_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->productincrease==0?$item->productdecrease:$item->productincrease,
        'price'=>0,
        'discount'=>0,
        'operation'=> $item->productincrease==0?__('home.decreasequentity'):__('home.increasequantity'),
        'type'=>$item->productincrease==0?6:5,
        'current_balance'=>$item->product_name,

    ];
}
foreach($return_sales as $item ){
    $resentsales=sales::where('product_id',$item->product_id)->whereDate('created_at', '<=', $item->created_at)->orderBy('id','desc')->first();
    $products[]=[
        'id'=>$item->invoice_id,
         'Product_id'=>$item->product_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->return_quantity,
        'price'=>$item->return_Unit_Price,
        'discount'=>$item->discountvalue,
        'operation'=> __('home.salesـreturned'),
        'type'=>2,
        'current_balance'=>$resentsales->reamingQuantity+$item->return_quantity,

    ];
}
foreach($sales as $item ){
       $itemreturn= return_sales::where('product_id', $item->product_id)->where('invoice_id',$item->invoice_id)->first();

$countreturn=0;
if($itemreturn){
 $countreturn= $itemreturn->discountvalue;  
}
    $products[]=[
        'id'=>$item->invoice_id,
        'Product_id'=>$item->product_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->quantity+$item->quantityreturn,
        'discount'=>$item->Discount_Value+ $countreturn,
        'price'=>$item->Unit_Price,
        'operation'=> __('home.sales'),
        'current_balance'=>$item->reamingQuantity,
        'type'=>1
    ];
}

foreach($orderDetails as $item ){
    if($item->returns_purchase!=0){
        
         $products[]=[
        'id'=>$item->order_owner,
         'Product_id'=>$item->product_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->product_name,
        'created_at'=>$item->updated_at,
        'quantity'=>$item->returns_purchase,
        'price'=>$item->purchasingـprice,
        'discount'=>0,
        'operation'=> __('home.purchase_return'),
        'type'=>4,
        'current_balance'=>$item->reamingQuantity-$item->returns_purchase,

    ]; 
    }
    $products[]=[
        'id'=>$item->order_owner,
        'Product_id'=>$item->product_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->numberofpice,
        'price'=>$item->purchasingـprice,
        'discount'=>0,
        'operation'=> __('home.purchases'),
        'type'=>3,
        'current_balance'=>$item->reamingQuantity+$item->numberofpice,

    ];
}

$dates = array();
foreach ($products as $key => $row) {
   $dates[$key] = strtotime($row['created_at']);
}
array_multisort($dates, SORT_ASC, $products);

    // return $products;
     $data=[
        'products'=>$products,
        'start_at'=>$request->start_at,
        'end_at'=> $request->end_at
        ];
        
                return view('reports.print_sales_and_purchases', compact('data'))->with('start',$request->start_at)->with('end',$request->end_at);
    }


    public function print_sales_and_purchases(Request $request)
    {
        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $productId = $request->productNo ?? '-';
        if ($productId == '-') {
            session()->flash('notfountreturnproduct', __('home.productnotfount'));
            $Invoices = [];
            return view('reports.product_sales_purchases', compact('Invoices'))->with('branch_Id', $request->branch);
        } else {
            if ($request->branch == '-' ) {
                $sales = sales::where('product_id', $productId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
                $return_sales = return_sales::where('product_id', $productId)->where('return_quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
                $orderDetails = orderDetails::where('product_id', $productId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

            } else {
                $return_sales = return_sales::where('product_id', $productId)->where('branch_id', $request->branch)->where('return_quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

                $orderDetails = orderDetails::where('product_id', $productId)->where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

                $sales = sales::where('product_id', $productId)->where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            }
        }

$count=0;
$products=[];
foreach($return_sales as $item ){
    
    $products[]=[
        'id'=>$item->invoice_id,
        'Product_Code'=>$item->product_id,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->return_quantity,
        'price'=>$item->return_Unit_Price,
        'discount'=>$item->discountvalue,
        'operation'=> __('home.salesـreturned')
    ];
}
foreach($sales as $item ){
       $itemreturn= return_sales::where('product_id', $item->product_id)->where('invoice_id',$item->invoice_id)->first();
$countreturn=0;
if($itemreturn){
 $countreturn= $itemreturn->discountvalue;  
}
    $products[]=[
        'id'=>$item->invoice_id,
        'Product_Code'=>$item->product_id,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->quantity+$item->quantityreturn,
        'discount'=>$item->Discount_Value+ $countreturn,
        'price'=>$item->Unit_Price,
        'operation'=> __('home.sales')
    ];
}

foreach($orderDetails as $item ){
    if($item->returns_purchase!=0){
         $products[]=[
        'id'=>$item->order_owner,
        'Product_Code'=>$item->product_id,
        'product_name'=>$item->product_name,
        'created_at'=>$item->updated_at,
        'quantity'=>$item->returns_purchase,
        'price'=>$item->purchasingـprice,
        'discount'=>0,
        'operation'=> __('home.purchase_return')
    ]; 
    }
    $products[]=[
        'id'=>$item->order_owner,
        'Product_Code'=>$item->product_id,
        'product_name'=>$item->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->numberofpice,
        'price'=>$item->purchasingـprice,
        'discount'=>0,
        'operation'=> __('home.purchases')
    ];
}

$dates = array();
foreach ($products as $key => $row) {
   $dates[$key] = strtotime($row['created_at']);
}
array_multisort($dates, SORT_ASC, $products);

    // return $products;
    $data=[
        'products'=>$products,
        'start_at'=>$request->start_at,
        'end_at'=> $request->end_at
        ];
                return view('reports.print_sales_and_purchases', compact('products'))->with('start',$request->start_at)->with('end',$request->end_at);

    }
    
    
  public function product_sales_purchases()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.product_sales_purchases');
    }
    public function Bank_Transfer()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];
        return view('reports.Bank_Transfer', compact('data'));
    }




    public function ConvertBoxtobankReport()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];
        return view('reports.convert_cash_to_bank', compact('data'));
    }


    public function Bank_Statement()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];
        return view('reports.bankDecument', compact('data'));
    }
    public function searchConvertBoxtobankReport(Request $request)
    {
        $data = convertcashboxToBank::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

        return view('reports.convert_cash_to_bank', compact('data'));
    }


    public function transactionsToMasterBranch()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];
        return view('reports.transactionsToMasterBranch', compact('data'));
    }

    public function products_Transfer()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];
        return view('reports.product_Transfer', compact('data'));
    }
    public function Delivery_notes()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Delivery_notes');
    }


    public function printInvoicesAllItemsWithReturned($request)
    {

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);

        $saleData = sales::where("invoice_id", $request)->get();
        $InvoiceData = invoices::find($request);

        $data = [
            "invoicetotal_price" => $InvoiceData->Price - $InvoiceData->discount,
            "invoicetotal_addedvalue" => ($InvoiceData->Price - $InvoiceData->discount) * $avtSaleRate->AVT,
            "invoicetotal_discount" => $InvoiceData->discount,
            'salesData' => $saleData,
            'invoiceData' =>  $InvoiceData,
            'taxrat' => $avtSaleRate->AVT,
        ];

        return view('reports.printInvoicesAllItemsWithReturned', compact('data'));
    }
    public function Customersـexceededـgraceـperiod()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $customers = customers::where('Balance', '!=', 0)->get();
        $Customersـexceededـgraceـperiod = [];
        foreach ($customers as $customer) {
            $ffdate = $customer->updated_at;
            $tdate = \Carbon\Carbon::now()->addHours(3)::now();
            $start = \Carbon\Carbon::parse($ffdate);
            $end =  \Carbon\Carbon::parse($tdate);
            $diff_in_days = $end->diffInDays($start);
            if ($diff_in_days > $customer->grace_period_in_days) {
                $Customersـexceededـgraceـperiod[] = $customer;
            }
            // $Customersـexceededـgraceـperiod
        }
        return view('reports.Customersexceededgraceperiod', compact('Customersـexceededـgraceـperiod'));
    }
    public function VAT()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.VAT');
    }




    public function Best_selling_products()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Best_selling_products');
    }



    public function budgetsheet()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        return view('reports.budget sheet');
    }


    public function stockquantity()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [
            'display' => 1
        ];
        return view('reports.stockquantity', compact('data'))->with('branchdata', '-' . "/" . '==' . "/" . '1');
    }





    public function shift_detailes()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.shift_detailes');
    }




    public function Supplier_credit_payment()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Supplier_credit_payment');
    }
    public function Customer_account_statement()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Customer_account_statement');
    }
    public function TransFerCashTothenNextDay()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.TransFerCashTothenNextDay');
    }
    public function print_supplierList()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.print_supplierList');
    }
    public function print_customeList()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.print_customeList');
    }
    public function Customerlist()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.customerList');
    }

    public function customerـpurchases()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.customerpurchases');
    }
    public function purchasproducttocustomer()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.purchasproducttocustomer');
    }
    public function credit_collection()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.credit_collection');
    }


    public function purchasereports()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.purchasereports');
    }

    public function Purchasesـfromـsuppliers()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Purchases_from_suppliers');
    }

    public function PurchasesـfromـsuppliersNew()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.purchaseFromSupplierreports');
    }


    public function Refundـofـresourceـpurchases()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Refund_of_resource_purchases');
    }




    public function Requestـaـquoteـfromـtheـsupplier()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Request_A_quote_from_supplier');
    }


    public function product_sales()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.product_sales');
    }

    public function report_returns_sale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.report_returns_sale');
    }


    public function salesـprofits()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.sales_profits');
    }
    public function supplierList()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.supplierList');
    }
    public function showallBranchs()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('users.show_branchs');
    }

    public function Expenses()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Expenses');
    }



    public function Creditsales()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Creditsales');
    }

    public function employeeـsales()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.employeesales');
    }

    public function Requestـoffersـfromـsuppliers()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.Request_offers_from_suppliers');
    }


    public function report_offer_price_customer()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $Invoices = null;
        return view('reports.report_offer_price_customer', compact('Invoices'));
    }




    public function show_offer_price_customer(Request $request)
    {
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $userId = $request->UserId;
        if ($userId == '-') {
            $Invoices = offer_price_to_customer::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            return view('reports.report_offer_price_customer', compact('Invoices'))->with('userId', $userId);
        }
        $Invoices = offer_price_to_customer::where('customer_id', $userId)->get();
        return view('reports.report_offer_price_customer', compact('Invoices'))->with('userId', $userId);
    }


    public function search_Delivery_notes(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->UserId;
        if ($supplierId == '-') {
            $Invoices = resource_purchases::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;

            return view('reports.Delivery_notes', compact('Invoices'))->with('supplierId', $supplierId);
        }
        $Invoices = resource_purchases::where('orderId', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        //return $Invoices;
        return view('reports.Delivery_notes', compact('Invoices'))->with('supplierId', $supplierId);
    }
    public function searchtransactionsToMasterBranch(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->UserId;
        if ($request->branch == '-') {
            $tansactions = transferMoney_to_mainbranch::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;
            $data = [
                'start_at' => $request->start_at,
                "end_at" => $request->end_at,
                "transactions" => $tansactions
            ];
            return view('reports.print_transactionsToMasterBranch', compact('data'));
        }
        $tansactions = transferMoney_to_mainbranch::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        //return $Invoices;
        $data = [
            'start_at' => $request->start_at,
            "end_at" => $request->end_at,
            "transactions" => $tansactions
        ];
        return view('reports.print_transactionsToMasterBranch', compact('data'));
    }

    public function search_Bank_Transfer(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];

        $data = cash_from__bank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        //return $Invoices;

        return view('reports.Bank_Transfer', compact('data'));
    }


    public function search_products_Transfer(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $transactions = [];
        $transctions = product_movement_another_branch::where('branch_from', $request->branch_from)->where('branch_to', $request->branch_to)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        $data = [
            "start_at" => $request->start_at,
            "end_at" => $request->end_at,
            "branch_to" =>  $request->branch_to,
            "branch_from" => $request->branch_from,
            "transctions" => $transctions
        ];
        // return $request;
        return view('reports.product_Transfer', compact('data'));
    }


    public function search_TransFerCashTothenNextDay(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $transactions = [];

        $data = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

        //  return $data;
        return view('reports.print_TransFerCashTothenNextDay', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at);
    }





    public function search_shift_detailes(Request $request)
    {
        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->UserId;
        if ($request->branch == '-' && $request->pay == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;

            return view('reports.shift_detailes', compact('Invoices'))->with('pay', [$request->pay, $request->branch]);
        }
        if ($request->branch == '-' && $request->pay == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;       
            return view('reports.shift_detailes', compact('Invoices'))->with('supplierId', $supplierId);
        } elseif ($request->branch != '-' && $request->pay == '-') {
            $Invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;

            return view('reports.shift_detailes', compact('Invoices'))->with('pay', [$request->pay, $request->branch]);
        } elseif ($request->branch == '-' && $request->pay != '-') {
            $Invoices = invoices::where('Pay', $request->pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;

            return view('reports.shift_detailes', compact('Invoices'))->with('pay', [$request->pay, $request->branch]);
        } else {
            $Invoices = invoices::where('branchs_id', $request->branch)->where('Pay', $request->pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;
            return view('reports.shift_detailes', compact('Invoices'))->with('pay', [$request->pay, $request->branch]);
        }
    }


    public function search_account_statement(Request $request)
{
    $branch_name='-';
    
    
    
                if ($request->branch == '-' ) {

                $LAST_credittransactions = credittransactions::where('customer_id',$request->supplierId)->whereDate('created_at', '<', $request->start_at)->where('save',1)->get();
                $credittransactions = credittransactions::where('customer_id',$request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save',1)->get();
            
                }else{
                    $branch=branchs::find($request->branch);
                    $branch_name=__('home.branch').' : '.$branch->name;
                    $financial_accounts=financial_accounts::find($request->supplierId);
                    if(Auth()->user()->branchs_id==$financial_accounts->candidate_branch){
                       $LAST_credittransactions = credittransactions::where('customer_id',$request->supplierId)->whereDate('created_at', '<', $request->start_at)->where('save',1)->get();
                $credittransactions = credittransactions::where('customer_id',$request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save',1)->get();
                     
                        
                    }else{
                $LAST_credittransactions = credittransactions::where('branchs_id',$request->branch)->where('customer_id',$request->supplierId)->whereDate('created_at', '<', $request->start_at)->where('save',1)->get();
                $credittransactions = credittransactions::where('branchs_id',$request->branch)->where('customer_id',$request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save',1)->get();
                    }
                    
                }
       
        $List_dely_record=[];
$financial_accounts=financial_accounts::find($request->supplierId);

foreach($credittransactions as $item){
    
    
                          $parent_name=credittransactions::where('note',$item->note)->where('sent_abd_count','!=',0)->orwhere('sent_serf_count','!=',0)->where('note',$item->note)->first();
                          $name_parent='-';
                          if($parent_name==null){
                                                        $name_parent='-';

                          }else{
                                                            $name_parent=$parent_name->financial_accounts_data->name;

                          }
                                   if ($item->type == 'Cash'){
                                      $payment= __('report.cash') ;
                                  }elseif ($item->type== 'Bank_transfer'){
                            $payment=__('home.Bank_transfer');

                             }
                            elseif ($item->type== 'Shabka'){
                                $payment= __('report.shabka') ;
                            }else{
                                                                $payment= '-';

                                if($item->type==null){
                                   
                                   
                                   
                                   
                                   
                                   
                                    if ($item->Pay_Method_Name == 'Cash'){
                                   $payment= __('report.cash') ;
                                    }
                                    elseif ($item->Pay_Method_Name== 'Bank_transfer'){
                                   $payment=__('home.Bank_transfer');
                                   }
                                   elseif ($item->Pay_Method_Name== 'Shabka'){
                                  $payment= __('report.shabka') ;
                                  } elseif ($item->Pay_Method_Name== 'Partition'){
                                  $payment=__('home.Partition of the amount') ;
                                  }else{
                                    $payment= __('report.credit') ;
                                  } 
                                    
                                }
                            }

    $List_dely_record[]=[
            'id' => $item->id,
            'recive_amount' => $item->recive_amount,
            'depit' => $item->debtor,
            'credit' => $item->creditor,
            'current_blance'=>$item->currentblance,
            'dely_record'=>$item->dely_record,
            'date_export'=>$item->date_export,
            'date'=>$item->created_at,
            'note'=>$item->note.'-'.'('.$payment.')'.'-'.$name_parent,
            'user'=>$item->user->name,
            'branch'=>$item->branch->name,

            ];

    
}
$credit=0;
$debit=0;
$blance=0;
foreach($LAST_credittransactions as $item){
   
$credit+=$item->creditor;
$debit+=$item->debtor;
$blance=$item->currentblance;
    
}

        $dates = array();
        
        foreach ($List_dely_record as $key => $row)
        {
            $dates[$key] = strtotime($row['date']);
        }
        array_multisort($dates, SORT_ASC, $List_dely_record);
        $data=$List_dely_record;
    return view('reports.print_acoount_statment', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('account_name', $financial_accounts->name)->with('account_id', $financial_accounts->id)->with('blance', $blance)->with('debit', $debit)->with('credit', $credit)->with('branch_name', $branch_name);


}


    public function searchbankDecument(Request $request)
    {
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay', '!=', 'Cash')->where('Pay', '!=', 'Credit')->where('save', 1)->get();
            $credittransactions = credittransactions::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('pay_method', '!=', 'Cash')->where('pay_method', '!=', 'Credit')->get();
            $transactiontosuplliers = transactiontosuplliers::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->where('Pay_Method_Name', '!=', 'Credit')->get();
            $expenses = expenses::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->get();
            $resource_purchases = resource_purchases::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->where('Pay_Method_Name', '!=', 'Credit')->where('save', 1)->get();
            $cash_from__bank = cash_from__bank::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('payment_method', '!=', 'Cash')->get();
            $convertcashboxToBank = convertcashboxToBank::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            // return $Invoices;
            $Cash_withdrawal_from_the_bank = Cash_withdrawal_from_the_bank::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } else {


            $Invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay', '!=', 'Cash')->where('Pay', '!=', 'Credit')->where('save', 1)->get();
            $credittransactions = credittransactions::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('pay_method', '!=', 'Cash')->where('pay_method', '!=', 'Credit')->get();
            $transactiontosuplliers = transactiontosuplliers::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->where('Pay_Method_Name', '!=', 'Credit')->get();
            $expenses = expenses::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->get();
            $resource_purchases = resource_purchases::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay_Method_Name', '!=', 'Cash')->where('Pay_Method_Name', '!=', 'Credit')->where('save', 1)->get();
            $cash_from__bank = cash_from__bank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('payment_method', '!=', 'Cash')->get();
            //return $Invoices;
            $convertcashboxToBank = convertcashboxToBank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $Cash_withdrawal_from_the_bank = Cash_withdrawal_from_the_bank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }

        $data = [];
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
        foreach ($Invoices  as   $Invoice) {
            $pays = '';
            if ($Invoice->Pay == 'Shabka') {
                $pays = __('report.shabka');
            } elseif ($Invoice->Pay == "Bank_transfer") {
                $pays = __('home.Bank_transfer');
            } else {
                $pays = __('home.Partition of the amount');
            }
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.sales'),
                'payment' => $pays,
                'in' => 1,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->Bank_transfer + $Invoice->bankamount)
            ];
        }
        foreach ($Cash_withdrawal_from_the_bank  as   $Invoice) {
            $pays = '';

            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.Cash_withdrawal_from_the_bank'),
                'payment' => __('report.cash'),
                'in' => 0,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->amount)
            ];
        }



        foreach ($resource_purchases  as   $Invoice) {
            $pays = '';
            if ($Invoice->Pay_Method_Name == 'Shabka') {
                $pays = __('report.shabka');
            } else {
                $pays = __('home.Bank_transfer');
            }
            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.purchases'),
                'payment' => $pays,
                'in' => 0,

                'user' =>  $orderTosupllier->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->In_debt) - (($Invoice->discount))
            ];
        }

        foreach ($convertcashboxToBank   as   $Invoice) {
            $pays = '';

            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.convertboxtobank'),
                'payment' => '-',
                'in' => 1,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->amount)
            ];
        }


        foreach ($credittransactions  as   $Invoice) {
            $pays = '';
            if ($Invoice->pay_method == 'Shabka') {
                $pays = __('report.shabka');
            } else {
                $pays = __('home.Bank_transfer');
            }
            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.voucher'),
                'payment' => $pays,
                'in' => 1,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->recive_amount)
            ];
        }
        foreach ($transactiontosuplliers  as   $Invoice) {
            $pays = '';
            if ($Invoice->Pay_Method_Name == 'Shabka') {
                $pays = __('report.shabka');
            } else {
                $pays = __('home.Bank_transfer');
            }
            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.Receipt document'),
                'payment' => $pays,
                'in' => 0,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->paidـamount)
            ];
        }


        foreach ($expenses  as   $Invoice) {
            $pays = '';
            if ($Invoice->Pay_Method_Name == 'Shabka') {
                $pays = __('report.shabka');
            } else {
                $pays = __('home.Bank_transfer');
            }
            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.other_expenses'),
                'payment' => $pays,
                'in' => 0,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->Theـamountـpaid)
            ];
        }

        foreach ($cash_from__bank  as   $Invoice) {
            $pays = '';
            if ($Invoice->Pay_Method_Name == 'Shabka') {
                $pays = __('report.shabka');
            } else {
                $pays = __('home.Bank_transfer');
            }
            $orderTosupllier = orderTosupllier::find($Invoice->orderId);
            // return $orderTosupllier;
            $data[] = [
                'date' =>  $Invoice->created_at,
                'type' => __('home.shabka_bank'),
                'payment' => $pays,
                'in' => 1,
                'user' =>  $Invoice->user->name,
                'branch' =>  $Invoice->branch->name,
                'amount' => ($Invoice->the_amount)
            ];
        }
        usort($data, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return view('reports.printBankStatment', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('branch', $request->branch);
    }


    public function search_Supplier_credit_payment(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch == '-') {
        if ($request->supplierId == '-') {
            $Invoices = credittransactions::where('type_decument', 2)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $request->supplierId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);
        }
        $Invoices = credittransactions::where('type_decument', 2)->where('customer_id', $request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $request->supplierId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);
        }else{
            
            if ($request->supplierId == '-') {
            $Invoices = credittransactions::where('branchs_id', $request->branch)->where('type_decument', 2)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $request->supplierId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);
        }
        $Invoices = credittransactions::where('branchs_id', $request->branch)->where('type_decument', 2)->where('customer_id', $request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $request->supplierId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);
           
            
        }
    }

  public function search_Customer_account_statement(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $customer = customers::find($request->UserId);
        $reamingamount = $customer->opeing_blance??0;

        $Invoices = invoices::where('customer_id', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        $returnsales = return_sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        $credittransactions = credittransactions::where('orginal_type', 1)->where('orginal_id', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        $credittransactionsLast = credittransactions::where('orginal_type', 1)->where('orginal_id', $request->UserId)->whereDate('created_at', '<', $request->start_at)->get();
        $returnsalesdata = [];
        $dataInvoices = [];
        $totaladdedvalue = 0;
        $totalpricefinal = 0;
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
        if ($returnsales) {
            foreach ($returnsales as  $returnsale) {
                $totaladdedvalue = 0;
                $totalpricefinal = 0;
                $invoice = invoices::find($returnsale->invoice_id);
                if ($invoice->customer_id  == $request->UserId) {
                    $totaladdedvalue = (($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice) * $saleavt;
                    $totalpricefinal = ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                    $pays = '';
                    if ($invoice->Pay == 'Cash') {
                        $pays = __('report.cash');
                    } elseif ($invoice->Pay == 'Shabka') {
                        $pays = __('report.shabka');
                    } elseif ($invoice->Pay == "Credit") {
                        $pays = __('report.credit');
                    } elseif ($invoice->Pay == "Bank_transfer") {
                        $pays = __('home.Bank_transfer');
                    } else {
                        $pays = __('home.Partition of the amount');
                    }
                    $returnsalesdata[] = $returnsale;
                    $dataInvoices[] = [
                        'id' => $returnsale->invoice_id,
                        'data' => $returnsale->created_at,
                        'branch' => $invoice->branch->name,
                        'payment' => $pays,
                        'user' => '-',
                        'type' => 2,
                        'typepayment' => $invoice->Pay == "Credit" ? 1 : 0,
                        'amoint' => $totaladdedvalue +  $totalpricefinal
                    ];
                }
            }
        }
    
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
        if ($Invoices != null) {
            foreach ($Invoices as $product) {
                $pays = '';
                if ($product->Pay == 'Cash') {
                    $pays = __('report.cash');
                } elseif ($product->Pay == 'Shabka') {
                    $pays = __('report.shabka');
                } elseif ($product->Pay == "Credit") {
                    $pays = __('report.credit');
                } elseif ($product->Pay == "Bank_transfer") {
                    $pays = __('home.Bank_transfer');
                } else {
                    $pays = __('home.Partition of the amount');
                }
                $dataInvoices[] = [
                    'id' => $product->id,
                    'data' => $product->created_at,
                    'branch' => $product->branch->name,
                    'payment' => $pays,
                    'user' => $product->user->name,
                    'type' => 1,
                    'typepayment' => $product->Pay == "Credit" ? 1 : 0,
                    'amoint' => $product->Bank_transfer +  $product->creaditamount + $product->bankamount + $product->cashamount
                ];
            }

}
            foreach ($credittransactions as $product) {
                $pays = '';
                if ($product->pay_method == 'Cash') {
                    $pays = __('report.cash');
                } elseif ($product->pay_method == 'Shabka') {
                    $pays = __('report.shabka');
                } elseif ($product->pay_method == "Credit") {
                    $pays = __('report.credit');
                } elseif ($product->pay_method == "Bank_transfer") {
                    $pays = __('home.Bank_transfer');
                } else {
                    $pays = __('home.Partition of the amount');
                }
                $dataInvoices[] = [
                    'id' => $product->id,
                    'data' => $product->created_at,
                    'branch' => $product->currentblance,
                    'payment' => $pays,
                    'user' => $product->user->name,
                    'type' => 3,
                    'typepayment' => $product->Pay == "Credit" ? 1 : 0,
                    'amoint' => $product->recive_amount
                ];
            }

    $Invoiceslast = [];
            $Invoiceslast = invoices::where('customer_id', $request->UserId)->where('Pay', 'Credit')->whereDate('created_at', '<', $request->start_at)->where('save', 1)->get();
            $returnsales = return_sales::whereDate('created_at', '<', $request->start_at)->get();
      
    foreach ($credittransactionsLast as $product) {
                $reamingamount -= $product->recive_amount;
            }
            foreach ($Invoiceslast as $product) {
                $reamingamount += $product->Bank_transfer +  $product->creaditamount + $product->bankamount + $product->cashamount;
            }
            
            // return $reamingamount;
            

            foreach ($returnsales as  $returnsale) {
                $totaladdedvalue = 0;
                $totalpricefinal = 0;
                $invoice = invoices::find($returnsale->invoice_id);
              
                if ($invoice->customer_id  == $request->UserId  &&$invoice->Pay == "Credit") {
                    $i[]=$returnsale->invoice_id;
                 $totaladdedvalue = (($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice) * $saleavt;
                    $totalpricefinal = ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                   $reamingamount -=($totaladdedvalue+$totalpricefinal);
                }
            }
        
            // return $reamingamount;
        
        $dates = array();
        foreach ($dataInvoices as $key => $row) {
            $dates[$key] = strtotime($row['data']);
        }
        array_multisort($dates, SORT_ASC, $dataInvoices);

        $data = [$credittransactions, $dataInvoices, round($reamingamount, 2), $dataInvoices];
        return view('reports.print_Customer_account_statement', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('customerId', $request->UserId)->with('customerName', $customer->name);
    }
    
    
     public function generate_customer_statment_pdf($customerId,$start,$end)
    {
    $branch_name='-';
    
    
    
                {

                $LAST_credittransactions = credittransactions::where('customer_id',$customerId)->whereDate('created_at', '<',$start)->where('save',1)->get();
                $credittransactions = credittransactions::where('customer_id',$customerId)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->where('save',1)->get();
            
                }
       
        $List_dely_record=[];
$financial_accounts=financial_accounts::find($customerId);

foreach($credittransactions as $item){
    $List_dely_record[]=[
            'id' => $item->id,
            'recive_amount' => $item->recive_amount,
            'depit' => $item->debtor,
            'credit' => $item->creditor,
            'current_blance'=>$item->currentblance,
            'dely_record'=>$item->dely_record,
            'date_export'=>$item->date_export,
            'date'=>$item->created_at,
            'note'=>$item->note,
            'user'=>$item->user->name,
            'branch'=>$item->branch->name,
            
            ];

    
}
$credit=0;
$debit=0;
$blance=0;
foreach($LAST_credittransactions as $item){
   
$credit+=$item->creditor;
$debit+=$item->debtor;
$blance=$item->currentblance;
    
}

        $dates = array();
        
        foreach ($List_dely_record as $key => $row)
        {
            $dates[$key] = strtotime($row['date']);
        }
        array_multisort($dates, SORT_ASC, $List_dely_record);
        
        
            // return view('reports.print_acoount_statment', compact('data'))->with('start_at', $request->start_at)->with('end_at', $request->end_at)->with('account_name', $financial_accounts->name)->with('account_id', $financial_accounts->id)->with('blance', $blance)->with('debit', $debit)->with('credit', $credit)->with('branch_name', $branch_name);

        $data = [$List_dely_record, 0, 0, 0,$start,$end,$financial_accounts->name,$financial_accounts->id,$debit,$credit];
        
                $tran = ['data'=>$data];
                $dateTime = now();


        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.Customer_account_statment', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "Customer_account_statment".$financial_accounts->name."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );


        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function printExpensesReportlast($branch, $reason, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $start_at = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        //return $request;
        if ($branch == '-') {
            if ($reason == '-') {
                $Invoices = expenses::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            } else {
                $Invoices = expenses::where('reasonId_id', $reason)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            }
        } else {
            if ($reason == '-') {
                $Invoices = expenses::where('branchs_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            } else {
                $Invoices = expenses::where('branchs_id', $branch)->where('reasonId_id', $reason)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            }
        }
        return view('reports.printExpensesReport', compact('Invoices'))->with('branch', $branch);
    }
    public function print_Bank_Transfer($branch, $startat, $end_at)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $branchname = branchs::find($branch);
        $branchname = $branchname->name;
        $data = [];

        $transactions = cash_from__bank::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $data = [
            'start_at' => $startat[0],
            "end_at" => $end_at[0],
            "branch" => $branchname,
            "transactions" => $transactions
        ];
        return view('reports.print_bank_transfer', compact('data'));
    }

    public function print_products_Transfer($branch_from, $branch_to, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $transactions = [];

        $transctions = product_movement_another_branch::where('branch_from', $branch_from)->where('branch_to', $branch_to)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        $data = [
            "start_at" => $startat,
            "end_at" => $end_at,
            "branch_to" =>  $branch_to,
            "branch_from" => $branch_from,
            "transctions" => $transctions
        ];
        return view('reports.print_transction_product', compact('data'));
    }

    public function printExpensesReport(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        //return $request;
        if ($request->branch == '-') {
            $Invoices = expenses::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end_at)->get();
        } else {
            $Invoices = expenses::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }
        return view('reports.Expenses', compact('Invoices'))->with('branch', $request->branch);
    }


 public function search_stockquantity(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        //  return  $request;
        $display = $request->choosequantitytodisplay;

        if ($request->branch == '-') {
            if ($display == '==') {
                if($request->Location== '-'){
                    $products = products::where('numberofpice', $request->quantity)->get();
                }
                else{
                 $products = products::where('Product_Location', 'LIKE', '%' . $request->Location . '%')->where('numberofpice', $request->quantity)->get();
  
                }
            } else {
                if($request->Location== '-'){

                $products = products::where('numberofpice', $display, $request->quantity)->get();
                }else{
                    $products = products::where('Product_Location', 'LIKE', '%' . $request->Location . '%')->where('numberofpice', $display, $request->quantity)->get();
 
                }
            }

        return view('reports.printstockquantity', compact('products'))->with('data', $request->end_at);


            // return view('reports.stockquantity', compact('products'))->with('branchdata', '-' . "/" . $display . "/" . $request->quantity."/".$request->Location);
        } else {
            if ($display == '==') {
                if($request->Location== '-'){
                    $products = products::where('numberofpice', $request->quantity)->where('branchs_id', $request->branch)->get();
                }
                else{
                 $products = products::where('Product_Location', 'LIKE', '%' . $request->Location . '%')->where('numberofpice', $request->quantity)->get();
  
                }
            } else {
                if($request->Location== '-'){
                    $products = products::where('numberofpice', $display, $request->quantity)->where('branchs_id', $request->branch)->get();
                }
                else{
                 $products = products::where('Product_Location', 'LIKE', '%' . $request->Location . '%')->where('numberofpice', $display, $request->quantity)->where('branchs_id', $request->branch)->get();
  
                }
            }
            //   return $products;
        return view('reports.printstockquantity', compact('products'))->with('data', $request->end_at);

            // return view('reports.stockquantity', compact('products'))->with('branchdata',  $request->branch . "/" . $display . "/" . $request->quantity."/".$request->Location);
        }
    }


    public function search_Expenses(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
$branch   =$request->branch;
if ($request->branch == '-') {
            if ($request->enpenses_reason == '-') {
                $Invoices = credittransactions::where('orginal_type', 3)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            } else {
                $Invoices = credittransactions::where('orginal_type', 3)->where('orginal_id', $request->enpenses_reason)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            }
        } else {
            if ($request->enpenses_reason == '-') {
                $Invoices = credittransactions::where('branchs_id', $request->branch)->where('orginal_type', 3)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            } else {
                $Invoices = credittransactions::where('reasonId_id', $request->enpenses_reason)->where('orginal_type', 3)->where('orginal_id', $request->enpenses_reason)->where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            }
        }
                return view('reports.printExpensesReport', compact('Invoices'))->with('branch', $branch);

    }

    public function search_Best_selling_products(Request $request)
    {
        //return  $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch == '-') {
            $bestSaleing = sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('quantity', ">", 0)->where('save', 1)->get();
        } else {
            $bestSaleing = sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('quantity', ">", 0)->where('save', 1)->get();
        }
        //return $bestSaleing;

        $bestselling = [];
        $listofId = [];
        $i = 0;
        foreach ($bestSaleing as $product) {
            if (!in_array($product->product_id, $listofId)) {
                if ($request->branch == '-') {
                    $bestSaleingofproduct = sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('product_id', $product->product_id)->where('quantity', ">", 0)->where('save', 1)->get();
                } else {
                    $bestSaleingofproduct = sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('product_id', $product->product_id)->where('quantity', ">", 0)->where('save', 1)->get();
                }
                $numberOfSall = 0;
                foreach ($bestSaleingofproduct as $saleproduct) {

                    $numberOfSall += $saleproduct->quantity;
                    $bestselling[$i] = [
                        'productcode' => $saleproduct->productData->Product_Code,
                        'productname' => $saleproduct->productData->product_name,
                        'numberofsall' => $numberOfSall,
                        'branch' => $saleproduct->branch->name,
                        'end_at' => $request->end_at,
                        'start_at' => $request->start_at
                    ];
                }
                $i++;
            }
            $listofId[] = $product->product_id;
        }
        
                $dates = array();
foreach ($bestselling as $key => $row) {
   $dates[$key] = ($row['numberofsall']);
}
array_multisort($dates, SORT_DESC, $bestselling);

        //return gettype($data);
        return view('reports.Best_selling_products', compact('bestselling'))->with('branch_id', $request->branch);
    }









    public function search_credit_collection(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $UserId = $request->UserId;
        if ($request->branch == '-') {
        if ($UserId == '-') {
            $Invoices = credittransactions::where('note', 'LIKE', '%' . 'سند قبض' . '%')->whereDate('created_at', '>=', $request->start_at)->where('decument_id', 0)->whereDate('created_at', '<=', $request->end_at)->get();

            return view('reports.print_credit_collection', compact('Invoices'))->with('customer_id', $UserId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);;
        }
               $Invoices = credittransactions::where('note', 'LIKE', '%'. 'سند قبض'. '%')->where('customer_id', $UserId)->where('decument_id', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
      

      
        return view('reports.print_credit_collection', compact('Invoices'))->with('customer_id', $UserId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);;
   
        }else{
            
             if ($UserId == '-') {
            $Invoices = credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'سند قبض' . '%')->whereDate('created_at', '>=', $request->start_at)->where('decument_id', 0)->whereDate('created_at', '<=', $request->end_at)->get();

            return view('reports.print_credit_collection', compact('Invoices'))->with('customer_id', $UserId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);;
        }
               $Invoices = credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'سند قبض' . '%')->where('orginal_id', $UserId)->where('decument_id', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        return view('reports.print_credit_collection', compact('Invoices'))->with('customer_id', $UserId)->with('start_at', $request->start_at)->with('end_at', $request->end_at);;
       
            
            
            
        }
  }





    public function search_VAT(Request $request)
    {
       app()->setLocale(LaravelLocalization::getCurrentLocale());
        //return $request;
        $totalVatSales = 0;
        $totalVatPrachese = 0;
        $totalvarExpenses = 0;

        $avt = Avt::find(1);
        $avtpurchases = Avt::find(2);
        $saleavt = $avt->AVT;
        $purchasesavt = $avtpurchases->AVT;
        $countsales = 0;
        $countpurchase = 0;
        $countexpanses = 0;
        $purachasereturntax = 0;
        $salesreturntax = 0;
        $totalpurchase = 0;
        $totalreturnpurchase= 0;

        $countofreturnsaleslist = [];
        $countofreturnpurshaseslist = [];
        if ($request->branch == '-') {
            $invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $expenses = credittransactions::where('note', 'LIKE', '%' . 'سند صرف' . '%')->where('vat', 1)->where('customer_id', 102)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            foreach ($invoices as $invoice) {
                $totalVatSales += $invoice->Bank_transfer+$invoice->creaditamount+$invoice->bankamount+$invoice->cashamount ;
            }
            foreach ($expenses as $expense) {
                $totalvarExpenses +=  $expense->recive_amount;
            }
            
    $resource_purchases = resource_purchases::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

            foreach ($resource_purchases as $item) {
            foreach (orderDetails::where('order_owner', $item->orderId)->get()  as $orderDetailes) {
                $totalVatPrachese += $orderDetailes->Added_Value * $orderDetailes->numberofpice;
                $purachasereturntax += $orderDetailes->Added_Value * $orderDetailes->returns_purchase;
                $totalreturnpurchase+=$orderDetailes->purchasingـprice * $orderDetailes->returns_purchase  ;
                $totalpurchase+=   $orderDetailes->purchasingـprice * $orderDetailes->numberofpice  ;
                
                if ($orderDetailes->returns_purchase > 0 && !in_array($orderDetailes->order_owner, $countofreturnpurshaseslist)) {
                    $countofreturnpurshaseslist[] = $orderDetailes->order_owner;
                }
            }
                
            }
            $resource_purchases = resource_purchases::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

            $returnsales = return_sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            foreach ($returnsales as $invoice) {

                $salesreturntax += (($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice) ;
                if (!in_array($invoice->invoice_id, $countofreturnsaleslist)) {
                    $countofreturnsaleslist[] = $invoice->invoice_id;
                }
            }
            $countsales = count($invoices);
            $countpurchase = count($resource_purchases);
            $countexpanses = count($expenses);
        } else {
            $invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $expenses = credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'سند صرف' . '%')->where('vat', 1)->where('customer_id', 102)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            foreach ($invoices as $invoice) {
                $totalVatSales += $invoice->Bank_transfer+$invoice->creaditamount+$invoice->bankamount+$invoice->cashamount ;
            }
            foreach ($expenses as $expense) {
                $totalvarExpenses +=  $expense->recive_amount;
            }
            $resource_purchases = resource_purchases::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            $ordersDetails = orderDetails::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            foreach ($ordersDetails as $orderDetailes) {
                if ($request->branch == $orderDetailes->productData->branchs_id) {
                 
                $totalVatPrachese += $orderDetailes->Added_Value * $orderDetailes->numberofpice;
                $purachasereturntax += $orderDetailes->Added_Value * $orderDetailes->returns_purchase;
                $totalreturnpurchase+=$orderDetailes->purchasingـprice * $orderDetailes->returns_purchase  ;
                $totalpurchase+=   $orderDetailes->purchasingـprice * $orderDetailes->numberofpice  ;
                
                    if ($orderDetailes->returns_purchase > 0 && !in_array($orderDetailes->order_owner, $countofreturnpurshaseslist)) {
                        $countofreturnpurshaseslist[] = $orderDetailes->order_owner;
                    }
                }
            }


            $returnsales = return_sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            foreach ($returnsales as $invoice) {
                $salesreturntax += (($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice) ;
                if (!in_array($invoice->invoice_id, $countofreturnsaleslist)) {
                    $countofreturnsaleslist[] = $invoice->invoice_id;
                }
            }
            $countsales = count($invoices);
            $countpurchase = count($resource_purchases);
            $countexpanses = count($expenses);
        }
        $data = [
            'returncountsales' => count($countofreturnsaleslist),
            'returncountpurchases' =>  count($countofreturnpurshaseslist),
            'salesreturntax' => $salesreturntax*$saleavt,
            'salesreturn_withodtaxtax' => $salesreturntax,
            'purachasereturntax' => $purachasereturntax,
            'totalpurchase' => $totalpurchase,
            'totalreturnpurchase' => $totalreturnpurchase,
            'purachasereturntax' => $purachasereturntax,
            'totalVatPrachese_tax' => $totalVatPrachese,
            'countsales' => $countsales,
            'countpurchase' => $countpurchase,
            'countexpanses' => $countexpanses,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'totalVatSales' =>  (($totalVatSales*100/115)*$saleavt),
            'totalVatPrachese' => $totalVatPrachese,
            'total_sale'=>($totalVatSales*100/115),
            'totalvarExpenses' => $totalvarExpenses ,
            
        ];
                return view('reports.print_VAT', compact('data'));

        return view('reports.VAT', compact('data'))->with('branch_id', $request->branch);
    }









    public function search_purchasereports(Request $request)
    {
        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->productname;
        if ($supplierId == '-') {
            $products = orderDetails::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            //return $Invoices;

            return view('reports.purchasereports', compact('products'))->with('supplierId', $supplierId);
        }
        $products = orderDetails::where('product_id', $supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        //return $Invoices;
        return view('reports.purchasereports', compact('products'))->with('supplierId', $supplierId);
    }




    public function search_customerـpurchases(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch == '-' && $request->UserId == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        } elseif ($request->branch == '-' && $request->UserId != '-') {
            $Invoices = invoices::where('customer_id', $request->UserId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        } elseif ($request->branch != '-' && $request->UserId == '-') {
            $Invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        } else {
            $Invoices = invoices::where('customer_id', $request->UserId)->where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        }
        //return $products;
        return view('reports.customerpurchases', compact('Invoices'))->with('branch', [$request->UserId, $request->branch])->with('userid', [$request->UserId, $request->branch]);
    }
    public function searchpurchasproducttocustomer(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $Saleing =[];
            $Invoices = invoices::where('customer_id',$request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
       foreach( $Invoices as $invoiceId){
        $result = sales::where('invoice_id', $invoiceId->id)->where('product_id', $request->productNo)->where('quantity', ">", 0)->where('save', 1)->first();
if($result!=[]){
    $Saleing[]=$result;
}
       }
        return view('reports.purchasproducttocustomer', compact('Saleing'));
    }



    public function search_Refundـofـresourceـpurchases(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch != '-') {
            $Invoices = resource_purchases::where('branchs_id', $request->branch)->where('recoveredـpieces', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
                    return view('reports.print_Refund_of_resource_purchases', compact('Invoices'))->with('branch_id', $request->branch);;
        } else {
            $Invoices = resource_purchases::where('recoveredـpieces', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
                    return view('reports.print_Refund_of_resource_purchases', compact('Invoices'))->with('branch_id', $request->branch);;

        }
        //return $Invoices;



    }








    public function search_budgetsheet(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $salesdebit = 0;
        $creadit = 0;
        $cash = 0;
        $shabka = 0;
        $purchese_debit = 0;
        $purchese_debit_paid = 0;
        $customersdebit = customers::where('Balance', '!=', 0)->get();
        $shippingandunloadingCost = 0;
        $cash_bank = 0;
        $depaid = 0;


        foreach ($customersdebit as $customer) {

            $salesdebit += $customer->Balance;
        }

        //return $month;
        $start_at = $request->start_at;
        $end_at = $request->end_at;
        $cash_last_month = $this->addetions($request);

        $Transfer_cash_to_the_next_day = 0;
        $Transfer_cash_from_the_last_day = 0;
        $totalconvertlastDay = 0;

        $returnsalescash = 0;
        $returnsalescredit = 0;
        $returnsalesshabka = 0;
        $returnSalespartial = 0;
        $returnSalesBankTransfer = 0;
        $returnsalespartialshabka = 0;


        $returnpurchasecash = 0;
        $returnpurchasecredit = 0;
        $returnpurchaseshabka = 0;
        $returnpurchasebanktransfer = 0;


        $salescash = 0;
        $salescredit = 0;
        $salesshabka = 0;
        $salesBankTransfer = 0;


        $purchesecash = 0;
        $purchesecredit = 0;
        $purcheseshabka = 0;
        $purchasebankTransfer = 0;

        $credittransaction_cash = 0;
        $credittransaction_shabka = 0;
        $credittransaction_banktransfer = 0;


        $transactiontosuplliers_cash = 0;
        $transactiontosuplliers_shabka = 0;
        $transactiontosuplliers_banktransfer = 0;

        $expenses_cash = 0;
        $expenses_shabka = 0;
        $expenses_banktransfer = 0;

        $expense_shabka=0;
        $expense_banktransfer=0;
        $expense_cash=0;


        $benfitcradit = 0;
        $benfitshabka = 0;
        $benfitcash = 0;
        $benfitBank_transfer = 0;

        $dorgcash = 0;
        $convertcashboxToBankitemamount = 0;

        $bank_cash = 0;
        $bank_shabka = 0;
        $totaltransferlastdayCash = 0;
        $totaltransferlastdaybank = 0;
        $Cash_withdrawal_from_the_banktotal = 0;

        $Transfer_bankblance_to_the_next_day = 0;
        $Transfer_bankblance_to_the_lastday_day = 0;

        $Invoices = [];
        $pirchese = [];
        $credittransactions = [];
        $transactiontosuplliers = [];
        $expenses = [];
        $returnsales = [];
        $returnpurchases = [];
        $bankblance = [];
        $transferMoney_to_mainbranch = [];
        $transferMoney_to_mainbranchCash = [];
        $transferMoney_to_mainbranchshabka = [];
        $transferMoney_to_mainbranchshabkafrombranchas = [];
        $Transfer_cash_to_the_next_dayList = [];
        $Transfer_cash_from_the_last_dayList = [];
        $convertcashboxToBank = [];
        $totalconvertlastDayList = [];
        $titalnextdayesall = [];
        $branchname = __('users.allbranchs');
        if ($request->branch == '-') {
            $convertcashboxToBank = convertcashboxToBank::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $returnsales = return_sales::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $returnpurchases = resource_purchases::where('recoveredـpieces', '!=', 0)->whereDate('updated_at', '>=', $start_at)->whereDate('updated_at', '<=', $end_at)->where('save', 1)->get();
            $Invoices = invoices::where('save', 1)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $pirchese = resource_purchases::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $credittransactions = credittransactions::where('note', 'LIKE', '%' . 'سند قبض' . '%')->whereDate('created_at', '>=', $request->start_at)->where('decument_id', 0)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiontosuplliers = credittransactions::where('type_decument', 2)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiont_dely_record = credittransactions::where('customer_id', 4)->where('dely_record','!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->orwhere('customer_id', 5)->where('dely_record','!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiont_dely_record_bank = credittransactions::where('customer_id', 4)->where('note', 'LIKE', '%' . 'قيد يومي ' . '%')->where('dely_record', 0)->where('parent_dely_record','!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiont_dely_record_cash = credittransactions::where('customer_id', 5)->where('note', 'LIKE', '%' . 'قيد يومي ' . '%')->where('dely_record', 0)->where('parent_dely_record','!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            $bankblance = cash_from__bank::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $Cash_withdrawal_from_the_bank = Cash_withdrawal_from_the_bank::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $Transfer_cash_to_the_next_dayList = Transfer_cash_to_the_next_day::whereDate('created_at', $end_at)->get();
            $start_atlastday = date("Y-m-d", strtotime('-24 hours', strtotime($start_at)));

            $titalnextdayesall = Transfer_cash_to_the_next_day::whereDate('created_at', '>=', $start_atlastday)->whereDate('created_at', '<=', $end_at)->get();
            $date = date("Y-m-d", strtotime('-24 hours', strtotime($end_at)));
            // $totalconvertlastDayList = Transfer_cash_to_the_next_day::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $totalconvertlastDayList = Transfer_cash_to_the_next_day::whereDate('created_at', $end_at)->get();
            $Transfer_cash_from_the_last_dayList = Transfer_cash_to_the_next_day::whereDate('created_at', $date)->get();
            $transferMoney_to_mainbranch = [];
        } else {
            $branchname = branchs::find($request->branch);
            $branchname = $branchname->name;
            $convertcashboxToBank = convertcashboxToBank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $returnsales = return_sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $returnpurchases = resource_purchases::where('branchs_id', $request->branch)->where('recoveredـpieces', '!=', 0)->whereDate('updated_at', '>=', $start_at)->whereDate('updated_at', '<=', $end_at)->where('save', 1)->get();
            $Invoices = invoices::where('save', 1)->where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $pirchese = resource_purchases::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $credittransactions = credittransactions::where('branchs_id', $request->branch)->where('note', 'LIKE', '%' . 'سند قبض' . '%')->whereDate('created_at', '>=', $request->start_at)->where('decument_id', 0)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiontosuplliers = credittransactions::where('branchs_id', $request->branch)->where('type_decument', 2)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiont_dely_record_bank = credittransactions::where('branchs_id', $request->branch)->where('customer_id', 4)->where('parent_dely_record','!=', 0)->where('note', 'LIKE', '%' . 'قيد يومي ' . '%')->where('dely_record', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            $transactiont_dely_record_cash = credittransactions::where('branchs_id', $request->branch)->where('customer_id', 5)->where('parent_dely_record','!=', 0)->where('note', 'LIKE', '%' . 'قيد يومي ' . '%')->where('dely_record', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            
            
            
            $expenses = expenses::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $bankblance = cash_from__bank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            // $Transfer_cash_to_the_next_dayList = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', $end_at)->get();
            $Cash_withdrawal_from_the_bank = Cash_withdrawal_from_the_bank::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $start_atlastday = date("Y-m-d", strtotime('-24 hours', strtotime($start_at)));

            $titalnextdayesall = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_atlastday)->whereDate('created_at', '<=', $end_at)->get();

            $Transfer_cash_to_the_next_dayList = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', $end_at)->get();
            $date = date("Y-m-d", strtotime('-24 hours', strtotime($end_at)));
            $Transfer_cash_from_the_last_dayList = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', $date)->get();
            $totalconvertlastDayList = Transfer_cash_to_the_next_day::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();

            if ($request->branch == 1) {
                $transferMoney_to_mainbranch = transferMoney_to_mainbranch::whereDate('updated_at', '>=', $start_at)->whereDate('updated_at', '<=', $end_at)->where('status', 1)->get();
            }
        }
        // return $transactiont_dely_record_cash;
        if ($totalconvertlastDayList != []) {
            foreach ($totalconvertlastDayList as $item) {
                $totalconvertlastDay += $item->amount;
            }
        }
        if ($Transfer_cash_to_the_next_dayList != []) {
            foreach ($Transfer_cash_to_the_next_dayList as $item) {
                $Transfer_cash_to_the_next_day += $item->amount;
                $Transfer_bankblance_to_the_next_day += $item->currentamount;
            }
        }
        if ($Transfer_cash_from_the_last_dayList != []) {
            foreach ($Transfer_cash_from_the_last_dayList as $item) {
                $Transfer_cash_from_the_last_day += $item->amount;
                $Transfer_bankblance_to_the_lastday_day += $item->currentamount;
            }
        }
        if ($titalnextdayesall != []) {
            foreach ($titalnextdayesall as $item) {
                $totaltransferlastdayCash += $item->amount;
                $totaltransferlastdaybank += $item->currentamount;
            }
        }
        if ($transferMoney_to_mainbranch != []) {
            foreach ($transferMoney_to_mainbranch as $transferMoney_to_main) {
                // return $transferMoney_to_main->branchs_id;

                if (!in_array($transferMoney_to_main->branchs_id, $transferMoney_to_mainbranchshabkafrombranchas)) {
                    $transferMoney_to_mainbranchshabkafrombranchas[] = $transferMoney_to_main->branchs_id;
                }

                $transferMoney_to_mainbranchCash[] = $transferMoney_to_main;

                $transferMoney_to_mainbranchshabka[] = $transferMoney_to_main;
            }
        }

        if ($convertcashboxToBank != []) {
            foreach ($convertcashboxToBank as $convertcashboxToBankitem) {
                // return $transferMoney_to_main->branchs_id;

                $convertcashboxToBankitemamount += $convertcashboxToBankitem->amount;
            }
        }

        if ($Cash_withdrawal_from_the_bank != []) {
            foreach ($Cash_withdrawal_from_the_bank as $convertcashboxToBankitem) {
                // return $transferMoney_to_main->branchs_id;

                $Cash_withdrawal_from_the_banktotal += $convertcashboxToBankitem->amount;
            }
        }
        $avt = Avt::find(1);
        $saleavt = $avt->AVT;
        $invoiceId = 0;
        if ($returnsales != []) {

            $valuewithoudtax = 0;
            $discountoninvoice = 0;
            $invoicesId = [];
            $i = 0;
            foreach ($returnsales as $returnsale) {
                if (!in_array($returnsale->invoice_id, $invoicesId)) {
                    $invoicesId[] = $returnsale->invoice_id;
                }
            }

            foreach ($invoicesId as $id) {
                $cominvoice = invoices::find($id);
                if ($cominvoice->payment_return == 'Cash') {

                    foreach (return_sales::where('invoice_id', $id)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get() as $returnsale) {
                        $valuewithoudtax += ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                    }
                    $returnsalescash += ($valuewithoudtax)  + (($valuewithoudtax) * $saleavt);
                    $valuewithoudtax = 0;
                } elseif ($cominvoice->payment_return == 'Credit') {

                    foreach (return_sales::where('invoice_id', $id)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get() as $returnsale) {
                        $valuewithoudtax += ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                    }
                    $returnsalescredit += ($valuewithoudtax)  + (($valuewithoudtax) * $saleavt);
                    $valuewithoudtax = 0;
                } elseif ($cominvoice->payment_return == 'Shabka') {

                    foreach (return_sales::where('invoice_id', $id)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get() as $returnsale) {
                        $valuewithoudtax += ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                    }
                    $returnsalesshabka += ($valuewithoudtax)  + (($valuewithoudtax) * $saleavt);
                    $valuewithoudtax = 0;
                } elseif ($cominvoice->Pay == 'Bank_transfer') {

                    foreach (return_sales::where('invoice_id', $id)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get() as $returnsale) {
                        $valuewithoudtax += ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;
                    }
                    $returnSalesBankTransfer += ($valuewithoudtax)  + (($valuewithoudtax) * $saleavt);
                    $valuewithoudtax = 0;
                } else {
                    foreach (return_sales::where('invoice_id', $id)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get() as $returnsale) {
                        $valuewithoudtax += ($returnsale->return_Unit_Price * $returnsale->return_quantity) - $returnsale->discountvalue - $returnsale->discountoninvoice;

                        $returnsalespartialshabka += $returnsale->returnshabkavalue;
                    }
                    $returnSalespartial += ($valuewithoudtax)  + (($valuewithoudtax) * $saleavt);
                    $valuewithoudtax = 0;
                }
            }
        }




        if ($returnpurchases != []) {
  
            foreach ($returnpurchases as $returnpurchase) {
                    $avt_rat=0;
        $temp=0;
                if ($returnpurchase->Pay_Method_Name == 'Cash') {
                    $allreturn = 1;
              
                    $returnpurchasesdetiales = orderDetails::where('order_owner', $returnpurchase->orderId)->where('returns_purchase', '!=', 0)->get();
                    foreach ($returnpurchasesdetiales as $returnpurchasesdetiale) {
$TEMP=$returnpurchasesdetiale->purchasingـprice==0?1:$returnpurchasesdetiale->purchasingـprice;
                            $avt_rat= $returnpurchasesdetiale->Added_Value/$TEMP;
                            $temp = $temp +($returnpurchasesdetiale->returns_purchase * $returnpurchasesdetiale->purchasingـprice);
                        if ($returnpurchasesdetiale->numberofpice != 0) {
                            $allreturn = 0;
                        }
                    }
                    if ($allreturn == 1) {
                        $resource_purchases1 =  resource_purchases::where('orderId', $returnpurchase->orderId)->first();
                        $returnpurchasecash +=($temp-$resource_purchases1->discount)+(($temp-$resource_purchases1->discount)*$avt_rat);

                    }else{
                        $returnpurchasecash +=($temp)+($temp*$avt_rat);
                    }
                } elseif ($returnpurchase->Pay_Method_Name == 'Credit') {
                    $allreturn = 1;

                    $returnpurchasesdetiales = orderDetails::where('order_owner', $returnpurchase->orderId)->where('returns_purchase', '!=', 0)->get();
                    foreach ($returnpurchasesdetiales as $returnpurchasesdetiale) {

                             $avt_rat= $returnpurchasesdetiale->Added_Value/$returnpurchasesdetiale->purchasingـprice;
                            $temp = $temp +($returnpurchasesdetiale->returns_purchase * $returnpurchasesdetiale->purchasingـprice);
                            if ($returnpurchasesdetiale->numberofpice != 0) {
                            $allreturn = 0;
                        }
                      
                    }
                
                      if ($allreturn == 1) {
                        $resource_purchases1 =  resource_purchases::where('orderId', $returnpurchase->orderId)->first();
                        $returnpurchasecredit +=($temp-$resource_purchases1->discount)+(($temp-$resource_purchases1->discount)*$avt_rat);

                    }else{
                        $returnpurchasecredit +=($temp)+($temp*$avt_rat);
                    }
                } elseif ($returnpurchase->Pay_Method_Name == 'Bank_transfer') {
                    $allreturn = 1;

                    $returnpurchasesdetiales = orderDetails::where('order_owner', $returnpurchase->orderId)->where('returns_purchase', '!=', 0)->get();
                    foreach ($returnpurchasesdetiales as $returnpurchasesdetiale) {

                            $avt_rat= $returnpurchasesdetiale->Added_Value/$returnpurchasesdetiale->purchasingـprice;
                            $temp = $temp +($returnpurchasesdetiale->returns_purchase * $returnpurchasesdetiale->purchasingـprice);
                            if ($returnpurchasesdetiale->numberofpice != 0) {
                            $allreturn = 0;
                        }
                    }
                   if ($allreturn == 1) {
                        $resource_purchases1 =  resource_purchases::where('orderId', $returnpurchase->orderId)->first();
                        $returnpurchasebanktransfer +=($temp-$resource_purchases1->discount)+(($temp-$resource_purchases1->discount)*$avt_rat);

                    }else{
                        $returnpurchasebanktransfer +=($temp)+($temp*$avt_rat);
                    }
                } else {
                    $allreturn = 1;

                    $returnpurchasesdetiales = orderDetails::where('order_owner', $returnpurchase->orderId)->where('returns_purchase', '!=', 0)->get();
                    foreach ($returnpurchasesdetiales as $returnpurchasesdetiale) {
                            $avt_rat= $returnpurchasesdetiale->Added_Value/$returnpurchasesdetiale->purchasingـprice;
                            $temp = $temp +($returnpurchasesdetiale->returns_purchase * $returnpurchasesdetiale->purchasingـprice);
                        if ($returnpurchasesdetiale->numberofpice != 0) {
                            $allreturn = 0;
                        }
                    }
                 if ($allreturn == 1) {
                        $resource_purchases1 =  resource_purchases::where('orderId', $returnpurchase->orderId)->first();
                        $returnpurchaseshabka +=($temp-$resource_purchases1->discount)+(($temp-$resource_purchases1->discount)*$avt_rat);

                    }else{
                        $returnpurchaseshabka +=($temp)+($temp*$avt_rat);
                    }
                }
            }

}

            if ($Invoices != []) {

                //return $Invoices;
                foreach ($Invoices as $invoice) {
                        $discount_invoiceby_item=$invoice->Number_of_Quantity==0?0:round(($invoice->discount-$invoice->discountOnProduct)/$invoice->Number_of_Quantity,2);
                               
                    if ($invoice->Pay == 'Cash') {

                        foreach (sales::where('invoice_id', $invoice->id)->where('save', 1)->get() as $salesbenfit) {

                            $benfitcash += (($salesbenfit->Unit_Price * $salesbenfit->quantity)) - ($salesbenfit->productData->purchasingـprice * $salesbenfit->quantity);
                        }
                        $benfitcash-=$invoice->discount;
                    } elseif ($invoice->Pay == 'Credit') {
                        foreach (sales::where('invoice_id', $invoice->id)->where('save', 1)->get() as $salesbenfit) {

                            $benfitcradit += (($salesbenfit->Unit_Price * $salesbenfit->quantity)) - ($salesbenfit->productData->purchasingـprice * $salesbenfit->quantity);
                        }
                        $benfitcradit -= $invoice->discount;
                    } elseif ($invoice->Pay == 'Bank_transfer') {
                        foreach (sales::where('invoice_id', $invoice->id)->where('save', 1)->get() as $salesbenfit) {

                            $benfitBank_transfer += (($salesbenfit->Unit_Price * $salesbenfit->quantity)) - ($salesbenfit->productData->purchasingـprice * $salesbenfit->quantity);
                        }
                        $benfitBank_transfer-=$invoice->discount;
                    } elseif ($invoice->Pay == 'Shabka') {
                        foreach (sales::where('invoice_id', $invoice->id)->where('save', 1)->get() as $salesbenfit) {

                            $benfitshabka += (($salesbenfit->Unit_Price * $salesbenfit->quantity)) - ($salesbenfit->productData->purchasingـprice * $salesbenfit->quantity);
                        }
                        $benfitshabka-=$invoice->discount;
                    } else {

                        $titalbenfitmix = 0;
                        $ratecash = 0;
                        $rateshabka = 0;
                        $ratecredit = 0;
                        foreach (sales::where('invoice_id', $invoice->id)->where('save', 1)->get() as $salesbenfit) {

                            $titalbenfitmix += (($salesbenfit->Unit_Price * $salesbenfit->quantity)) - ($salesbenfit->productData->purchasingـprice * $salesbenfit->quantity);
                        }
                        $titalbenfitmix -= $invoice->discount;
                        $ratecash = ($invoice->cashamount / ($invoice->cashamount + $invoice->bankamount + $invoice->creaditamount));
                        $rateshabka = ($invoice->bankamount / ($invoice->cashamount + $invoice->bankamount + $invoice->creaditamount));
                        $ratecredit = ($invoice->creaditamount / ($invoice->cashamount + $invoice->bankamount + $invoice->creaditamount));
                        $benfitshabka += $titalbenfitmix * $rateshabka;
                        $benfitcradit += $titalbenfitmix * $ratecredit;
                        $benfitcash += $titalbenfitmix * $ratecash;
                        //    return $ratecash;
                        //  return $titalbenfitmix*$ratecash;


                    }
                    $salescash += $invoice->cashamount;
                    $salesshabka += $invoice->bankamount;
                    $salescredit += $invoice->creaditamount;
                    $salesBankTransfer += $invoice->Bank_transfer;
                }
                // return $salesshabka;
                foreach ($pirchese as $purchese) {
                    $shippingandunloadingCost += $purchese['shipping fee'] + $purchese['Other expenses'];

$avt_rat=0;
$temp=0;
                    if ($purchese->Pay_Method_Name == 'Cash') {
                        $numberofpice = 0;
                        foreach (orderDetails::where('order_owner', $purchese->orderId)->get() as $item) {
                            $numberofpice = $item->numberofpice + $item->returns_purchase;
                            $TEMP=$item->purchasingـprice==0?1:$item->purchasingـprice;
                            $avt_rat= $item->Added_Value/$TEMP;
                            $temp = $temp + ($numberofpice * $item->purchasingـprice);
                        }

                        $temp=($temp-$purchese->discount);
                        $purchesecash =$purchesecash +($temp)+($temp*$avt_rat);
                        
                    } elseif ($purchese->Pay_Method_Name == 'Credit') {

                        $numberofpice = 0;
                        foreach (orderDetails::where('order_owner', $purchese->orderId)->get() as $item) {
                            $numberofpice = $item->numberofpice + $item->returns_purchase;

                             $avt_rat= $item->Added_Value/$item->purchasingـprice;
                            $temp = $temp + ($numberofpice * $item->purchasingـprice);
                        }
                        $temp=($temp-$purchese->discount);
                        $purchesecredit =$purchesecredit+($temp)+($temp*$avt_rat);

                    } elseif ($purchese->Pay_Method_Name == 'Bank_transfer') {

                        $numberofpice = 0;
                        foreach (orderDetails::where('order_owner', $purchese->orderId)->get() as $item) {
                            $numberofpice = $item->numberofpice + $item->returns_purchase;

                          $avt_rat= $item->Added_Value/$item->purchasingـprice;
                                                        $temp = $temp + ($numberofpice * $item->purchasingـprice);
                        }
                        $temp=($temp-$purchese->discount);

                        $purchasebankTransfer = $purchasebankTransfer+($temp)+($temp*$avt_rat);
                    } else {

                        $numberofpice = 0;
                        foreach (orderDetails::where('order_owner', $purchese->orderId)->get() as $item) {
                            $numberofpice = $item->numberofpice + $item->returns_purchase;

                          $avt_rat= $item->Added_Value/$item->purchasingـprice;
                            $temp = $temp + ($numberofpice * $item->purchasingـprice);
                        }


                                                $temp=($temp-$purchese->discount);

                        $purcheseshabka =$purcheseshabka +($temp)+($temp*$avt_rat);

                    }
                }
            }
            if ($bankblance != []) {
                foreach ($bankblance as $banktan) {

                    if ($banktan->payment_method == 'Cash') {
                        $bank_cash += $banktan->the_amount;
                    } else {
                        $bank_shabka += $banktan->the_amount;
                    }
                }
            }
                   foreach ($transactiont_dely_record_cash as $credittransaction) {

                    if ($credittransaction->debtor    >0) {
                        $credittransaction_cash += $credittransaction->debtor;
                    } else {
                        $transactiontosuplliers_cash += $credittransaction->creditor;
                    }
                }
        foreach ($transactiont_dely_record_bank as $credittransaction) {

                    if ($credittransaction->debtor    >0) {
                        $credittransaction_banktransfer += $credittransaction->debtor;
                    } else {
                        $transactiontosuplliers_banktransfer += $credittransaction->creditor;
                    }
                }
            if ($credittransactions != []) {

                foreach ($credittransactions as $credittransaction) {

                    if ($credittransaction->type    == 'Cash') {
                        $credittransaction_cash += $credittransaction->recive_amount;
                    } elseif ($credittransaction->type    == 'Bank_transfer') {
                        $credittransaction_banktransfer += $credittransaction->recive_amount;
                    } else {
                        $credittransaction_shabka += $credittransaction->recive_amount;
                    }
                }

                foreach ($transactiontosuplliers as $transactiontosupllier) {
if($transactiontosupllier->orginal_type!=3){
              if ($transactiontosupllier->type == 'Cash') {
                        $transactiontosuplliers_cash += $transactiontosupllier->recive_amount;
                    } elseif ($transactiontosupllier->type == 'Bank_transfer') {
                        $transactiontosuplliers_banktransfer += $transactiontosupllier->recive_amount;
                    } else {
                        $transactiontosuplliers_shabka += $transactiontosupllier->recive_amount;
                    } 
}else{
              if ($transactiontosupllier->type == 'Cash') {
                        $expense_cash += $transactiontosupllier->recive_amount;
                    } elseif ($transactiontosupllier->type == 'Bank_transfer') {
                        $expense_banktransfer += $transactiontosupllier->recive_amount;
                    } else {
                        $expense_shabka += $transactiontosupllier->recive_amount;
                    } 
    
    
}
         
                }
            }

     
        
                $creadit_customers = customers::where('Balance', '!=', 0)->get();
                $credit_suppliers = supllier::where('In_debt', '!=', 0)->get();
                $creadit_customer_amount = 0;
                $credit_supplier_amount = 0;

                foreach ($creadit_customers as $creadit_customer) {
                    $creadit_customer_amount +=  $creadit_customer->Balance;
                }

                foreach ($credit_suppliers as $credit_supplier) {
                    $credit_supplier_amount +=  $credit_supplier->In_debt;
                }
                // return  $credit_supplier_amount;
            
            $data = [
                'totaltransferlastdaybank' => $totaltransferlastdaybank,
                'totaltransferlastdayCash' => $totaltransferlastdayCash,
                'transferMoney_to_mainbranchshabka' => $transferMoney_to_mainbranchshabka,
                'transferMoney_to_mainbranchCash' => $transferMoney_to_mainbranchCash,
                'transferMoney_to_mainbranchshabkafrombranchas' => $transferMoney_to_mainbranchshabkafrombranchas,
                'reportforbranch' => $request->branch,

                'returnsalescash' => round($returnsalescash, 1),
                'returnsalescredit' => round($returnsalescredit, 2),
                'returnsalesshabka' => round($returnsalesshabka, 2),
                'returnSalespartial' => round($returnSalespartial, 2),
                'returnsalespartialshabka' => round($returnsalespartialshabka, 2),
                'returnSalesBankTransfer' => round($returnSalesBankTransfer, 2),

                'Cash_withdrawal_from_the_banktotal' => $Cash_withdrawal_from_the_banktotal,

                'returnpurchasecash' => round($returnpurchasecash, 2),
                'returnpurchasecredit' => round($returnpurchasecredit, 2),
                'returnpurchaseshabka' => round($returnpurchaseshabka, 2),
                'returnpurchasebanktransfer' => round($returnpurchasebanktransfer, 2),

                'Transfer_cash_to_the_next_day' => $Transfer_cash_to_the_next_day,
                'Transfer_cash_from_the_last_day' => $Transfer_cash_from_the_last_day,
                'totalconvertlastDay' => $totalconvertlastDay,

                'Transfer_bankblance_to_the_next_day' => $Transfer_bankblance_to_the_next_day,
                'Transfer_bankblance_to_the_lastday_day' => $Transfer_bankblance_to_the_lastday_day,

                 'expense_shabka'=>$expense_shabka,
                 'expense_banktransfer'=>$expense_banktransfer,
                 'expense_cash'=>$expense_cash,

                'shippingandunloadingCost' => round($shippingandunloadingCost, 2),
                'salescash' => round($salescash, 2),
                'salescredit' => round($salescredit, 2),
                'salesshabka' => round($salesshabka, 2),
                'salesBankTransfer' => round($salesBankTransfer, 2),

                'purchesecash' => round($purchesecash, 2),
                'purchesecredit' => round($purchesecredit, 2),
                'purcheseshabka' => round($purcheseshabka, 2),
                'purchasebankTransfer' => round($purchasebankTransfer, 2),

                'credittransaction_cash' => round($credittransaction_cash, 2),
                'credittransaction_shabka' => round($credittransaction_shabka, 2),
                'credittransaction_banktransfer' => round($credittransaction_banktransfer, 2),

                'transactiontosuplliers_cash' => round($transactiontosuplliers_cash, 2),
                'transactiontosuplliers_shabka' => round($transactiontosuplliers_shabka, 2),
                'transactiontosuplliers_banktransfer' => round($transactiontosuplliers_banktransfer, 2),

           
                'cash_last_month' => round($cash_last_month, 2),
                'creadit_customer_amount' => round($creadit_customer_amount, 2),
                'credit_supplier_amount' => round($credit_supplier_amount, 2),
                "start_at" => $start_at,
                "end_at" => $end_at,
                'bank_cash' => $bank_cash,
                'bank_shabka' => $bank_shabka,
                'branch' => $branchname,

                'benfitcradit' => $benfitcradit,
                'benfitshabka' => $benfitshabka,
                'benfitcash' => $benfitcash,
                'benfitBank_transfer' => $benfitBank_transfer,
                'convertcashboxToBankitemamount' => $convertcashboxToBankitemamount
            ];

            return view('reports.print_budget_sheet', compact('data'));
        
    }

    public function print_Transfer_products($invoiceId)
    {
        $product_movement_another_branch_data = product_movement_another_branch::find($invoiceId);
        $items = product_movement_another_branch_items::where('order_id', $invoiceId)->get();
        $data = [
            "invoice" => $product_movement_another_branch_data,
            "itemsdetails" => $items
        ];
        return view('supProcesses.print_send_product', compact('data'));
    }

    //adition AboFhad

    public function addetions($request)
    {


        $start_at = $request->start_at;
        $end_at = $request->end_at;

        $salescash = 0;


        $purchesecash = 0;


        $credittransaction_cash = 0;


        $transactiontosuplliers_cash = 0;

        $expenses_cash = 0;


        $dorgcash = 0;

        $Invoices = [];
        $pirchese = [];
        $credittransactions = [];
        $transactiontosuplliers = [];
        $expenses = [];
        if ($request->branch == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $pirchese = resource_purchases::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $credittransactions = credittransactions::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $transactiontosuplliers = transactiontosuplliers::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $expenses = expenses::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
        } else {
            $Invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $pirchese = resource_purchases::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $credittransactions = credittransactions::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $transactiontosuplliers = transactiontosuplliers::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            $expenses = expenses::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
        }
        //return $Invoices;
        foreach ($Invoices as $invoice) {
            if ($invoice->Pay == 'Cash') {
                $salescash += $invoice->Price + $invoice->Added_Value;
            }
        }

        foreach ($pirchese as $purchese) {


            if ($purchese->Pay_Method_Name == 'Cash') {
                $purchesecash += $purchese->In_debt;
            }
        }

        foreach ($credittransactions as $credittransaction) {

            if ($credittransaction->pay_method    == 'Cash') {
                $credittransaction_cash += $credittransaction->recive_amount;
            }
        }

        foreach ($transactiontosuplliers as $transactiontosupllier) {

            if ($transactiontosupllier->Pay_Method_Name == 'Cash') {
                $transactiontosuplliers_cash += $transactiontosupllier->paidـamount;
            }
        }


        foreach ($expenses as $expense) {

            if ($expense->Pay_Method_Name == 'Cash') {
                $expenses_cash += $expense->Theـamountـpaid;
            }
        }
        $total_cash_dorg = ($salescash + $credittransaction_cash) - ($expenses_cash + $transactiontosuplliers_cash  + $purchesecash);

        return $total_cash_dorg;
    }



    //end addetion








    public function search_Purchasesـfromـsuppliers(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $pay = $request->pay;
        $branch = $request->branch;

        if ($request->clientnamesearch == '-') {


            if ($pay == '-' && $branch == '-') {
                $Invoices = resource_purchases::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            } elseif ($pay != '-' && $branch == '-') {
                $Invoices = resource_purchases::where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            } elseif ($pay == '-' && $branch != '-') {
                $Invoices = resource_purchases::where('branchs_id', $branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            }
            $Invoices = resource_purchases::where('branchs_id', $branch)->where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
        } else {

            if ($pay == '-' && $branch == '-') {
                $Invoices = resource_purchases::where('suplier_id', $request->clientnamesearch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            } elseif ($pay != '-' && $branch == '-') {
                $Invoices = resource_purchases::where('suplier_id', $request->clientnamesearch)->where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            } elseif ($pay == '-' && $branch != '-') {
                $Invoices = resource_purchases::where('suplier_id', $request->clientnamesearch)->where('branchs_id', $branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
            }
            $Invoices = resource_purchases::where('suplier_id', $request->clientnamesearch)->where('branchs_id', $branch)->where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();


        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);

            // return view('reports.Purchases_from_suppliers', compact('Invoices'))->with('pay', [$pay, $branch, $request->clientnamesearch]);
        }
    }






    public function search_Requestـaـquoteـfromـtheـsupplier(Request $request)
    {
        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->supplierId;
        if ($supplierId == '-' && $request->branch == '-') {
            $Invoices = order_price_from_supplier::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;

        } elseif ($supplierId != '-' && $request->branch == '-') {
            $Invoices = order_price_from_supplier::where('suplier_id', $supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } elseif ($supplierId == '-' && $request->branch != '-') {
            $Invoices = order_price_from_supplier::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } else {
            $Invoices = order_price_from_supplier::where('suplier_id', $supplierId)->where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }
        //return $Invoices;
        return view('reports.Request_A_quote_from_supplier', compact('Invoices'))->with('supplierId', [$supplierId, $request->branch]);
    }




    public function search_Requestـoffersـfromـsuppliers(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $request->supplierId;
        if ($supplierId == '-') {
            $Invoices = orderTosupllier::where('Limit_credit', '')->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
            //return $Invoices;        
            return view('reports.Request_offers_from_suppliers', compact('Invoices'))->with('supplierId', $supplierId);
        }
        $Invoices = orderTosupllier::where('Limit_credit', '')->where('suplier_id', $request->supplierId)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        //return $Invoices;        

        return view('reports.Request_offers_from_suppliers', compact('Invoices'))->with('supplierId', $supplierId);
    }



    public function search_product_sales(Request $request)
    {
        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $productId = $request->productNo ?? '-';
        if ($productId == '-') {
            session()->flash('notfountreturnproduct', __('home.productnotfount'));
            $Invoices = [];
            return view('reports.product_sales', compact('Invoices'))->with('branch_Id', $request->branch);
        } else {
            if ($request->branch == '-' && $productId == '-') {
                $products = sales::where('product_id', $productId)->where('quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            } elseif ($request->branch == '-' && $productId != '-') {
                $products = sales::where('product_id', $productId)->where('quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            } elseif ($request->branch != '-' && $productId == '-') {
                $products = sales::where('branch_id', $request->branch)->where('quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            } else {
                $products = sales::where('product_id', $productId)->where('branch_id', $request->branch)->where('quantity', '!=', 0)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
            }
        }


        //return $products;
        return view('reports.product_sales', compact('products'))->with('branch_Id', $request->branch);
    }


    public function viewnetworksales(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //  return $request;

        $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay', 'SHABKA')->get();

        //return $Invoices;
        return view('reports.networksales', compact('Invoices'));
    }

    public function employeeSalesSearch(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //  return $request;

        $Invoices = invoices::where('user_id', $request->productname)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();

        //return $Invoices;
        return view('reports.employeesales', compact('Invoices'));
    }
    public function viewCashsales(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay', 'Cash')->get();


        return view('reports.Cashsales', compact('Invoices'));
    }


    public function search_report_returns_sale(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->branch == '-') {
            $Invoices = return_sales::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } else {
            $Invoices = return_sales::where('branch_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }

        //return $Invoices;
        return view('reports.report_returns_sale', compact('Invoices'))->with('branch_Id', $request->branch)->with('start',$request->start_at)->with('end', $request->end_at);
    }
    public function Show_return_Sales_Details($request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $return_sales = return_sales::where('invoice_id', $request)->get();
        $InvoiceSale = invoices::where('id', $request)->first();
        $data = [
            'invoiceData' => $InvoiceSale,
            'salesData' => $return_sales
        ];

        //return $data;
        return view('reports.report_returns_sale_details', compact('data'));
    }




    
    
    
    public function salesـprofitssearch(Request $request)
    {
       app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ( $request->branch == '-') {

            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        }  else {
            $Invoices = invoices::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        }
        
$data=[
    'sales'=>$Invoices,
    ];
        return view('reports.printReportProfitSales', compact('data'))->with('start', $request->start_at)->with('end', $request->end_at)->with('branch_id', $request->branch);
    }


    public function viewCreditsales(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('Pay', 'Credit')->get();
        return view('reports.Creditsales', compact('Invoices'));
    }


    public function print_Supplier_credit_payment($supplierId, $startat, $end_at)
    {
        //return $request;
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($supplierId == '-') {
            $Invoices = transactiontosuplliers::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;
            return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $supplierId);
        }

        //return $Invoices;
        $Invoices = transactiontosuplliers::where('orginal_id', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        //return $Invoices;
        return view('reports.print_Supplier_credit_payment', compact('Invoices'))->with('supplierId', $supplierId);
    }



    public function print_shift_detailes($branch, $pay, $startat, $end_at)
    {
        // return $request;
        $start_at = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branch == '-' && $pay == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

            return view('reports.print_shift_detailes', compact('Invoices'));
        }
        if ($branch == '-' && $pay == '-') {
            $Invoices = invoices::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;       
            return view('reports.print_shift_detailes', compact('Invoices'));
        } elseif ($branch != '-' && $pay == '-') {
            $Invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

            return view('reports.print_shift_detailes', compact('Invoices'))->with('pay', [$pay, $branch]);
        } elseif ($branch == '-' && $pay != '-') {
            $Invoices = invoices::where('Pay', $pay)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

            return view('reports.print_shift_detailes', compact('Invoices'))->with('pay', [$pay, $branch]);
        } else {
            $Invoices = invoices::where('branchs_id', $branch)->where('Pay', $pay)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;
            return view('reports.print_shift_detailes', compact('Invoices'));
        }
    }











    public function printReportemployeeSales($usertId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $Invoices = invoices::where('user_id', $usertId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();



        //  return $usertId;
        return view('reports.print_report_employee_sales', compact('Invoices'));
    }
    public function print_credit_collection($userId, $startat, $end_at)
    {

  
        
        
      
    
        
    }
    public function  print_purchasereports($productId, $startat, $end_at)
    {



        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $supplierId = $productId;
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        if ($supplierId == '-') {
            $products = orderDetails::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

            return view('reports.print_purchasereports', compact('products'));
        }
        $products = orderDetails::where('product_id', $productId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        //return $Invoices;
        return view('reports.print_purchasereports', compact('products'));
    }






    public function print_Purchasesـfromـsuppliers($branch, $pay, $supplierId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $Invoices = [];
        if ($supplierId) {
            if ($pay == '-' && $branch == '-') {
                $Invoices = resource_purchases::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } elseif ($pay != '-' && $branch == '-') {
                $Invoices = resource_purchases::where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } elseif ($pay == '-' && $branch != '-') {
                $Invoices = resource_purchases::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } else {
                $Invoices = resource_purchases::where('Pay_Method_Name', $pay)->where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            }
        } else {
            if ($pay == '-' && $branch == '-') {
                $Invoices = resource_purchases::where('suplier_id', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } elseif ($pay != '-' && $branch == '-') {
                $Invoices = resource_purchases::where('suplier_id', $supplierId)->where('Pay_Method_Name', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } elseif ($pay == '-' && $branch != '-') {
                $Invoices = resource_purchases::where('suplier_id', $supplierId)->where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            } else {
                $Invoices = resource_purchases::where('suplier_id', $supplierId)->where('Pay_Method_Name', $pay)->where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            }
        }



        return view('reports.print_Purchases_from_suppliers', compact('Invoices'))->with('pay', $pay);
    }



    public function print_Refundـofـresourceـpurchases($branch_id, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        if ($branch_id != '-') {
            $Invoices = resource_purchases::where('branchs_id', $branch_id)->where('recoveredـpieces', '!=', 0)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();

            return view('reports.print_Refund_of_resource_purchases', compact('Invoices'));
        }
        $Invoices = resource_purchases::where('recoveredـpieces', '!=', 0)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();

        return view('reports.print_Refund_of_resource_purchases', compact('Invoices'));
    }


    public function printReportoffer_price_customer($userId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        if ($userId == '-') {
            $Invoices = offer_price_to_customer::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=',   $end_at)->get();
            return view('reports.printReportoffer_price_customer', compact('Invoices'));
        }
        $Invoices = offer_price_to_customer::where('customer_id', $userId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=',   $end_at)->get();



        // return $Invoices;
        return view('reports.printReportoffer_price_customer', compact('Invoices'));
    }


    public function print_Requestـaـquoteـfromـtheـsupplier($branch, $supplierId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $supplierId = $supplierId;




        if ($supplierId == '-' && $branch == '-') {
            $Invoices = order_price_from_supplier::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

        } elseif ($supplierId != '-' && $branch == '-') {
            $Invoices = order_price_from_supplier::where('suplier_id', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        } elseif ($supplierId == '-' && $branch != '-') {
            $Invoices = order_price_from_supplier::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        } else {
            $Invoices = order_price_from_supplier::where('branchs_id', $branch)->where('suplier_id', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        }






        return view('reports.print_Request_A_quote_from_supplier', compact('Invoices'));
    }







    public function printDelivery_notes($orderId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $supplierId = $orderId;
        if ($supplierId == '-') {
            $Invoices = resource_purchases::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
            //return $Invoices;

            return view('reports.print_Report_delivery_notes', compact('Invoices'));
        }
        $Invoices = resource_purchases::where('orderId', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        //return $Invoices;
        return view('reports.print_Report_delivery_notes', compact('Invoices'));
    }

    public function print_report_order_from_supplier($supplierId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);

        //


        if ($supplierId == '-') {
            $Invoices = orderTosupllier::where('Limit_credit', '')->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=',   $end_at)->get();
            return view('reports.print_report_order_from_supplier', compact('Invoices'));
        }
        $Invoices = orderTosupllier::where('Limit_credit', '')->where('suplier_id', $supplierId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=',   $end_at)->get();

        //



        // return $Invoices;
        return view('reports.print_report_order_from_supplier', compact('Invoices'));
    }



    public function printReportProfitSales($branch, $UserId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);

        if ($UserId == '-' && $branch == '-') {

            $Invoices = invoices::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } elseif ($UserId != '-' && $branch == '-') {

            $Invoices = invoices::where('customer_id', $UserId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } elseif ($UserId == '-' && $branch != '-') {

            $Invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } else {
            $Invoices = invoices::where('customer_id', $UserId)->where('branchs_id', $branch)->where('customer_id', $UserId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        }


        // return $Invoices;
        return view('reports.printReportProfitSales', compact('Invoices'));
    }



    public function printReportProductSales($branch, $productId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);





        if ($branch == '-') {
            $products = sales::where('product_id', $productId)->where('quantity', '!=', 0)->whereDate('created_at', '>=',  $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } else {
            $products = sales::where('product_id', $productId)->where('branch_id', $branch)->where('quantity', '!=', 0)->whereDate('created_at', '>=',  $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        }


        //return $products;
        return view('reports.printReportProductSales', compact('products'));
    }

    public function print_customerـpurchases($branch, $customerId, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $Invoices = [];
        $typeinvoise = '';
        $salesreport = 'no';
        if ($customerId == '-' && $branch == "-") {

            $Invoices = invoices::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } else {

            if ($customerId == "-" && $branch != "-") {
                $Invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } elseif ($customerId != "-" && $branch == "-") {
                $Invoices = invoices::where('customer_id', $customerId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } else {
                $Invoices = invoices::where('branchs_id', $branch)->where('customer_id', $customerId)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            }




            $typeinvoise = __('report.customerpurchases');
        }


        $data = [
            'invoices' => $Invoices,
            'typeinvoise' => $typeinvoise,
            'salesreport' => 'no'
        ];
        //  return $data;
        return view('reports.print_customer_purchases', compact('data'));
    }




    public function printReportsaleswithoud_deatails($branch, $pay, $startat, $end_at)
    {
        //return $request
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $Invoices = [];
        if ($pay == '-' && $branch == "-") {
            $Invoices = invoices::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
        } else {

            if ($pay == "-" && $branch != "-") {
                $Invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } elseif ($pay != "-" && $branch == "-") {
                $Invoices = invoices::where('Pay', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } else {
                $Invoices = invoices::where('branchs_id', $branch)->where('Pay', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            }
        }


         
     
        return view('reports.printReportsaleswithoud_deatails', compact('Invoices'))->with('start',$startat)->with('end',$end_at );
    }


    public function printInvoicesReport($branch, $pay, $startat, $end_at)
    {
        //return $request
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        $Invoices = [];
        $typeinvoise = '';
        $salesreport = 'no';
        if ($pay == '-' && $branch == "-") {

            $Invoices = invoices::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $salesreport = 'yes';
            $typeinvoise = 'Seles report';
        } else {

            if ($pay == "-" && $branch != "-") {
                $Invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } elseif ($pay != "-" && $branch == "-") {
                $Invoices = invoices::where('Pay', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            } else {
                $Invoices = invoices::where('branchs_id', $branch)->where('Pay', $pay)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            }




            $typeinvoise = $pay;
        }


        $data = [
            'invoices' => $Invoices,
            'typeinvoise' => $typeinvoise,
            'salesreport' => $salesreport
        ];
        if ($typeinvoise == 'Seles report') {
            return view('reports.printReportsales', compact('data'));
        }
        //  return $data;
        return view('reports.printReportInvoices', compact('data'));
    }
    public function salesReport()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return  view('reports.sales_report');
    }
    
    

    public function supplierlist_export()
    {
        
        return Excel::download(new supllierExport(), 'supplierlist_export.xlsx');
}


    public function customerslist_export()
    {
        
        return Excel::download(new customersExport(), 'customerslist_export.xlsx');
}

public function financial_accounts_Export()
{
    
    return Excel::download(new financial_accounts_Export(), 'financial_accounts_Export.xlsx');

    $data = products::get(['id','product_name','Product_Code','Product_Location','numberofpice','sale_price']);

    $html = View::make('reports.customerList', compact('data'))->render();

// Export the view to Excel
return Excel::download(function($excel) use ($html) {
$excel->sheet('Sheet 1', function($sheet) use ($html) {
    $sheet->fromHTML($html);
});
}, 'my_file.csv');

    // app()->setLocale(LaravelLocalization::getCurrentLocale());
    // return Excel::download(new Exportproducts, 'products.csv', \Maatwebsite\Excel\Excel::CSV, [
    //     'Content-Type' => 'text/csv',
    // ]);
}

public function financial_accounts_Export_CSV()
{
    
    return Excel::download(new financial_accounts_Export(), 'financial_accounts_Export.CSV');

 

    // app()->setLocale(LaravelLocalization::getCurrentLocale());
    // return Excel::download(new Exportproducts, 'products.csv', \Maatwebsite\Excel\Excel::CSV, [
    //     'Content-Type' => 'text/csv',
    // ]);
}


    public function Stocktaking()
    {
        
        return Excel::download(new Exportproducts(), 'users.xlsx');

        $data = products::get(['id','product_name','Product_Code','Product_Location','numberofpice','sale_price']);

        $html = View::make('reports.customerList', compact('data'))->render();

// Export the view to Excel
return Excel::download(function($excel) use ($html) {
    $excel->sheet('Sheet 1', function($sheet) use ($html) {
        $sheet->fromHTML($html);
    });
}, 'my_file.csv');

        // app()->setLocale(LaravelLocalization::getCurrentLocale());
        // return Excel::download(new Exportproducts, 'products.csv', \Maatwebsite\Excel\Excel::CSV, [
        //     'Content-Type' => 'text/csv',
        // ]);
    }


    public function Stocktakingpdf()
    {
        return Excel::download(new Exportproducts, 'products.pdf', \Maatwebsite\Excel\Excel::DOMPDF, [
            // 'Content-Type' => 'text/csv',
        ]);
    }



    public function salesReportsearch(Request $request)
    {
        //  return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->pay == "-" && $request->branch == "-") {
            $Invoices = invoices::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->where('save', 1)->get();
        } elseif ($request->pay == "-" && $request->branch != "-") {
            $Invoices = invoices::where('branchs_id', $request->branch)->where('save', 1)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } elseif ($request->pay != "-" && $request->branch == "-") {
            $Invoices = invoices::where('Pay', $request->pay)->where('save', 1)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        } else {
            $Invoices = invoices::where('branchs_id', $request->branch)->where('save', 1)->where('Pay', $request->pay)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
        }

        return view('reports.sales_report', compact('Invoices'))->with('pay', [$request->pay, $request->branch]);
    }

    public function print_return_Report($branch, $startat, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $startat = str_split($startat, 10);
        $end_at = str_split($end_at, 10);
        if ($branch == '-') {
            $Invoices = return_sales::whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        } else {
            $Invoices = return_sales::where('branch_id', $branch)->whereDate('created_at', '>=', $startat)->whereDate('created_at', '<=', $end_at)->get();
        }




        //return  $Invoices;
        return view('reports.print_report_sales_returen', compact('Invoices'))->with('start',$startat)->with('end', $end_at);
    }


    public function printstockquantity($branch, $operation, $quantity,$loction)
    {
                $products =[];

        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branch == '-') {
            if ($operation == '==') {
                if($loction== '-'){
                    $products = products::where('numberofpice', $quantity)->get();
                }
                else{
                    $products = products::where('Product_Location', 'LIKE', '%' . $loction . '%')->where('numberofpice', $quantity)->get();
 
                }
            } else {
                    if($loction== '-'){
                        $products = products::where('numberofpice', $operation, $quantity)->get();
                    }
                    else{
                        $products = products::where('Product_Location', 'LIKE', '%' . $loction . '%')->where('numberofpice', $operation, $quantity)->get();
     
                    
            }}
        }
        else {
            if ($operation == '==') {
                if($loction== '-'){
                    $products = products::where('numberofpice', $quantity)->where('branchs_id', $branch)->get();
                }
                else{
                    $products = products::where('Product_Location', 'LIKE', '%' . $loction . '%')->where('numberofpice', $quantity)->where('branchs_id', $branch)->get();
 
                }
            } else {
                if($loction== '-'){
                    $products = products::where('branchs_id', $branch)->where('numberofpice', $operation, $quantity)->get();
                }
                else{
                    $products = products::where('Product_Location', 'LIKE', '%' . $loction . '%')->where('branchs_id', $branch)->where('numberofpice', $operation, $quantity)->get();
 
                }
            }
        }
        return view('reports.printstockquantity', compact('products'));
    }



    public function printBest_selling_products($branch, $start_at, $end_at)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branch == '-') {
            $bestSaleing = sales::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('quantity', ">", 0)->where('save', 1)->get();
        } else {
            $bestSaleing = sales::where('branch_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('quantity', ">", 0)->where('save', 1)->get();
        }
        //return $bestSaleing;

        $bestselling = [];
        $listofId = [];
        $i = 0;
        foreach ($bestSaleing as $product) {
            if (!in_array($product->product_id, $listofId)) {
                //   return $bestSaleing;

                if ($branch == '-') {
                    $bestSaleingofproduct = sales::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('product_id', $product->product_id)->where('quantity', ">", 0)->where('save', 1)->get();
                } else {
                    $bestSaleingofproduct = sales::where('branch_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('product_id', $product->product_id)->where('quantity', ">", 0)->where('save', 1)->get();
                }
                $numberOfSall = 0;
                foreach ($bestSaleingofproduct as $saleproduct) {

                    $numberOfSall += $saleproduct->quantity;
                    $bestselling[$i] = [
                        'productcode' => $saleproduct->productData->Product_Code,
                        'productname' => $saleproduct->productData->product_name,
                        'numberofsall' => $numberOfSall,
                        'branch' => $saleproduct->branch->name,
                        'end_at' => $end_at,
                        'start_at' => $start_at
                    ];
                }
                $i++;
            }
            $listofId[] = $product->product_id;
        }

        // $bestselling=asort($bestSaleing);
        // return $bestSaleing;
                $dates = array();
foreach ($bestselling as $key => $row) {
   $dates[$key] = ($row['numberofsall']);
}
array_multisort($dates, SORT_DESC, $bestselling);

        return view('reports.printBest_selling_products', compact('bestselling'))->with('date', [$start_at, $end_at]);
    }





    public function print_VAT($branch, $start_at, $end_at)
    {
               app()->setLocale(LaravelLocalization::getCurrentLocale());
        //return $request;
        $totalVatSales = 0;
        $totalVatPrachese = 0;
        $totalvarExpenses = 0;
        $avt = Avt::find(1);
        $avtpurchases = Avt::find(2);
        $saleavt = $avt->AVT;
        $purchasesavt = $avtpurchases->AVT;
        $countsales = 0;
        $countpurchase = 0;
        $countexpanses = 0;
        $purachasereturntax = 0;
        $salesreturntax = 0;
        $countofreturnsaleslist = [];
        $countofreturnpurshaseslist = [];
        if ($branch == '-') {
            $invoices = invoices::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $expenses = expenses::where('expensesAvt', 1)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();


            foreach ($invoices as $invoice) {
                $totalVatSales += ($invoice->Price - $invoice->discount) * $saleavt;
            }
            foreach ($expenses as $expense) {
                $totalvarExpenses +=  $expense->Theـamountـpaid;
            }
            $resource_purchases = resource_purchases::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $ordersDetails = orderDetails::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            foreach ($ordersDetails as $orderDetailes) {
                $totalVatPrachese += $orderDetailes->Added_Value * $orderDetailes->numberofpice;
                $purachasereturntax += $orderDetailes->Added_Value * $orderDetailes->returns_purchase;
                if ($orderDetailes->returns_purchase > 0 && !in_array($orderDetailes->order_owner, $countofreturnpurshaseslist)) {
                    $countofreturnpurshaseslist[] = $orderDetailes->order_owner;
                }
            }
            $countsales = count($invoices);
            $countpurchase = count($resource_purchases);
            $countexpanses = count($expenses);
            $returnsales = return_sales::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
            foreach ($returnsales as $invoice) {
                $salesreturntax += (($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice) * $saleavt;
                if (!in_array($invoice->invoice_id, $countofreturnsaleslist))
                    $countofreturnsaleslist[] = $invoice->invoice_id;
            }
        } else {
            $invoices = invoices::where('branchs_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $expenses = expenses::where('expensesAvt', 1)->where('branchs_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();

            foreach ($invoices as $invoice) {
                $totalVatSales += ($invoice->Price - $invoice->discount) * $saleavt;
            }
            foreach ($expenses as $expense) {
                $totalvarExpenses +=  $expense->Theـamountـpaid;
            }
            $resource_purchases = resource_purchases::where('branchs_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            $ordersDetails = orderDetails::whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->where('save', 1)->get();
            foreach ($ordersDetails as $orderDetailes) {
                if ($branch == $orderDetailes->productData->branchs_id) {
                    $totalVatPrachese += $orderDetailes->Added_Value * $orderDetailes->numberofpice;
                    $purachasereturntax += $orderDetailes->Added_Value * $orderDetailes->returns_purchase;
                    if ($orderDetailes->returns_purchase > 0 && !in_array($orderDetailes->order_owner, $countofreturnpurshaseslist)) {
                        $countofreturnpurshaseslist[] = $orderDetailes->order_owner;
                    }
                }
            }
        }
        $countsales = count($invoices);
        $countpurchase = count($resource_purchases);
        $countexpanses = count($expenses);
        $returnsales = return_sales::where('branch_id', $branch)->whereDate('created_at', '>=', $start_at)->whereDate('created_at', '<=', $end_at)->get();
        foreach ($returnsales as $invoice) {
            $salesreturntax += (($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice) * $saleavt;
            if (!in_array($invoice->invoice_id, $countofreturnsaleslist)) {
                $countofreturnsaleslist[] = $invoice->invoice_id;
            }
        }

        $data = [
            'returncountsales' => count($countofreturnsaleslist),
            'returncountpurchases' => count($countofreturnpurshaseslist),
            'salesreturntax' => $salesreturntax,
            'purachasereturntax' => $purachasereturntax,
            'countsales' => $countsales,
            'countpurchase' => $countpurchase,
            'countexpanses' => $countexpanses,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'totalVatSales' =>  $totalVatSales,
            'totalVatPrachese' => $totalVatPrachese,
            'totalvarExpenses' => $totalvarExpenses - round($totalvarExpenses * 100 / 115)
        ];

        return view('reports.print_VAT', compact('data'));
    }
    
              public function updatestockquentity()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.updatestockquentity');
    }
   public function search_updatestockquentity(Request $request){
        $stock_update=stock_update::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();
                return view('reports.print_stock_update', compact('stock_update'));


        
        
    }
}


