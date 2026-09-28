<?php

namespace App\Http\Controllers;

use App\Models\product_movement_another_branch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Models\product_movement_another_branch_items;
use App\Models\products;

class ProductMovementAnotherBranchController extends Controller
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

        return view('supProcesses.send_product_to_branch');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        // return $request;
        $product = products::find($request->product_no);
        if ($request->order_id == 0) {
            $product_movement_another_branch = product_movement_another_branch::create(
                [
                    'branch_from' => Auth()->user()->branchs_id,
                    'branch_to' => $request->branch,
                    'user_from' => Auth()->user()->id,
                    'reciveInvoiceNumber' => $request->reciveInvoiceNumber,
                    'user_to' => $request->userfrom,
                    'Totalcost' => $request->thecostProduct * $request->quentity,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );
            $product_movement_another_branch_items = product_movement_another_branch_items::create(
                [
                    'order_id' => $product_movement_another_branch->id,
                    'product_id' => $request->product_no,
                    'quantity' => $request->quentity,
                    'cost_per_each_withoud_tax' => $request->thecostProduct,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $product = products::find($request->product_no);
            products::where('id', $request->product_no)->Update([

                'numberofpice' => $product->numberofpice - $request->quentity,
            ]);
            $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $product_movement_another_branch->id)->get();
            $orderDate = [];
            $count = 0;
            foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
                $count++;

                $product = products::find($product_movement_another_branch_item->product_id);

                $dataitems[] = [
                    "count" => $count,
                    'productname' => $product->product_name,
                    "product_code" => $product->Product_Code,
                    "details_items_no" => $product_movement_another_branch_item->id,
                    'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                    'quantity' => $product_movement_another_branch_item->quantity,
                    'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

                ];
            }

            $data = [
                "orderData" => $product_movement_another_branch,
                "orderItems" => $dataitems
            ];
            return $data;
        } else {
            $product_movement_another_branch_data = product_movement_another_branch::find($request->order_id);
            $product_movement_another_branch = product_movement_another_branch::where('id', $request->order_id)->update(
                [

                    'Totalcost' => $product_movement_another_branch_data->Totalcost + ($request->thecostProduct * $request->quentity),
                    'created_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );
            $product_movement_another_branch_items = product_movement_another_branch_items::create(
                [
                    'order_id' => $request->order_id,
                    'product_id' => $request->product_no,
                    'quantity' => $request->quentity,
                    'cost_per_each_withoud_tax' => $request->thecostProduct,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $product = products::find($request->product_no);
            products::where('id', $request->product_no)->Update([

                'numberofpice' => $product->numberofpice - $request->quentity,
            ]);
            $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $request->order_id)->get();

            $dataitems = [];
            $count = 0;
            foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
                $count++;

                $product = products::find($product_movement_another_branch_item->product_id);

                $dataitems[] = [
                    "count" => $count,
                    'productname' => $product->product_name,
                    "product_code" => $product->Product_Code,
                    "details_items_no" => $product_movement_another_branch_item->id,
                    'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                    'quantity' => $product_movement_another_branch_item->quantity,
                    'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

                ];
            }

            $data = [
                "orderData" => $product_movement_another_branch_data,
                "orderItems" => $dataitems
            ];
            return $data;
        }
    }




    public function reciveNewF(Request $request)
    {
        
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        // return $request;
        $product = products::find($request->product_no);
        if ($request->order_id == 0) {
            $product_movement_another_branch = product_movement_another_branch::create(
                [
                    'branch_from' => Auth()->user()->branchs_id,
                    'branch_to' => $request->branch,
                    'user_from' => Auth()->user()->id,
                    'reciveInvoiceNumber' => $request->send_invoice_no,
                    'user_to' => $request->userfrom,
                    'Totalcost' => $request->thecostProduct * $request->quentity,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'send_invoice_number'=>$request->send_invoice_no,

                ]
            );
            $product_movement_another_branch_items = product_movement_another_branch_items::create(
                [
                    'order_id' => $product_movement_another_branch->id,
                    'product_id' => $request->product_no,
                    'quantity' => $request->quentity,
                    'cost_per_each_withoud_tax' => $request->thecostProduct,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $product = products::find($request->product_no);
            products::where('id', $request->product_no)->Update([

                'numberofpice' => $product->numberofpice + $request->quentity,
                'purchasingـprice'=>$request->thecostProduct
            ]);
            $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $product_movement_another_branch->id)->get();
            $orderDate = [];
            $count = 0;
            foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
                $count++;

                $product = products::find($product_movement_another_branch_item->product_id);

                $dataitems[] = [
                    "count" => $count,
                    'productname' => $product->product_name,
                    "product_code" => $product->Product_Code,
                    "details_items_no" => $product_movement_another_branch_item->id,
                    'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                    'quantity' => $product_movement_another_branch_item->quantity,
                    'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

                ];
            }

            $data = [
                "orderData" => $product_movement_another_branch,
                "orderItems" => $dataitems
            ];
            return $data;
        } else {
            $product_movement_another_branch_data = product_movement_another_branch::find($request->order_id);
            $product_movement_another_branch = product_movement_another_branch::where('id', $request->order_id)->update(
                [

                    'Totalcost' => $product_movement_another_branch_data->Totalcost + ($request->thecostProduct * $request->quentity),
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'send_invoice_number'=>$request->send_invoice_no,
                    'reciveInvoiceNumber'=>$request->send_invoice_no,


                ]
            );
            $product_movement_another_branch_items = product_movement_another_branch_items::create(
                [
                    'order_id' => $request->order_id,
                    'product_id' => $request->product_no,
                    'quantity' => $request->quentity,
                    'cost_per_each_withoud_tax' => $request->thecostProduct,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );
            $product = products::find($request->product_no);
            products::where('id', $request->product_no)->Update([

 'numberofpice' => $product->numberofpice + $request->quentity,
                'purchasingـprice'=>$request->thecostProduct
                ]);
            $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $request->order_id)->get();

            $dataitems = [];
            $count = 0;
            foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
                $count++;

                $product = products::find($product_movement_another_branch_item->product_id);

                $dataitems[] = [
                    "count" => $count,
                    'productname' => $product->product_name,
                    "product_code" => $product->Product_Code,
                    "details_items_no" => $product_movement_another_branch_item->id,
                    'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                    'quantity' => $product_movement_another_branch_item->quantity,
                    'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

                ];
            }

            $data = [
                "orderData" => $product_movement_another_branch_data,
                "orderItems" => $dataitems
            ];
            return $data;
        }
    }






    public function findinvoiceMovmevt($id)
    {
        $result = product_movement_another_branch::find($id);
        $data = [
            'barnch_id' => $result->branchfrom->id,
            'barnch_name' => $result->branchfrom->name,
            'user_id' => $result->userfrom->id,
            'user_name' => $result->userfrom->name,

        ];
        $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $id)->get();
        $dataitems = [];
        $count = 0;
        foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
            $count++;

            $product = products::find($product_movement_another_branch_item->product_id);

            $dataitems[] = [
                "count" => $count,
                'productname' => $product->product_name,
                "product_code" => $product->Product_Code,
                "details_items_no" => $product_movement_another_branch_item->id,
                'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                'quantity' => $product_movement_another_branch_item->quantity,
                'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

            ];
        }
        $data = [
            'senderdata' => $data,
            'orderItems' => $dataitems
        ];
        return $data;
    }
    public function store(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        if ($request->invoiceId == '-') {
            session()->flash('nodataprint', '');

            return view('supProcesses.recive_product_from_another_branch');
        }
        $notregisterproductCount = 0;
        //
        $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $request->invoiceId)->get();
        foreach ($product_movement_another_branch_items as $item) {
            $productrecive = products::find($item->product_id);
            $updateProduct = products::where('branchs_id', Auth()->user()->branchs_id)->where('Product_Code', $productrecive->Product_Code)->first();
            if ($updateProduct != null) {
                products::where('branchs_id', Auth()->user()->branchs_id)->where('Product_Code', $updateProduct->Product_Code)->Update([
                    'numberofpice' => $updateProduct->numberofpice + $item->quantity,
                    'purchasingـprice' => $item->cost_per_each_withoud_tax,


                ]);
            } else {
                $notregisterproductCount++;


                $newproducts = products::create(
                    [
                        'product_name' => $productrecive->product_name,
                        'name_en' => $productrecive->name_en,
                        'branchs_id' => Auth()->user()->branchs_id,
                        'numberofpice' => $item->quantity,
                        'user_id' => Auth()->User()->id,
                        'Product_Location' => 'Transfer',
                        'Product_Code' => $productrecive->Product_Code,
                        'purchasingـprice' => $item->cost_per_each_withoud_tax,
                        'sale_price' => 0,
                        'Status' => 1,
                        'notes' => $productrecive->notes,
                        'unit' => $productrecive->unit,
                        'minmum_quantity_stock_alart' => $productrecive->minmum_quantity_stock_alart,
                    ]
                );
            }
        }


        product_movement_another_branch::where('id', $request->invoiceId)->update(
            [

                'reciveInvoiceNumber' => 10,
                'created_at' => \Carbon\Carbon::now()->addHours(3),

            ]
        );
        if ($notregisterproductCount > 0) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم تزويد المخزون بمنتجات المستلمة بنجاح وتحديث سعر التكلفة. و تم انشاء " . $notregisterproductCount . " منتجات لم يتم عثور علي ارقمهم واضافة الكمية و سعر التكلفة بنجاح وشكرا" : "Inventory has been replenished with successfully received products and the cost price has been updated. And has been created" . $notregisterproductCount . "Products whose number was not found and the quantity and cost price were added successfully, thank you.";
            session()->flash('createnewproduct', $message);
        } else {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? "تم تزويد المخزون بمنتجات المستلمة بنجاح وتحديث سعر التكلفة. " : "Inventory has been replenished with successfully received products and the cost price has been updated.";
            session()->flash('confirmed', $message);
        }
        $data = [
            'mesage' => $message,
            'invoice_number' => $request->invoiceId,
            'invoices_Reciepts' => product_movement_another_branch::where('branch_to', Auth()->user()->branchs_id)->where('reciveInvoiceNumber', 1)->get()
        ];

        return $data;
        // return view('supProcesses.recive_product_from_another_branch',compact('data'));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\product_movement_another_branch  $product_movement_another_branch
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = 0;
        return view('supProcesses.recive_product_from_another_branch', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\product_movement_another_branch  $product_movement_another_branch
     * @return \Illuminate\Http\Response
     */
    public function print_Transfer_items(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        if ($request->sprint_invoice_number == null) {
            session()->flash('nodataprint', '');
            return view('supProcesses.send_product_to_branch');
        }
        $product_movement_another_branch_data = product_movement_another_branch::find($request->sprint_invoice_number);
        $items = product_movement_another_branch_items::where('order_id', $product_movement_another_branch_data->id)->get();
        $data = [
            "invoice" => $product_movement_another_branch_data,
            "itemsdetails" => $items
        ];
        return view('supProcesses.print_send_product', compact('data'));
    }
    public function print_Recive_items(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        if ($request->sprint_invoice_number == null) {
            session()->flash('nodataprint', '');
            return view('supProcesses.recive_product_from_another_branch');
        }
        $product_movement_another_branch_data = product_movement_another_branch::find($request->sprint_invoice_number);
        $items = product_movement_another_branch_items::where('order_id', $product_movement_another_branch_data->id)->get();
        $data = [
            "invoice" => $product_movement_another_branch_data,
            "itemsdetails" => $items
        ];
        return view('supProcesses.print_recive_product', compact('data'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product_movement_another_branch  $product_movement_another_branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, product_movement_another_branch $product_movement_another_branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\product_movement_another_branch  $product_movement_another_branch
     * @return \Illuminate\Http\Response
     */
     public function deleteproduct($id){
         
         app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        $item = product_movement_another_branch_items::find($id);
        $order = product_movement_another_branch::find($item->order_id);
        $product = products::find($item->product_id);
        products::find($item->product_id)->Update([

            'numberofpice' => $product->numberofpice - $item->quantity,
        ]);
        product_movement_another_branch::where('id', $item->order_id)->Update(
            [

                'Totalcost' => $order->Totalcost - ($item->cost_per_each_withoud_tax * $item->quantity),
                'created_at' => \Carbon\Carbon::now()->addHours(3),

            ]
        );
        $product_movement_another_branch_items = product_movement_another_branch_items::where('id', $id)->first();
        $product_movement_another_branch_items->delete();

        $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $order->id)->get();
        $count = 0;
        $dataitems = [];
        foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
            $count++;

            $product = products::find($product_movement_another_branch_item->product_id);

            $dataitems[] = [
                "count" => $count,
                'productname' => $product->product_name,
                "product_code" => $product->Product_Code,
                "details_items_no" => $product_movement_another_branch_item->id,
                'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                'quantity' => $product_movement_another_branch_item->quantity,
                'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

            ];
        }

        $data = [
            "orderData" => $order,
            "orderItems" => $dataitems
        ];
        return $data;  
         
     }
    public function destroy($product_movement_another_branch)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        $item = product_movement_another_branch_items::find($product_movement_another_branch);
        $order = product_movement_another_branch::find($item->order_id);
        $product = products::find($item->product_id);
        products::find($item->product_id)->Update([

            'numberofpice' => $product->numberofpice + $item->quantity,
        ]);
        product_movement_another_branch::where('id', $item->order_id)->Update(
            [

                'Totalcost' => $order->Totalcost - ($item->cost_per_each_withoud_tax * $item->quantity),
                'created_at' => \Carbon\Carbon::now()->addHours(3),

            ]
        );
        $product_movement_another_branch_items = product_movement_another_branch_items::where('id', $product_movement_another_branch)->first();
        $product_movement_another_branch_items->delete();

        $product_movement_another_branch_items = product_movement_another_branch_items::where('order_id', $order->id)->get();
        $count = 0;
        $dataitems = [];
        foreach ($product_movement_another_branch_items as $product_movement_another_branch_item) {
            $count++;

            $product = products::find($product_movement_another_branch_item->product_id);

            $dataitems[] = [
                "count" => $count,
                'productname' => $product->product_name,
                "product_code" => $product->Product_Code,
                "details_items_no" => $product_movement_another_branch_item->id,
                'cost' => $product_movement_another_branch_item->cost_per_each_withoud_tax,
                'quantity' => $product_movement_another_branch_item->quantity,
                'total' => $product_movement_another_branch_item->quantity * $product_movement_another_branch_item->cost_per_each_withoud_tax,

            ];
        }

        $data = [
            "orderData" => $order,
            "orderItems" => $dataitems
        ];
        return $data;
    }
}
