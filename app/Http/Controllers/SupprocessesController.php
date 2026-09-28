<?php

namespace App\Http\Controllers;

use App\Models\ProductsDamage;
use App\Models\products;
use App\Models\supllier;
use App\Models\Cost_centers;
use App\Models\customers;
use App\Models\Expenses_reasons;
use App\Models\stock_update;
use App\Models\products_group;
use App\Models\financial_accounts;
use App\Models\branchs;
use App\Models\acounts_type;

use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use Illuminate\Support\Facades\DB;


class SupprocessesController extends Controller
{
    //
    public function index(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());
// products::where('photo',null)->update(
//     [
//         'photo'=>'productunKnown.png']);
        return view('supProcesses.addProduct');
    }  
    
    public function Goupdatecustomer(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.updatecustomer');
    }  
      public function Goupdatesupplier(){
       
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.updatesupplier');
    }   
    
    public function show_groups(){
       
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.show_groups');
    } 
    

    public function expenses_reason(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.enpenses_reason');
    } 


    public function create_expenses_reason(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
 



            $createexpenses_reasons=Cost_centers::create([
                'cost_center_ar'=> $request->breanchName ,
                'cost_center_en'=>$request->expenses_reason_en??$request-> breanchName,
                'expensesAvt'=>0,
                'created_at'=> \Carbon\Carbon::now()->addHours(3)
                ]);

            
if($createexpenses_reasons!=null){
    session()->flash('Cost_center_created_successfully','تم انشاء  مركز التكلفة  بنجاج');
    return view('supProcesses.enpenses_reason');


}
else{
    session()->flash('notcreate','حدث مشكلة اثناء انشاء الفرع');
    return view('supProcesses.enpenses_reason');
}
    }
