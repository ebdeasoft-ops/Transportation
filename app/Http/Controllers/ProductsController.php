<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\supllier;
use PDF;
use App\Models\financial_accounts;

use App\Models\resource_purchases;
use App\Models\customers;
use App\Models\sales;
use App\Models\User;
use App\Models\credittransactions;
use App\Models\Avt;
use App\Models\order_price_from_supplier;
use App\Models\order_price_from_supplier_items;
use Illuminate\Http\Request;
use App\Models\orderDetails;
use App\Models\orderTosupllier;
use Carbon\Carbon;
use App\Models\offer_price_to_customer_items;
use App\Models\offer_price_to_customer;
use App\Models\invoices;
use App\Models\return_sales;
use App\Models\product_movement_another_branch_items;
use App\Models\product_movement_another_branch;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

class ProductsController extends Controller
{
   
    public function clientnamesearch(Request $request)
    {
        return customers::where('name', 'like', '%' . $request->q . '%')->orwhere('tax_no', 'like', '%' . $request->q . '%')->orwhere('phone', 'like', '%' . $request->q . '%')
            ->limit(20)
            ->get(['id', 'name', 'tax_no']);
    } 
        public function save_invoice_qutation(Request $request)
    {


        $customertid = $request->clientnamesearch;


        $avt = Avt::find(1);

        //  return $request;
        if ($request->show_invoice_number_update == 0) {
            $create_order = offer_price_to_customer::create(
                [
                    'customer_id' => $customertid,
                    'branchs_id' => Auth()->User()->branchs_id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'notes' => $request->notes,
                    'converted'  => $request->convert,
                    'waiting' => $request->waiting,
                    'note1' => $request->note1,
                    'note2' => $request->note2,
                    'note3' => $request->note3,
                    'note4' => $request->note4,
                    'note5' => $request->note5,
                    'note6' => $request->note6,
                    'payment_per_day' => $request->payment_per_day,
                    'discount' =>0,
                    'numbershowstatus' => $request->shownumberproduct,


                ]
            );

        } else {
            $create_order = offer_price_to_customer::find($request->show_invoice_number_update);
            offer_price_to_customer_items::where('order_id', $request->show_invoice_number_update)->delete();
            offer_price_to_customer::find($request->show_invoice_number_update)->update(
                [
                    'customer_id' => $customertid,
                    'branchs_id' => Auth()->User()->branchs_id,
                     'note1' => $request->note1,
                    'note2' => $request->note2,
                    'note3' => $request->note3,
                    'note4' => $request->note4,
                    'note5' => $request->note5,
                    'note6' => $request->note6,
                    'note7' => $request->note7,
                    'note8' => $request->note8,
                    'payment_per_day' => $request->payment_per_day,
                    'notes' => $request->notes,
                    'p_o' => $request->p_o,
                    'type' => $request->typeedecument,
                    'discount' => 0,
                    'convert' => $request->convert,
                    'waiting' => $request->waiting,
                    'numbershowstatus' => $request->shownumberproduct,



                ]
            );
        }
        foreach ($request->products as $sale) {
            $create_order_price_from_supplier_items = offer_price_to_customer_items::create(
                [
                    'notes' => $request->notes,
                    'product_id' => 1,
                    'loading' => $sale['loading'],
                    "quantity" => $sale['quentity'],
                    "PriceWithoudTax" => $sale['price'],
                    "Unloading" => $sale['Unloading'],
                    "discount" => $sale['discound'],
                    "note" => $sale['notesitem'],
                    "truck_type" => $sale['truck_type'],
                    'order_id' => $create_order->id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),


                ]
            );

        }




        return $create_order->id;

    }



    public function updateofficebyidforupdate($invoiceNumber)
    {

        $InvoiceData = offer_price_to_customer::find($invoiceNumber);



        //return $product;
        $allProdctsD = [];
        $i = 0;
        if ($InvoiceData != null) {
            $products = offer_price_to_customer_items::where('order_id', $invoiceNumber)->get();


            $allProdctsD = [];
            $i = 0;
            foreach ($products as $product) {
                $i++;
                $updateProduct = products::find($product->product_id);




                $allProdctsD[] = [
                    'loading' => $product->loading,
                    'truck_type' => $product->truck_type,
                    'Unloading' => $product->Unloading,
                    'quantity' => $product->quantity,
                    'Unit_Price' => $product->PriceWithoudTax,
                    'Discount_Value' => $product->discount,
                    'Added_Value' => 0,
                    'count' => $i,
                    'id' => $product->id
                ];

            }






            $customer = customers::find($InvoiceData->customer_id);

            $data = [
                'invoice_number' => $InvoiceData->id,
                'customer' => $customer,
                'product' => $allProdctsD,
                "invoice_id" => $InvoiceData->id
            ];



            return ($data);
        }
        return 0;


    }

 
    public function ChooseProductpaginatenewupdate(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
       $products = [];
       $searchtext= $request->searchtext;
       $branchs_id=$request->branchs_id;
     if ($branchs_id == '-') {
            $data = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('notes', 'LIKE', '%' . $searchtext . '%')->paginate(20);
            return view('ajax_choose_product', compact('data'));
        }
        $data =products::where('branchs_id', $branchs_id)->where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->where('branchs_id', $branchs_id)->paginate(20);
        return view('ajax_choose_product', compact('data'));
    }
    
     public function getallpurshasesfromsupplier($id)
    {
        $data= orderDetails::where('product_id', $id)->where('save', 1)->orderBy('id', 'desc')->paginate(5);
        $data_supplier=[];
        foreach(   $data as $purchases_item){
            
                    $data_supplier[]=[
                        'invoiceid'=>$purchases_item->order_owner ,
                        'date'=>substr($purchases_item->created_at,0,10),
                        'supplier_name'=>$purchases_item->supllier->supllier->name,
                        'cost'=>$purchases_item->purchasingـprice,
                        'quantity'=>$purchases_item->numberofpice,
                        ];

        }
        return $data_supplier;

    }
    
       public function  generate_pdf_qoute($request) {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $avtSaleRate = Avt::find(1);
        $itemsRequest =  offer_price_to_customer_items::where('order_id', $request)->get();

        $tran = ['itemsRequest'=>$itemsRequest];
        // Get the current date and time.
        $dateTime = now();

        // Generate a unique filename.
        //  return view('pdf.translation', compact('data'));

        $fileName = $dateTime->format('Y-m-d H:i:s') ;
        $html = view('pdf.qutation', $tran)->toArabicHTML();
        
        $pdf = PDF::loadHTML($html)->output();
        
        //Generate the pdf file
        $headers = array(
            "Content-type" => "application/pdf",
        );
        
        // Create a stream response as a file download
        return response()->streamDownload(
            fn () => print($pdf), // add the content to the stream
            "Quote_No_".$request."_". $fileName.".pdf", // the name of the file/stream
            $headers
        );


       

    
    }

    
    
    
    function uploadImage($folder, $image)
{
$extension = strtolower($image->extension());
$filename = time() . rand(100, 999) . '.' . $extension;
$image->getClientOriginalName = $filename;
$image->move($folder, $filename);
return $filename;
}

  public function uploadfilepurchases(Request $request){
      
      if ($request->has('attachments')) {
$request->validate([
'attachments' => 'required|mimes:png,jpg,jpeg,pdf|max:2000',
]);
$the_file_path = $this->uploadImage('assets//attachments', $request->attachments);
$photo =$the_file_path;

resource_purchases::where('orderId', $request->orderidpurchase)->update(['attachments'=>$photo]);

}


 app()->setLocale(LaravelLocalization::getCurrentLocale());
       $data = resource_purchases::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices_purchases', compact('data'));
    }

    public function openfilefile($path){
          return redirect(asset('/assets/attachments').'/'.$path);

        return response()->file(asset('/assets/attachments').'/'.$path);

    }
    
    public function detproductbycode($code,$branch){
    $product=products::where('Product_Code',$code)->where('branchs_id', $branch)->first();
    if($product!=null)
    {
        return $product;
    }
    return 0;
}


       public function updatequtation($id){
        $avt = Avt::find(1);

        $itemsRequest =  offer_price_to_customer_items::where('order_id', $id)->get();
        $ListProducts = [];
        $count = 0;
     $itemsRequest =  offer_price_to_customer_items::where('order_id', $id)->get();
     $offer_price_to_customer=offer_price_to_customer::find($id);

        $ListProducts = [];
        $count = 0;
        foreach ($itemsRequest as $item) {
            $count++;
            $product = products::find($item->product_id);
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $product->Product_Code,
                "productName" => $product->product_name,
                "sale_price" => $item->PriceWithoudTax,
                "discount" => $item->discount,
                "order_id" => $item->order_id,
                'id'=>$item->id,
                "quantity" => $item->quantity,
                "added_value" => $avt->AVT * $item->PriceWithoudTax,
                'totaldiscount'=>$offer_price_to_customer->discount



            ];
        }
        return $ListProducts;

    }
    public function replaceproducts($branchs_id,$productId)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
       
        $data= products::where('branchs_id', $branchs_id)->where('main_product',$productId)->paginate();
        return view('ajax_choose_product_replace2', compact('data'));
    }

public function operationproducts($branchs_id,$productId)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        {
           {
                $orderDetails = orderDetails::where('product_id', $productId)->where('save', 1)->get();

                $sales = sales::where('product_id', $productId)->where('quantity', '!=', 0)->where('save', 1)->get();  
                $return_sales = return_sales::where('product_id', $productId)->get();
                $product_movement_another_branch_items = product_movement_another_branch_items::where('product_id', $productId)->get();
            }
        }

$count=0;
$products=[];
foreach($product_movement_another_branch_items as $item ){
    $invoice=product_movement_another_branch::find($item->order_id);

    $products[]=[
        'id'=>$item->order_id,
        'Product_Code'=>$item->product->Product_Code,
        'product_name'=>$item->product->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->quantity,
        'price'=>$item->cost_per_each_withoud_tax,
        'operation'=>$invoice->reciveInvoiceNumber==0?  __('home.send_product_from_brance')  : __('home.recive_product_from_other_branch_other'),
        'type'=>3,
        'man'=>$invoice->branchto->name   ,

    ];
}
foreach($return_sales as $item ){
    $invoice=invoices::find($item->invoice_id);

    $products[]=[
        'id'=>$item->invoice_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->return_quantity,
        'price'=>$item->return_Unit_Price,
        'operation'=> __('home.salesـreturned'),
        'type'=>1,
        'man'=>$invoice->customer->name   ,

    ];
}
foreach($sales as $item ){
    $invoice=invoices::find($item->invoice_id);

    $products[]=[
        'id'=>$item->invoice_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->quantity,
        'price'=>$item->Unit_Price,
        'operation'=> __('home.sales'),
        'type'=>2,
                'man'=>$invoice->customer->name   ,

    ];
}

