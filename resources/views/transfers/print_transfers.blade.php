@extends('layouts.master')
@section('css')
<style>
:root {
    --ts-primary: #1c2b39; --ts-accent: #419BB2; --ts-border: #e3e8ee; --ts-radius: 10px; --ts-shadow: 0 2px 10px rgba(20,40,60,.06);
}
.card-invoice { border: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) !important; box-shadow: var(--ts-shadow) !important; }
table.key-buttons thead th, table.table thead th { background: var(--ts-primary) !important; color: #fff !important; font-size: 12.5px !important; padding: 10px !important; }
table.key-buttons tbody td, table.table tbody td { padding: 9px !important; font-size: 13.5px !important; }
.btn-danger { background: #e74c3c !important; border-color: #e74c3c !important; border-radius: 8px !important; font-weight: 600 !important; }

/* [تم الإصلاح] الشاشة دي ماكانتش متجاوبة (Responsive) مع شاشات الموبايل - العناصر كانت بتتزنق
   أو بتخرج بره حدود الشاشة. الكود ده بيظبط العرض على الشاشات الصغيرة (تابلت وموبايل). */
@media (max-width: 991px) {
    .breadcrumb-header, .main-parent > .breadcrumb-header {
        flex-wrap: wrap !important;
        row-gap: 10px;
    }
    .card-header form .row, .card-header .row {
        row-gap: 10px;
    }
    .row > [class*="col-"] {
        margin-bottom: 10px;
    }
}
@media (max-width: 767px) {
    .content-title { font-size: 16px !important; }
    .card-header { padding: 14px !important; }
    .btn, .button-eng {
        width: 100% !important;
        justify-content: center !important;
        margin-bottom: 8px;
    }
    .d-flex.justify-content-center, .d-flex.justify-content-end, .d-flex.justify-content-between {
        flex-wrap: wrap !important;
        row-gap: 10px;
        justify-content: center !important;
    }
    center > form, center {
        width: 100%;
    }
    /* أزرار الحفظ/الطباعة اللي كان ليها عرض ثابت بالبكسل (زي width:120px) بتبقى مرنة دلوقتي */
    button[style*="width"], a[style*="width"] {
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 8px;
    }
    .modal-dialog, .modal-special {
        max-width: 94vw !important;
        width: 94vw !important;
        margin: 6vh auto !important;
    }
    table.our-table, table.table {
        font-size: 11.5px !important;
    }
    table.our-table thead th, table.table thead th {
        padding: 8px 6px !important;
        font-size: 11px !important;
    }
    table.our-table tbody td, table.table tbody td {
        padding: 6px !important;
    }
    /* منع الزوم التلقائي في آيفون لما تدوس على حقل إدخال */
    .form-control, input, select.select2, textarea {
        font-size: 16px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        font-size: 14px !important;
    }
}
</style>
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
{{__('home.banks_transfer')}} @stop
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
    <a href="https://ebdeasoft.com/"><img src="{{ asset('assets/img/brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 110px; height: 70px;"></a>

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
<center> {{__('home.banks_transfer')}} </center>
                        <div class="row mg-t-12">
                         
                        
                        
                        </div>
                      

                        <div class="card-body">
                        <br><br><br><br>
								<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap table-bordered table-striped text-center">
										<thead>
											<tr>
                                    <th class="border-bottom-0">{{ __('home.decoumentNo') }}</th>
                                    <th class="border-bottom-0">{{ __('home.exportTime') }}</th>
                                    <th class="border-bottom-0">{{ __('report.date') }}</th>
                                    <th class="border-bottom-0"> {{__('home.branch')}}</th>
                                    <th class="border-bottom-0"> {{__('home.name')}}</th>
                                    <th class="border-bottom-0">{{__('accountes.Theamountpaid')}}</th>
                                    <th class="border-bottom-0">{{__('home.paymentmethod')}}</th>
                                    <th class="border-bottom-0">{{ __('home.notesClient') }} </th>
											</tr>
										</thead>
                                        <tbody>

                                        @foreach($data['transaction'] as $item)
                                        <tr>
                                        <td>{{$item['sent_abd_count']}}</td>
                                        <td>{{$item['created_at']}}</td>
                                        <td>{{$item['date']}}</td>
                                        <td>{{$item['branch']}}</td>

                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paid_amount']}}</td>
                                        <?php
                                        $paymethod=$item['method_pay'];
                                        ?>
                                        <td>{{$paymethod}}</td>
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