function uploadImage($folder, $image)
{
$extension = strtolower($image->extension());
$filename = time() . rand(100, 999) . '.' . $extension;
$image->getClientOriginalName = $filename;
$image->move($folder, $filename);
return $filename;
}

    public function create_products_group(Request $request){
$products_group=products_group::create([
    'group_en'=> $request->groub_en,
    'group_ar'=>$request->groub_ar
    ]
    );
    
    return $products_group;

}
    public function create_addnewProduct(Request $request){
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $this->validate($request, [
            'product_name_ar' => 'required',
            'Section' => 'required',
            'product_location' => 'required',
            'minmum_quantity_stock_alart' => 'required',
        ]);
        
        

$photo ='productunKnown.png';

if ($request->has('Item_img')) {
$request->validate([
'Item_img' => 'required|mimes:png,jpg,jpeg|max:2000',
]);
$the_file_path = $this->uploadImage('assets//admin//uploads', $request->Item_img);
$photo =$the_file_path;
}
 
            foreach(branchs::get() as $branch){
                             $lastproduct = products::orderBy('id', 'DESC')->first();

                   $check_product= products::where('branchs_id',$branch->id)->where('Product_Code',$request->product_code)->first();
               $newcustomer=products::create(
            [
                'product_name'=>$request->product_name_ar."  ".$request->product_name_en ,
                'name_en'=>$request->product_name_en??'' ,
                'branchs_id'=>$branch->id,
                'user_id'=>Auth()->User()->id,   
                'Product_Location'=>$request->product_location,
                'Product_Code'=>$request->product_code??$lastproduct->id+1,
                'Status'=>1,
                'notes'=>$request->product_notes??"-",
                'unit'=>$request->unit,
                'product_group'=>$request->product_group,
                'refnumber'=>$request->refnumber,
                'minmum_quantity_stock_alart'=>$request->minmum_quantity_stock_alart,
                'photo'=>$photo,
                

            ]
            );  
                   
             products::where('id',$newcustomer->id)->update(
                    [
                        'main_product'=>$newcustomer->id,
    
                    ]);
                       $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اضافة المنتج بنجاح':'Product added successfully'   ;
    session()->flash('addProduct',$message);
                    
                     
                }
   
            
            
            // if($request->MAINproduct==0){
            //     $updatedproduct=    products::where('id',$newcustomer->id)->update(
            //         [
            //             'main_product'=>$newcustomer->id,
    
            //         ]);
            // }
            // else{
            // $updatedproduct=    products::where('id',$newcustomer->id)->update(
            //     [
            //         'main_product'=>$request->MAINproduct,

            //     ]);
            // }

        return view('supProcesses.addProduct');
    }




    public function addnewProductajax(Request $request){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $this->validate($request, [
            'product_name_ar' => 'required',
            'Section' => 'required',
            'product_location' => 'required',
            'minmum_quantity_stock_alart' => 'required',
        ]);
     //return $request;


   
            
            
                        foreach(branchs::get() as $branch){
                                  $lastproduct = products::orderBy('id', 'DESC')->first();

                   $check_product= products::where('branchs_id',$branch->id)->where('Product_Code',$request->product_code)->first();
               $newcustomer=   products::create(
            [
              'product_name'=>$request->product_name_ar."  ".$request->product_name_en ,
                'name_en'=>$request->product_name_en ,
                'branchs_id'=>$branch->id,
                'user_id'=>Auth()->User()->id,   
                'Product_Location'=>$request->product_location,
                'Product_Code'=>$request->product_code??$lastproduct->id+1,
                'Status'=>1,
                'product_group'=>$request->product_group,
                'notes'=>$request->product_notes??'-',
                'refnumber'=>$request->refnumber,
                'unit'=>$request->unit,
                'minmum_quantity_stock_alart'=>$request->minmum_quantity_stock_alart,
                

            ]
            );  
            
             products::where('id',$newcustomer->id)->update(
                    [
                        'main_product'=>$newcustomer->id,
    
                    ]);
                    $newcustomer=1;
            
                        }
            
            
            
            //   if($request->MAINproduct==0){
            //     $updatedproduct=    products::where('id',$newcustomer->id)->update(
            //         [
            //             'main_product'=>$newcustomer->id,
    
            //         ]);
            // }
            // else{
            // $updatedproduct=    products::where('id',$newcustomer->id)->update(
            //     [
            //         'main_product'=>$request->MAINproduct,

            //     ]);
            // }
if(  $newcustomer!=null){
    $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اضافة المنتج بنجاح':'Product added successfully'   ;

  return [$message];

}
else{
    return 0;
    }
    }



    
    public function product_movement(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.product_movement');
    } 
    public function product_damage(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.product damage');
    } 
    
   
    
      public function addnewcustomer(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.addnewcustomer');
    }  
     public function addnewsupplier(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('supProcesses.addnewsupplier');
    }
    

    public function stockAdjastment(){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        
        return view('supProcesses.stockAdjastment');
    }

    public function stock_update(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $productId = $request->productNo;
       $productdata= products::find( $productId);
       if($request->productdecrease>0){
        stock_update::create(
            [
                'productdecrease'=>$request->productdecrease,
                'productincrease'=>$request->productincrease,
                'product_id'=>$productId,
                'product_name'=>$productdata->numberofpice- $request->productdecrease,
                'branchs_id'=>Auth()->user()->branchs_id,
                'user_id'=>Auth()->user()->id,
                'created_at'=>\Carbon\Carbon::now(),
                'note'=>$request->note,

            ]
            );
            $updatedproduct =    products::where('id', $productId)->update(
                [
                    'numberofpice' =>$productdata->numberofpice- $request->productdecrease,
                ]
            );

       }
       if($request->productincrease>0){


        $updatedproduct =    products::where('id', $productId)->update(
            [
                'numberofpice' => $productdata->numberofpice+$request->productincrease,
            ]
        );
        stock_update::create(
            [
                'productdecrease'=>$request->productdecrease,
                'productincrease'=>$request->productincrease,
                'note'=>$request->note,
                'product_id'=>$productId,
                'product_name'=>$productdata->numberofpice+$request->productincrease,
                'branchs_id'=>Auth()->user()->branchs_id,
                'user_id'=>Auth()->user()->id,
                'created_at'=>\Carbon\Carbon::now(),
            ]
            );

       }
      
      
     
        if ($updatedproduct != null) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل كمية المنتج بنجاح' : "Product quantity has been modified successfully.";
            session()->flash('productupdated', $message);
        }
        return view('supProcesses.stockAdjastment');
    }



    public function update_product_movement(Request $request){
        app()->setLocale(LaravelLocalization::getCurrentLocale());
//  return $request;
           $productId=$request->product_no;
           
           
$photo ='productunKnown.png';
if ($request->has('Item_img')) {
$request->validate([
'Item_img' => 'required|mimes:png,jpg,jpeg|max:2000',
]);
$the_file_path = $this->uploadImage('assets//admin//uploads', $request->Item_img);
$photo =$the_file_path;
}

// return $photo;
           $productData=products::find($productId);

    $updatedproduct=    products::where('id',$productId)->update(
               [
                'Product_Location'=>$request->new_location,
                'product_name'=>$request->productnameshow,
                 'main_product'=>$request->MAINproduct==0?$productId:$request->MAINproduct,
                'Product_Code'=>$request->productcode,
                'sale_price'=>$request->product_price,
                'purchasingـprice'=>$request->purachesepice,
                'refnumber'=>$request->refnumber,
                'notes'=>$request->product_notes??' ',
                'Wholesale_price'=>$request->Wholesale_price??$request->product_price,
                'photo'=>$photo,


                
               ]
           );
           
           
                products::where('Product_Code', $request->productcode)->update(
            [
                'sale_price'=>$request->product_price,
                'Wholesale_price'=>$request->purachesepice,    
                'purchasingـprice'=>$request->purachesepice,    
                ]
             );
   if($updatedproduct!=null){
       $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم تعديل بيانات المنتج و المنتجات المسجلة بنفس الرقم بالفروع الاخري بنجاح':'Product data and hijacked products with active short code have been modified';
       session()->flash('productupdatedlocation', $message);
   
   }
        return view('supProcesses.product_movement');
    } 
    public function product_damage_add(Request $request){
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        
           $productId=$request->productNo;
           $product=products::find($productId);
           $updatedproduct=products::where('id',$productId)->update(
               [
                   'numberofpice'=>$product->numberofpice-$request->newquentity
               ]
           );
           ProductsDamage::create([
            'damage_quantity'=>$request->newquentity,
            'product_id'=>$productId,
            'product_name'=>$product->product_name,
            'branchs_id'=>$product->branchs_id,
            'user_id'=>Auth()->user()->id,
            'created_at'=>\Carbon\Carbon::now()->addHours(3),
           ]);
         
   if($updatedproduct!=null){
       $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اتلاف  المنتج بنجاح':"Product damage successfully.";
       session()->flash('damageproduct', $message);
   
   }
        return view('supProcesses.product damage');
    } 





    
    public function getcustomerdata($id){
        $updatecustomerdata=customers::find($id);
        return $updatecustomerdata;
    }  
     public function getsupplierdata($id){
        $updatecustomerdata=supllier::find($id);
        return $updatecustomerdata;
    }

    public function updatesupplier(Request $request){

    
        //  return $request;
           $newcustomer=supllier::find($request->clientnamesearch)->update(
              [
                'name'=>$request->name,
                'phone'=>$request->phone,
                'comp_name'=>$request->name,
                'email'=>$request->email??"supplier@gamil.com",
                'location'=>$request->loction,
                'notes'=>$request->notes??"لا توجد",
                'TaxـNumber'=>$request->TaxـNumber??'0'
              ]
              );

              financial_accounts::where('orginal_id',$request->clientnamesearch)->where('orginal_type',2)->update([
                'name'=>$request->name,

              ]);
              $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم  تعديل البيانات  بنجاح':'Data modified successfully'  ;

              session()->flash('updateseccess',$message);
              return view('supProcesses.updatesupplier');
            }

    public function updatecustomer(Request $request){

         $this->validate($request, [
       
            'TaxـNumber'=>  'required ' ,
           
        ]);
        //  return $request;
           $newcustomer=customers::find($request->clientnamesearch)->update(
              [
                  'name'=>$request->nameclient,
                  'comp_name'=>$request->nameclient,
           'street_name'=>$request->StreetName,
        'building_number'=>$request->buildnumber,
        'plot_identification'=>$request->plot_identification,
        'address'=> $request->city??"Client Address",
                'sub_city'=>$request->sub_city??"Client Address",

                  'tax_no'=> $request->TaxـNumber ??0 ,
                  'phone'=>  $request->phone??'05----------'  ,
                  'email'=> $request->email??'Email@gmail.com'  ,
                  'notes'=>$request->product_notes??"لا توجد ملاحظات ",
                  'Limit_credit'=>$request->credit_limit,
                  'grace_period_in_days'=>$request->grace_period_in_days,
                  'postcode'=>$request->postcode,
              ]
              );
                  financial_accounts::where('orginal_id',$request->clientnamesearch)->where('orginal_type',1)->update([
                'name'=>$request->nameclient,

              ]);
              $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم  تعديل البيانات  بنجاح':'Data modified successfully'  ;

              session()->flash('updateseccess',$message);
              return view('supProcesses.updatecustomer');
            }








    public function createnewcustomerajax(Request $request){

    
  //  return $request;
     $newcustomer=customers::create(
        [
            'name'=>$request->name,
            'comp_name'=>$request->name,
            'address'=> $request->address??"Client Address",
            'tax_no'=> $request->tax_no ??0 ,
            "Balance"=> $request->Balance ??0 ,
            'phone'=>  $request->phone??'05----------'  ,
            'email'=> $request->email??'Email@gmail.com'  ,
            'notes'=>$request->notes??"لا توجد ملاحظات ",
            'Limit_credit'=>$request->Limit_credit,
            'grace_period_in_days'=>$request->grace_period_in_days,
                 'street_name'=>$request->StreetName,
            'building_number'=>$request->buildnumber,
            'plot_identification'=>$request->plot_identification,
            'address'=> $request->city??"Client Address",
            'sub_city'=>$request->sub_city??"Client Address",
                              'postcode'=>$request->postcode,
        ]
        );
        $financial_accounts_last=financial_accounts::where('account_type',1)->where('orginal_type',1)->latest()->first();
        financial_accounts::create(
            [
                'name'=>$request->name,
                'account_type'=>1,
                'parent_account_number'=>2,
                'account_number'=>1,
                'start_balance'=>0,
                'current_balance'=>0,
                'start_balance_status'=>3,
                'other_table_FK'=>NULL,
                 'notes'=>NULL
              
                ,'added_by'=>1,
                'updated_by'=>NULL,
                'com_code'=>1
                ,'date'=>\Carbon\Carbon::now()->addHours(3),
                'active'=>1
                ,'is_parent'=>0,
                'start_balance_status'=>3,
                'orginal_id'=> $newcustomer->id,
                'orginal_type'=>1

            ]
            );

        return  $newcustomer;
    }





    public function create_addnewcustomer(Request $request){

        $this->validate($request, [
            'name'=>'required',
            'TaxـNumber'=>  'required | numeric' ,
            "balance"=> 'required | numeric'  ,
            'timeout_periodـinـdays'=>'required | numeric',
            'credit_limit'=>'required| numeric'
        ]);
        app()->setLocale(LaravelLocalization::getCurrentLocale());
      // return $request;

    
$newcustomer=customers::create(
    [
        'name'=>$request->name,
        'comp_name'=>$request->name,
        'tax_no'=> $request->TaxـNumber ??0 ,
        "Balance"=> $request->balance??0  ,
        'phone'=>  $request->phone??'05----------'  ,
        'email'=> $request->email??'Email@gmail.com'  ,
        'notes'=>$request->product_notes??"لا توجد ملاحظات ",
        'Limit_credit'=>$request->credit_limit,
        'grace_period_in_days'=>$request->grace_period_in_days,
             'street_name'=>$request->StreetName,
        'building_number'=>$request->buildnumber,
        'plot_identification'=>$request->plot_identification,
        'address'=> $request->city??"Client Address",
        'sub_city'=>$request->sub_city??"Client Address",
        'postcode'=>$request->postcode,

    ]
    );

    $financial_accounts_last=financial_accounts::where('account_type',1)->where('orginal_type',1)->latest()->first();
    financial_accounts::create(
        [
            'name'=>$request->name,
            'account_type'=>1,
            'parent_account_number'=>2,
            'account_number'=>$financial_accounts_last->account_number+1,
            'start_balance'=>0,
            'current_balance'=>0,
            'start_balance_status'=>3,
            'other_table_FK'=>NULL,
             'notes'=>NULL
         
            ,'added_by'=>1,
            'updated_by'=>NULL,
            'com_code'=>1
            ,'date'=>\Carbon\Carbon::now()->addHours(3)
            ,'active'=>1
            ,'is_parent'=>0,
            'start_balance_status'=>3,
            'orginal_id'=> $newcustomer->id,
            'orginal_type'=>1
           
        ]
        );
    

       


           

       if(  $newcustomer!=null){
        $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اضافة العميل بنجاح':'Client added successfully'   ;

        session()->flash('newcustomer',$message);
    
    }
        return view('supProcesses.addnewcustomer');
    }

    public function create_addnewsupplier(Request $request){
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $this->validate($request, [
            'name' => 'required',
            'email' => 'email',
            'phone' => 'required',
            'loction' => 'required',
            'TaxـNumber'=>'required|numeric',
        ]);
               // return $request;

        $supllier=supllier::create(
            [
                'name'=>$request->name,
                'phone'=>$request->phone,
                'comp_name'=>$request->name,
                'email'=>$request->email,
                'location'=>$request->loction,
                'notes'=>$request->notes??"لا توجد",
                'TaxـNumber'=>$request->TaxـNumber
            ]
            );

            $financial_accounts_last=financial_accounts::where('account_type',2)->where('orginal_type',2)->latest()->first();
            financial_accounts::create(
                [
                    'name'=>$request->name,
                    'account_type'=>2,
                    'parent_account_number'=>1,
                    'account_number'=>$financial_accounts_last->account_number+1,
                    'start_balance'=>0,
                    'current_balance'=>0,
                    'start_balance_status'=>3,
                    'other_table_FK'=>NULL,
                     'notes'=>NULL
                  
                    ,'added_by'=>1,
                    'updated_by'=>NULL,
                    'com_code'=>1
                    ,'date'=>\Carbon\Carbon::now()->addHours(3),
                    'active'=>1
                    ,'is_parent'=>0,
                    'start_balance_status'=>3,
                    'orginal_id'=> $supllier->id,
                    'orginal_type'=>2
    
                ]
                );


            if(  $supllier!=null){
                $message=LaravelLocalization::getCurrentLocale()=='ar'?'تم اضافة الموارد بنجاح':'Supplier added successfully'   ;

                session()->flash('addnewsupplier',$message);
            
            }
        //return $request;
        return view('supProcesses.addnewsupplier');
    }

    public function create_addnewsupplierajax(Request $request){
       

        $supllier=supllier::create(
            [
                'name'=>$request->name,
                'phone'=>$request->phone,
                'comp_name'=>$request->name,
                'email'=>$request->email??"supplier@gamil.com",
                'location'=>$request->loction,
                'notes'=>$request->notes??"لا توجد",
                'TaxـNumber'=>$request->TaxـNumber
            ]
            );
     
            $financial_accounts_last=financial_accounts::where('account_type',2)->where('orginal_type',2)->latest()->first();
            financial_accounts::create(
                [
                    'name'=>$request->name,
                    'account_type'=>2,
                    'parent_account_number'=>1,
                    'account_number'=>$financial_accounts_last->account_number+1,
                    'start_balance'=>0,
                    'current_balance'=>0,
                    'start_balance_status'=>3,
                    'other_table_FK'=>NULL,
                     'notes'=>NULL
                  
                    ,'added_by'=>1,
                    'updated_by'=>NULL,
                    'com_code'=>1
                    ,'date'=>\Carbon\Carbon::now()->addHours(3),
                    'active'=>1
                    ,'is_parent'=>0,
                    'start_balance_status'=>3,
                    'orginal_id'=> $supllier->id,
                    'orginal_type'=>2
    
                ]
                );

  

               return  $supllier;
            
         

    }
    
}