foreach($orderDetails as $item ){
        $invoice=orderTosupllier::find($item->order_owner);
if($item->returns_purchase>0){
    $products[]=[
        'id'=>$item->invoice_id,
        'Product_Code'=>$item->productData->Product_Code,
        'product_name'=>$item->productData->product_name,
        'created_at'=>$item->updated_at,
        'quantity'=>$item->returns_purchase,
        'price'=>$item->purchasingـprice,
        'operation'=> __('home.purchase_return'),
        'type'=>2,
        'man'=>$invoice->supllier->name   ,

    ];
}
}
foreach($orderDetails as $item ){
        $invoice=orderTosupllier::find($item->order_owner);

    $products[]=[
        'id'=>$item->order_owner,
        'Product_Code'=>$item->product_id,
        'product_name'=>$item->product_name,
        'created_at'=>$item->created_at,
        'quantity'=>$item->numberofpice,
        'price'=>$item->purchasingـprice,
        'man'=>$invoice->supllier->name   ,

        'operation'=> __('home.purchases'),
                'type'=>1
    ];
}

$dates = array();
foreach ($products as $key => $row) {
   $dates[$key] = strtotime($row['created_at']);
}
array_multisort($dates, SORT_ASC, $products);
        return view('ajax_choose_product_replace', compact('products'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $allcustomers = customers::get();
        $allproduct = products::where('branchs_id', Auth()->User()->branchs_id)->get();
        $data = [
            "allcustomers" =>  $allcustomers,
            "allproduct" => $allproduct
        ];
        //return $data;
        return  view('products.transactions', compact('data'));
    }


     public function updateproductallDataofferprice(Request $request){
        $avt = Avt::find(1);
        $offer_price_to_customer_items=offer_price_to_customer_items::find($request->id);
        offer_price_to_customer_items::find($request->id)->update([
            'PriceWithoudTax'=>$request->price ,
            'quantity'=>$request->quentity,
            'discount'=>$request->discount
        ]);

        $itemsRequest =  offer_price_to_customer_items::where('order_id', $offer_price_to_customer_items->order_id)->get();
                    $offer_price_to_customer=offer_price_to_customer::find( $offer_price_to_customer_items->order_id);

        $ListProducts = [];
        $count = 0;
        foreach ($itemsRequest as $item) {
            $count++;
            $product = products::find($item->product_id);
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $product->Product_Code,
                "productName" => $product->product_name,
                "sale_price" => $item->PriceWithoudTax,
                "discount" => $item->discount,
                "order_id" => $item->order_id,
                'id'=>$item->id,

                "quantity" => $item->quantity,
                "added_value" => $avt->AVT * $item->PriceWithoudTax,
                 'totaldiscount'=>$offer_price_to_customer->discount



            ];
        }
        return $ListProducts;    }
         public function deleteitem( $id){
        $avt = Avt::find(1);
        $offer_price_to_customer_items=offer_price_to_customer_items::find($id);
        offer_price_to_customer_items::find($id)->delete();

        $itemsRequest =  offer_price_to_customer_items::where('order_id', $offer_price_to_customer_items->order_id)->get();
        $offer_price_to_customer=offer_price_to_customer::find($offer_price_to_customer_items->order_id);

        $ListProducts = [];
        $count = 0;
        foreach ($itemsRequest as $item) {
            $count++;
            $product = products::find($item->product_id);
            $ListProducts[] = [
                "count" => $count,
                "productCode" => $product->Product_Code,
                "productName" => $product->product_name,
                "sale_price" => $item->PriceWithoudTax,
                "discount" => $item->discount,
                "order_id" => $item->order_id,
                'id'=>$item->id,

                "quantity" => $item->quantity,
                "added_value" => $avt->AVT * $item->PriceWithoudTax,
                'totaldiscount'=>$offer_price_to_customer->discount



            ];
        }
        return $ListProducts;    }

    public function showAllProducts()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('showAllProducts');
    }
    public function ShowAllNotifications()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $data;
        return  view('Notifications_Products');
    }
    public function searchaboutinvoiceByIdfunctionpurchases($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = resource_purchases::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('orderId', $date)->orwhere('Purchase_invoice_number', $date)->paginate(20);
        return view('ajax_Recent_Invoices_purchases', compact('data'));
    }




 public function getinvoicesbyspplluer($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = resource_purchases::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('suplier_id', $date)->paginate(20);
        return view('ajax_Recent_Invoices_purchases', compact('data'));
    }
    
    
    public function product_mix()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $products =products::where('branchs_id',Auth()->User()->branchs_id)->paginate(20) ;
        return  view('supProcesses.product_mix',compact('products'));
    }
    
    public function profile()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $data;
        return  view('profile.show');
    }
        public function getAllinvicesapurchasesjax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = resource_purchases::where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->orderby('id', 'desc')->paginate(20);
        return view('ajax_Recent_Invoices_purchases', compact('data'));
    }

    public function previousPurchasesInvoices()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $data;
        return  view('previousPurchasInvoicesNew');
    }





    public function previousSalesInvoices()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

       $data=invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',0)->paginate(20);
        return  view('previousSalesInvoices',compact(('data')));
    }
    public function getAllinvicesajax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data=invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',0)->orderby('id','desc')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }
    public function searchAllInvoicespaginatenew($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',0)->where('created_at', 'LIKE', '%' . $date . '%')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }
    public function searchaboutinvoiceByIdfunction($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',0)->where('id', $date )->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }

   public function getinvoicesbycustomer($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',0)->where('customer_id', $date )->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }
    public function searchaboutReciptByIdfunction($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',1)->where('id', $date )->paginate(20);
        return view('ajax_Recent_Reciepts', compact('data'));
    }
    public function getAllRecieptsjax()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data=invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',1)->orderby('id','desc')->paginate(20);
        return view('ajax_Recent_Reciepts', compact('data'));
    }
    public function searchAllRecieptspaginatenew($date)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = invoices::where('branchs_id',Auth()->user()->branchs_id)->where('save',1)->where('status',1)->where('created_at', 'LIKE', '%' . $date . '%')->paginate(20);
        return view('ajax_Recent_Invoices', compact('data'));
    }

    public function previousRecieptInvoices()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $data;
        return  view('previousRecieptInvoices');
    }


    /**
     * Show the form for creating a new resource.
     *
     * 
     * 
     * @return \Illuminate\Http\Response
     */

    public function changePaymethodIPurchases($orderId, $paymentMethod)
    {

        $resource_purchases = resource_purchases::where('orderId', $orderId)->first();
        $order_tosuplliers = orderTosupllier::find($orderId);
        // if ($resource_purchases->Pay_Method_Name == "Credit") {
        //     $supllier = supllier::find($order_tosuplliers->suplier_id);
        //     supllier::where('id', $order_tosuplliers->suplier_id)->update(
        //         [
        //             'In_debt' => $supllier->In_debt - $resource_purchases->In_debt,
        //         ]
        //     );
        // }


        resource_purchases::where('orderId', $orderId)->update(
            [
                'Pay_Method_Name' => $paymentMethod,

            ]
        );
        orderTosupllier::where('id', $orderId)->update(
            [
                'Limit_credit' => $paymentMethod,

            ]
        );


        // if ($paymentMethod == "Credit") {
        //     $supllier = supllier::find($order_tosuplliers->suplier_id);
        //     supllier::where('id', $order_tosuplliers->suplier_id)->update(
        //         [
        //             'In_debt' => $supllier->In_debt + $resource_purchases->In_debt,
        //         ]
        //     );
        // }
        return 'Done';
    }

