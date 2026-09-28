@extends('layouts.master')
@section('css')
<style>
:root {
    --ts-primary: #1c2b39; --ts-primary-light: #2d4356; --ts-accent: #419BB2; --ts-accent-dark: #2f7d91;
    --ts-success: #2ecc71; --ts-danger: #e74c3c; --ts-warning: #f39c12; --ts-border: #e3e8ee; --ts-bg-soft: #f7f9fb;
    --ts-radius: 12px; --ts-radius-sm: 8px; --ts-shadow: 0 2px 10px rgba(20, 40, 60, 0.06);
}
.card { border: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) !important; box-shadow: var(--ts-shadow) !important; overflow: hidden; }
.card-header { background: var(--ts-bg-soft) !important; border-bottom: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) var(--ts-radius) 0 0 !important; padding: 18px 20px !important; }
.content-title { color: var(--ts-primary) !important; font-weight: 700 !important; }
.form-control, .select2-container .select2-selection--single { border: 1.5px solid var(--ts-border) !important; border-radius: var(--ts-radius-sm) !important; min-height: 42px !important; font-size: 14px !important; transition: border-color .15s ease, box-shadow .15s ease; }
.select2-container .select2-selection--single .select2-selection__rendered { line-height: 42px !important; }
.select2-container .select2-selection--single .select2-selection__arrow { height: 40px !important; }
.form-control:focus, .select2-container--focus .select2-selection--single { border-color: var(--ts-accent) !important; box-shadow: 0 0 0 3px rgba(65, 155, 178, 0.15) !important; }
label, .parent-label, .control-label { font-weight: 600 !important; color: var(--ts-primary-light) !important; font-size: 13px !important; margin-bottom: 6px !important; display: inline-block; }
.btn { border-radius: var(--ts-radius-sm) !important; font-weight: 600 !important; font-size: 13.5px !important; padding: 8px 18px !important; transition: transform .15s ease, box-shadow .15s ease; }
.btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(20,40,60,.10); }
.btn-success { background: var(--ts-accent) !important; border-color: var(--ts-accent) !important; }
.btn-success:hover { background: var(--ts-accent-dark) !important; border-color: var(--ts-accent-dark) !important; }
.btn-danger { background: var(--ts-danger) !important; border-color: var(--ts-danger) !important; }
.btn-secondary { background: #eef1f4 !important; border-color: #eef1f4 !important; color: var(--ts-primary) !important; }
.btn-info { background: var(--ts-primary-light) !important; border-color: var(--ts-primary-light) !important; }
.button-eng { display: inline-flex !important; align-items: center; gap: 6px; height: auto !important; width: auto !important; padding: 9px 16px !important; white-space: nowrap; }
.button-eng i { font-style: normal; }
.btn-warning { background: var(--ts-warning) !important; border-color: var(--ts-warning) !important; }
.table-responsive, .hoverable-table { border-radius: var(--ts-radius) !important; overflow: hidden !important; border: 1px solid var(--ts-border) !important; }
table.our-table, table.table { margin-bottom: 0 !important; }
table.our-table thead th, table.table thead th { background: var(--ts-primary) !important; color: #fff !important; font-weight: 600 !important; font-size: 12.5px !important; border: none !important; padding: 12px 10px !important; white-space: nowrap; }
table.our-table tbody td, table.table tbody td { padding: 10px !important; font-size: 13.5px !important; border-color: var(--ts-border) !important; vertical-align: middle !important; }
table.our-table tbody tr:hover, .hoverable-table tbody tr:hover { background: var(--ts-bg-soft) !important; }
.modal-content { border-radius: var(--ts-radius) !important; border: none !important; box-shadow: 0 10px 40px rgba(0,0,0,.15) !important; }
.modal-header { background: var(--ts-bg-soft) !important; border-bottom: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) var(--ts-radius) 0 0 !important; }
.modal-title { color: var(--ts-primary) !important; font-weight: 700 !important; }
.modal-footer { border-top: 1px solid var(--ts-border) !important; }
.alert { border-radius: var(--ts-radius-sm) !important; border: none !important; }
.alert-danger { background: #fdecea !important; color: #c0392b !important; }
.alert-warning { background: #fef6e6 !important; color: #a76b06 !important; }

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
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<!-- Internal Spectrum-colorpicker css -->
<link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">

<!-- Internal Select2 css -->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

@section('title')
{{ __('home.Receipt document') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('home.Receipt document') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                </span>
            </div>
        </div>
        <div class="row">
           <button class="modal-effect btn btn-sm btn-info button-eng" data-effect="effect-scale" data-toggle="modal" href="#updateinvoicebyidmodale" title="تحديد"><i
                                    class="las"> {{ __('home.update_decument') }}</i>
                                        <svg style="width:16px !important" class="svg-icon-buttons" viewBox="0 0 20 20">
                                            <path d="M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z"></path>
                                        </svg>
                                    </button>

                                    <button style="background-color: var(--ts-success) !important; border-color: var(--ts-success) !important;" class="modal-effect btn btn-sm btn-info button-eng" data-effect="effect-scale" data-toggle="modal" href="#createsupplier" title="تحديد"><i class="las"> {{__('home.addnewsupplier')}}</i>
                            <svg style="width: 18px;height:18px" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                <path d="M16 19h6"></path>
                                <path d="M19 16v6"></path>
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4"></path>
                            </svg>
                        </button>
</div>
    </div>
    <!-- breadcrumb -->
    @endsection
    @section('content')

    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <button aria-label="Close" class="close" data-dismiss="alert" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>خطا</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- row -->
    <div class="row">

        <div class="col-xl-12">
            <div class="card mg-b-20">

            <div class="card-header pb-0">


                    <form enctype="multipart/form-data" method="POST" role="search" name="form-name" id='formdata' autocomplete="off">
                        {{ csrf_field() }}
                        @csrf

                        <input type="hidden" id="token_search" value="{{ csrf_token() }}">



                        <div class="row">
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0" >
                                <p class="mg-b-10"> {{__('home.paymentmethod')}}</p>
                                <select class="form-control select2" name="paymentmethod" id="paymentmethod" required>
                                <option value="-"> {{__('home.acount_name')}}</option>
                                    @foreach (App\Models\financial_accounts::where('parent_account_number',4)->orwhere('parent_account_number',5)->where('parent_account_number','!=',NULL)->get() as $section)
                                    <option value="{{ $section->id }}"> {{ $section->name }} ({{ $section->account_number }})</option>
                                    @endforeach
                                </select>
                            </div><!-- col-4 -->

                            <div class="col-lg-2 mg-t-20 mg-lg-t-0" id="type">
                                <p class="mg-b-10 parent-label"> . </p>
                                <select class="form-control parent-input " name="pay" id="pay" required>
                                    <option id="Cash" value="Cash"> {{ __('report.cash') }}</option>
                                    <option value="Shabka"> {{ __('report.shabka') }} </option>
                                    <option value="Bank_transfer"> {{ __('home.Bank_transfer') }} </option>


                                </select>
                            </div>


                            <div class="col-lg-2">
                                <label for="inputName" class="control-label parent-label"> {{ __('accountes.Theamountpaid') }}
                                </label>
                                <input type="text" class="form-control  parent-input" id="cashreceived" name="cashreceived" title="يرجي ادخال الكمية  " value=0 required onkeyup="moneyconvertToNumber()">
                            </div>

                   
                            <div class="col-lg-2 mg-t-20 mg-lg-t-0" id="type">
                                <p class="mg-b-10"> {{__('home.saerch_by_numberaccount_or_name')}}</p>
                                <select class="form-control select2" name="clientnamesearch" id="clientnamesearch" required>
                                <option value="-"> {{__('home.acount_name')}}</option>


                                    @foreach (App\Models\financial_accounts::get() as $section)
                                    <option value="{{ $section->id }}"> {{ $section->name }} ({{ $section->account_number }})</option>
                                    @endforeach
                                </select>
                            </div><!-- col-4 -->


     <div class="col-lg-2 parent-label" id="start_at">
                                <label for="exampleFormControlSelect1"> {{ __('home.exportTime') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div><input class="form-control parent-input fc-datepicker" value="{{ $start_at ?? '' }}" name="date" id="date" placeholder="YYYY-MM-DD" type="text" required>
                                </div>
                            </div>

                          
                              <div class="col-lg-2" >
                  <label> {{__('home.attachments')}}</label>
                  <input autocomplete="off" onchange="readURL(this)" type="file" id="attachments" name="attachments" class="form-control">
                
               </div>
               
              
            </div>

            <input type="number" class="form-control " name="id_create" id="id_create" title=" رقم الفاتورة " value=1 readonly required hidden>

                        
                                                <div class="row">
                                                       <div class="col-lg-2">
                                <label for="inputName" class="control-label parent-label">{{ __('home.notesClient') }} </label>
                                <input type="text" class="parent-input form-control" id="notes" name="notes" value='-' title="يرجي ادخال ملاحظا ت  ">
                            </div>
                    
                      
       <div class="col-lg-2 mb-2">
                                <label for="inputName" class="control-label parent-label">{{ __('home.expensesHaveavt') }}</label>
                                <select name="AVT"  id="AVT" class="form-control parent-input">
                                    <!--placeholder-->
                                                                        <option value=0 selected> {{ __('home.nothaveAVT')}}</option>

                                    <option value=1 > {{__('home.haveAVT')}}</option>
                                </select>
                            </div>
                                              <div class="col-lg-1 mb-2">
                                <label for="inputName" class="control-label parent-label">{{ __('home.cost_center') }}</label>
                                <br>
                         <input style="  width: 30px;
    height: 30px;
    accent-color: #007bff;  optional: changes checkbox color "type="checkbox" id="checkboxId" name="checkboxId" value="checkboxValue">

                            </div>
                                <div  class="col-lg-2 mg-t-20 mg-lg-t-0" id="AVT_Div_cost">
                                <p class="mg-b-10"> {{__('home.cost_center')}}</p>
                                <select style="width:100%" class="form-control select2" name="cost_center" id="cost_center" required>
                                <option id="value_zero" value="0"> {{__('home.cost_center')}}</option>


                                    @foreach (App\Models\Cost_centers::get() as $section)
                                    <option value="{{ $section->id }}"> {{App::getLocale()=='ar'?$section->cost_center_ar:$section->cost_center_en}}</option>
                                    @endforeach
                                </select>
                            </div><!-- col-4 -->
                                  <div id='AVT_Div' class="col-lg-3">
                                  <p class="mg-b-10 parent-label"> {{ __('home.shearchbysuppliername') }}</p>
                                <select style="width:100%" class="form-control select2" name="name_buyer_search" id="name_buyer_search" required>

                                    <option style="font-size: 15px" value="-">{{ __('home.entersuppliername') }}
                                    </option>
                                    @foreach (App\Models\supllier::get() as $section)
                                    <option style="font-size: 15px" value="{{ $section->id }}"> {{ $section->name }}
                                    </option>
                                    @endforeach
                                </select>
                  </div>   
               
                             
                            
                                  
                                     <div id="AVT_Div2" class="col-lg-2">
                     <label for="inputName" class="control-label parent-label"> {{ __('home.tax_number') }}</label>
                     <input type="number" class="form-control parent-input" id="TaxـNumber" name="TaxـNumber" onkeyup="TaxـNumberConvert()" title="{{ __('supprocesses.TaxـNumber') }}">
                     <input type="text" class="form-control parent-input" id="name_buyer" name="name_buyer"  hidden >
                  </div>
                              
                                                    </div>

                        <br>
                  
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-success print-style p-1"     type='submit' id="button_1">
                       
                            {{ __('home.savedecoument') }}
                            <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                            </svg>
                        </button>
                        <br>

                    </div>
                      </form>
                  




                <br>


                <div id="AVT_Div2" class="col-lg-2">
                     <label for="inputName" class="control-label parent-label"> {{ __('home.decoumentNo') }}</label>
                     <input type="number" readonly class="form-control parent-input" id="decoumentNo_show" name="decoumentNo_show" onkeyup="TaxـNumberConvert()" title="{{ __('supprocesses.TaxـNumber') }}">
                  </div>
          
               <br>
               
               
                <div class="col-xl-12">
                              <div class="table-responsive">
                        <table id="example" class="table text-md-nowrap text-center our-table" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">-</th>
                                    <th class="border-bottom-0"> {{ __('home.acount_name') }}</th>
                                    <th class="border-bottom-0">{{ __('accountes.Theamountpaid') }}</th>
                                    <th class="border-bottom-0">{{ __('home.paymentmethod') }}</th>
                                    <th class="border-bottom-0">{{ __('home.operations') }}</th>
  </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>

                        </table>
                       
                        <div id="printdiv" class="d-flex justify-content-center">


                            <div class="row  d-flex justify-content-end mt-3">
                                <form action="{{ '/' . ($page = 'print_reciept') }}" method="POST" role="search" autocomplete="off">
                                    {{ csrf_field() }}



                                    <div class='col ' id="printdiv">
                                        <input type="number" class="form-control " name="id" id="id" title=" رقم الفاتورة " readonly required hidden>


                                        <button style="background-color: #419BB2;font-size:15px;width: 120px!important;height:30px"  class="btn btn-success p-1 px-2 fw-bolder">
                                            {{ __('home.print') }}
                                            <svg style="width: 15px !important" class="svg-icon-buttons" viewBox="0 0 20 20">
                                                <path d="M17.453,12.691V7.723 M17.453,12.691V7.723 M1.719,12.691V7.723 M18.281,12.691V7.723 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M7.309,13.312h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,13.312,7.309,13.312 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M16.625,6.066h-1.449V3.168c0-0.228-0.186-0.414-0.414-0.414H5.238c-0.228,0-0.414,0.187-0.414,0.414v2.898H3.375c-0.913,0-1.656,0.743-1.656,1.656v4.969c0,0.913,0.743,1.656,1.656,1.656h1.449v2.484c0,0.228,0.187,0.414,0.414,0.414h9.523c0.229,0,0.414-0.187,0.414-0.414v-2.484h1.449c0.912,0,1.656-0.743,1.656-1.656V7.723C18.281,6.81,17.537,6.066,16.625,6.066 M5.652,3.582h8.695v2.484H5.652V3.582zM14.348,16.418H5.652v-4.969h8.695V16.418z M17.453,12.691c0,0.458-0.371,0.828-0.828,0.828h-1.449v-2.484c0-0.228-0.186-0.414-0.414-0.414H5.238c-0.228,0-0.414,0.186-0.414,0.414v2.484H3.375c-0.458,0-0.828-0.37-0.828-0.828V7.723c0-0.458,0.371-0.828,0.828-0.828h13.25c0.457,0,0.828,0.371,0.828,0.828V12.691z M7.309,13.312h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,13.312,7.309,13.312M7.309,15.383h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,15.383,7.309,15.383 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555"></path>
                                            </svg>
                                        </button>





                                    </div>


                                </form>
                            </div>
                        </div>
                        <br>
                    </div>
                    <br />
                </div>
            </div>
       
        <br>
                                <br>
                                <div class="col-xl-12">
            <div class="card mg-م-20">
                 <div class="card mg-م-20">
                      <div id="AVT_Div2" class="col-lg-2">
                     <label for="inputName" class="control-label parent-label"> {{ __('home.decoumentNo') }}</label>
                     <input type="number"  class="form-control parent-input" id="search_by_decoumentNo" name="search_by_decoumentNo" onkeyup="search_by_decoumentNo_function()" title="{{ __('supprocesses.TaxـNumber') }}">
                  </div>
                                    <div class="table-responsive " id="ajax_responce_allinvoicesDiv">
                                    <br>

                                    
                                        <div>



                                        </div>
                                        <br>

                                    </div>
                                </div>
                                </div>
                   
    </div>
    <!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
</div>


 <div class="modal fade product-selection" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="SearchProduct" name="SearchProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
            <div class="modal-dialog modal-xl" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" role="document">
                <div class="modal-content">
                  
                    <div class="modal-body" style="justify-content: center;">


 <center><img style="width:250px;height:250px;" class="custom_img" src="{{ asset('assets/admin/uploads/done.png') }}" >
                        
</center>



                          
                        </div>

                     
                    </div>


                </div>
            </div>

        </div>


        <div class="modal p-3" id="createsupplier">
    <div style="margin: 0 9% !important;" class="modal-dialog modal-dialog-centered modal-special" role="document">
        <div class="modal-content modal-content-demo p-3">
            <div class="modal-header">
                <h6 class="modal-title"> {{__('home.addnewsupplier')}} </h6><button aria-label="Close" class="close close-special" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            {{ csrf_field() }}
            <div class="row mb-2">
                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label">
                        {{ __('home.entersuppliername') }}</label>
                    <input type="text" class="form-control parent-input" id="suppliername" name="suppliername" required>
                </div>

                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label"> {{ __('supprocesses.phone') }}</label>
                    <input type="number" class="form-control parent-input" id="phone" name="phone" title="{{ __('supprocesses.phone') }}" required>
                </div>

                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label"> {{ __('supprocesses.email') }}</label>
                    <input type="text" class="form-control parent-input" id="email" name="email" title="{{ __('supprocesses.email') }}" value='Example@gmail.com'>
                </div>

            </div>

            {{-- 2 --}}
            <div class="row mb-2">
                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label"> {{ __('supprocesses.Location') }}</label>
                    <input type="test" class="form-control parent-input" id="supplierloction" name="supplierloction" title="{{ __('supprocesses.Location') }}" required>
                </div>
                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label">
                        {{ __('supprocesses.TaxـNumber') }}</label>
                    <input type="number" class="form-control parent-input" id="TaxـNumber_create" name="TaxـNumber_create" title="{{ __('supprocesses.TaxـNumber') }}" required>
                </div>

                <div class="col-lg-4 mb-2">
                    <label for="inputName" class="control-label parent-label">
                        {{ __('supprocesses.product_notes') }}</label>
                    <input type="text" class="form-control parent-input" id="suppliernotes" name="suppliernotes" title="{{ __('supprocesses.product_notes') }}">
                </div>

            </div><br>

            <br>
            <div class="d-flex justify-content-center">
                <button style="background-color: #419BB2" class="btn btn-primary p-1" data-dismiss="modal" onclick="createsupplierajax()">
                    {{ __('supprocesses.save_data') }}
                    <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                        <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>


<!-- edit -->
<div class="modal fade" id="increaseProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div style="margin: 5% !important;" class="modal-dialog modal-special" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('home.updatedecoument') }}</h5>
                <button type="button" class="close choose-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="hidden" name="transactionId" id="transactionId" value="">

                    </div>



            <div class="row">


                 
            <div class="col " >
                                <p for="inputName" class="control-label parent-label"> {{__('home.saerch_by_numberaccount_or_name')}}</p>
                                <select style="width:100%" class="form-control select2" name="payupdate" id="payupdate" required>
                                <option value="-"> {{__('home.acount_name')}}</option>


                                    @foreach (App\Models\financial_accounts::where('parent_account_number',4)->orwhere('parent_account_number',5)->where('parent_account_number','!=',NULL)->get() as $section)
                                    <option value="{{ $section->id }}"> {{ $section->name }} ({{ $section->account_number }})</option>
                                    @endforeach
                                </select>
                            </div><!-- col-4 -->
    <div class="col-lg-2 mg-t-20 mg-lg-t-0"  id="type">
                                <p class="mg-b-10 parent-label"> . </p>
                                <select style="width:100%" class="form-control parent-input " name="pay_type_desc" id="pay_type_desc" required>
                                    <option value="Cash"> {{ __('report.cash') }}</option>
                                    <option value="Shabka"> {{ __('report.shabka') }} </option>
                                    <option value="Bank_transfer"> {{ __('home.Bank_transfer') }} </option>


                                </select>
                            </div>

                <div class="col">
                    <label for="inputName" class="control-label parent-label"> {{ __('accountes.Theamountpaid') }}
                    </label>
                    <input type="text" class="form-control parent-input" id="cashreceivedupdate" name="cashreceivedupdate" value=0 title="يرجي ادخال الكمية  " required onkeyup="moneyconvertToNumber()">
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('home.cancel')}}</button>
            <button id="updatedecoument" name="updatedecoument" data-dismiss="modal" class="btn btn-danger">{{ __('home.confirm') }}</button>
        </div>

        </form>
    </div>
</div>
</div>

  <div class="modal p-3" id="updateinvoicebyidmodale">
        <div style="margin: 0 9% !important;" class="modal-dialog modal-dialog-centered modal-special" role="document">
            <div class="modal-content modal-content-demo p-3">
                <form>
                    <div class="modal-header">
                        <h6 class="modal-title"> {{ __('home.update_decument') }} </h6><button aria-label="Close" class="close close-special" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    {{ csrf_field() }}
                    <div class="row mb-1">
                        <div class="col-lg-6 col-md-6 col-md-4 mb-2">
                            <label style="font-size: 12px;" for="inputName" class="control-label parent-label"> {{ __('home.decoumentNo') }}</label>
                            <input style="height:32px" type="text" class="form-control parent-input" id="updateinvoicebyid" name="name" title="{{ __('supprocesses.name') }}" required>
                        </div>


                    </div>


                    <br>
                    <div class="d-flex justify-content-center">
                        <button style="background-color: #419BB2" class="btn btn-primary p-1" data-dismiss="modal" id="getinvoiceupdate">
                            {{ __('home.search') }}
                            <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                            </svg>
                        </button>
                    </div>
            </div>

        </div>
    </div>
</div>
        
 <div class="modal fade product-selection" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="loading" name="loading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
            <div class="modal-dialog modal-xl" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" role="document">
                <div class="modal-content">
                  
                    <div class="modal-body" style="justify-content: center;">


 <center><img style="width:250px;height:250px;" class="custom_img" src="{{ asset('assets/admin/uploads/loading.png') }}" >
                        
</center>



                          
                        </div>

                     
                    </div>

                </div>
            </div>

        </div>
        <div class="modal fade product-selection" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="massagesave" name="massagesave" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
            <div class="modal-dialog modal-xl" style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" role="document">
                <div class="modal-content">
                  
                    <div class="modal-body" style="justify-content: center;">


 <center><img style="width:250px;height:250px;" class="custom_img" src="{{ asset('assets/admin/uploads/done.png') }}" >
                        
</center>



                          
                        </div>

                     
                    </div>


                </div>
            </div>

        </div>

        <input type="hidden" id="token_search" value="{{ csrf_token() }}">

      
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>

<!--Internal  Datepicker js -->
<script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<!--Internal  jquery.maskedinput js -->
<script src="{{ URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
<!--Internal  spectrum-colorpicker js -->
<script src="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
<!-- Internal Select2.min js -->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!--Internal Ion.rangeSlider.min js -->
<script src="{{ URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
<!--Internal  jquery-simple-datetimepicker js -->
<script src="{{ URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}"></script>
<!-- Ionicons js -->
<script src="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js') }}"></script>
<!--Internal  pickerjs js -->
<script src="{{ URL::asset('assets/plugins/pickerjs/picker.min.js') }}"></script>
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
<script>
     var createsupplierInFlight = false;
     function createsupplierajax() {
        // [تم الإصلاح] حماية من إرسال طلب إضافة المورد مرتين لو المستخدم دبس على الزرار أكتر من مرة بسرعة.
        if (createsupplierInFlight) {
            return;
        }
        createsupplierInFlight = true;
        var url = " {{ URL::to('create_addnewsupplierajax') }}";
        console.log($('#suppliernotes').val())
        console.log($('#TaxـNumber_create').val())
        console.log($('#supplierloction').val())
        console.log($('#email').val())
        console.log($('#phone').val())
        console.log($('#suppliername').val())
        var token_search = $("#token_search").val();
        if ($('#supplierloction').val() == '') {
            Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{ __('supprocesses.Location')}}", confirmButtonText: 'حسنًا'})
            createsupplierInFlight = false;
        } else if ($('#suppliername').val() == '') {
            Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{  __('home.entersuppliername') }}", confirmButtonText: 'حسنًا'})
            createsupplierInFlight = false;
        } else if ($('#TaxـNumber_create').val() == '') {
            Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{ __('supprocesses.TaxـNumber') }}", confirmButtonText: 'حسنًا'})
            createsupplierInFlight = false;
        } else {


            $.ajax({
                url: url,
                type: 'post',
                cache: false,

                data: {
                    _token: token_search,
                    name: $('#suppliername').val() ?? '-',
                    phone: $('#phone').val(),
                    email: $('#email').val(),
                    loction: $('#supplierloction').val(),
                    TaxـNumber: $('#TaxـNumber_create').val(),
                    notes: $('#suppliernotes').val() ?? '-',

                },
                success: function(data) {
                    console.log(data['name'])

                    $('#suppliernotes').val('');
                    $('#TaxـNumber_create').val('');
                    $('#supplierloction').val('');
                    $('#suppliername').val('');
                    $('#email').val('')
                    $('#phone').val('')
                    console.log('seccusss12111');

          $('#massagesave').modal().show();
 setTimeout(() => {
         $('#massagesave').modal('hide');

        }, 1000);
        

              
        $('#name_buyer_search').append($('<option>', {
                        value: data['id'],
                        text: data['name']
                    }));

                    $('#name_buyer_search').val(data['id']).change();
                },
                error: function(response) {
                    Swal.fire({icon: 'error', title: 'خطأ', text: "{{ __('home.sorryerror') }}", confirmButtonText: 'موافق'})

                },
                complete: function() {
                    createsupplierInFlight = false;
                }
            });







        }


    }





     $('select[name="name_buyer_search"]').on('change', function() {
            console.log('AJAX load   work 0000');

            var selectclientid = $(this).val();
            if (selectclientid) {
                console.log("{{ URL::to('getsupllier') }}/" + selectclientid);

                $.ajax({
                    url: "{{ URL::to('getsupllier') }}/" + selectclientid,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                       
                        $('#TaxـNumber').val(data['TaxـNumber']);
                        $('#name_buyer').val(data['name']);
                       
                    },
                });
            } else {
                Swal.fire({icon: 'error', title: 'خطأ', text: "{{ __('home.sorryerror') }}", confirmButtonText: 'موافق'})
            }
        });

$('select[name="paymentmethod"]').on('change', function() {
                console.log('AJAX load   work 0000');

                var selectclientid = $(this).val();
                text_currnt=$('#paymentmethod').find(":selected").text();
                text=$('#payupdate').find(":selected").text();
              
           if(text_currnt.includes('البنك')){
                    $('#pay').attr("disabled", false); 
        $('#Cash').attr("disabled", true);

                 document.getElementById('type').style.visibility = 'visible';
    $('#pay').val('Shabka').change();
    $('#pay_type_desc').val('Shabka').change();

               
           }else{
                  $('#pay').val('Cash').change();
                  $('#pay_type_desc').val('Cash').change();
        $('#Cash').attr("disabled", false);

 
           }
           
           


    $('#payupdate').val(selectclientid).change();


            });



$('select[name="payupdate"]').on('change', function() {
                console.log('AJAX load   work 0000');

                var selectclientid = $(this).val();
       
                text=$('#payupdate').find(":selected").text();
              
           if(text.includes('البنك')){
    $('#pay_type_desc').val('Shabka').change();

               
           }else{
                  $('#pay_type_desc').val('Cash').change();
 
           }
           
           




            });






  $(document).on('change', '#AVT', function(e) {
         if ($(this).val() == 1) {
            $("#AVT_Div").show();
            $("#AVT_Div2").show();
         } else {
            $("#AVT_Div").hide();
            $("#AVT_Div2").hide();
         }
      });



    var date = $('.fc-datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    }).val();


    $('#increaseProduct').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)

        var id = button.data('id')
        var amount = button.data('amount')
        var modal = $(this)



        modal.find('.modal-body #transactionId').val(id);
        modal.find('.modal-body #cashreceivedupdate').val(amount);

    })
