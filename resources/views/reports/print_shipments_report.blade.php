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
                <div class="card-body">
       <div class="invoice-header" style="display: flex;justify-content:space-between;width:100%" dir=rtl>




                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>

                            <span  class="tx-18 thick">{{Namear}}</span>
                            <br>
                            <p class="tx-16 thick"> {{describtionar}}</p>
                            <p class="tx-16 thick">{{STar}}</p>
                            <p class="tx-16 thick">{{Taxar}}</p>

                        </div><!-- billed-from -->
                        <div class="row">
                            <?php
                            $logo = camplogo;
                            ?>
                            <a href="https://ebdeasoft.com/"><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 110px; height: 100px;"></a>

                        </div>

                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>
                            <span class="tx-18 thick">{{Nameen}}</span>
                            <br>
                            <p class="tx-16 thick" > {{describtionen}} </p>
                            <span class="tx-16 thick">{{STen}} </span>
                            <p class="tx-16 thick"> {{Taxen}} </p>

                        </div>

                    </div><!-- invoice-header -->
                    <br>
                    <center>
                        
                                          <span>{{ __('home.banks_transfer') }}</span>
  
                        
                    </center>
                    <br>
                    <div class="col-lg-3" id="start_at">
                        <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('home.exportTime') }} : </label>
                        <?php
                        $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");

                        ?>
                        <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $currentdata }}</label>

                    </div>
                    <div style="border-radius: 10px" class="card m-3 p-3">
                        <div class="table-responsive">
                            <span style="font-size: 17px;color:#419BB2" class="text-">{{ __('report.from') }} :
                                {{ $start_at }} </span>
                            &nbsp;&nbsp;
                            <span style="font-size: 17px;color:#419BB2" class="text">{{ __('report.to') }} :
                                {{ $end_at }} </span>


                            <br>

                            <div class="table-responsive hoverable-table my-3">
                <div style="border-radius: 10px" class="card m-3 p-3">
                    <div class="table-responsive">
                        <div class="table-responsive hoverable-table table-padding">
                            <table class="table table-bordered table-striped table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                                      <col style="width:2%">
        <col style="width:10%">
        <col style="width:25%">
        <col style="width:20%">
        <col style="width:7%">
        <col style="width:9%">
        <col style="width:6%">
        <col style="width:20%">

        <thead>
            <tr>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.acount_name') }} </th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.exportTime') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.status') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.total') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.paymentmethod') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>

            @foreach ($data as $product)
            <?php $i++; ?>
    <?php
                    $pay = __('home.Bank_transfer');
                ?>
            <tr >
                <td data-target="id">{{ $product->id }}</td>
                <td dir="ltr" data-target="id">{{ $product->financial_accounts_data->name??'' }}</td>
                <td data-target="numberofpice">{{ $product->date }}</td>
                <td data-target="numberofpice">{{ $product->created_at }}</td>
                <td data-target="numberofpice">{{ $product->branch->name }}</td>
                @if($product->status==0)
                <td data-target="numberofpice" style="color:red; font-weight: bold;
font-size:20px">{{ __('home.waiting') }}</td>

                @endif
                         @if($product->status==1)
                <td data-target="numberofpice" style="color:orange; font-weight: bold;
font-size:20px">{{ __('home.data_complete') }}</td>

                @endif
                         @if($product->status==2)
                <td data-target="numberofpice" style="color:green; font-weight: bold;
font-size:20px">{{ __('home.confirm_done') }}</td>

                @endif
                <td data-target="numberofpice" style="  font-weight: bold;
font-size:20px">{{ $product->price }}</td>
                <td>{{$pay}}</td>
             

            </tr>
            @endforeach
                            </table>




                            </div>
                            <hr class="mg-b-40">



                            <div class="d-flex justify-content-center">
                                <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                                    {{ __('home.print') }}
                                    <i class="mdi mdi-printer ml-1"></i>
                                </button>
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