public function makeTotalDiscontOferprice($id,$discountvalue){
    
    offer_price_to_customer::find($id)->update([
        'discount'=>$discountvalue
        ]);
          $offer_price_to_customer=offer_price_to_customer::find($id);
          $itemsRequest =  offer_price_to_customer_items::where('order_id', $id)->get();
            $totalsale_price=0;
            $totaldiscount=0;
            $totaladdedvalue=0;
            $count = 0;
            $totaldiscount=+$offer_price_to_customer->discount;

            foreach ($itemsRequest as $item) {
                $product = products::find($item->product_id);
                
            $totalsale_price=$totalsale_price+($item->PriceWithoudTax*$item->quantity);
            $totaldiscount=$totaldiscount+($item->discount);
                
                
               
            }
            
        $avt = Avt::find(1);
       $totaladdedvalue=($totalsale_price-$totaldiscount)*$avt->AVT;


return [
    'total_purchases_AddedValue'=>round($totaladdedvalue,2)  ,
    'totaldiscount'=> round($totaldiscount,2) ,
    'total_purchases'=>round($totalsale_price,2),
    'totalafterdiscount'=>round($totalsale_price-$totaldiscount,2)
    ];
            
            
    
}

    public function set_customer_quotation($orderid,$customerid)
{
  offer_price_to_customer::find($orderid)->update(
                [
                    'customer_id' => $customerid,
                ]);

}

    public function AddproductPriceToCustomer(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $avt = Avt::find(1);

        //  return $request;
        $customertid = $request->clientnamesearch;
        $productid = $request->productNo;
        if ($request->orderNo == null) {
            $create_order = offer_price_to_customer::create(
                [
                    'customer_id' => $customertid,
                    'branchs_id' => Auth()->User()->branchs_id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'notes' => $request->notes,
                    'numbershowstatus' => $request->numbershowstatus,


                ]
            );
            $create_order_price_from_supplier_items = offer_price_to_customer_items::create(
                [
                                        'notes' => $request->notes,

                    'product_id' => $productid,
                    'quantity' => $request->quentity,
                    "PriceWithoudTax" => $request->saleprice,
                    "discount" => $request->discount,
                    'order_id' => $create_order->id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'note' => $request->notes,


                ]
            );
            $offer_price_to_customer=offer_price_to_customer::find($create_order->id);
            $itemsRequest =  offer_price_to_customer_items::where('order_id', $create_order->id)->get();
            $ListProducts = [];
            $count = 0;
            foreach ($itemsRequest as $item) {
                $count++;
                $product = products::find($item->product_id);
                $ListProducts[] = [
                    "count" => $count,
                    "productCode" => $product->Product_Code,
                    "productName" => $product->product_name,
                    "sale_price" => $item->PriceWithoudTax,
                    "discount" => $item->discount,
                    "order_id" => $item->order_id,
                    "quantity" => $item->quantity,
                    "added_value" => $avt->AVT * $item->PriceWithoudTax,
                    'id'=>$item->id,
                    'totaldiscount'=>$offer_price_to_customer->discount



                ];
            }
            return $ListProducts;
            return view('products.OfferPricesTocustomer', compact('itemsRequest'))->with('supplierdata', $request);
        } else {
            //  return 'agin';
   offer_price_to_customer::find( $request->orderNo)->update(
                [
                    'notes' => $request->notes,
                ]);
                
                            $offer_price_to_customer=offer_price_to_customer::find($request->orderNo);

            $create_order_price_from_supplier_items = offer_price_to_customer_items::create(
                [
                    'product_id' => $productid,
                    'quantity' => $request->quentity,
                    "PriceWithoudTax" => $request->saleprice,
                    "discount" => $request->discount,
                    'order_id' => $request->orderNo,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'note' => $request->notes,

                ]
            );


            $itemsRequest =  offer_price_to_customer_items::where('order_id', $request->orderNo)->get();
            
            $ListProducts = [];
            $count = 0;
            foreach ($itemsRequest as $item) {
                $count++;
                $product = products::find($item->product_id);
                $ListProducts[] = [
                    "count" => $count,
                    "productCode" => $product->Product_Code,
                    "productName" => $product->product_name,
                    "sale_price" => $item->PriceWithoudTax,
                    "discount" => $item->discount,
                    "order_id" => $item->order_id,
                    "quantity" => $item->quantity,
                    "added_value" => $avt->AVT * $item->PriceWithoudTax,
                     'id'=>$item->id,
                     'totaldiscount'=>$offer_price_to_customer->discount


                ];
            }
            return $ListProducts;
            return view('products.OfferPricesTocustomer', compact('itemsRequest'))->with('supplierdata', $request);
        }
    }

    public function showAllproductpaginatepurchase($branchId)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $products = products::where('branchs_id', $branchId)->paginate(50);
        $resultProducts = [];


        foreach ($products as $product) {
            $resultProducts[] = [
                'id' => $product->id,
                'Product_Code' => $product->Product_Code,
                'product_name' => $product->product_name,
                'purchasingـprice' => $product->purchasingـprice,
                'sale_price' => $product->sale_price,
                'numberofpice' => $product->numberofpice,
                'Product_Location' => $product->Product_Location,
                'branch' => $product->branch->name,
            ];
        }
        $products['otherdata'] = $resultProducts;
        return $products;
    }

    public function searchChooseProductpaginatenewpurchaseBypost(Request $request)
    {

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $branchId= $request->branchs_id;
        $searchtext=$request->searchtext ;
        $products = [];

        $data = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchId)->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchId)->paginate(50);
        return view('ajax_choose_product', compact('data'));
       
    }

    public function searchAllproductpaginatepurchase($branchId, $searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $products = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchId)->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchId)->paginate(100);
        $resultProducts = [];


        foreach ($products as $product) {
            $resultProducts[] = [
                'id' => $product->id,
                'Product_Code' => $product->Product_Code,
                'product_name' => $product->product_name,
                'purchasingـprice' => $product->purchasingـprice,
                'sale_price' => $product->sale_price,
                'numberofpice' => $product->numberofpice,
                'Product_Location' => $product->Product_Location,
                'branch' => $product->branch->name,
            ];
        }
        $products['otherdata'] = $resultProducts;
        return $products;
    }
    public function update_offer_price_supplier( $id)
{
    
        $itemsRequest =  order_price_from_supplier_items::where('order_id', $id)->get();
            $ListProducts = [];
            $count = 0;
            foreach ($itemsRequest as $item) {
                $count++;
                $product = products::find($item->product_id);
                $ListProducts[] = [
                    "count" => $count,
                    "productCode" => $product->Product_Code,
                    "productName" => $product->product_name,
                    "productQuantity" => $item->quantity,
                    "order_id" => $item->order_id
                ];
            }
            
            return $ListProducts ;
    
}
    public function order_price_from_suppliers(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //return $request;
        $suppliertid = $request->supplierId;
        $productid = $request->productNo;
        if ($request->orderNo == null) {
            $create_order = order_price_from_supplier::create(
                [
                    'suplier_id' => $suppliertid,
                    'branchs_id' =>  Auth()->User()->branchs_id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );
            $create_order_price_from_supplier_items = order_price_from_supplier_items::create(
                [
                    'product_id' => $productid,
                    'quantity' => $request->quentity,
                    'order_id' => $create_order->id,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),


                ]
            );
            $itemsRequest =  order_price_from_supplier_items::where('order_id', $create_order->id)->get();
            $ListProducts = [];
            $count = 0;
            foreach ($itemsRequest as $item) {
                $count++;
                $product = products::find($item->product_id);
                $ListProducts[] = [
                    "count" => $count,
                    "productCode" => $product->Product_Code,
                    "productName" => $product->product_name,
                    "productQuantity" => $item->quantity,
                    "order_id" => $item->order_id
                ];
            }
            return $ListProducts;
            //  return view('products.Requestpricesofproductsfromsupplier',compact('itemsRequest'))->with('supplierdata',$request);

        } else {
            $create_order_price_from_supplier_items = order_price_from_supplier_items::create(
                [
                    'product_id' => $productid,
                    'quantity' => $request->quentity,
                    'order_id' => $request->orderNo,
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),

                ]
            );


            $itemsRequest =  order_price_from_supplier_items::where('order_id', $request->orderNo)->get();
            $ListProducts = [];
            $count = 0;
            foreach ($itemsRequest as $item) {
                $count++;
                $product = products::find($item->product_id);
                $ListProducts[] = [
                    "count" => $count,
                    "productCode" => $product->Product_Code,
                    "productName" => $product->product_name,
                    "productQuantity" => $item->quantity,
                    "order_id" => $item->order_id

                ];
            }
            return $ListProducts;
            // return view('products.Requestpricesofproductsfromsupplier',compact('itemsRequest'))->with('supplierdata',$request);

        }
    }


    public function print_order_perice_to_customer($product_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $itemsRequest =  offer_price_to_customer_items::where('order_id', $product_id)->get();

        return view('products.print_order_perice_to_customer', compact('itemsRequest'));
    }
    public function printOrderPriceFromSupplier($product_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $itemsRequest =  order_price_from_supplier_items::where('order_id', $product_id)->get();

        return view('products.print_order_perice_from_supplier', compact('itemsRequest'))->with('id',$product_id);
    }

    public function print_order_perice_to_customerByPost(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $itemsRequest =  offer_price_to_customer_items::where('order_id', $request->OrderNoprint)->get();
// return $request;
        return view('products.print_order_perice_to_customer', compact('itemsRequest'))->with('id', $request->OrderNoprint);
    }
    public function printOrderPriceFromSupplierBypost(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $itemsRequest =  order_price_from_supplier_items::where('order_id', $request->OrderNoprint)->get();

        return view('products.print_order_perice_from_supplier', compact('itemsRequest'))->with('id',$request->OrderNoprint);
    }

    public function purchases()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(50);

        //return $data;
        return  view('products.purchases', compact('products'));
    }


    public function Purchase_returns()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        return  view('products.purchase_return', compact('data'));

        $orderTosupllier = orderTosupllier::get();
        //purchase_return
    }







    public function Purchase_returns_Data(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderOwner = orderTosupllier::find($request->clientName);
        $resource_purchases = resource_purchases::where('orderId', $request->clientName)->where('save', 1)->first();
        $orderdetails = orderDetails::where('order_owner', $request->clientName)->get();
        if ($resource_purchases == null) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? '  لم يتم العثور علي فاتورة بهذة الرقم' : 'No invoice with this number was found';

            session()->flash('notfountreturnpuracheseproduct', $message);
            $data = [];
            return view('products.purchase_return', compact('data'));
        }
        $user = User::find($orderOwner->user_id);
        $branch = $user->branch->name;

        $data = [
            'branch' => $branch,
            'supllier' => $orderOwner,
            'resource_purchases' => $resource_purchases,

            'product' => $orderdetails
        ];
        //return $data;
        return    view('products.purchase_return', compact('data'));
    }


    public function printReturnpurchases($id)
    {
        //return $id;
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $orderOwner = orderTosupllier::find($id);
        $resource_purchases = resource_purchases::where('orderId', $id)->first();

        $orderdetails = orderDetails::where('order_owner', $id)->where('returns_purchase', "!=", 0)->get();
        if ($orderOwner == null) {
            $message = LaravelLocalization::getCurrentLocale() == 'ar' ? '  لم يتم العثور علي فاتورة بهذة الرقم' : 'No invoice with this number was found';

            session()->flash('notfountreturnpuracheseproduct', $message);
            $data = [];
            return view('products.purchase_return', compact('data'));
        }
        $user = User::find($orderOwner->user_id);
        $branch = $user->branch->name;

        $data = [
            'branch' => $branch,
            'supllier' => $orderOwner,
            'product' => $orderdetails,
            'resource_purchases' => $resource_purchases
        ];
        // return $data;
        return    view('products.print_purchase_return', compact('data'));
    }

    public function create(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        $t = time();

        $clientphone   = $request["phonenumber"];;
        $clientname = $request["clientName"];
        $clientaddress = $request["address"];
        $clientnote = $request["notes"];
        $product = products::get();
        $data = [
            "date" => date("Y-m-d", $t),
            'product' => $product,
            'clientnote' => $clientnote,
            'clientphone' => $clientphone,
            'clientname' => $clientname,
            'clientaddress' => $clientaddress,
            'print' => 'print quentity'

        ];
        return view('products.print_products', compact('data'));
    }



    public function getProductsPriceFromSupplier()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);

        //return $data;
        return  view('products.Requestpricesofproductsfromsupplier', compact('products'))->with('order_id', "-");
    }

    public function showProductsPrice(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        //
        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(20);

        return  view('products.OfferPricesTocustomer', compact('products'))->with('order_id', '-');
    }

    public function print_all_products_price(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $t = time();


        $product = products::get();
        $data = [
            "date" => date("Y-m-d", $t),
            'product' => $product,
        ];
        return view('products.print_all_products_price', compact('data'));
    }
    public function printProductPriceToCustomer($request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $t = time();

        $clientphone   = $request["phonenumber"];;
        $clientname = $request["clientName"];
        $clientaddress = $request["address"];
        $clientnote = $request["notes"];
        $product = products::get();
        $data = [
            "date" => date("Y-m-d", $t),
            'product' => $product,
            'clientnote' => $clientnote,
            'clientphone' => $clientphone,
            'clientname' => $clientname,
            'clientaddress' => $clientaddress,
            'print' => 'printprice'
        ];
        return view('products.print_products', compact('data'));
    }

    public function printProductPrice(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $t = time();

        $clientphone   = $request["phonenumber"];;
        $clientname = $request["clientName"];
        $clientaddress = $request["address"];
        $clientnote = $request["notes"];
        $product = products::get();
        $data = [
            "date" => date("Y-m-d", $t),
            'product' => $product,
            'clientnote' => $clientnote,
            'clientphone' => $clientphone,
            'clientname' => $clientname,
            'clientaddress' => $clientaddress,
            'print' => 'printprice'
        ];
        return view('products.print_products', compact('data'));
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
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($products)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $product = products::find($products);
        return  $product;
    }
    
    
    
    
    
