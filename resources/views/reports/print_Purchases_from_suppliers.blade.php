@extends('layouts.master')
@section('css')
    <style>
        @media print {
            #print_Button {
                display: none;
            }
        }

        body {
            font: 13pt Georgia, "Times New Roman", Times, serif;
            line-height: 1.5;
            border-style: solid;

        }
    </style>
@endsection
@section('title')
    {{ __('home.print') }}
@stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class=" main-content-body-invoice" id="print">
                <div class="card card-invoice">
                       <div class="d-flex justify-content-center">
                            <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                                {{ __('home.print') }}
                                <i class="mdi mdi-printer ml-1"></i>
                        </div>
                        <br>

                    <div class="card-body">
                  <div class="invoice-header" style="display: flex;justify-content:space-between;width:100%">

                        <div class="billed-from" style="width:33%;text-align: center;" >
                            <br>
                             <span style="font-size:25px">{{Nameen}}</span>
                            <br>
                            <p dir=ltr> {{describtionen}} </p>
                            <span dir=ltr>{{STen}} </span>
                            <p dir=ltr> {{Taxen}} </p>

                        </div>
                        <div class="row">
                        <?php
$logo=camplogo;
    ?>
    <a href="https://ebdeasoft.com/"><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 110px; height: 70px;"></a>

                        </div>


                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>

                           <span style="font-size:25px">{{Namear}}</span>
                            <br>
                            <p> {{describtionar}}</p>
                            <p>{{STar}}</p>
                            <p>{{Taxar}}</p>

                        </div><!-- billed-from -->
                    </div><!-- invoice-header -->



                        @if (isset($Invoices))
                 <center>
    
           <div class="col-lg-3" id="start_at">
                        <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('home.exportTime') }} : </label>
                        <?php
                        $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");

                        ?>
                        <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $currentdata }}</label>

                    </div>
    
</center>
                                <br>
                        <div style="border-radius: 10px" class="card px-3 m-3">
                                <?php
                                $userId = 0;
                                $count = 0;
                                ?>
                                <?php
                                $userId = 0;
                                $startat = '';
                                $endat = '';
                                $totalprice = 0;
                                $totalshipping = 0;
                                $totaladdedvalue = 0;
                                $totaldiscount=0;
                                $vatrat=0;

                                ?>
                                @foreach ($Invoices as $invoice)
                                    <?php
                                     $totaldiscount+=$invoice->discount ;

                                    $totalshipping += $invoice['shipping fee'] + $invoice['Other expenses'];
                                    
                                    if ($count == 0) {
                                        $userId = $invoice->user_id;
                                        $startat = $invoice->created_at;
                                    }
                                    $endat = $invoice->created_at;
                                    $count++;
                                    
                                    ?>

                                    <br>


                       

                     
                                        <table class="table  table-striped table-bordered px-1 text-center" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>{{__('home.Invoice_no')}}</th>
                                                    <th>{{ $invoice->orderId }}</th>
                                                </tr>
                                                <tr>
                                                    <th class="border-bottom-0">#</th>
                                                    <th class="border-bottom-0">{{ __('report.date') }}</th>

                                                    <th class="border-bottom-0"> {{ __('home.productNo') }}</th>
                                                    <th class="border-bottom-0"> {{ __('home.product') }}</th>
                                                    <th class="border-bottom-0"> {{ __('home.quantity') }}</th>

                                                    <th class="border-bottom-0">{{ __('home.price') }}</th>
                                                    <th class="border-bottom-0"> {{ __('home.addedValue') }}</th>
                                                    <th class="border-bottom-0"> {{ __('home.total') }}</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $i = 0;
                                            $totalcost=0;
                                            ?>
                                            @foreach (App\Models\orderDetails::where('order_owner', $invoice->orderId)->get() as $product)
                                                <?php
                                                $i++;
                                                $totalcost+= $product->numberofpice * $product->purchasingـprice;
                                                $totalcost+= $product->returns_purchase * $product->purchasingـprice;




                                                $totalprice += $product->numberofpice * $product->purchasingـprice;
                                                $vatrat=round(  $product->Added_Value  / $product->purchasingـprice,2);
                                                ?>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        <td>{{ $invoice->created_at}}</td>
                                                        <td dir='ltr'>{{ $product->productData->Product_Code }}</td>
                                                        <td>{{ $product->productData->product_name }}</td>
                                                        <td>{{ ( $product->numberofpice+  $product->returns_purchase ) }}</td>
                                                        <td>{{ $product->purchasingـprice }}</td>
                                                        <td>{{ $product->Added_Value }}</td>
                                                        <td>{{( $product->numberofpice+  $product->returns_purchase ) * $product->Added_Value +( $product->numberofpice+  $product->recoveredـpieces ) * $product->purchasingـprice }}
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            @endforeach
                                         </table>

                                  
                                       
                                       
               <div class="">
                                        <table class="table  table-sm striped table-bordered text-center">
                                            <thead>
                                                <tr style="font-size:11px !important;color:#419BB2">
                                                    <th style="color: #419BB2">{{ __('home.paymentmethod') }}</th>
                                                    <th style="color: #419BB2">@if ($invoice->Pay_Method_Name == 'Cash')
                                                            {{ __('report.cash') }}
                                                    @elseif($invoice->Pay_Method_Name == 'Credit')
                                                        {{ __('report.credit') }}
                                                    @else
                                                        {{ __('report.shabka') }}
                                                    @endif</th>
                                                
                                                
                                                    <th style="color: #419BB2">{{ __('home.suppliername') }}</th>
                                                    <th style="color: #419BB2">{{ $invoice->supllier->name }}</th>
                                                
                                                    
                                                    <th style="color: #419BB2">{{ __('report.Shipping and unloading cost') }}</th>
                                                    <th style="color: #419BB2">{{ $invoice['shipping fee'] + $invoice['Other expenses'] }}</th>
                                                    
                                                    
                                                    <th style="color: #419BB2">{{ __('home.total') }}</th>
                                                    <th style="color: #419BB2">{{ ($totalcost-$invoice['discount'])+(    ($totalcost-$invoice['discount']) *$vatrat) + ($invoice['shipping fee'] + $invoice['Other expenses']) }}</th>
                                                
                                            
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                @endforeach
                                


                                <div class="table-padding">
                                    <table class="table table-bordered table-hover text-center table-striped mt-5">
                                        <thead>
                                          <tr>
                                              <th scope="col">{{ __('report.totalprice') }}</th>
                                            <th scope="col">{{ __('home.the amount') }}</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('report.totalpricewithoudtax') }}</td>
                                                <td>{{ $totalprice }}</td>
                                            </tr>
                                                <tr>
                                                <td>{{ __('home.discount') }}</td>
                                                <td>{{ $totaldiscount }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('report.totaltax') }}</td>
                                                <td>{{ round(($totalprice-$totaldiscount)*$vatrat,2) }}</td>
                                            </tr>
                                        
                                            <tr>
                                                <td>{{ __('report.totalallprice') }}</td>
                                                <td>{{ round(($totalprice-$totaldiscount)*$vatrat,2)  + ($totalprice-$totaldiscount) + $totalshipping-$totaldiscount  }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                </div>



                                <br>

                                <br>
                        @endif


                    </div>
                    <hr class="mg-b-50">



               
                    <br>


                </div>
            </div>
        </div>
    </div><!-- COL-END -->
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>


    <script type="text/javascript">
        function printDiv() {
            var printContents = document.getElementById('print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>

@endsection
