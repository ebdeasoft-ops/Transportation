  @extends('layouts.master')
@section('css')
<style>
    @media print {
            @page { 
        size: landscape;
    }
        #print_Button {
            display: none;
        }
          .double {
      border: 3px solid grey;
      border-radius: 5px;
      width: 200px;
      font-size: 15px !important;

    }

    }
  .double {
      border: 3px solid grey;
      border-radius: 5px;
      width: 200px;
      font-size: 15px !important;

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
                  <div class="invoice-header" dir =rtl style="display: flex;justify-content:space-between;width:100%">

                      
                        
                        
                             <div class="billed-from" style="width:33%;text-align: center;">
                            <br>

                           <span style="font-size:16px">{{Namear}}</span>
                            <br>
                            <p> {{describtionar}}</p>
                            <p>{{STar}}</p>
                            <p>{{Taxar}}</p>

                        </div><!-- billed-from -->
                        <div class="row">
                        <?php
$logo=camplogo;
    ?>
    <a href="https://ebdeasoft.com/"><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 110px; height: 100px;"></a>

                        </div>

  <div class="billed-from" style="width:33%;text-align: center;" >
                            <br>
                             <span style="font-size:19px">{{Nameen}}</span>
                            <br>
                            <p dir=ltr style="font-size:12px"> {{describtionen}} </p>
                            <span dir=ltr>{{STen}} </span>
                            <p dir=ltr> {{Taxen}} </p>

                        </div>
                   
                    </div><!-- invoice-header -->

                    <div class="card-body">
                        <br>
                   <center>    <span class='double'>ارباح المبيعات  Sales profits</span></center> 
                        
                        <br>
                        <div class="">

                                <?php
                                $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");

                                ?>

                          
                                          <div class="table-padding table-responsive ">
                                <table style="border: 2px solid rgba(0,0,0,0)" class="table table-striped table-bordered text-center my-2">
                                    <col style="width:15%">
                                    <col style="width:15%">
                                    <col style="width:15%">
                                    <col style="width:20%">
                                    <col style="width:15%">
                                    <col style="width:20%">
                                    <thead>
                                        <tr>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('report.fromdate') }}:</label>

                                            </th>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $start }}</label>
                                            </th>

                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('report.todate') }}</label>

                                            </th>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1">{{ $end }}</label>
                                            </th>



                                           
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('home.exportTime') }} </label>

                                            </th>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $currentdata }}</label>
                                            </th>
                                        </tr>


                                    </thead>
                                </table>
                            </div>
                            <div style="border-radius: 10px" class="card pb-0 px-3">
                                <br>

                                <?php
                                $count = 0;
                             
                                $totalcost = 0;
                                $totalsales = 0;
                                $totaldiscount = 0;
                                $totalprofit = 0;
                                $i=0;
                                $quantity=0;
                                $quentitywithreturn=0;
                                $discountreturn=0;
                                $totaldiscountelment=0;
                                ?>
                              
                                <br>





                                <table style="width:100%"  class="table   table-bordered text-center">
                                    <thead>
                                        <tr>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.sallerName') }} </th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.clietName') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.paymentmethod') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.total') }}</th>
                <th class="border-bottom-0"> {{ __('report.profit') }}</th>

                                        </tr>
                                    </thead>
                                  
                                    @foreach ($data['sales'] as $product)
                                  <?php
                                      
                                       
                                        $i++;
                                        $totalsales=0;
                                         $totalcost=0;
                                     

                                foreach (App\Models\sales::where('invoice_id', $product->id)->get() as $item){
                                                
                                $totalcost += ($item->productData->purchasingـprice*$item->quantity);
                                $totalsales+=($item->Unit_Price *$item->quantity );
                                               }
                                $profit=$totalsales-$totalcost-$product->discount;
                                $totalprofit +=$profit;
                                 
                    $avt = App\Models\Avt::find(1);
                    $saleavt = $avt->AVT;

              
                $pay = '';
                if ($product->Pay == 'Cash') {
                    $pay = __('report.cash');
                } elseif ($product->Pay == 'Shabka') {
                    $pay = __('report.shabka');
                } elseif ($product->Pay == "Credit") {
                    $pay = __('report.credit');
                } elseif ($product->Pay == "Bank_transfer") {
                    $pay = __('home.Bank_transfer');
                } else {
                    $pay = __('home.Partition of the amount');
                }

                ?>
                                    <tbody>
                                        <tr>
                <td data-target="id">{{ $product->id }}</td>
                <td data-target="id">{{ $product->user->name??'' }}</td>
                <td dir="ltr" data-target="id">{{ $product->customer->name??'' }}</td>
                <td data-target="numberofpice">{{ $product->created_at }}</td>
                <td data-target="numberofpice">{{ $product->branch->name }}</td>
                <td> {{$profit  }}  </td>
                <td>{{$pay}}

                    @if($product->Pay =="Partition")
                    <br>
                    {{__('report.cash')}} : {{round(($product->Price-$product->discount) + (($product->Price-$product->discount)*$saleavt),2)==0?__('home.return'):round(($product->Price-$product->discount) + (($product->Price-$product->discount)*$saleavt),2)-$product->bankamount-$product->Bank_transfer }}
                    {{__('report.shabka')}} : {{$product->bankamount}}
                    {{__('home.Bank_transfer')}} : {{$product->Bank_transfer}}
                    @endif
                </td>
                                <td data-target="numberofpice">{{ round(($product->Price-$product->discount) + (($product->Price-$product->discount)*$saleavt),2)}}</td>

                                                                                      
                                        </tr>
                                        @endforeach
                                       <tr>
                                           <td>-</td>
                                           <td>-</td>
                                           <td>-</td>
                                           <td>-</td>
                                           <td>-</td>
                                           <td>-</td>
                                           <td>{{$totalprofit}}</td>
                                        

                                       </tr>
                                    </tbody>
                                    
                                </table>


                               

                                <br>
 <table class="table table-responsive table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0">{{ __('report.profit') }}</th>
                                            <th class="border-bottom-0"> {{$totalprofit}}</th>
                                         
                                        </tr>
                                    </thead>
                                    
                                    
                                    </table>
                            

                            </div>
                        
                        </div>
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