public function updatepurchasesbyid($id){
        $resources = resource_purchases::where('save',1)->where('orderId',$id)->first();
        if($resources!=null){
        $recentsupllier = supllier::find($resources->suplier_id);
        $orderdetails = orderDetails::where('order_owner' ,$id)->get();

        resource_purchases::where('orderId',$id)->update(['save'=>0]);
        orderDetails::where('order_owner' ,$id)->update(['save'=>0]);
        
        
        $payment=$resources->Pay_Method_Name;
        $paymentMethod=$payment;
   
        $resource_purchases=$resources;
        
$total_value=$resources->In_debt - $resources->discount;
$avtSaleRate = Avt::find(2);
$vat_purchase_rate= $avtSaleRate->AVT;
$tax_value=0;
$total_cost=0;
if($vat_purchase_rate==0){
        $total_cost=$total_value;

}
else{
    
    
    $vat_purchase_rate=($vat_purchase_rate*100)+100;
    $total_cost=$total_value*100/$vat_purchase_rate;
    $tax_value=$total_value-$total_cost;

    
}
        
        $allProdctsD = [];
      
            foreach ($orderdetails as $product) {
                  $productreturnq=products::find($product->product_id);
                  products::where('id', $product->product_id)->update(
                      [
        'numberofpice'=>   $productreturnq->numberofpice-$product->numberofpice
                          ]);
}
  if ($resources->Pay_Method_Name == "Credit") {
      
      
      
            $supplier = supllier::find($resources->suplier_id);
            //return $supplier->In_debt + $resources->In_debt;
            supllier::where('id', $resources->suplier_id)->update(
                [
                    'In_debt' => $supplier->In_debt - $resources->In_debt - $resources->discount
                ]
            );
            
               $financial_accounts= financial_accounts::where('orginal_type',2)->where('orginal_id', $resource_purchases->suplier_id )->first();
            financial_accounts::where('orginal_type',2)->where('orginal_id', $resource_purchases->suplier_id )->update(
             [
                 'current_balance'=> $financial_accounts->current_balance - ($total_cost + $tax_value),
                 'debtor_current'=>$financial_accounts->debtor_current+ ($total_cost + $tax_value),

             ]
             ); 
             
                         credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $payment,
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance-($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
             
             
            
            
        }
    else {

            $financial_accounts= financial_accounts::find(5);
            financial_accounts::find(5)->update(
               [
                   'current_balance'=> $financial_accounts->current_balance+ ($total_cost + $tax_value),
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
               ]
               ); 



            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
             $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+ ($total_cost + $tax_value),
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
       ]
       ); 
              credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
    }
    
    
    
    
    
        

// new addition 2024-12-9




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
         'creditor_current'=>$financial_accounts->creditor_current+$tax_value,

     ]
     ); 
$customerdata = supllier::find($resource_purchases->suplier_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $tax_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $tax_value,
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

            ]
        );



               $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->current_balance-$tax_value,
         'creditor_current'=>$financial_accounts->creditor_current+$tax_value,

       ]
       ); 
 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $tax_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance-$tax_value,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $tax_value,
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

            ]
        );


  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost,
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
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
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
               'current_balance'=> $financial_accounts->current_balance+$total_cost,
         'creditor_current'=>$financial_accounts->creditor_current+ $total_cost,

       ]
       ); 
// end addition

  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$id,
                'currentblance'=>$financial_accounts->current_balance+$total_cost,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $total_cost,
                'debtor'=>0

            ]
        );







        $i = 0;
        foreach ($orderdetails as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->numberofpice,
                'purchasingـprice' => $product->purchasingـprice,
                'Added_Value' => $product->Added_Value,
                'saleperpice' => $product->sale_price,
                'count' => $i,
                'id' => $product->id
            ];
        }
        $data = [
            "Purchase_invoice_number_supplier" => $id,
            "Other expenses" => $resources['Other expenses'],
            "shipping fee" => $resources['shipping fee'],
            'orderNo' => $id,
            "purchase_invoice_no" => $resources['purchase_invoice_no'],
            'pay' => $resources->pay,
            'In_debt' => $resources->In_debt,
            'discount' => $resources->discount,
            'recentsupllier' => $recentsupllier,
            "product" => $allProdctsD
        ];
        
        return $data;
        }
        return 0;
}

    public function Addproducttopurchases(Request $request)
    {
        $avtPurcheseRate = Avt::find(2);
        $orderdatareturn = [];
        $clientNo = $request->clientnamesearch;

        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($request->orderNo == null) {


            $createorder = orderTosupllier::create(
                [
                    'user_id' => Auth()->user()->id,
                    'suplier_id' => $clientNo,
                    'Limit_credit' => $request->pay,
                    'purchaseـamount' => $request->quentity * $request->quentityprice,
                    'added_value' => $request->quentity * $request->quentityprice * $avtPurcheseRate->AVT,
                    'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );


            $orderdatareturn = resource_purchases::create(
                [
                    "Other expenses" => $request->Otherexpenses,
                    "shipping fee" => $request->shippingfee,
                    "purchase_invoice_no" => $request->purchase_invoice_no,
                    "Purchase_invoice_number" => $request->Purchase_invoice_number_supplier,
                    'orderId' => $createorder->id,
                    'suplier_id' => $clientNo,
                    'In_debt' => ($request->quentity * $request->quentityprice) + ($request->quentityprice * $avtPurcheseRate->AVT * $request->quentity),
                    'Pay_Method_Name' => $request->pay,
                    'notes' => $request->notes,
                    'branchs_id' => $request->branchs_id,
                    'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                ]
            );

          
        } else {


        if(orderDetails::where('product_id' ,$request->productname )->where('order_owner',$request->orderNo)->first()==null){

            $getrecientorder = orderTosupllier::where('id', $request->orderNo)->first();

            $createorder = orderTosupllier::where('id', $request->orderNo)->update(
                [

                    'purchaseـamount' => $getrecientorder->purchaseـamount + ($request->quentity * $request->quentityprice),
                    'added_value' => $getrecientorder->added_value + ($request->quentity * $request->quentityprice * $avtPurcheseRate->AVT)

                ]
            );
            $resourceـpurchases = resource_purchases::where('orderId', $request->orderNo)->first();
            $orderdatareturn = resource_purchases::where('orderId', $request->orderNo)->first();
            resource_purchases::where('orderId', $request->orderNo)->update(
                [
                    'In_debt' => $resourceـpurchases->In_debt + ($request->quentity * $request->quentityprice) + ($request->quentityprice * $avtPurcheseRate->AVT * $request->quentity),
                    "shipping fee" => $request->shippingfee,

                ]
            );
        }
        }
        $productno = 0;
        if ($request->productNo == null) {
            $productno = $request->productname;
        }


$Pre_existing=1;
        $getProduct = products::where('id', $productno)->first();
        $productno = $getProduct->id;
        $Added_value = $request->quentityprice * $avtPurcheseRate->AVT;
        $product_Price = $request->quentityprice;
        $purchaseorder=$createorder->id ?? $request->orderNo;
        // return orderDetails::where('product_id' ,$productno )->where('order_owner',$purchaseorder)->first()==null;
        if(orderDetails::where('product_id' ,$productno )->where('order_owner',$purchaseorder)->first()==null){
            $Pre_existing=0;

        $orderdetails = orderDetails::create(
            [
                'product_id' => $productno,
                'order_owner' => $createorder->id ?? $request->orderNo,
                'product_name' => $request->productnameshow,
                'purchasingـprice' => $product_Price,
                'Added_Value' => $Added_value,
                'numberofpice' => $request->quentity,
                'sale_price' => $request->sale_price,
                'unit' => $request->unit_pice,
                    'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at' =>  \Carbon\Carbon::now()->addHours(3),
            ]
        );
     
        }
     
        $recentsupllier = supllier::find($clientNo);
        $orderId = $purchaseorder;
        $orderdetails = orderDetails::where('order_owner', $orderId)->get();
        // return $request;
        $allProdctsD = [];
        $resourceـpurchases = resource_purchases::where('orderId', $orderId)->first();

        $i = 0;
        foreach ($orderdetails as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->numberofpice,
                'purchasingـprice' => $product->purchasingـprice,
                'Added_Value' => $product->Added_Value,
                'saleperpice' => $product->sale_price,
                'count' => $i,
                'id' => $product->id
            ];
        }
        $data = [
            'Pre_existing'=>$Pre_existing,
            "Purchase_invoice_number_supplier" => $resourceـpurchases['Purchase_invoice_number'],
            "Other expenses" => $resourceـpurchases['Other expenses'],
            "shipping_fee" => $resourceـpurchases['shipping fee'],
            'orderNo' => $orderId,
            "purchase_invoice_no" => $resourceـpurchases['purchase_invoice_no'],
            'pay' => $request->pay,
            'In_debt' => $resourceـpurchases->In_debt,
            'discount' => $resourceـpurchases->discount,
            'recentsupllier' => $recentsupllier,
            "product" => $allProdctsD
        ];
        
        return $data;
    }

    public function get_all_products_in_orderto_supplier($id)
    {
        return orderDetails::where('order_owner', $id)->get();
    }

  public function updateorder_purchase($id)
    {
    
        
          $orderdetails = orderDetails::where('order_owner', $id)->get();
        $listOfProduct = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->numberofpice * $orderitem->Added_Value;
            $totalPrice += $orderitem->numberofpice * $orderitem->purchasingـprice;
            $count++;
            $listOfProduct[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->product_id,
                "quantity" => $orderitem->numberofpice,
                "purchasingـprice" => $orderitem->purchasingـprice,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->numberofpice * $orderitem->Added_Value) + ($orderitem->numberofpice * $orderitem->purchasingـprice),
                "orderNo" => $orderitem->order_owner,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice
            ];
        }

        return $listOfProduct;
        
    }

    public function AddproducttoSupllier(Request $request)
    {
        //return  $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        if ($request->orderNo == null) {
            $clientNo = 0;
            $clientNo = $request->clientnamesearch;

            $createorder = orderTosupllier::create(
                [
                    'user_id' => Auth()->user()->id,
                    'suplier_id' => $clientNo,

                ]
            );
        }


        $productno = 0;
        if ($request->productNo == "ادخل  رقم المنتج") {
            $productno = $request->productname;
        } else {
            $productno = $request->productNo;
        }
        $avtPurcheseRate = Avt::find(2);
        $Added_value = $request->quentityprice * $avtPurcheseRate->AVT;
        $product_Price = $request->quentityprice;
        $orderdetails = orderDetails::create(
            [
                'product_id' => $productno,
                'order_owner' => $createorder->id ?? $request->orderNo,
                'product_name' => $request->productnameshow,
                'purchasingـprice' => $product_Price,
                'Added_Value' => $Added_value,
                'numberofpice' => $request->quentity,
                'created_at' => date("Y-m-d"),
                'updated_at' => date("Y-m-d"),
            ]
        );
        //  return $orderdetails;

        $orderdetails = orderDetails::where('order_owner', $orderdetails->order_owner)->get();
        $listOfProduct = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->numberofpice * $orderitem->Added_Value;
            $totalPrice += $orderitem->numberofpice * $orderitem->purchasingـprice;
            $count++;
            $listOfProduct[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->product_id,
                "quantity" => $orderitem->numberofpice,
                "purchasingـprice" => $orderitem->purchasingـprice,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->numberofpice * $orderitem->Added_Value) + ($orderitem->numberofpice * $orderitem->purchasingـprice),
                "orderNo" => $orderitem->order_owner,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice
            ];
        }

        return $listOfProduct;
        return  view('products.Purchase_order_of_resources', compact('data'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function goToSale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(50);
        // return $products;
        return view('products.sales', compact('products'));
    }
    public function goToSaleByPage()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $products = products::where('branchs_id', Auth()->User()->branchs_id)->paginate(50);
        return $products;
    }

    public function searchaboutproduct($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $products = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', Auth()->User()->branchs_id)->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', Auth()->User()->branchs_id)->paginate(50);

        return $products;
    }


    public function showAllproductpaginate()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $products = products::paginate(50);
        $resultProducts = [];


        foreach ($products as $product) {
            $resultProducts[] = [
                'id' => $product->id,
                'Product_Code' => $product->Product_Code,
                'product_name' => $product->product_name,
                'purchasingـprice' => $product->purchasingـprice,
                'sale_price' => $product->sale_price,
                'numberofpice' => $product->numberofpice,
                'Product_Location' => $product->Product_Location,
                'branch' => $product->branch->name,
            ];
        }
        $products['otherdata'] = $resultProducts;
        return $products;
    }

    public function searchAllproductpaginate($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $products = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->paginate(100);
        $resultProducts = [];


        foreach ($products as $product) {
            $resultProducts[] = [
                'id' => $product->id,
                'Product_Code' => $product->Product_Code,
                'product_name' => $product->product_name,
                'purchasingـprice' => $product->purchasingـprice,
                'sale_price' => $product->sale_price,
                'numberofpice' => $product->numberofpice,
                'Product_Location' => $product->Product_Location,
                'branch' => $product->branch->name,
            ];
        }
        $products['otherdata'] = $resultProducts;
        return $products;
    }
    public function Allproductpaginatenew()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $data = products::paginate(20);



        return view('ajax_search', compact('data'));
    }
    
       public function product_group_ajax(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
if($request->branchs_id=='-'){

        $data = products::where('product_group',$request->group_id)->paginate(20);

}else{
            $data = products::where('product_group',$request->group_id)->where('branchs_id',$request->branchs_id)->paginate(20);

}

        return view('ajax_search', compact('data'));
    }
    
    
    
    
           public function product_branchs_id_ajax(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
if($request->branchs_id=='-'){
    $data = products::paginate(30);



        return view('ajax_search', compact('data'));
}
        $data = products::where('branchs_id',$request->branchs_id)->orderBy('numberofpice', 'desc')->paginate(20);



        return view('ajax_search', compact('data'));
    }
    
    
    
        public function product_sale_group_ajax(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = products::where('product_group',$request->group_id)->paginate(20);



        return view('ajax_choose_product_sale', compact('data'));
    }
    
    public function makeTotalDiscontpurchases($invoiceId, $discountValue)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = resource_purchases::where('orderId', $invoiceId)->first();
        $orderDetails = orderDetails::where('order_owner', $invoiceId)->get();
        $totalpurcgaseswithoudTax = 0;
        foreach ($orderDetails as $item) {
            $totalpurcgaseswithoudTax +=   $item->purchasingـprice * $item->numberofpice;
        }
        
