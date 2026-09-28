<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;
use App\Models\transactiontosuplliers;
use App\Models\supllier;
use App\Models\User;
use App\Models\expenses;
use App\Models\convertcashboxToBank;
use App\Models\customers;
use App\Models\credittransactions;
use App\Models\Transactions;
use App\Models\financial_accounts;

use App\Models\cash_from__bank;
use App\Models\Transfer_cash_to_the_next_day;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

class AcountesController extends Controller
{
    //
    
            public function get_all_kid_yaomy_jax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

$data= credittransactions::where('branchs_id',Auth()->user()->branchs_id)->where('dely_record','!=',0)->where('note', 'LIKE', '%' . 'قيد يومي رقم' . '%')->whereDate('created_at', '<=',date('Y-m-d'))->whereDate('created_at', '>=',date('Y'). '-1'. '-1' )->where('decument_id', 0)->orderby('id', 'desc')->paginate(4);
       
        return view('ajax_dely_record', compact('data'));
    }
    
           public function search_by_decoumentNo_kid_yomy($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

$data= credittransactions::where('branchs_id',Auth()->user()->branchs_id)->where('dely_record',$id)->where('note', 'LIKE', '%' . 'قيد يومي رقم' . '%')->orderby('id', 'desc')->paginate(4);
       
        return view('ajax_dely_record', compact('data'));
    }
     
    
    
    
function uploadImage($folder, $image)
{
$extension = strtolower($image->extension());
$filename = time() . rand(100, 999) . '.' . $extension;
$image->getClientOriginalName = $filename;
$image->move($folder, $filename);
return $filename;
}
    public function voncher()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $allcustomers = financial_accounts::where('orginal_type','!=',2)->where('id','!=',1)->get();
        $data = [
            "transaction" => [],
            "allcustomers" =>  $allcustomers,
        ];
        return view('acountes.voncher', compact('data'));
    }


    public function convertcashboxToBank()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];

        return view('acountes.convertcashboxToBank', compact('data'));
    }


    public function Cash_withdrawal_from_the_bank()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];

        return view('acountes.Cash_withdrawal_from_the_bank', compact('data'));
    }


    public function Transfer_cash_to_next_day()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = [];



        return view('acountes.Transfer_cash_to_next_day', compact('data'));
    }
    public function transferMainBranch()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        return view('acountes.Transfertomainbranch', compact('data'));
    }
    public function go_to_bank()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        return view('acountes.cash_from_bank', compact('data'));
    }
    public function confirmTransfertomainbranch()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        return view('acountes.confirm_transferTomainBranch', compact('data'));
    }
    public function Transfercashto_the_next_day(Request $request)
    {
    $data = Transfer_cash_to_the_next_day::create(
            [
                'user_id' => Auth()->user()->id,
                'branchs_id' => Auth()->user()->branchs_id,
                'amount' => $request->The_amount_transferred_amount,
                'currentamount'=>$request->bank_balance_amount,
                'note' => $request->notes,
                'created_at' =>$request->date,
            ]
        );
        $data1 = [
            'id' => $data->id,
            'user' => $data->user->name,
            'branch' => $data->branch->name,
            'the_amount' => $data->amount,
            'currentamount'=>$data->currentamount,
            'date' => $data->created_at->format('d/m/Y'),
        ];
        return $data1;
    }


    public function updatedecoumentcashNextDay(Request $request)
    {
       // return $request;
     Transfer_cash_to_the_next_day::find($request->transactionId)->update(
            [
               
                'amount' => $request->The_amount_transferred_amount,
                'currentamount'=>$request->bank_balance_amount,
                'note' => $request->notes,
            ]
        );
        $data = Transfer_cash_to_the_next_day::find($request->transactionId);
        $data1 = [
            'id' => $data->id,
            'user' => $data->user->name,
            'branch' => $data->branch->name,
            'the_amount' => $data->amount,
            'currentamount' => $data->currentamount,
            'date' => $data->created_at->format('d/m/Y'),
        ];
        return $data1;
    }



    public function Add_blance_from_bank(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = cash_from__bank::create(
            [
                'user_id' => Auth()->user()->id,
                'branchs_id' => $request->branchs_id,
                'the_amount' => $request->cashreceived,
                'payment_method' => $request->pay,
                'created_at' => $request->start_at,
                'notes' => $request->notes,
                
            ]
        );
        $pay = '-';
        if ($request->pay == 'Cash') {
            $pay = __('report.cash');
        } elseif ($request->pay == 'Bank_transfer') {
            $pay = __('home.Bank_transfer');
        } else {
            $pay = __('report.shabka');
        }
        $data = [
            'id' => $data->id,
            'user' => $data->user->name,
            'branchs' => $data->branch->name,
            'the_amount' => $request->cashreceived,
            'payment_method' => $pay,
            'created_at' => $request->start_at,
        ];
        return $data;
        return view('acountes.cash_from_bank', compact('data'));
    }



    public function updateAdd_blance_from_bank(Request $request)
    {
        $cash_from__bank = cash_from__bank::find($request->transactionId);
        $data = cash_from__bank::find($request->transactionId)->update(
            [
                'user_id' => Auth()->user()->id,
                'branchs_id' => $request->updatebranchs_id,
                'the_amount' => $request->cashreceivedupdate,
                'payment_method' => $request->payupdate,

            ]
        );
        $pay = '-';
        if ($request->payupdate == 'Cash') {
            $pay = __('report.cash');
        } elseif ($request->payupdate == 'Bank_transfer') {
            $pay = __('home.Bank_transfer');
        } else {
            $pay = __('report.shabka');
        }
        $data1 = cash_from__bank::find($request->transactionId);

        $data = [
            'id' => $data1->id,
            'user' => $data1->user->name,
            'branchs' => $data1->branch->name,
            'the_amount' => $data1->the_amount,
            'payment_method' => $pay,
            'created_at' => $data1->created_at->format('d/m/Y'),
        ];
        return $data;
    }







    public function SearchconvertcashboxToBank(Request $request)
    {

        //eturn $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = convertcashboxToBank::create(
            [
                'from_user_id' => Auth()->user()->id,
                'amount' => $request->cashreceived,
                'branchs_id' => $request->branchs_id,
                'note' => $request->notes ?? "-",
                'created_at' => $request->start_at,
            ]
        );

     
            $financial_accounts= financial_accounts::find(5);
            financial_accounts::find(5)->update(
               [
                   'current_balance'=> $financial_accounts->current_balance- $request->cashreceived
               ]
               ); 
        
         
            $financial_accounts= financial_accounts::find(4);
            financial_accounts::find(4)->update(
             [
                 'current_balance'=> $financial_accounts->current_balance+ $request->cashreceived
             ]
             ); 
        
        $data = [
            'user' => Auth()->user()->name,
            'amount' => $request->cashreceived,
            'branchs_id' => $data->branch->name,
            'note' => $request->notes ?? "-",
            'created_at' => $request->start_at,
            'id' => $data->id
        ];
        return $data;
        return view('acountes.convertcashboxToBank', compact('data'));
    }
    public function printconvertcashboxToBank(Request $request)
    {
        if ($request == null) {
            $data = [];
            session()->flash('nodataprint', '');
            return view('acountes.convertcashboxToBank', compact('data'));
        }
        //eturn $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = convertcashboxToBank::find($request->id);
        //  return $data;
        return view('acountes.printconvertcashboxToBank', compact('data'));
    }
    public function cashEcprnse()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $allcustomers = User::get();
        $data = [
            'transaction' => [],
            "allusers" =>  $allcustomers,
        ];
        return view('acountes.cash expense', compact('data'));
    }

    public function income()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $allcustomers = customers::get();
        $data = [
            "transaction" =>  [],
        ];
        return view('acountes.Expensesowner', compact('data'));
    }

    public function reciept_decoument()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());   
        return view('acountes.reciept_decoment');
    }

    public function opining_balnce_ajax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

