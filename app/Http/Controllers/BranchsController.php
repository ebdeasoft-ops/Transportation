<?php

namespace App\Http\Controllers;
use App\Models\financial_accounts;

use App\Models\branchs;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

class BranchsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('users.Add_branch');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
      //  return $request;
      app()->setLocale(LaravelLocalization::getCurrentLocale());

$createBranch=branchs::create([
'name'=> $request-> breanchName ,
'place'=>$request->branchLoction   ,
'created_at'=> \Carbon\Carbon::now()->addHours(3)
]);
 $financial_accounts_last=financial_accounts::latest()->first();

        financial_accounts::create(
            [
                'name'=>'الخزينة فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>5,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );


 $financial_accounts_last=financial_accounts::latest()->first();
        financial_accounts::create(
            [
                'name'=>'حساب البنك فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>4,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );
 $financial_accounts_last=financial_accounts::latest()->first();


        financial_accounts::create(
            [
                'name'=>'ضريبة القيمة المضافة	 فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>102,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );


 $financial_accounts_last=financial_accounts::latest()->first();

        financial_accounts::create(
            [
                'name'=>'المبيعات فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>112,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );


 $financial_accounts_last=financial_accounts::latest()->first();


        financial_accounts::create(
            [
                'name'=>'مخزون فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>181,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );

 $financial_accounts_last=financial_accounts::latest()->first();

        financial_accounts::create(
            [
                'name'=>'تكاليف المبيعات فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>183,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );







$financial_accounts_last=financial_accounts::latest()->first();

        financial_accounts::create(
            [
                'name'=>'مردود المشتريات النقدية  فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>159,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );
            
            
            
            $financial_accounts_last=financial_accounts::latest()->first();

        financial_accounts::create(
            [
                'name'=>'مردو المشتريات الاجلة  فرع '.' '.$request-> breanchName,
                'account_type'=>1,
                'parent_account_number'=>160,
                'account_number'=>$financial_accounts_last->account_number+1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL,
                 'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> NULL,
                'orginal_type'=>NULL,
                'branchs_id'=>$createBranch->id,
                

            ]
            );
            
// foreach(branchs::get() as $item)
// {
 
// $financial_accounts_last=financial_accounts::latest()->first();

//         financial_accounts::create(
//             [
//                 'name'=>'مردود المشتريات النقدية  فرع '.' '.$item-> name,
//                 'account_type'=>1,
//                 'parent_account_number'=>159,
//                 'account_number'=>$financial_accounts_last->account_number+1,
//                 'start_balance'=>0,
//                 'current_balance'=>0,
//                 'start_balance_status'=>3,
//                 'other_table_FK'=>NULL,
//                  'notes'=>NULL,
//                  'added_by'=>1,
//                 'updated_by'=>NULL,
//                 'com_code'=>1
//                 ,'date'=>\Carbon\Carbon::now()->addHours(3),
//                 'active'=>1
//                 ,'is_parent'=>0,
//                 'start_balance_status'=>3,
//                 'orginal_id'=> NULL,
//                 'orginal_type'=>NULL,
//                 'branchs_id'=>$item->id,
                

//             ]
//             );
            
            
            
//             $financial_accounts_last=financial_accounts::latest()->first();

//         financial_accounts::create(
//             [
//                 'name'=>'مردو المشتريات الاجلة  فرع '.' '.$item-> name,
//                 'account_type'=>1,
//                 'parent_account_number'=>160,
//                 'account_number'=>$financial_accounts_last->account_number+1,
//                 'start_balance'=>0,
//                 'current_balance'=>0,
//                 'start_balance_status'=>3,
//                 'other_table_FK'=>NULL,
//                  'notes'=>NULL,
//                  'added_by'=>1,
//                 'updated_by'=>NULL,
//                 'com_code'=>1
//                 ,'date'=>\Carbon\Carbon::now()->addHours(3),
//                 'active'=>1
//                 ,'is_parent'=>0,
//                 'start_balance_status'=>3,
//                 'orginal_id'=> NULL,
//                 'orginal_type'=>NULL,
//                 'branchs_id'=>$item->id,
                

//             ]
//             );   
// }
if($createBranch!=null){
    session()->flash('create','تم انشاء الفرع  بنجاج');
    return view('users.Add_branch');


}
else{
    session()->flash('notcreate','حدث مشكلة اثناء انشاء الفرع');
    return view('users.Add_branch');
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
     * @param  \App\Models\branchs  $branchs
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $branches=branchs::get();
        return view('Branches',compact('branches'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\branchs  $branchs
     * @return \Illuminate\Http\Response
     */
    public function updatebranch(Request $request)
    {
        //
       // return $request;
       app()->setLocale(LaravelLocalization::getCurrentLocale());

        branchs::where('id',$request->id)->update([
            'name'=>$request->breanchName,
            'place'=>$request->branchLoction
        ]);
        $branches=branchs::get();
        return view('Branches',compact('branches'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\branchs  $branchs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, branchs $branchs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\branchs  $branchs
     * @return \Illuminate\Http\Response
     */
    public function destroy(branchs $branchs)
    {
        //
    }
}