$avtSaleRate = Avt::find(2);
$avtSaleRate = $avtSaleRate->AVT;
$totalcost_withoud_tax=($totalpurcgaseswithoudTax-$discountValue);

        resource_purchases::where('orderId', $invoiceId)->update(
            [
                'discount' => $discountValue ,
                'In_debt' => $totalcost_withoud_tax+round($totalcost_withoud_tax*$avtSaleRate,2)
            ]
        );
        $data = resource_purchases::where('orderId', $invoiceId)->first();
        $dataN = [
            'discount' => $discountValue ?? 0,
            'totalpurcgaseswithoudTax' => $totalpurcgaseswithoudTax,
            'Addedvalue' =>  $totalpurcgaseswithoudTax * $avtSaleRate,
            'In_debt' => $data->In_debt,
            "shipping_fee" => $data['shipping fee'],

        ];
        return $dataN;
    }
    public function cancelInvoiceDiscontpurcgases($invoiceId)
    {

    
        $data = resource_purchases::where('orderId', $invoiceId)->first();
        $avtPurcheseRate = Avt::find(2);
$avtPurcheseRate = $avtPurcheseRate->AVT;

        $orderDetails = orderDetails::where('order_owner', $invoiceId)->get();
        $totalpurcgaseswithoudTax = 0;
        foreach ($orderDetails as $item) {
            $totalpurcgaseswithoudTax +=   $item->purchasingـprice * $item->numberofpice;
        }
        
        
            resource_purchases::where('orderId', $invoiceId)->update(
            [
                'discount' => 0,
                'In_debt' => $totalpurcgaseswithoudTax+round($totalpurcgaseswithoudTax*$avtPurcheseRate,2),


            ]
        );
        
        
        $data = resource_purchases::where('orderId', $invoiceId)->first();
        $dataN = [
            'discount' => 0,
            'totalpurcgaseswithoudTax' => $totalpurcgaseswithoudTax,
            'Addedvalue' =>  $totalpurcgaseswithoudTax * $avtPurcheseRate,
            'In_debt' => $data->In_debt,
            "shipping_fee" => $data['shipping fee'],

        ];
        return $dataN;
    }

    public function searchAllproductpaginatenew($searchtext)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

               $data = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->orwhere('notes', 'LIKE', '%' . $searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->paginate(20);




        return view('ajax_search', compact('data'));
    }


    public function searchAllproductpaginatenew_by_post(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

if($request->branchs_id=='-'){

               $data = products::where('product_name', 'LIKE', '%' . $request->searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $request->searchtext . '%')->orwhere('notes', 'LIKE', '%' . $request->searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $request->searchtext . '%')->paginate(20);

}else{
               $data = products::where('branchs_id',$request->branchs_id)->where('product_name', 'LIKE', '%' . $request->searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $request->searchtext . '%')->orwhere('notes', 'LIKE', '%' . $request->searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $request->searchtext . '%')->paginate(20);

}



        return view('ajax_search', compact('data'));
    }


    public function searchChooseProductpaginatenew($searchtext, $branchs_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $data = products::where('branchs_id', $branchs_id)->where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->where('branchs_id', $branchs_id)->paginate(20);
        return view('ajax_choose_product', compact('data'));
    }
    public function ChooseProductpaginatenew($branchs_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = products::where('branchs_id', $branchs_id)->paginate(20);
        // return $data;

        return view('ajax_choose_product', compact('data'));
    }


    public function searchChooseProductpaginatenewSale($searchtext, $branchs_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];
        if ($branchs_id == '-') {
            $data = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('notes', 'LIKE', '%' . $searchtext . '%')->paginate(20);
            return view('ajax_choose_product_sale', compact('data'));
        }
        $data =products::where('branchs_id', $branchs_id)->where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->where('branchs_id', $branchs_id)->paginate(20);
        return view('ajax_choose_product_sale', compact('data'));
    }
    
    
        public function searchChooseProductpaginatenewSaleBypost(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];
       $searchtext= $request->searchtext;
       $branchs_id=$request->branchs_id;
     if ($branchs_id == '-') {
            $data = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('notes', 'LIKE', '%' . $searchtext . '%')->paginate(20);
            return view('ajax_choose_product_sale', compact('data'));
        }
        $data =products::where('branchs_id', $branchs_id)->where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('refnumber', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->orwhere('notes', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchs_id)->paginate(20);
        return view('ajax_choose_product_sale', compact('data'));
    }
    
    
    
    
    
    public function ChooseProductpaginatenewSale($branchs_id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branchs_id == '-') {
            $data = products::paginate(20);
            return view('ajax_choose_product_sale', compact('data'));
        }
        $data = products::where('branchs_id', $branchs_id)->paginate(20);
        // return $data;

        return view('ajax_choose_product_sale', compact('data'));
    }



    public function searchaboutproductwithBranchId($searchtext, $branchId)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $products = [];

        $products = products::where('product_name', 'LIKE', '%' . $searchtext . '%')->orwhere('Product_Code', 'LIKE', '%' . $searchtext . '%')->where('branchs_id', $branchId)->paginate(50);

        return $products;
    }
    public function goToReceipt()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
        $products = products::paginate(50);
        // return $products;
        return view('products.Receipt', compact('products'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
     
     
     
     
     
     
     
     
    public function savepurchase($request, $payment, $supplier,$shipping,$date,$another_bank)
    {
        
        $resource_purchases= resource_purchases::where('orderId', $request)->first();
        $branchs_id=$resource_purchases->branchs_id;
        resource_purchases::where('orderId', $request)->update(
            [
                'save' => 1,
                'suplier_id' => $supplier,
                'Pay_Method_Name' => $payment,
                'shipping fee' => $shipping,
                'created_at'  =>$date!='0'?$date.' '.substr(\Carbon\Carbon::now()->addHours(3), 12):\Carbon\Carbon::now()->addHours(3),

            ]
        );
        
        $orderOwner = orderTosupllier::find($request)->update(
            [

                'Limit_credit' => $payment
            ]
        );

        orderDetails::where('order_owner', $request)->where('numberofpice', '!=', 0)->update(
            ['save' => 1,]
        );
        $orderOwner = orderTosupllier::find($request)->update(
            ['suplier_id' => $supplier,]
        );
        $total_number_items=0;
                foreach (orderDetails::where('order_owner', $request)->where('numberofpice', '!=', 0)->get() as $item) {
        $total_number_items+=$item->numberofpice;


}

$cost_shipping_per_item=round($shipping/$total_number_items  ,3);
        foreach (orderDetails::where('order_owner', $request)->where('numberofpice', '!=', 0)->get() as $item) {
            
            $cost=0;
            $updateProduct = products::find($item->product_id);

if($updateProduct->numberofpice + $item->numberofpice==0 ||$updateProduct->numberofpice<0){
   $cost= $item->purchasingـprice+$cost_shipping_per_item;
}else{
   $cost= round(((($item->purchasingـprice+$cost_shipping_per_item)*$item->numberofpice) +($updateProduct->purchasingـprice*$updateProduct->numberofpice))/($updateProduct->numberofpice + $item->numberofpice),2) ;
}

            products::where('id', $item->product_id)->Update([
                'purchasingـprice' =>$item->purchasingـprice+$cost_shipping_per_item,
                'average_cost' =>$cost,
                'sale_price' => $item->sale_price,
                'numberofpice' => $updateProduct->numberofpice + $item->numberofpice,
            ]);
        }
            orderDetails::find($item->id)->update(['reamingQuantity'=>$updateProduct->numberofpice + $item->numberofpice]);

        $resource_purchases =   resource_purchases::where('orderId', $request)->first();
        
        
        $paymentMethod=$payment;
        
        
        
         if ($shipping >0) {
         
            $financial_accounts= financial_accounts::find(133);
            financial_accounts::find(133)->update(
             [
                 'current_balance'=>$financial_accounts->current_balance+$shipping,
                 'debtor_current'=>$financial_accounts->debtor_current+$shipping,

             ]
             );
                 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  133,
                'recive_amount' => $shipping,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance+$shipping,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>0,
                'debtor'=>$shipping,
              

            ]
        );
  
        }
        
        
        
        
        
        
        if ($payment == "Credit") {
            
            $supplier = supllier::find($resource_purchases->suplier_id);
            //return $supplier->In_debt + $resource_purchases->In_debt;
            supllier::where('id', $resource_purchases->suplier_id)->update(
                [
                    'In_debt' => $supplier->In_debt + $resource_purchases->In_debt 
                ]
            );

            $financial_accounts= financial_accounts::where('orginal_type',2)->where('orginal_id',$resource_purchases->suplier_id )->first();
            financial_accounts::where('orginal_type',2)->where('orginal_id',$resource_purchases->suplier_id )->update(
             [
                   'current_balance'=> $financial_accounts->current_balance+$resource_purchases->In_debt,
                 'creditor_current'=>$financial_accounts->creditor_current+ $resource_purchases->In_debt,

             ]
             );
                 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $resource_purchases->In_debt,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance+ $resource_purchases->In_debt,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $resource_purchases->In_debt ,
                'debtor'=>0,
              
            ]
        );
  

            $financial_accounts= financial_accounts::find(4);
            financial_accounts::find(4)->update(
             [
                 'current_balance'=> $financial_accounts->current_balance-$shipping,
                 'creditor_current'=>$financial_accounts->creditor_current+$shipping,

             ]
             ); 
                   credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' =>  $shipping,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات مصريف شحن و تفريغ رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance-$shipping,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $shipping,
                'debtor'=>0,
              

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', $branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', $branchs_id)->update(
       [
                 'current_balance'=> $financial_accounts->current_balance-  $shipping,
                 'creditor_current'=>$financial_accounts->creditor_current+$shipping,

       ]
       ); 
               credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $shipping,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance-$shipping,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $shipping,
                'debtor'=>0,
              

            ]
        );


        }
        
        
        
        
        
                    $value_total=( $resource_purchases->In_debt+$shipping);

        if ($resource_purchases->Pay_Method_Name == 'Cash') {
            $financial_accounts= financial_accounts::find(5);
            financial_accounts::find(5)->update(
               [
                   'current_balance'=> $financial_accounts->current_balance-$value_total,
                    'creditor_current'=>$financial_accounts->creditor_current+ $value_total,

               ]
               ); 

            $customer_id_suplier= financial_accounts::where('orginal_type',2)->where('orginal_id',$resource_purchases->suplier_id )->first();

            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $customer_id_suplier->id,
                'recive_amount' => $resource_purchases->In_debt,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance+ $resource_purchases->In_debt,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0 ,
                'debtor'=>0,
              
            ]
        );
  

























         credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' => $value_total,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance- $value_total,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $value_total,
                'debtor'=>0,
              

            ]
        );

              $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', $branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', $branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance-  $value_total,
        'creditor_current'=>$financial_accounts->creditor_current+ $value_total,

       ]
       ); 
       
           credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' =>  $value_total,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance-$value_total,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $value_total,
                'debtor'=>0,
              

            ]
        );

       
       
        } 
        
        
        
        
        
        if ($resource_purchases->Pay_Method_Name == 'Bank_transfer'||$resource_purchases->Pay_Method_Name == 'Shabka') {
            $customer_id_suplier= financial_accounts::where('orginal_type',2)->where('orginal_id',$resource_purchases->suplier_id )->first();

            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $customer_id_suplier->id,
                'recive_amount' => $resource_purchases->In_debt,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0 ,
                'debtor'=>0,
              
            ]
        );
        
        
            $financial_accounts= financial_accounts::find(4);
            $value_total=( $resource_purchases->In_debt+$shipping);
            financial_accounts::find(4)->update(
             [
                 'current_balance'=> $financial_accounts->current_balance-  $value_total,
                 'creditor_current'=>$financial_accounts->creditor_current+$value_total,

             ]
             ); 
                   credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  4,
                'recive_amount' =>  $resource_purchases->In_debt - $value_total,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance- $value_total,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $value_total,
                'debtor'=>0,
              

            ]
        );


 if($another_bank==4){
                    $financial_accounts= financial_accounts::where('id',1157)->first();
    financial_accounts::where('id',1157)->update(
       [
                 'current_balance'=> $financial_accounts->current_balance-  $value_total,
                 'creditor_current'=>$financial_accounts->creditor_current+ $value_total,

       ]
       ); 
               credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $value_total,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance-$value_total,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $value_total,
                'debtor'=>0,
              

            ]
        );
     
 }else{


               $financial_accounts= financial_accounts::where('parent_account_number',4)->where('branchs_id', $branchs_id)->first();
    financial_accounts::where('parent_account_number',4)->where('branchs_id', $branchs_id)->update(
       [
                 'current_balance'=> $financial_accounts->current_balance-  $value_total,
                 'creditor_current'=>$financial_accounts->creditor_current+ $value_total,

       ]
       ); 
               credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $value_total,
                'branchs_id' => $branchs_id,
                'pay_method' => $payment,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance-$value_total,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  $value_total,
                'debtor'=>0,
              

            ]
        );
}
}
// new addition 2024-12-9


