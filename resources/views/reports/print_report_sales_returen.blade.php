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
{{__('home.print')}}
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
                    <div class=" mg-t-12">
                        <br>
                        <br>
                        <br>
                        <div class="row d-flex justify-content-center">


                            <div class="col-lg-3" id="start_at">
                                <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('home.exportTime') }} : </label>
                                <?php
                                $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");

                                ?>
                                <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $currentdata }}</label>

                            </div>

                        </div>
                        <br>
                        <?php
                        $userId = 0;
                        $count = 0;
                        ?>
                        <?php
                        $userId = 0;
                        $startat = '';
                        $endat = '';
                        $totalprice = 0;
                        $totaladdedvalue = 0;
                        $totalpricefinal = 0;
                        $invoiceIds = [];
                        $i = 0;
                        ?>

                        <div class="card-body">
                            <div>






                                <table class="table table-bordered table-striped" id="example1">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0">#</th>
                                            <th class="border-bottom-0">{{__('report.date')}}</th>
                                            <th class="border-bottom-0">{{__('report.invoiceNo')}}</th>


                                            <th class="border-bottom-0"> {{__('home.quantity')}}</th>
                                            <th class="border-bottom-0">{{__('home.price')}}</th>
                                            <th class="border-bottom-0"> {{__('home.addedValue')}}</th>
                                            <th class="border-bottom-0"> {{__('home.discount')}}</th>
                                            <th class="border-bottom-0"> {{__('home.total')}}</th>

                                        </tr>
                                    </thead>
                                    <?php
                                    $avt = App\Models\Avt::find(1);
                                    $saleavt = $avt->AVT;
                                    ?>
                                    @foreach ($Invoices as $invoice)
                                    <?php
                                    $totaladdedvalue += (($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice) * $saleavt;
                                    $totalpricefinal += ($invoice->return_Unit_Price * $invoice->return_quantity) - $invoice->discountvalue - $invoice->discountoninvoice;

                                    if ($count == 0) {
                                        $startat = $invoice->created_at;
                                    }
                                    $endat = $invoice->created_at;
                                    $count++;
                                    ?>

                              
                                    @if(!in_array($invoice->invoice_id, $invoiceIds))
                                    <?php
                                    $invoiceIds[] = $invoice->invoice_id;
                                    ?>
                                    @endif
                                    @endforeach


                                    <?php


                                    ?>


                                    <tbody>
                                        @foreach($invoiceIds as $invoiceid)
                                        <?php
                                        $Invoices = App\Models\return_sales::where('invoice_id', $invoiceid)->get();
                                        $date = 0;
                                        $numberofPice = 0;
                                        $total_addedvalue = 0;
                                        $totalprice = 0;
                                        foreach ($Invoices as $invoice) {
                                            $date = $invoice->created_at;
                                            $numberofPice += $invoice->return_quantity;
                                            $totalprice  += $invoice->return_quantity * $invoice->return_Unit_Price - $invoice->discountvalue - $invoice->discountoninvoice;
                                        }
                                        $i++;
                                        $date = explode(" ",  $date);

                                        ?>
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$date[0]}}</td>
                                            <td>{{$invoiceid}}</td>
                                            <td>{{ $numberofPice }}</td>
                                            <td>{{ $totalprice}}</td>
                                            <td>{{round( $totalprice*$saleavt,2)}}</td>
                                            <td>{{ $invoice->discountvalue + $invoice->discountoninvoice}}</td>
                                            <td>{{round(( $totalprice)+( $totalprice*$saleavt),2)}}</td>

                                        </tr>
                                        @endforeach

                                    </tbody>

                                </table>


                                <div class="table-padding">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('report.totalpricewithoudtax')}}</th>
                                                <th>{{ round($totalpricefinal,2)}}</th>
                                            </tr>
                                            <tr>
                                                <th>{{__('report.totaltax')}}</th>
                                                <th>{{round($totaladdedvalue,2)}}</th>
                                            </tr>
                                            <tr>
                                                <th>{{__('report.totalallprice')}}</th>
                                                <th>{{round(($totaladdedvalue+ $totalpricefinal),2)}}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                            </div>
                        </div>
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