$data= credittransactions::where('branchs_id',Auth()->user()->branchs_id)->whereDate('created_at', '<=',date('Y-m-d'))->whereDate('created_at', '>=',date('Y'). '-1'. '-1' )->where('parent_Opening_entry', 0)->where('save', 1)->where('Opening_entry','>=', 1)->orderby('id', 'desc')->paginate(3);
        return view('opining_balnce_ajax', compact('data'));
    }

    public function get_all_send_kabd_jax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

$data= credittransactions::where('note', 'LIKE', '%' . 'سند قبض' . '%')->whereDate('created_at', '<=',date('Y-m-d'))->whereDate('created_at', '>=',date('Y'). '-1'. '-1' )->where('decument_id', 0)->orderby('id', 'desc')->paginate(3);
        return view('sant_abd_ajax', compact('data'));
    }

    public function get_all_send_serf_jax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data= credittransactions::where('type_decument', 2)->whereDate('created_at', '<=',date('Y-m-d'))->whereDate('created_at', '>=',date('Y'). '-1'. '-1' )->where('decument_id', 0)->orderby('id', 'desc')->paginate(3);
   
        return view('sant_serf_ajax', compact('data'));
    }

    public  function createTransfertomainbranch($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $transactiontosupllier = transactiontosuplliers::find($id);

        $supllierdata = supllier::find($transactiontosupllier->suplier_id);
        $data = [
            "transaction" => [
                'sent_serf_count' => $transactiontosupllier->sent_serf_count,
                'name' => $supllierdata->name,
                'Limit_credit' => $supllierdata->Limit_credit,
                'Balance' => $supllierdata->In_debt,
                'camp_name' => $supllierdata->comp_name,
                'camp_phone' => $supllierdata->phone,
                'created_at' => $transactiontosupllier->created_at,
                'date' => $transactiontosupllier->created_at,

                'date_export' => $transactiontosupllier->date_export,
                'method_pay' => $transactiontosupllier->Pay_Method_Name,
                'paid_amount' => $transactiontosupllier->paidـamount
            ],
        ];
        return view('acountes.print_voucher_to_supplier', compact('data'));
        # code...
    }






    public  function print_voucher(Request $request)
    {
               $transactiontocustomer = credittransactions::find($request->id);
               $credittransactions=credittransactions::where( 'sent_serf_count' ,$transactiontocustomer->sent_serf_count)->where('type_decument',2)->get();
               $data=[];
               $transaction=[];
               $pay='';
                     foreach($credittransactions as $item){
                    
                 

                        $transaction[] = [
                            "sent_serf_count" => $transactiontocustomer->sent_serf_count,
                            'name' => $item->financial_accounts_data->name,
                            'created_at' => $item->created_at,
                            'date' => $item->created_at,
                            'date_export' => $item->date_export, 
                            'method_pay' => $item->Pay_Method_Name,
                            'paid_amount' => $item->recive_amount,
                            'note' => $item->note
            
                        ];
                    
                           
                       }
                       
        $data = [
            "transaction" =>  $transaction,
        ];
        # code...
        return view('acountes.print_voucher_to_supplier', compact('data'));
        # code...
    }
    public  function print_expansedecoument(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $transactiontocustomer;
        $expense = expenses::find($request->id);
        if ($expense->Pay_Method_Name == 'Cash') {
            $pay = __('report.cash');
        } elseif ($expense->Pay_Method_Name == 'Bank_transfer') {
            $pay = __('home.Bank_transfer');
        } else {
            $pay = __('report.shabka');
        }
        //return  $expense;
        $data = [
            'id' =>  $expense->id,
            'user' => Auth()->user()->name,
            'Pay_Method_Name' => $pay,
            'Theـamountـpaid' => $expense->Theـamountـpaid,
            'Reasonforspendingmoney' =>LaravelLocalization::getCurrentLocale()=='ar'? $expense->Expenses_reasons->expenses_reason:($expense->Expenses_reasons->expenses_reason_en=='-'?$expense->Expenses_reasons->expenses_reason:$expense->Expenses_reasons->expenses_reason_en) ,
            'notes' => $expense->notes,


        ];
        //return $data;
        return view('acountes.print_cashexpenswe', compact('data'));
        # code...

    }


    public function print_reciept_ducoument(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

     


        $Transactions = Transactions::where('id',$request->id)->get();
               $data=[];
               $transaction=[];
               $pay='';
                     foreach($Transactions as $item){
                    
                 

                        $transaction[] = [
                            "sent_abd_count" => $item->id,
                            'name' => $item->financial_accounts_data->name,
                            'created_at' => $item->created_at,
                            'date' => $item->date,
                            'date_export' => $item->created_at, 
                            'method_pay' => __('home.Bank_transfer'),
                            'paid_amount' => $item->price,
                            'note' => $item->note
            
                        ];
                    
                           
                       }
                       
        $data = [
            "transaction" =>  $transaction,
        ];
        # code...
        return view('acountes.print_reciept_decoment_to_customer', compact('data'));
        # code...
    }
    
        public function generate_pdf(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $transactiontocustomer = credittransactions::find($request->id);
        //return $transactiontocustomer;
        $customerdata = customers::find($transactiontocustomer->customer_id);
        $data = [
            "transaction" => [
                "sent_abd_count" => $transactiontocustomer->sent_abd_count,
                'name' => $customerdata->name,
                'Limit_credit' => $customerdata->Limit_credit,
                'Balance' => $customerdata->Balance,
'created_at' => $transactiontocustomer->created_at,
'date' => $transactiontocustomer->created_at,
                'date_export' => $transactiontocustomer->date_export, 
                'method_pay' => $transactiontocustomer->Pay_Method_Name,
                'paid_amount' => $transactiontocustomer->recive_amount
            ],
        ];
        
        $tran =['data'=>$data] ;

$dateTime = now();

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.pdf_reciept_ducoument', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), 
            "Invoice_No_".$transactiontocustomer->id."_". $fileName.".pdf",
            $headers
        );

        # code...
    }
}