$total_value=$resource_purchases->In_debt ;

$avtSaleRate = Avt::find(2);
$vat_purchase_rate= $avtSaleRate->AVT;
$tax_value=0;
$total_cost=0;
if($vat_purchase_rate==0){
        $total_cost=$total_value;

}
else{
    
    
    $vat_purchase_rate=($vat_purchase_rate*100)+100;
    $total_cost=$total_value*100/$vat_purchase_rate;
    $tax_value=$total_value-$total_cost;

    
}

  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current-$tax_value),
         'debtor_current'=>$financial_accounts->debtor_current+$tax_value,

     ]
     ); 
     
   
$customerdata = supllier::find($resource_purchases->suplier_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $tax_value,
                'branchs_id' => $branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current-$tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$tax_value,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', $branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', $branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current-$tax_value),
         'debtor_current'=>$financial_accounts->debtor_current+$tax_value,

       ]
       ); 

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $tax_value,
                'branchs_id' => $branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current-$tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$tax_value,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

            ]
        );



  $financial_accounts= financial_accounts::find(181);
    financial_accounts::find(181)->update(
     [
         'current_balance'=> $financial_accounts->current_balance+$total_cost+$shipping,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost+$shipping,

     ]
     ); 

  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  181,
                'recive_amount' => $total_cost,
                'branchs_id' => $branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance+$total_cost+$shipping,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost+$shipping

            ]
        );


              $financial_accounts= financial_accounts::where('parent_account_number',181)->where('branchs_id', $branchs_id)->first();
    financial_accounts::where('parent_account_number',181)->where('branchs_id', $branchs_id)->update(
       [
        'current_balance'=> $financial_accounts->current_balance+$total_cost+$shipping,
         'debtor_current'=>$financial_accounts->debtor_current+ $total_cost+$shipping,

       ]
       ); 
