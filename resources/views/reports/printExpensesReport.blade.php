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
                        <div class=" mg-t-12">
                            <br>
                            <br>
                            <span>{{ __('report.Expenses') }}</span>
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
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if (isset($Invoices))
                                        <div style="border-radius: 10px" class="card m-3 p-3">
                                            <div class="">
                                                <div class=" hoverable-table px-1">
                                                    <table
                                                        class="table table-hover table-bordered table-striped text-center ">
                                                        <thead>
                                                            <tr>
                                                                <th class="border-bottom-0">#</th>
                                                                <th class="border-bottom-0">{{ __('report.date') }}</th>
                                                                <th class="border-bottom-0">{{ __('users.branch') }} </th>
                                                                <th class="border-bottom-0"> {{ __('accountes.user') }}</th>
                                                                <th class="border-bottom-0">
                                                                    {{ __('accountes.Theamountpaid') }}</th>
                                                                <th class="border-bottom-0">
                                                                    {{ __('accountes.Reasonforspendingmoney') }}</th>
                                                                <th class="border-bottom-0"> {{ __('home.paymentmethod') }}
                                                                </th>
                                                                <th class="border-bottom-0"> {{ __('home.notesClient') }}</th>


                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $i = 0;
                                                        ?>
                                                        <?php
                                                        $count = 0;
                                                        ?>
                                                        <?php
                                                        $startat = '';
                                                        $endat = '';
                                                        $totalprice = 0;
                                                        $totaladdedvalue = 0;
                                                        ?>
                                                        @foreach ($Invoices as $invoice)
                                                            <?php
                                                            $totalprice += $invoice->recive_amount;
                                                        
                                                            $count++;
                                                            $i++;
                                                            ?>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ $i }}</td>
                                                                    <td>{{ $invoice->created_at}}</td>

                                                                    <td>{{ $invoice->branch->name }}</td>
                                                                    <td>{{ $invoice->user->name }}</td>
                                                                    <td>{{ $invoice->recive_amount }}</td>
                                                                    <td> {{$invoice->financial_accounts_data->name }}</td>

                                                                    <td>

                                                                        @if ($invoice->Pay_Method_Name == 'Cash')
                                                                            <span
                                                                                class="text-success">{{ __('report.cash') }}</span>
                                                                                @elseif($invoice->Pay_Method_Name == 'Credit')
                                                                            <span
                                                                                class="text-danger">{{ __('report.credit') }}</span>
                                                                                @elseif($invoice->Pay_Method_Name == 'Bank_transfer')
                                                                            <span
                                                                                class="text-danger">{{ __('home.Bank_transfer') }}</span>
                                                                        @else
                                                                            <span
                                                                                class="text-warning">{{ __('report.shabka') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $invoice->note }}</td>

                                                                </tr>
                                                        @endforeach

                                                        </tbody>

                                                    </table>

                                                    <div class="table-padding">
                                                        <table
                                                            class="table table-bordered table-hover text-center table-striped mt-5">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col"></th>
                                                                    <th scope="col">{{ __('report.totalprice') }}</th>
                                                                    <th>{{ __('home.the amount') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">1</th>
                                                                    <td>{{ __('home.total') }}</td>
                                                                    <td>{{ $totalprice }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>


                                                    <br>
                                                    @if (count($Invoices) > 0)
                                                        <br>
                                                    @endif
                                    @endif

                                

                                </div>
                                <hr class="mg-b-40">



                             
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