</script>


<script>
function search_by_decoumentNo_function(){
    if($("#search_by_decoumentNo").val()==''){
        
               $.ajax({
                url: " {{URL::to('get_all_send_serf_jax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });

    }else{
     $.ajax({
                url: " {{URL::to('search_by_decoumentNo_send_serf')}}/"+$("#search_by_decoumentNo").val(),
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            }); 
    }
}

  const checkbox = document.getElementById('checkboxId');

  checkbox.addEventListener('change', function () {
    if (this.checked) {
            $("#AVT_Div_cost").show();
    } else {
            $('#cost_center').val('0').change();

            $("#AVT_Div_cost").hide();
    }
  });
  
  
  
  
  
  
  
var updatedecoumentInFlight = false;
$("#updatedecoument").click(function(e) {
        // [تم الإصلاح] حماية من إرسال طلب التعديل مرتين لو المستخدم دبس على الزرار أكتر من مرة بسرعة.
        if (updatedecoumentInFlight) {
            return;
        }
        updatedecoumentInFlight = true;

        console.log($('#notes').val())

        console.log($('#clientnamesearch').val())
        var url = " {{ URL::to('updaterecieptdecoument') }}";
        var token_search = $("#token_search").val();
        if ($('#cashreceivedupdate').val() == 0) {
                Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{__('home.should')}}", confirmButtonText: 'حسنًا'})
                updatedecoumentInFlight = false;

            } else {
                cashreceived=$('#cashreceivedupdate').val()
            $('#cashreceivedupdate').val(0)
            text=$('#payupdate').find(":selected").text();

                $.ajax({
                    url: url,
                    type: 'post',
                    cache: false,

                    data: {
                        _token: token_search,
                        transactionId: $('#transactionId').val(),
                        payupdate: $('#payupdate').val(),
                        cashreceivedupdate: cashreceived,
                        text:text,
                        pay_type_desc:$('#pay_type_desc').val()
                    },


                success: function(data) {
  $('#cashreceived').val(0)
  document.getElementById('printdiv').style.visibility = 'visible';

                    console.log(data)
                    let table = document.getElementById("example");
                    var tableHeaderRowCount = 1;

                    var rowCount = table.rows.length;

                    for (var i = tableHeaderRowCount; i < rowCount; i++) {
                        table.deleteRow(tableHeaderRowCount);
                    }



                    data.forEach(async (product) =>{
                                                        
                    let row = table.insertRow(-1); // We are adding at the end
                    update = ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                        update = update.concat(product['id'], '  ', ' data-amount=', product['paid_amount'], '  ',
                            '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                        )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c6 = row.insertCell(4);

                    c1.innerText = product['id']
                    c2.innerText = product['name']
                    c3.innerHTML = ' <span dir=ltr style="color:red">' + product['paid_amount'] + '</span>'
                    c4.innerText = product['method_pay']
                    c6.innerHTML = update
                    $('#decoumentNo_show').val(product['sent_serf_count'])
                    $('#id').val(product['id'])
                    $('#id_create').val(product['sent_serf_count'])
                    $('#transactionId').val(product['id'])
                    })
         $('#SearchProduct').modal('show');
 setTimeout(() => {
         $('#SearchProduct').modal('hide');

        }, 1000);
setTimeout(() => {
         $('#loading').modal('hide');

        }, 500); 
                  
                },
                error: function(response) {
                                        console.log(response)

                    Swal.fire({icon: 'error', title: 'خطأ', text: "{{ __('home.sorryerror') }}", confirmButtonText: 'موافق'})

                },
                complete: function() {
                    updatedecoumentInFlight = false;
                }
            });



        }



    })


    $("#formdata").on('submit',function(e) {
        e.preventDefault();
        // [تم الإصلاح] حماية من إرسال الفورم مرتين لو المستخدم دبس على زرار الحفظ أكتر من مرة بسرعة.
        var $addBtn = $('#button_1');
        if ($addBtn.prop('disabled')) {
            return;
        }
        $addBtn.prop('disabled', true);
        $('#loading').modal().show();

        console.log($('#notes').val())
        console.log('*********************')

        console.log($('#pay').val())
        console.log($('#clientnamesearch').val())
        var url = " {{ URL::to('reciepttransactions') }}";
        var token_search = $("#token_search").val();
        if ($('#cashreceived').val() == 0) {
            Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{__('home.should')}}", confirmButtonText: 'حسنًا'})
            $addBtn.prop('disabled', false);

        }else if ($('#date').val() == 0) {
                Swal.fire({icon: 'warning', title: 'تنبيه', text: "{{__('home.exportTime')}}", confirmButtonText: 'حسنًا'})
                $addBtn.prop('disabled', false);

            } else {
            cashreceived=$('#cashreceived').val()
            attachments=document.querySelector('#attachments');

$.ajax({
                url: url,
                type: 'post',
                cache: false,
                  contentType:false,
                processData:false,
                data:new FormData(this),


                success: function(data) {
                    console.log(data)
  $('#cashreceived').val(0)
  document.getElementById('printdiv').style.visibility = 'visible';

                    console.log(data)
                    let table = document.getElementById("example");
                    var tableHeaderRowCount = 1;

                    var rowCount = table.rows.length;

                    for (var i = tableHeaderRowCount; i < rowCount; i++) {
                        table.deleteRow(tableHeaderRowCount);
                    }



                    data.forEach(async (product) =>{
                                                        
                             

                    let row = table.insertRow(-1); // We are adding at the end
                    update = ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                        update = update.concat(product['id'], '  ', ' data-amount=', product['paid_amount'], '  ',
                            '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                        )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c6 = row.insertCell(4);

                    c1.innerText = product['id']
                    c2.innerText = product['name']
                    c3.innerHTML = ' <span dir=ltr style="color:red">' + product['paid_amount'] + '</span>'
                    c4.innerText = product['method_pay']
                    c6.innerHTML = update
                    $('#decoumentNo_show').val(product['sent_serf_count'])
                    $('#id').val(product['id'])
                    $('#id_create').val(product['sent_serf_count'])
                    $('#transactionId').val(product['id'])
                    })
         $('#SearchProduct').modal('show');
 setTimeout(() => {
         $('#SearchProduct').modal('hide');

        }, 1000);
setTimeout(() => {
         $('#loading').modal('hide');

        }, 500); 
                  
                },
                error: function(response) {
                    console.log(response)
                    Swal.fire({icon: 'error', title: 'خطأ', text: "{{ __('home.sorryerror') }}", confirmButtonText: 'موافق'})

                },
                complete: function() {
                    $addBtn.prop('disabled', false);
                }
            });



        }



    })




 $("#getinvoiceupdate").click(function(e) {
            event.preventDefault();
            var url = " {{ URL::to('getAndUpdate_reciptdecument') }}" + "/" + $('#updateinvoicebyid').val();
            console.log(url)
            jQuery.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                cache: false,

                    success: function(data) {
                        
                        if(data==0){
                        Swal.fire({icon: 'error', title: 'تنبيه', text: 'سند الصرف غير مسجل في النظام نرجو التحقق من رقم السند\nThe payment voucher is not registered in the system. Please check the voucher number.', confirmButtonText: 'موافق'})



                        }
                        
                       else {

                        $('#cashreceived').val(0)
  document.getElementById('printdiv').style.visibility = 'visible';

                    console.log(data)
                    let table = document.getElementById("example");
                    var tableHeaderRowCount = 1;

                    var rowCount = table.rows.length;

                    for (var i = tableHeaderRowCount; i < rowCount; i++) {
                        table.deleteRow(tableHeaderRowCount);
                    }



                    data.forEach(async (product) =>{
                                                        
                    let row = table.insertRow(-1); // We are adding at the end
                    update = ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                        update = update.concat(product['id'], '  ', ' data-amount=', product['paid_amount'], '  ',
                            '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                        )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c6 = row.insertCell(4);

                    c1.innerText = product['id']
                    c2.innerText = product['name']
                    c3.innerHTML = ' <span dir=ltr style="color:red">' + product['paid_amount'] + '</span>'
                    c4.innerText = product['method_pay']
                    c6.innerHTML = update
                    $('#decoumentNo_show').val(product['sent_serf_count'])
                    $('#id').val(product['id'])
                    $('#id_create').val(product['sent_serf_count'])
                    $('#transactionId').val(product['id'])
                    })
         $('#SearchProduct').modal('show');
 setTimeout(() => {
         $('#SearchProduct').modal('hide');

        }, 1000);
setTimeout(() => {
         $('#loading').modal('hide');

        }, 500); 
                      }  },
                    error: function(response) {
                        Swal.fire({icon: 'error', title: 'خطأ', text: "{{ __('home.sorryerror') }}", confirmButtonText: 'موافق'})

                    }
                })
            
       })
       
       
    function moneyconvertToNumber() {
        var input = document.getElementById("cashreceived");
        var val = toEnglishNumber(input.value)
        input.value = val;
    }

    function toEnglishNumber(strNum) {
        var ar = '٠١٢٣٤٥٦٧٨٩'.split('');
        var en = '0123456789'.split('');
        strNum = strNum.replace(/[٠١٢٣٤٥٦٧٨٩]/g, x => en[ar.indexOf(x)]);
        //  strNum = strNum.replace(/[^\d]/g, '');
        return strNum;
    }
</script>
<script>


$(document).on('click', '#ajax_pagination_in_search a ', function(e) {
        e.preventDefault();
        var search_by_text = $("#date").val();
        var url = $(this).attr("href");
        var token_search = $("#token_search").val();

        jQuery.ajax({
            url: url,
            type: 'get',
            dataType: 'html',
            cache: false,
            data: {
                "_token": token_search
            },
            success: function(data) {
                $("#ajax_responce_allinvoicesDiv").html(data);
            },
            error: function() {

            }
        });
    });


    $(document).ready(function() {


        $.ajax({
                url: " {{URL::to('get_all_send_serf_jax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });


        document.getElementById('printdiv').style.visibility = 'hidden';

             $("#AVT_Div").hide();
            $("#AVT_Div2").hide();
            $("#AVT_Div_cost").hide();

        $(function() {
            var timeout = 4000; // in miliseconds (3*1000)
            $('.alert').delay(timeout).fadeOut(500);
        });





        $(document).ready(function() {
            $('select[name="clientNosearch"]').on('change', function() {
                console.log('AJAX load   work 0000');

                var selectclientid = $(this).val();
                if (selectclientid) {
                    console.log('AJAX load   work');

                    $.ajax({
                        url: "{{ URL::to('getsupllier') }}/" + selectclientid,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            console.log("success");
                            console.log(data['name']);
                            $('#debtamount').val(data['In_debt']);
                            $('#clientName').val(data['name']);
                            $('#address').val(data['location']);
                            $('#phonenumber').val(data['phone']);
                        },
                    });
                } else {
                    console.log('AJAX load did not work');
                }
            });

      
        });
    });
</script>






@endsection