// end addition
  credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $total_cost+$shipping,
                'branchs_id' => $branchs_id,
                'pay_method' => $paymentMethod,
                'note' =>  '  فاتورة مشتريات رقم :'.(string)$request,
                'currentblance'=>$financial_accounts->current_balance+$total_cost+$shipping,
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> 0,
                'debtor'=>$total_cost+$shipping

            ]
        );







        

        

        return 1;
    }
    public function update(Request $request)
    {

        //

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderDetails =   orderDetails::where('product_id', $request->id)
            ->where('order_owner', $request->ordernumber)->first();
        $resource_purchases =resource_purchases::where('orderId', $request->ordernumber)->first();
        $payment='Cash';
        $paymentMethod=$payment;
        $discount=0;
        $productData = products::find($request->id);
        products::where('id', $request->id)->Update([
            'numberofpice' => $productData->numberofpice - $request->return_quentity,
        ]);
        orderDetails::where('product_id', $request->id)
            ->where('order_owner', $request->ordernumber)->update(
                [
                    'returns_purchase' => $orderDetails->returns_purchase + $request->return_quentity,
                    'numberofpice' => $orderDetails->numberofpice - $request->return_quentity,
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );
        $i = 1;
        $vatrat=0;
        
        foreach (orderDetails::where('order_owner', $request->ordernumber)->get() as $item) {
                $vatrat=round($item->Added_Value  / $item->purchasingـprice,2);
            if ($item->numberofpice>=1 ) {
                $i = 0;
            }
        }
                $totalcost_discount=0;
                $totalvat_discount=0;

        if($i==1){
            
           $discount= $resource_purchases->discount;
   
        }
     
        $total_cost=round(($orderDetails->purchasingـprice * $request->return_quentity)-$discount,2);
        $tax_value=round($total_cost*$vatrat,2);





        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            
            resource_purchases::where('orderId', $request->ordernumber)->update(
                [
                    'recoveredـpieces' => $resource_purchases->recoveredـpieces + $request->return_quentity,
                    'In_debt' => ($resource_purchases->In_debt -($tax_value+$total_cost)) ,
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );
                    $resource_purchases =resource_purchases::where('orderId', $request->ordernumber)->first();




            $financial_accounts= financial_accounts::where('orginal_type',2)->where('orginal_id', $resource_purchases->suplier_id )->first();
              supllier::where('id', $resource_purchases->suplier_id)->update(
                [
                    'In_debt' => $financial_accounts->current_balance -($total_cost + $tax_value)
                ]
            );
            financial_accounts::where('orginal_type',2)->where('orginal_id', $resource_purchases->suplier_id )->update(
             [
                 'current_balance'=> $financial_accounts->current_balance -($total_cost + $tax_value),
                 'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value),

             ]
             ); 
             
                         credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => $payment,
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->current_balance-($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
             
            $financial_accounts= financial_accounts::where('parent_account_number',160)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',160)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
       ]
       ); 
            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );     
             
             
        }else{
                 
            resource_purchases::where('orderId', $request->ordernumber)->update(
                [
                    'recoveredـpieces' => $resource_purchases->recoveredـpieces + $request->return_quentity,
                    'In_debt' => $resource_purchases->In_debt -($total_cost + $tax_value),
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );

            $financial_accounts= financial_accounts::find(5);
            financial_accounts::find(5)->update(
               [
                   'current_balance'=> $financial_accounts->current_balance+ ($total_cost + $tax_value),
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
               ]
               ); 



            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  5,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
             $financial_accounts= financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',5)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
          'current_balance'=> $financial_accounts->current_balance+ ($total_cost + $tax_value),
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
       ]
       ); 
            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
            
   $financial_accounts= financial_accounts::where('parent_account_number',159)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',159)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
        
                   'debtor_current'=>$financial_accounts->debtor_current+($total_cost + $tax_value)
       ]
       ); 
            credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => ($total_cost + $tax_value),
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->current_balance+($total_cost + $tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=>  0,
                'debtor'=>($total_cost + $tax_value),
              

            ]
        );
    }
    
        
        

// new addition 2024-12-9




  $financial_accounts= financial_accounts::find(102);
    financial_accounts::find(102)->update(
     [
         'current_balance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
         'creditor_current'=>$financial_accounts->creditor_current+$tax_value,

     ]
     ); 
