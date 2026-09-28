<?php

namespace App\Http\Controllers;

use App\Models\Delivery_product_to_the_customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product_movement_another_branch;
use App\Models\product_movement_another_branch_items;
use App\Models\products;
use App\Models\Avt;
use App\Models\products_mix_items;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

class DeliveryProductToTheCustomerController extends Controller
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
        $data = [];
        return view('products.confirm_delivery_another_branch', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

        return $request;
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

        $Delivery_product_to_the_customer = Delivery_product_to_the_customer::where('invoice_id', $request->invoice_no)->where('status', 0)->where('branch_to', Auth()->user()->branchs_id)->get();
        // return  $Delivery_product_to_the_customer;
        if (count($Delivery_product_to_the_customer) == 0) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? '  لم يتم العثور علي فاتورة بهذة الرقم' : 'No invoice with this number was found';

            session()->flash('notfountreturnproduct', $message);
            $data = [];
            return view('products.confirm_delivery_another_branch', compact('data'));
        } else {
            session()->flash('foundinvoice', '   تم العثور علي فاتورة ');

            $datamain = [
                'branch_from' => $Delivery_product_to_the_customer[0]->branchfrom->name,
                'branch_to' => $Delivery_product_to_the_customer[0]->branchto->name,
                'user_from' => $Delivery_product_to_the_customer[0]->userfrom->name,
                'invoice_id' => $Delivery_product_to_the_customer[0]->invoice_id,
            ];

            // 'quantity'=> $request->quantity,
            // 'status'=>0,
            // 'created_at'=>\Carbon\Carbon::now()->addHours(3),
            $data = [
                'products' => $Delivery_product_to_the_customer,
                'datamain' => $datamain

            ];

            return view('products.confirm_delivery_another_branch', compact('data'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Delivery_product_to_the_customer  $delivery_product_to_the_customer
     * @return \Illuminate\Http\Response
     */
    public function show(Delivery_product_to_the_customer $delivery_product_to_the_customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Delivery_product_to_the_customer  $delivery_product_to_the_customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //


        $avtSaleRate = Avt::find(1);

        $order_id_send = 0;
        $order_id_recive = 0;
        $Delivery_product_to_the_customerData = Delivery_product_to_the_customer::where('invoice_id', $request->invoice_no)->where('status', 0)->where('branch_to', Auth()->user()->branchs_id)->get();
        $Delivery_product_to_the_customer = Delivery_product_to_the_customer::where('invoice_id', $request->invoice_no)->where('status', 0)->where('branch_to', Auth()->user()->branchs_id)->update([
            'status' => 1
        ]);
        // return  $Delivery_product_to_the_customer;
        if ($Delivery_product_to_the_customer == 0) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'عذرا حدث خطا نرجو المحاولة مرة اخري' : 'Sorry, an error occurred, please try again';

            session()->flash('notfountreturnproduct', $message);
            $data = [];
            return view('products.confirm_delivery_another_branch', compact('data'));
        } else {
            foreach ($Delivery_product_to_the_customerData as $product) {
                $productdata = products::find($product->product_id);

                if ($order_id_send == 0) {

                    $product_movement_another_branch = product_movement_another_branch::create(
                        [
                            'branch_from' => Auth()->user()->branchs_id,
                            'branch_to' => $product->branch_from,
                            'user_from' => Auth()->user()->id,
                            'reciveInvoiceNumber' => 10,
                            'user_to' => $product->user_from,
                            'Totalcost' => $productdata->purchasingـprice * $product->quantity,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),

                        ]
                    );



                    $product_movement_another_branch_items = product_movement_another_branch_items::create(
                        [
                            'order_id' => $product_movement_another_branch->id,
                            'product_id' => $product->product_id,
                            'quantity' => $product->quantity,
                            'cost_per_each_withoud_tax' => $productdata->purchasingـprice,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        ]
                    );

                
                    if($productdata->products_mix==0){
                        products::where('id', $product->product_id)->Update([
                    
                            'numberofpice' => $productdata->numberofpice - $product->quantity,
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
                                

                    $order_id_send = $product_movement_another_branch->id;
                } else {
                    $product_movement_another_branch_data = product_movement_another_branch::find($order_id_send);
                    $product_movement_another_branch = product_movement_another_branch::where('id', $order_id_send)->update(
                        [

                            'Totalcost' => $product_movement_another_branch_data->Totalcost + ($productdata->purchasingـprice * $product->quantity),
                            'created_at' => \Carbon\Carbon::now()->addHours(3),

                        ]
                    );
                 
                    $product_movement_another_branch_items = product_movement_another_branch_items::create(
                        [
                            'order_id' => $order_id_send,
                            'product_id' => $product->product_id,
                            'quantity' => $product->quantity,
                            'cost_per_each_withoud_tax' => $productdata->purchasingـprice,
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        ]
                    );

                    $productdata = products::find($product->product_id);
                    if($productdata->products_mix==0){
                        products::where('id', $product->product_id)->Update([

                            'numberofpice' => $productdata->numberofpice - $product->quantity,
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
                
                }
            }

            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تسجيل عميلة التسليم بنجاح' : 'Delivery customer registered successfully';

            session()->flash('foundinvoice', $message);

            $data = [];
            return view('products.confirm_delivery_another_branch', compact('data'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Delivery_product_to_the_customer  $delivery_product_to_the_customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Delivery_product_to_the_customer $delivery_product_to_the_customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Delivery_product_to_the_customer  $delivery_product_to_the_customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delivery_product_to_the_customer $delivery_product_to_the_customer)
    {
        //
    }
}
