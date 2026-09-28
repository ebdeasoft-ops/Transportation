<?php

namespace App\Http\Controllers;

use App\Models\financial_accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\supllier;
use App\Models\customers;
use App\Models\acounts_type;
use App\Models\Expenses_reasons;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use Illuminate\Support\Facades\DB;

class FinancialAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        
        return view('acountes.accounts');  
    
        //
    }  
    public function tree()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        
        return view('acountes.tree');  
    
        //
    }
    public function searchaboutaccountByname_numberfunction($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data= financial_accounts::where('name', 'LIKE', '%' . $searchtext . '%')->orwhere('account_number',$searchtext)->paginate(15);
        return view('ajax_choose_account',compact('data'));  
    
        //
    }

    public function searchaboutaccountBytype_function($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data= financial_accounts::where('account_type', $searchtext)->paginate(15);
        return view('ajax_choose_account',compact('data'));  
    
        //
    }
    
       public function searchMaster_account_function($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data= financial_accounts::where('parent_account_number', $searchtext)->paginate(20);
        return view('ajax_choose_account',compact('data'));  
    
        //
    }
    
    
    public function ajax_choose_account()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data= financial_accounts::paginate(15);
        return view('ajax_choose_account',compact('data'));  
    
        //
    }
    public function create_new_acount()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        
        return view('acountes.create_acount');  
    
        //
    }