$customerdata = supllier::find($resource_purchases->suplier_id);

 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  102,
                'recive_amount' => $tax_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $tax_value,
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

            ]
        );

               $financial_accounts= financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->first();
    financial_accounts::where('parent_account_number',102)->where('branchs_id', Auth()->user()->branchs_id)->update(
       [
         'current_balance'=> $financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
         'creditor_current'=>$financial_accounts->creditor_current+$tax_value,

       ]
       ); 


 credittransactions::create(
            [
                // 'attachments'=>$the_file_path_2??'-',
                'user_id' => Auth()->user()->id,
                'customer_id' =>  $financial_accounts->id,
                'recive_amount' => $tax_value,
                'branchs_id' => Auth()->user()->branchs_id,
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
                'currentblance'=>$financial_accounts->debtor_current- ($financial_accounts->creditor_current+$tax_value),
                'Pay_Method_Name' => $paymentMethod,
                'created_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),
                'orginal_id'=>0,
                'creditor'=> $tax_value,
                'debtor'=>0,
                'vat'=>1,
                'name'=>$customerdata->name,
                'tax'=>$customerdata->TaxـNumber,

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
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
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
                'pay_method' => 'Cash',
                'note' =>  '   مرتجع مشتريات فاتورة رقم  :'.(string)$request->ordernumber,
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


       

        $data = [];

        $orderOwner = orderTosupllier::find($request->ordernumber);
        $orderdetails = orderDetails::where('order_owner', $request->ordernumber)->get();

        $user = User::find($orderOwner->user_id);
        $branch = $user->branch->name;
        $resource_purchases =   resource_purchases::where('orderId', $request->ordernumber)->first();

        $data = [
            'branch' => $branch,
            'supllier' => $orderOwner,
            'resource_purchases' => $resource_purchases,
            'product' => $orderdetails
        ];
        return    view('response_return_purchases', compact('data'));
    }

    public function updateproductalldatapurchases(Request $request)
    {

        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderDetails =   orderDetails::find($request->id);

        $resource_purchases =   resource_purchases::where('orderId', $orderDetails->order_owner)->first();

        $increasequantity = $request->quantity - $orderDetails->numberofpice;
        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice *  $orderDetails->numberofpice) + ($orderDetails->Added_Value *  $orderDetails->numberofpice))
                ]
            );
          
        } else {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice *  $orderDetails->numberofpice) + ($orderDetails->Added_Value *  $orderDetails->numberofpice))

                ]
            );
        }



        $productData = products::find($orderDetails->product_id);
        // products::where('id', $orderDetails->product_id)->Update([
        //     'numberofpice' => $productData->numberofpice - $orderDetails->numberofpice,
        // ]);
        orderDetails::where('id', $request->id)->update(
            [
                'numberofpice' => $orderDetails->numberofpice - $orderDetails->numberofpice
            ]
        );

        $avtPurcheseRate = Avt::find(2);

        $orderDetails =   orderDetails::find($request->id);

        $resource_purchases =   resource_purchases::where('orderId', $orderDetails->order_owner)->first();

        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt + (($request->pricepurchases * $request->quantity) + (($request->pricepurchases * $request->quantity) * $avtPurcheseRate->AVT))
                ]
            );
            // $supplier = supllier::find($resource_purchases->suplier_id);
            // supllier::where('id', $resource_purchases->suplier_id)->update(
            //     [
            //         'In_debt' => $supplier->In_debt + (($request->pricepurchases * $request->quantity) + ($request->pricepurchases * $request->quantity * $avtPurcheseRate->AVT))
            //     ]
            // );
        } else {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt + (($request->pricepurchases * $request->quantity) + ($request->pricepurchases * $request->quantity * $avtPurcheseRate->AVT))

                ]
            );
        }

        // $productData = products::find($orderDetails->product_id);
        // products::where('id', $orderDetails->product_id)->Update([
        //     'numberofpice' => $productData->numberofpice + $request->quantity,
        // ]);
        orderDetails::where('id', $request->id)->update(
            [
                'numberofpice' => $request->quantity,
                'purchasingـprice' => $request->pricepurchases,
                'Added_Value' => $request->pricepurchases * $avtPurcheseRate->AVT,
            ]
        );


        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل  بنجاج' : 'has been modified successfully';

        session()->flash('editpurchasein', $message);
        $recentsupllier = supllier::find($resource_purchases->suplier_id);

        $orderdetails = orderDetails::where('order_owner', $orderDetails->order_owner)->where('numberofpice', '>', 0)->get();
        // return  $orderdetails;
        $allProdctsD = [];
        $i = 0;
        foreach ($orderdetails as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->numberofpice,
                'purchasingـprice' => $product->purchasingـprice,
                'Added_Value' => $product->Added_Value,
                'saleperpice' => $product->sale_price,
                'count' => $i,
                'id' => $product->id
            ];
        }
        $data = [
            'message' => $message,
            'pay' => $resource_purchases->Pay_Method_Name,
            'recentsupllier' => $recentsupllier,
            "product" => $allProdctsD,
            "discount" => $resource_purchases->discount,
            "shipping_fee" => $resource_purchases['shipping fee'],

        ];
        return $data;
    }

    public function increasePurchase(Request $request)
    {

        //

        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderDetails =   orderDetails::find($request->id);
        $resource_purchases =   resource_purchases::where('orderId', $orderDetails->order_owner)->first();
        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt + (($orderDetails->purchasingـprice * $request->increasequentity) + ($orderDetails->Added_Value * $request->increasequentity))
                ]
            );
            // $supplier = supllier::find($resource_purchases->suplier_id);
            // supllier::where('id', $resource_purchases->suplier_id)->update(
            //     [
            //         'In_debt' => $supplier->In_debt + (($orderDetails->purchasingـprice * $request->increasequentity) + ($orderDetails->Added_Value * $request->increasequentity))
            //     ]
            // );
        } else {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    'In_debt' => $resource_purchases->In_debt + (($orderDetails->purchasingـprice * $request->increasequentity) + ($orderDetails->Added_Value * $request->increasequentity))

                ]
            );
        }

        // $productData = products::find($orderDetails->product_id);
        // products::where('id', $orderDetails->product_id)->Update([
        //     'numberofpice' => $productData->numberofpice + $request->increasequentity,
        // ]);
        orderDetails::where('id', $request->id)->update(
            [
                'numberofpice' => $orderDetails->numberofpice + $request->increasequentity
            ]
        );
        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل  بنجاج' : 'has been modified successfully';

        session()->flash('editpurchasein', $message);
        $recentsupllier = supllier::find($resource_purchases->suplier_id);

        $orderdetails = orderDetails::where('order_owner', $orderDetails->order_owner)->where('numberofpice', '>', 0)->get();
        // return  $orderdetails;
        $allProdctsD = [];
        $i = 0;
        foreach ($orderdetails as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->numberofpice,
                'purchasingـprice' => $product->purchasingـprice,
                'Added_Value' => $product->Added_Value,
                'saleperpice' =>$product->sale_price,
                'count' => $i,
                'id' => $product->id,
                
            ];
        }
        $data = [
            'pay' => $resource_purchases->Pay_Method_Name,
            'recentsupllier' => $recentsupllier,
            'In_debt' => $resource_purchases->In_debt,
            'discount' => $resource_purchases->discount,
            "product" => $allProdctsD,
            "shipping_fee" => $resource_purchases['shipping fee'],

        ];
        return $data;
        return  view('products.purchases', compact('data'));
    }



    public function updatePurchaseOrder(Request $request)
    {

        //

        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderDetails = orderDetails::where('order_owner', $request->ordernumber)->where('product_id', $request->id)->first();



        orderDetails::where('order_owner', $request->ordernumber)->where('product_id', $request->id)->update(
            [
                'numberofpice' => $orderDetails->numberofpice - $request->return_quentity
            ]
        );
        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل  بنجاج' : 'has been modified successfully';

        session()->flash('editpurchasein', $message);

        $orderdetails = orderDetails::where('order_owner',  $request->ordernumber)->where('numberofpice', '>', 0)->get();
        // return  $orderdetails;
        $listOfProduct = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->numberofpice * $orderitem->Added_Value;
            $totalPrice += $orderitem->numberofpice * $orderitem->purchasingـprice;
            $count++;
            $listOfProduct[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->product_id,
                "quantity" => $orderitem->numberofpice,
                "purchasingـprice" => $orderitem->purchasingـprice,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->numberofpice * $orderitem->Added_Value) + ($orderitem->numberofpice * $orderitem->purchasingـprice),
                "orderNo" => $orderitem->order_owner,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice
            ];
        }

        return $listOfProduct;
        return  view('products.purchases', compact('data'));
    }


    public function updatePurchaseOrderToIncrease(Request $request)
    {

        //

        //return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $orderDetails = orderDetails::where('order_owner', $request->ordernumber)->where('product_id', $request->id)->first();



        orderDetails::where('order_owner', $request->ordernumber)->where('product_id', $request->id)->update(
            [
                'numberofpice' => $orderDetails->numberofpice + $request->increasequentity
            ]
        );
        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل  بنجاج' : 'has been modified successfully';

        session()->flash('editpurchasein', $message);

        $orderdetails = orderDetails::where('order_owner',  $request->ordernumber)->where('numberofpice', '>', 0)->get();
        // return  $orderdetails;
        $listOfProduct = [];
        $count = 0;
        $totalAdded_value = 0;
        $totalPrice = 0;

        foreach ($orderdetails as $orderitem) {
            $totalAdded_value += $orderitem->numberofpice * $orderitem->Added_Value;
            $totalPrice += $orderitem->numberofpice * $orderitem->purchasingـprice;
            $count++;
            $listOfProduct[] = [
                "count" => $count,
                "productCode" => $orderitem->productData->Product_Code,
                "product_name" => $orderitem->productData->product_name,
                "product_id" => $orderitem->product_id,
                "quantity" => $orderitem->numberofpice,
                "purchasingـprice" => $orderitem->purchasingـprice,
                "Added_Value" => $orderitem->Added_Value,
                "total" => ($orderitem->numberofpice * $orderitem->Added_Value) + ($orderitem->numberofpice * $orderitem->purchasingـprice),
                "orderNo" => $orderitem->order_owner,
                "totalAdded_Value" => $totalAdded_value,
                "totalPrice" => $totalPrice
            ];
        }

        return $listOfProduct;
    }


    public function updatePurchase(Request $request)
    {

        //

        // return $request;
        app()->setLocale(LaravelLocalization::getCurrentLocale());


        $orderDetails =   orderDetails::find($request->id);
        $resource_purchases =   resource_purchases::where('orderId', $orderDetails->order_owner)->first();
        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    //  'recoveredـpieces' => $resource_purchases->recoveredـpieces + $request->return_quentity,
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice * $request->return_quentity) + ($orderDetails->Added_Value * $request->return_quentity))
                ]
            );
            // $supplier = supllier::find($resource_purchases->suplier_id);
            // supllier::where('id', $resource_purchases->suplier_id)->update(
            //     [
            //         'In_debt' => $supplier->In_debt - (($orderDetails->purchasingـprice * $request->return_quentity) + ($orderDetails->Added_Value * $request->return_quentity))
            //     ]
            // );
        } else {
            resource_purchases::where('orderId', $orderDetails->order_owner)->update(
                [
                    //     'recoveredـpieces' => $resource_purchases->recoveredـpieces + $request->return_quentity,
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice * $request->return_quentity) + ($orderDetails->Added_Value * $request->return_quentity))

                ]
            );
        }

        $productData = products::find($orderDetails->product_id);
        // products::where('id', $orderDetails->product_id)->Update([
        //     'numberofpice' => $productData->numberofpice - $request->return_quentity,
        // ]);
        orderDetails::where('id', $request->id)->update(
            [
                //'returns_purchase' => $orderDetails->returns_purchase + $request->return_quentity,
                'numberofpice' => $orderDetails->numberofpice - $request->return_quentity
            ]
        );
        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم تعديل  بنجاج' : 'has been modified successfully';

        session()->flash('editpurchasein', $message);
        $recentsupllier = supllier::find($resource_purchases->suplier_id);

        $orderdetails = orderDetails::where('order_owner', $orderDetails->order_owner)->where('numberofpice', '>', 0)->get();
        // return  $orderdetails;
        $allProdctsD = [];
        $i = 0;
        foreach ($orderdetails as $product) {
            $i++;
            $allProdctsD[] = [
                'Product_Code' => $product->productData->Product_Code,
                'product_name' => $product->productData->product_name,
                'quantity' => $product->numberofpice,
                'purchasingـprice' => $product->purchasingـprice,
                'Added_Value' => $product->Added_Value,
                'saleperpice' => $product->sale_price,
                'count' => $i,
                'id' => $product->id
            ];
        }
        $data = [
            'pay' => $resource_purchases->Pay_Method_Name,
            'recentsupllier' => $recentsupllier,
            "product" => $allProdctsD,
            'In_debt' => $resource_purchases->In_debt,
            'discount' => $resource_purchases->discount,
            "shipping_fee" => $resource_purchases['shipping fee'],

        ];
        return $data;
        return  view('products.purchases', compact('data'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        //====

        //return $request;
        $orderDetails =   orderDetails::where('product_id', $request->id)->where('product_name', $request->product_name)
            ->where('order_owner', $request->ordernumber)->first();
        //return $orderDetails;
        $resource_purchases =   resource_purchases::where('orderId', $request->ordernumber)->first();
        //return $resource_purchases;
        if ($resource_purchases->Pay_Method_Name == 'Credit') {
            resource_purchases::where('orderId', $request->ordernumber)->update(
                [
                    'recoveredـpieces' => $resource_purchases->recoveredـpieces + $orderDetails->numberofpice,
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice * $orderDetails->numberofpice) + ($orderDetails->Added_Value * $orderDetails->numberofpice)),
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );
            $supplier = supllier::find($resource_purchases->suplier_id);
            supllier::where('id', $resource_purchases->suplier_id)->update(
                [
                    'In_debt' => $supplier->In_debt - (($orderDetails->purchasingـprice * $orderDetails->numberofpice) + ($orderDetails->Added_Value * $orderDetails->numberofpice))
                ]
            );
        } else {
            resource_purchases::where('orderId', $request->ordernumber)->update(
                [
                    'recoveredـpieces' => $resource_purchases->recoveredـpieces + $orderDetails->numberofpice,
                    'In_debt' => $resource_purchases->In_debt - (($orderDetails->purchasingـprice * $orderDetails->numberofpice) + ($orderDetails->Added_Value * $orderDetails->numberofpice)),
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),


                ]
            );
        }

        $productData = products::find($request->id);
        products::where('id', $request->id)->Update([
            'numberofpice' => $productData->numberofpice - $orderDetails->numberofpice,
        ]);
        orderDetails::where('product_id', $request->id)->where('product_name', $request->product_name)
            ->where('order_owner', $request->ordernumber)->update(
                [
                    'returns_purchase' => $orderDetails->returns_purchase + $orderDetails->numberofpice,
                    'numberofpice' => $orderDetails->numberofpice - $orderDetails->numberofpice,
                    'updated_at'  =>  \Carbon\Carbon::now()->addHours(3),

                ]
            );

        //===
        $i = 1;
        foreach (orderDetails::where('order_owner', $request->ordernumber)->get() as $item) {
            if ($item->numberofpice > 0) {
                $i = 0;
            }
        }
        if ($resource_purchases->Pay_Method_Name == 'Credit' && $i == 1) {
            //  resource_purchases::where('orderId', $request->ordernumber)->update(
            //     [
            //         'In_debt' => $resource_purchases->In_debt - $resource_purchases->discount
            //     ]
            // );
            $supplier = supllier::find($resource_purchases->suplier_id);
            supllier::where('id', $resource_purchases->suplier_id)->update(
                [
                    'In_debt' => $supplier->In_debt  + $resource_purchases->discount
                ]
            );
        } else {
            // resource_purchases::where('orderId', $request->ordernumber)->update(
            //     [
            //         'In_debt' => $resource_purchases->In_debt - $resource_purchases->discount

            //     ]
            // );
        }

        $message = LaravelLocalization::getCurrentLocale() == 'ar' ? 'تم حذف  بنجاح' : 'has been deleted successfully';

        session()->flash('delete', $message);
        $orderOwner = orderTosupllier::find($request->ordernumber);
        $orderdetails = orderDetails::where('order_owner', $request->ordernumber)->get();

        $user = User::find($orderOwner->user_id);
        $branch = $user->branch->name;
        $resource_purchases =   resource_purchases::where('orderId', $request->ordernumber)->first();

        $data = [
            'branch' => $branch,
            'supllier' => $orderOwner,
            'resource_purchases' => $resource_purchases,

            'product' => $orderdetails
        ];
        //return $data;
        return    view('products.purchase_return', compact('data'));
    }


    public function getProductdJsonDecode($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $product =   products::find($id);

        return   json_encode($product);
    }
}
