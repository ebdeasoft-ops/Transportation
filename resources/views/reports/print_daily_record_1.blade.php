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
{{__('home.Daily_record')}}
@stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
        <?php
                            $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");
                            $banktransfertotal=0;
                            ?>

    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class=" main-content-body-invoice" id="print">
                <div class="card card-invoice">
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
<br>
<center> <h3>
    {{__('home.Daily_record')}}
</h3> </center>
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
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $start_at}}</label>
                                            </th>

                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('report.todate') }}</label>

                                            </th>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1">{{ $end_at }}</label>
                                            </th>
                                          
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ __('home.exportTime') }} </label>

                                            </th>
                                            <th style="background-color: rgba(236, 240, 250, 1);"> <label style="font-size: 14px;color:#419BB2 ;font-weight:bold;" for="exampleFormControlSelect1"> {{ $currentdata }}</label>
                                            </th>
                                        </tr>


                                    </thead>
                                </table>
                            </div>
                           
                        <div class="card-body">
                        <br><br><br><br>
								<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap table-bordered table-striped text-center">
										<thead>
											<tr>

                                    <th class="border-bottom-0"> {{__('home.decoumentNo')}}</th>
                                    <th class="border-bottom-0"> {{__('home.name')}}</th>
                                                <th class="border-bottom-0">{{ __('home.credit') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.debit') }}</th>
                                    <th class="border-bottom-0">{{__('home.date')}}</th>
                                                                        <th class="border-bottom-0">{{ __('home.notesClient') }} </th>

											</tr>
										</thead>
                                        <tbody>
                                            
                                        @foreach( $List_dely_record     as $item)
                                        <tr>  
                                        <td>{{$item['dely_record']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['debtor']}}</td>
                                        <td>{{$item['creditor']}}</td>
                                      
                                        <td style="color:green">{{$item['date']}}</td>
                                        <td>{{$item['note']}}</td>

                                        </tr>   
                                        @endforeach  
                                          
                                        </tbody>
										<tbody>
										</tbody>
									</table>
                                    <br>
                                    <br>
                        <p>{{__('home.employeereciver')}} : {{Auth()->user()->name}}</p>
                        <br>
                        <p>{{__('home.thesignature')}} : </p>
                                    </div>

                        <hr class="mg-b-40">



                        <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button" onclick="printDiv()"> <i
                                class="mdi mdi-printer ml-1"></i>{{__('home.print')}}</button>
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
    <script>
              $(document).ready(function() {
            $(function() {
var timeout = 4000; // in miliseconds (3*1000)
$('.alert').delay(timeout).fadeOut(500);
});
           
              });
              $(document).ready(function() {
       
    });
    </script>

@endsection