public function getfinancialaccount($id){
 
    $financial_accounts=financial_accounts::find($id);


    return $financial_accounts;

}

    public function update_acount($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        
        return view('acountes.update_acount');  
    
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_new_acount_finance(Request $request)
    {
        //
        $this->validate($request, [
            'name'=>'required',
          
        ]);

        try {
            //check if not exsits for name
        $checkExists_name=financial_accounts::where('name',$request->name)->where('name_en',$request->name)->first();
        
        $dataid=NULL;
        $supplierid=NULL;
        $data_insert['orginal_type'] = NULL;
        if($request->parent_account_number==1){
            $data_insert['orginal_type']=2;
            $supllier=supllier::create(
                [
                    'name'=>$request->name,
                    'phone'=>$request->phone??'05*********',
                    'comp_name'=>$request->name,
                    'email'=>'Email@gmail.com',
                    'location'=>$request->address??'-',
                    'notes'=>$request->notes??"لا توجد",
                    'TaxـNumber'=>$request->TaxـNumber??0
                ]
                );
                $dataid=$supllier->id;
            }
            elseif($request->parent_account_number==3){
                $data_insert['orginal_type']=4;

             
                    $customer=customers::create(
                        [
                            'name'=>$request->name,
                            'comp_name'=>$request->name,
                            'address'=> $request->address??"Client Address",
                            'tax_no'=> $request->TaxـNumber ??0 ,
                            "Balance"=> $request->start_balance ??0 ,
                            'phone'=>  $request->phone??'05*********' ,
                            'email'=> 'Email@gmail.com'  ,
                            'notes'=>$request->notes??"لا توجد ملاحظات ",
                            'Limit_credit'=>10000,
                            'grace_period_in_days'=>30
                        ]
                        );
        
        
                        $dataid=$customer->id;
        

            }
            
            elseif($request->parent_account_number==2){
                $data_insert['orginal_type']=1;

            $customer=customers::create(
                [
                    'name'=>$request->name,
                    'comp_name'=>$request->name,
                    'address'=> $request->address??"Client Address",
                    'tax_no'=> $request->TaxـNumber ??0 ,
                    "Balance"=> $request->start_balance ??0 ,
                    'phone'=>  $request->phone??'05*********' ,
                    'email'=> 'Email@gmail.com'  ,
                    'notes'=>$request->notes??"لا توجد ملاحظات ",
                    'Limit_credit'=>10000,
                    'grace_period_in_days'=>30
                ]
                );


                $dataid=$customer->id;

        }elseif($request->account_type==4)
        {
            $data_insert['orginal_type']=3;
            $createexpenses_reasons=Expenses_reasons::create([
                'expenses_reason'=> $request->name ,
                'expenses_reason_en'=>$request->name,
                'expensesAvt'=>$request-> AVT ,
                'created_at'=> \Carbon\Carbon::now()->addHours(3)
                ]);

                $dataid=$createexpenses_reasons->id;
        }else{



        }

      
            //set account number
            $row=financial_accounts::latest()->first();
            if (!empty($row)) {
            $data_insert['account_number'] = $row['account_number'] + 1;
            } else {
            $data_insert['account_number'] = 1;
            }

            $data_insert['orginal_id'] = $dataid;
            $data_insert['orginal_supplier'] = $supplierid;

            $data_insert['name'] = $request->name;
            $data_insert['account_type'] = $request->account_type;
            $data_insert['is_parent'] = $request->is_parent;
            if ($data_insert['is_parent'] == 0) {
            $data_insert['parent_account_number'] = $request->parent_account_number;
            }
            $data_insert['start_balance_status'] = $request->start_balance_status;
            if ($data_insert['start_balance_status'] == 1) {
            //credit
            $data_insert['start_balance'] = $request->start_balance * (-1);
            $data_insert['debtor_current'] = $request->start_balance ;
            $data_insert['debtor_opening'] = $request->start_balance ;
            } elseif ($data_insert['start_balance_status'] == 2) {
            //debit
            $data_insert['start_balance'] = $request->start_balance;
             $data_insert['creditor_opening'] = $request->start_balance ;
            $data_insert['creditor_current'] = $request->start_balance ;
            if ($data_insert['start_balance'] < 0) {
            $data_insert['start_balance'] = $data_insert['start_balance'] * (-1);
            }
            } elseif ($data_insert['start_balance_status'] == 3) {
            //balanced
            $data_insert['start_balance'] = 0;
            } else {
            $data_insert['start_balance_status'] = 3;
            $data_insert['start_balance'] = 0;
            }
            $data_insert['current_balance'] = $data_insert['start_balance'];
            $data_insert['notes'] = $request->notes;
            $data_insert['active'] = $request->active;
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['date'] = \Carbon\Carbon::now()->addHours(3);
            $data_insert['com_code'] = 0;
            $data=financial_accounts::create($data_insert);  
            if($request->parent_account_number==3){
                supllier::find( $supplierid)->update(
                   [ 'mantob_account_id'=> $data->id]
                );
                customers::find($dataid)->update(
                  [  'mantob_account_id'=> $data->id]
                );
            }

            
$i=0;
foreach(acounts_type::get()  as $type){
$i++;
$x=$i*10;
foreach(financial_accounts::where('account_type',$type->id)->where('parent_account_number',null)->get()  as $v){
    $x++;
    financial_accounts::find($v->id)->update([
'account_number'=>$x
    ]);
    $v= financial_accounts::find($v->id);
    $Y=$v->account_number*10;

foreach(financial_accounts::where('parent_account_number',$v->id)->get()  as $v){

$Y++;
    financial_accounts::find($v->id)->update([
        'account_number'=>$Y
                ]);

                $v= financial_accounts::find($v->id);
                  $z=$v->account_number*10000;

        
 foreach(financial_accounts::where('parent_account_number',$v->id)->get()  as $v){
    $z++;
    financial_accounts::find($v->id)->update([
        'account_number'=>$z
                ]);
                $v= financial_accounts::find($v->id);
                $a=$v->account_number*100;
        
 foreach(financial_accounts::where('parent_account_number',$v->id)->get()  as $v){


    $a++;
    financial_accounts::find($v->id)->update([
        'account_number'=>$a
                ]);                                             

           }

           }

           }

           }


           }

            $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اضافة الحساب المالي  بنجاح':'Active financial account added'  ;

            session()->flash('create_acount',$message);
        
            return view('acountes.create_acount');  


        }

        catch (\Exception $ex) {

            return $ex;
            $message=LaravelLocalization::getCurrentLocale()=='ar'?'عذرا لم يتم التسجيل نرجو المحاولة مرة اخري':'Sorry, you have not registered. Please try again'   ;

            session()->flash('error_mastik',$message);
        
            return redirect()->back()
            ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
            ->withInput();
            }
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\financial_accounts  $financial_accounts
     * @return \Illuminate\Http\Response
     */
    public function show(financial_accounts $financial_accounts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\financial_accounts  $financial_accounts
     * @return \Illuminate\Http\Response
     */
    public function edit(financial_accounts $financial_accounts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\financial_accounts  $financial_accounts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, financial_accounts $financial_accounts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\financial_accounts  $financial_accounts
     * @return \Illuminate\Http\Response
     */
    public function destroy(financial_accounts $financial_accounts)
    {
        //
    }
}
