<?php

namespace App\Http\Controllers;
use App\Models\resource_purchases;
use App\Models\supllier;
use App\Models\products;
use App\Models\orderDetails;
use App\Models\orderTosupllier;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use PDF;

use Illuminate\Http\Request;

class SupllierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $products =products::where('branchs_id',Auth()->User()->branchs_id)->paginate(20) ;

     
       // return $data;
            return  view('products.Purchase_order_of_resources',compact('products'));
        
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\supllier  $supllier
     * @return \Illuminate\Http\Response
     */
    public function show( $supllier)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $customerdata=supllier::find($supllier);

        return   json_encode($customerdata);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\supllier  $supllier
     * @return \Illuminate\Http\Response
     */
     public function printProductToSupllierOrder_pdf( $request)
     {
         //
         app()->setLocale(LaravelLocalization::getCurrentLocale());

         $orderdetails=orderDetails::where('order_owner',$request)->get();
         $resource_purchases=orderTosupllier::where('id',$request)->first();
         $sepllierdata=supllier::find($resource_purchases->suplier_id);
     

        $data =[
            'id'=>$request,
         'pay'=> $resource_purchases->Limit_credit,
         'resource_purchases'=>$resource_purchases,
         'supllierdata'=>$sepllierdata,
         'productsdata'=>$orderdetails
        ];
                 $tran = ['data'=>$data];

        // Get the current date and time.
        $dateTime = now();

        // Generate a unique filename.
        //  return view('pdf.translation', compact('data'));

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.order_purshace_from_supplier', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "Order_No_".$request."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );
     }
 
 
 
 
     
     public function printProductToSupllierOrder(Request $request)
     {
         //
         app()->setLocale(LaravelLocalization::getCurrentLocale());
 //return $request;
 if($request->show_invoice_number==null){
    $products =products::where('branchs_id',Auth()->User()->branchs_id)->paginate(20) ;
    session()->flash('nodataprint', '');

     //return $data;
         return  view('products.Purchase_order_of_resources',compact('products'));
 }
         $orderdetails=orderDetails::where('order_owner',$request->show_invoice_number)->get();
         $resource_purchases=orderTosupllier::where('id',$request->show_invoice_number)->first();
       // return $resource_purchases;
        $sepllierdata=supllier::find($orderdetails[0]->supllier->suplier_id);
        $data=[
         'pay'=> $resource_purchases->Limit_credit,
         'resource_purchases'=>$resource_purchases,
         'supllierdata'=>$sepllierdata,
         'productsdata'=>$orderdetails
        ];
 //return $data;
         return view('supplier.print_order_purchases_to_supplier',compact('data'))->with('order',1) ;
     }
 


    public function edit(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
if($request->orderId==null){
    $products =products::where('branchs_id',Auth()->User()->branchs_id)->paginate(20) ;
    //return $data;
    session()->flash('nodataprint', '');
        return  view('products.purchases',compact('products'));
}
        $orderdetails=orderDetails::where('order_owner',$request->orderId)->get();
        $resource_purchases=resource_purchases::where('orderId',$request->orderId)->first();
       // return $orderdetails;
       $sepllierdata=supllier::find($orderdetails[0]->supllier->suplier_id);
       $data=[
        'pay'=> $resource_purchases->Pay_Method_Name,
        'resource_purchases'=>$resource_purchases,
        'supllierdata'=>$sepllierdata,
        'productsdata'=>$orderdetails
       ];
//return $data;
        return view('supplier.print_products_to_supplier',compact('data'))->with('order',0) ;
    }


    public function prindorderToSupplier( $id)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
//return $request;
if($id==null){
    $data=[

    ];
    //return $data;
        return  view('products.purchases',compact('data'));
}
        $orderdetails=orderDetails::where('order_owner',$id)->get();
        $resource_purchases=resource_purchases::where('orderId',$id)->first();
       // return $orderdetails;
       $sepllierdata=supllier::find($orderdetails[0]->supllier->suplier_id);
       $data=[
        'pay'=> $resource_purchases->Pay_Method_Name,
        'resource_purchases'=>$resource_purchases,
        'supllierdata'=>$sepllierdata,
        'productsdata'=>$orderdetails
       ];
//return $data;
        return view('supplier.print_products_to_supplier',compact('data'))->with('order',0) ;
    }



    public function purchasesShow( $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

    //return $data;

        $orderdetails=orderDetails::where('order_owner',$request)->get();
       // return $orderdetails;
       $resource_purchases=resource_purchases::where('orderId',$request)->first();

       $sepllierdata=supllier::find($resource_purchases->suplier_id);
       $data=[
        'pay'=> $resource_purchases->Pay_Method_Name,
        'supllierdata'=>$sepllierdata,
        'productsdata'=>$orderdetails,
        'resource_purchases'=>$resource_purchases
       ];
//return $data;
        return view('supplier.print_products_to_supplier',compact('data'))->with('order',0) ;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\supllier  $supllier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, supllier $supllier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\supllier  $supllier
     * @return \Illuminate\Http\Response
     */
    public function destroy(supllier $supllier)
    {
        //
    }
}
