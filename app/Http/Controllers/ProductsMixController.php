<?php

namespace App\Http\Controllers;

use App\Models\products_mix;
use App\Models\products;
use App\Models\products_mix_items;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Models\Avt;
use Carbon\Carbon;

class ProductsMixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getmixproduct($id)
    {
        //

        $products_mix=  products_mix::where('mixcode',$id)->first();

        $orderdetails = products_mix_items::where('products_mix_id', $products_mix->id)->get();
        $ListProducts = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->quantity * $orderitem->Added_Value;
            $totalPrice += $orderitem->quantity * $orderitem->cost;
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->id,
                "quantity" => $orderitem->quantity,
                "purchasingـprice" => $orderitem->cost,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->quantity * $orderitem->Added_Value) + ($orderitem->quantity * $orderitem->cost),
                "orderNo" => $orderitem->products_mix_id,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice,
                'productcode_mix'=>$products_mix->mixcode,
                'product_name_mix'=>$products_mix->name,

            ];
        }
        return $ListProducts;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $avt = Avt::find(2);

        app()->setLocale(LaravelLocalization::getCurrentLocale());
$orderid=0;
        if ($request->orderNo == null) {
            $clientNo = 0;
            $clientNo = $request->clientnamesearch;

            $createorder = products_mix::create(
                [
                    'user_id' => Auth()->user()->id,
                    'created_at'=>date("Y-m-d"),
                    'name'=>$request->mixproductname,
                    'branchs_id'=> Auth()->user()->branchs_id 

                ]
            );
            $orderid=$createorder->id;
            products_mix::find($createorder->id)->update(
                [
                    'mixcode'=>'M001'.$createorder->id,

                ]
            );
            $products_mix=  products_mix::find($orderid);

            products::create(
                [
                    'product_name'=>$request->mixproductname,
                    'name_en'=>$request->mixproductname,
                    'branchs_id'=>$products_mix->branchs_id,
                    'user_id'=>Auth()->User()->id,   
                    'Product_Location'=>"MIX",
                    'Product_Code'=> $products_mix->mixcode,
                    'Status'=>1,
                    'notes'=>"-",
                    'minmum_quantity_stock_alart'=>2,
                    'products_mix'=>$createorder->id,
    
                ]
                );
          
        }
        else{
            $orderid=$request->orderNo;

        }
          $products_mix=  products_mix::find($orderid);
            products_mix::find($orderid)->update(
                [
                    'cost_withoud_tax'=>$request->mixproduct_cost==0?$products_mix->cost_withoud_tax+($request->quentityprice*$request->quentity):$request->mixproduct_cost,

                ]
            );
            $products_mix=  products_mix::find($orderid);

            products::where('products_mix' ,$orderid)->update(
                [
            
                    'purchasingـprice'=>$products_mix->cost_withoud_tax
    
                ]
                );
    

        products_mix_items::create(
            [
                'created_at'=>date("Y-m-d"),
                'note'=>'note',
                'product_id'=>$request->productNo,
                'cost'=>$request->quentityprice ,
                'Added_Value'=>$avt->AVT* $request->quentityprice ,
                'quantity'=>$request->quentity ,
                'products_mix_id'=>$orderid,
            ]
            );


            $products_mix=  products_mix::find($orderid);

        $orderdetails = products_mix_items::where('products_mix_id', $orderid)->get();
        $ListProducts = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->quantity * $orderitem->Added_Value;
            $totalPrice += $orderitem->quantity * $orderitem->cost;
            $count++;
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->id,
                "quantity" => $orderitem->quantity,
                "purchasingـprice" => $orderitem->cost,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->quantity * $orderitem->Added_Value) + ($orderitem->quantity * $orderitem->cost),
                "orderNo" => $orderitem->products_mix_id,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice,
                'productcode_mix'=>$products_mix->mixcode,

            ];
        }
        return $ListProducts;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\products_mix  $products_mix
     * @return \Illuminate\Http\Response
     */
    public function updateproduct_mix_Increase(Request $request)
    {
        //
$orderid=$request->ordernumber;
$item_id=$request->id;
$item=products_mix_items::find($item_id);
products_mix_items::find($item_id)->update(
    [
      
        'quantity'=>$item->quantity+$request->increasequentity ,
        'updated_at' => \Carbon\Carbon::now()->addHours(3),

    ]
    );
            $products_mix=  products_mix::find($orderid);
              products_mix::find($orderid)->update(
                  [
                      'cost_withoud_tax'=>$products_mix->cost_withoud_tax+($request->increasequentity*$item->cost),
                      'updated_at' => \Carbon\Carbon::now()->addHours(3),

                  ]
              );
              $products_mix=  products_mix::find($orderid);

              products::where('products_mix' ,$orderid)->update(
                  [
              
                      'purchasingـprice'=>$products_mix->cost_withoud_tax
      
                  ]
                  );
      
  
  
              $products_mix=  products_mix::find($orderid);

          $orderdetails = products_mix_items::where('products_mix_id', $orderid)->get();
          $ListProducts = [];
          $count = 0;
          $totalAdded_value = 0;
          $totalPrice = 0;
  
          foreach ($orderdetails as $orderitem) {
              $totalAdded_value += $orderitem->quantity * $orderitem->Added_Value;
              $totalPrice += $orderitem->quantity * $orderitem->cost;
              $count++;
              $ListProducts[] = [
                  "count" => $count,
                  "productCode" => $orderitem->productData->Product_Code,
                  "product_name" => $orderitem->productData->product_name,
                  "product_id" => $orderitem->id,
                  "quantity" => $orderitem->quantity,
                  "purchasingـprice" => $orderitem->cost,
                  "Added_Value" => $orderitem->Added_Value,
                  "total" => ($orderitem->quantity * $orderitem->Added_Value) + ($orderitem->quantity * $orderitem->cost),
                  "orderNo" => $orderitem->products_mix_id,
                  "totalAdded_Value" => $totalAdded_value,
                  "totalPrice" => $totalPrice,
                  'productcode'=>$products_mix->mixcode,

              ];
          }
          return $ListProducts;
    }




    public function updateproduct_mix_decrease(Request $request)
    {
        //
$orderid=$request->ordernumber;
$item_id=$request->id;
$item=products_mix_items::find($item_id);
products_mix_items::find($item_id)->update(
    [
      
        'quantity'=>$item->quantity-$request->return_quentity ,
        'updated_at' => \Carbon\Carbon::now()->addHours(3),

    ]
    );
            $products_mix=  products_mix::find($orderid);
              products_mix::find($orderid)->update(
                  [
                      'cost_withoud_tax'=>$products_mix->cost_withoud_tax-($request->return_quentity*$item->cost),
                      'updated_at' => \Carbon\Carbon::now()->addHours(3),

                  ]
              );
          
  
      
              $products_mix=  products_mix::find($orderid);

              products::where('products_mix' ,$orderid)->update(
                  [
              
                      'purchasingـprice'=>$products_mix->cost_withoud_tax
      
                  ]
                  );
      
  
          $products_mix=  products_mix::find($orderid);

          $orderdetails = products_mix_items::where('products_mix_id', $orderid)->get();
          $ListProducts = [];
          $count = 0;
          $totalAdded_value = 0;
          $totalPrice = 0;
  
          foreach ($orderdetails as $orderitem) {
              $totalAdded_value += $orderitem->quantity * $orderitem->Added_Value;
              $totalPrice += $orderitem->quantity * $orderitem->cost;
              $count++;
              $ListProducts[] = [
                  "count" => $count,
                  "productCode" => $orderitem->productData->Product_Code,
                  "product_name" => $orderitem->productData->product_name,
                  "product_id" => $orderitem->id,
                  "quantity" => $orderitem->quantity,
                  "purchasingـprice" => $orderitem->cost,
                  "Added_Value" => $orderitem->Added_Value,
                  "total" => ($orderitem->quantity * $orderitem->Added_Value) + ($orderitem->quantity * $orderitem->cost),
                  "orderNo" => $orderitem->products_mix_id,
                  "totalAdded_Value" => $totalAdded_value,
                  "totalPrice" => $totalPrice,
                  'productcode'=>$products_mix->mixcode,
              ];
          }
          return $ListProducts;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\products_mix  $products_mix
     * @return \Illuminate\Http\Response
     */
    public function edit(products_mix $products_mix)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\products_mix  $products_mix
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, products_mix $products_mix)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\products_mix  $products_mix
     * @return \Illuminate\Http\Response
     */
    public function destroy(products_mix $products_mix)
    {
        //
    }
}
