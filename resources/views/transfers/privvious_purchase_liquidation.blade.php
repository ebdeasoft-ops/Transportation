@extends('layouts.master')
@section('css')
<style>
:root {
    --ts-primary: #1c2b39; --ts-primary-light: #2d4356; --ts-accent: #419BB2; --ts-accent-dark: #2f7d91;
    --ts-success: #2ecc71; --ts-danger: #e74c3c; --ts-warning: #f39c12; --ts-border: #e3e8ee; --ts-bg-soft: #f7f9fb;
    --ts-radius: 12px; --ts-radius-sm: 8px; --ts-shadow: 0 2px 10px rgba(20, 40, 60, 0.06);
}
.card { border: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) !important; box-shadow: var(--ts-shadow) !important; overflow: hidden; }
.card-invoice { border: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) !important; box-shadow: var(--ts-shadow) !important; }
.card-header { background: var(--ts-bg-soft) !important; border-bottom: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) var(--ts-radius) 0 0 !important; padding: 18px 20px !important; }
.content-title { color: var(--ts-primary) !important; font-weight: 700 !important; }
.form-control, .select2-container .select2-selection--single { border: 1.5px solid var(--ts-border) !important; border-radius: var(--ts-radius-sm) !important; min-height: 42px !important; font-size: 14px !important; }
label, .parent-label, .control-label { font-weight: 600 !important; color: var(--ts-primary-light) !important; font-size: 13px !important; margin-bottom: 6px !important; display: inline-block; }
.btn { border-radius: var(--ts-radius-sm) !important; font-weight: 600 !important; font-size: 13.5px !important; padding: 8px 18px !important; transition: transform .15s ease, box-shadow .15s ease; }
.btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(20,40,60,.10); }
.btn-success { background: var(--ts-accent) !important; border-color: var(--ts-accent) !important; }
.btn-danger { background: var(--ts-danger) !important; border-color: var(--ts-danger) !important; }
.btn-secondary { background: #eef1f4 !important; border-color: #eef1f4 !important; color: var(--ts-primary) !important; }
.table-responsive, .hoverable-table { border-radius: var(--ts-radius) !important; overflow: hidden !important; border: 1px solid var(--ts-border) !important; }
table.our-table thead th, table.table thead th, table.key-buttons thead th { background: var(--ts-primary) !important; color: #fff !important; font-weight: 600 !important; font-size: 12.5px !important; border: none !important; padding: 12px 10px !important; }
table.our-table tbody td, table.table tbody td, table.key-buttons tbody td { padding: 10px !important; font-size: 13.5px !important; border-color: var(--ts-border) !important; vertical-align: middle !important; }
.modal-content { border-radius: var(--ts-radius) !important; border: none !important; box-shadow: 0 10px 40px rgba(0,0,0,.15) !important; }
.modal-header { background: var(--ts-bg-soft) !important; border-bottom: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) var(--ts-radius) 0 0 !important; }
.modal-title { color: var(--ts-primary) !important; font-weight: 700 !important; }
.box { background: linear-gradient(135deg, var(--ts-primary) 0%, var(--ts-primary-light) 100%) !important; color: #fff !important; border: none !important; border-radius: 999px !important; padding: 12px 24px !important; font-weight: 700 !important; font-size: 15px !important; display: inline-flex !important; align-items: center; gap: 8px; letter-spacing: .2px; box-shadow: 0 4px 14px rgba(28,43,57,.25) !important; transition: transform .15s ease, box-shadow .15s ease; }
.box:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(28,43,57,.32) !important; }
.box::before { content: "💰"; font-size: 16px; }

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
{{ __('home.update_liquidation') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('home.update_liquidation') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">
                </span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
    @endsection
    @section('content')
    @if (session()->has('nodataprint'))
        {{-- [تم الإصلاح] الرسالة دي كانت مربّعة حمرا ثابتة جوه الصفحة - حولتها لـ SweetAlert
             (SWAL) زي باقي رسائل النظام، بتظهر كنافذة منبثقة بدل مربع ثابت. --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: @json(__('home.nodataprint')),
                    confirmButtonText: 'حسنًا'
                });
            });
        </script>
    @endif
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


                    <form enctype="multipart/form-data" method="POST" role="search" name="form-name" id='formdata'
                        autocomplete="off">
                        {{ csrf_field() }}
                        <input hidden type="text" class="form-control parent-input" name="id" id="id" value="{{ $id }}">

                        <div class="row">

                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.loading') }}</label>
                                <input type="text" class="form-control parent-input" name="loading" id="loading"
                                    required>
                            </div>
                            <div class="col">
                                <label class="control-label parent-label">{{ __('home.Unloading') }}</label>
                                <input type="text" class="form-control parent-input" name="Unloading" id="Unloading"
                                    required>
                            </div>
                            <input type="text" class="form-control parent-input" name="transactions_id"
                                id="transactions_id" value="{{ $id }}" hidden>
                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.truck_no') }}</label>
                                <input type="text" class="form-control parent-input" name="truck_no" id="truck_no"
                                    required>
                            </div>
                            <div class="col">
                                <label class="control-label parent-label">{{ __('home.invoice_no') }}</label>
                                <input type="text" class="form-control parent-input" name="invoice_no" id="invoice_no"
                                    required>
                            </div>
                            <div class="col">
                                <label class="control-label parent-label">{{ __('home.polica_number') }}</label>
                                <input type="text" class="form-control parent-input" name="polica_number"
                                    id="polica_number" required>
                            </div>
                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.PRICE_SHIPMENT') }}</label>
                                <input type="number" class="form-control parent-input" name="PRICE_SHIPMENT"
                                    id="PRICE_SHIPMENT" min="0.01" step="0.01" required>
                            </div>
                            <div class="col" id="end_at">
                                <label class="parent-label" for="exampleFormControlSelect1">
                                    {{ __('home.date') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div><input class="form-control parent-input fc-datepicker" id="date" name="date"
                                        placeholder="YYYY-MM-DD" type="text" required>
                                </div><!-- input-group -->
                            </div>

                        </div>
                        <div class="row">

                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.delay') }}</label>
                                <input type="number" class="form-control parent-input" name="delay" id="delay" value=0
                                    required>
                            </div>
                            <div class="col">
                                <label class="control-label parent-label">{{ __('home.ext') }}</label>
                                <input type="number" class="form-control parent-input" name="ext" id="ext" value=0
                                    required>
                            </div>
                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                                <input type="text" class="form-control parent-input" name="note" id="note" value="-"
                                    required>
                            </div>
                            <div class="form-group">
                                <label> {{__('home.attachments')}}</label>
                                <input autocomplete="off" onchange="readURL(this)" type="file" id="attachments"
                                    name="attachments" class="form-control">

                            </div>


                        </div>

                        <br>

                        <div class="d-flex justify-content-center">
                            <button type='submit' class="btn btn-success print-style p-1" id="button_1">
                                {{ __('home.Add') }}
                                <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                    <path fill="none"
                                        d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z">
                                    </path>
                                </svg>
                            </button>
                            <br>

                        </div>
                        <br>
                        <br>


                        <div style="border-radius: 10px">

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example" class="table text-md-nowrap text-center our-table" width="100%"
                                        style="border: 2px solid rgba(0,0,0,.3);">
                                        <thead>
                                            <tr>

                                                <th class="border-bottom-0"> {{ __('home.no_liquidation') }}</th>
                                                <th class="border-bottom-0"> {{ __('home.loading') }}</th>
                                                <th class="border-bottom-0">{{ __('home.Unloading') }}</th>
                                                <th class="border-bottom-0">{{ __('home.truck_no') }}</th>
                                                <th class="border-bottom-0">{{ __('home.invoice_no') }}</th>
                                                <th class="border-bottom-0">{{ __('home.polica_number') }}</th>
                                                <th class="border-bottom-0">{{ __('home.PRICE_SHIPMENT') }}</th>
                                                <th class="border-bottom-0">{{ __('home.delay') }}</th>
                                                <th class="border-bottom-0">{{ __('home.ext') }}</th>
                                                <th class="border-bottom-0">{{ __('home.total') }}</th>
                                                <th class="border-bottom-0">{{ __('home.notesClient') }}</th>
                                                <th class="border-bottom-0">{{ __('home.operations') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                $total=0;

                            $shipments_details=App\Models\shipments_details::where('save',1)->where('Transactions_id',$id)->get()
                            ?>
                                            @if($shipments_details!=NULL)
                                            @foreach ($shipments_details as $item)
                                            <tr>

                                                <td>{{ $id}}</td>
                                                <td>{{ $item->loading }}</td>
                                                <td>{{ $item->unloading }}</td>
                                                <td>{{ $item->truck_data }}</td>
                                                <td>{{ $item->invoice_number }}</td>
                                                <td>{{ $item->polica_number }}</td>
                                                <td>{{ $item->price_shipment }}</td>
                                                <td>{{ $item->Daily}}</td>
                                                <td>{{ $item->Ext }}</td>
                                                <td>{{ $item->note_detaials }}</td>
                                                <td> @if($item['attachments_2']!=null)<a target="_blank"
                                                        href="{{ url('/' . ($page = 'openfile') .'/'.$item['attachments_2']) }}">{{  __('home.show')}}</a>
                                                    @endif
                                                </td>
                                                <td>

                                                    <a style="width:40px;height:20px"
                                                        class="modal-effect btn btn-sm btn-warning mb-1"
                                                        data-effect="effect-scale" data-id="{{ $item->id }}"
                                                        data-loading="{{ $item->loading }}"
                                                        data-unloading="{{ $item->unloading }}"
                                                        data-truck_data="{{ $item->truck_data }}"
                                                        data-invoice_number="{{ $item->invoice_number }}"
                                                        data-priceshipment="{{ $item->price_shipment }}"
                                                        data-date="{{ $item->date }}"
                                                        data-polica_number="{{ $item->polica_number }}"
                                                        data-daily="{{ $item->Daily }}" data-ext="{{ $item->Ext }}"
                                                        data-note_detaials="{{ $item->note_detaials }}"
                                                        data-toggle="modal" href="#increaseProduct" title="تعديل"><i
                                                            class="las la-align-justify"></i></a>
                                                </td>


                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ $total }}</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>

                                            @else



                                            <tr>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                </div>


                <br>



                </form>

                <center>


                    <div class="row  d-flex justify-content-end mt-3">
                        <form action="{{ '/' . ($page = 'save_transaction_details') }}" method="POST" role="search"
                            autocomplete="off">
                            {{ csrf_field() }}



                            <div class='col ' id="printdiv">
                                <input hidden type="text" class="form-control parent-input"
                                    name="Transactions_price_id_print" id="Transactions_price_id_print"
                                    value="{{ $id }}">
                                <input hidden type="text" class="form-control parent-input" name="Transactions_price"
                                    id="Transactions_price">


                                <button type='submit' class="btn btn-success p-1 px-2 fw-bolder"
                                    style="background-color: #419BB2;font-size:15px;width: 120px!important;height:30px"
                                    id="save_1">
                                    {{ __('home.savedecoument') }}
                                    <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                        <path fill="none"
                                            d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z">
                                        </path>
                                    </svg>
                                </button>





                                <br>
                                <br>


                            </div>


                        </form>
                    </div>
                </center>

            </div>
            <br>



        </div>

    </div>
</div>
<div class="modal fade product-selection"
    style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="loading"
    name="loading_modale" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
    <div class="modal-dialog modal-xl"
        style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" role="document">
        <div class="modal-content">

            <div class="modal-body" style="justify-content: center;">


                <center><img style="width:250px;height:250px;" class="custom_img"
                        src="{{ asset('assets/admin/uploads/loading.png') }}">

                </center>

                <input type="hidden" id="token_search" value="{{ csrf_token() }}">



            </div>


        </div>


    </div>
</div>

<!-- edit -->
<div class="modal fade" id="increaseProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div style="margin: 5% !important;" class="modal-dialog modal-special" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('home.update_data_shipment') }}</h5>
                <button type="button" class="close choose-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form enctype="multipart/form-data" method="POST" role="search" name="form-name" id='formdata_update'
                autocomplete="off">
                {{ csrf_field() }}
                <input hidden type="text" class="form-control parent-input" name="id_update" id="id_update">
                <div style="width:99%">
                    <div class="row">

                        <div style="width:1%">
                        </div>
                        <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.loading') }}</label>
                            <input type="text" class="form-control parent-input" name="loading_update"
                                id="loading_update" required>
                        </div>
                        <div class="col-lg-1 mg-t-20 mg-lg-t-0">
                            <label class="control-label parent-label">{{ __('home.Unloading') }}</label>
                            <input type="text" class="form-control parent-input" name="Unloading_update"
                                id="Unloading_update" required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.truck_no') }}</label>
                            <input type="text" class="form-control parent-input" name="truck_no_update"
                                id="truck_no_update" required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                            <label class="control-label parent-label">{{ __('home.invoice_no') }}</label>
                            <input type="text" class="form-control parent-input" name="invoice_no_update"
                                id="invoice_no_update" required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                            <label class="control-label parent-label">{{ __('home.polica_number') }}</label>
                            <input type="text" class="form-control parent-input" name="polica_number_update"
                                id="polica_number_update" required>
                        </div>
                        <div class="col" id="end_at">
                            <label class="parent-label" for="exampleFormControlSelect1"> {{ __('home.date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div><input class="form-control parent-input fc-datepicker" id="date_update"
                                    name="date_update" placeholder="YYYY-MM-DD" type="text" required>
                            </div><!-- input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div style="width:1%">
                        </div>
                        <div class="col">

                            <label class="control-label parent-label">{{ __('home.PRICE_SHIPMENT') }}</label>
                            <input type="number" class="form-control parent-input" name="PRICE_SHIPMENT_update"
                                id="PRICE_SHIPMENT_update" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.delay') }}</label>
                            <input type="text" class="form-control parent-input" name="delay_update" id="delay_update"
                                required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                            <label class="control-label parent-label">{{ __('home.ext') }}</label>
                            <input type="text" class="form-control parent-input" name="ext_update" id="ext_update"
                                required>
                        </div>
                        <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                            <input type="text" class="form-control parent-input" name="note_update" id="note_update"
                                required>
                        </div>
                        <div class="col-lg-2 mg-t-20 mg-lg-t-0 form-group">
                            <label> {{__('home.attachments')}}</label>
                            <input autocomplete="off" onchange="readURL(this)" type="file" id="attachments_update"
                                name="attachments_update" class="form-control">

                        </div>


                    </div>
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('home.cancel')}}</button>
                    <button type='submit' class="btn btn-danger">{{ __('home.confirm') }}</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Internal Data tables -->

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
var date = $('.fc-datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
}).val();

$('#increaseProduct').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget)
    var id = button.data('id')
    var loading = button.data('loading')
    var unloading = button.data('unloading')
    var truck_data = button.data('truck_data')
    var invoice_number = button.data('invoice_number')
    var price_shipment = button.data('priceshipment')
    var polica_number = button.data('polica_number')
    var Daily = button.data('daily')
    var Ext = button.data('ext')
    var date = button.data('date')
    var note_detaials = button.data('note_detaials')
    var modal = $(this)

    var modal = $(this)

    modal.find('#date_update').val(date);

    modal.find('#id_update').val(id);
    modal.find('#loading_update').val(loading);
    modal.find('#Unloading_update').val(unloading);
    modal.find('#truck_no_update').val(truck_data);
    modal.find('#invoice_no_update').val(invoice_number);
    modal.find('#PRICE_SHIPMENT_update').val(price_shipment);
    modal.find('#delay_update').val(Daily);
    modal.find('#polica_number_update').val(polica_number);
    modal.find('#ext_update').val(Ext);
    modal.find('#note_update').val(note_detaials);

})
</script>



<script>
$("#formdata_update").on('submit', function(e) {
    e.preventDefault();
    // [تم الإصلاح] حماية من إرسال النموذج مرتين لو المستخدم دبس على زرار الحفظ أكتر من مرة.
    var $submitBtn = $(this).find('button[type="submit"], input[type="submit"]').first();
    if ($submitBtn.prop('disabled')) {
        return;
    }
    $submitBtn.prop('disabled', true);
    console.log('*********************')

    var url = " {{ URL::to('update_describtion') }}";
    var token_search = $("#token_search").val(); {
        $('#loading_modale').modal().show();

        $.ajax({
            url: url,
            type: 'post',
            cache: false,

            contentType: false,
            processData: false,
            data: new FormData(this),
            complete: function() {
                $submitBtn.prop('disabled', false);
            },


            success: function(data) {
                $('#attachments_update').val('')
                $('#ext_update').val('')
                $('#delay_update').val('')
                $('#PRICE_SHIPMENT_update').val('')
                $('#polica_number_update').val('')
                $('#invoice_no_update').val('')
                $('#truck_no_update').val('')
                $('#Unloading_update').val('')
                $('#loading_update').val('')
                $('#transactions_id').val(data['id'])


                let table = document.getElementById("example");
                var tableHeaderRowCount = 1;

                var rowCount = table.rows.length;

                for (var i = tableHeaderRowCount; i < rowCount; i++) {
                    table.deleteRow(tableHeaderRowCount);
                }

                i = 0;

                data['shipments_details'].forEach(async (product) => {

                    i = i + (product['Ext'] * 1) + (product['Daily'] * 1) + (product[
                        'price_shipment'] * 1);

                    let row = table.insertRow(-1); // We are adding at the end
                    update =
                        ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                    update = update.concat(product['id'], '  ', ' data-loading=',
                        product['loading'], '  ', ' data-unloading=', product[
                            'unloading'], '  ', ' data-truck_data=', product[
                            'truck_data'], '  ',
                        ' data-amount=', product['price_shipment'], '  ',
                        ' data-amount=', product['price_shipment'], '  ',
                        ' data-amount=', product['price_shipment'], '  ',
                        ' data-amount=', product['price_shipment'], '  ',
                        ' data-invoice_number=', product['invoice_number'], '  ',
                        ' data-price_shipment=', product['price_shipment'], '  ',
                        ' data-polica_number=', product['polica_number'], '  ',
                        ' data-Daily=', product['Daily'], '  ',
                        ' data-date=', product['date'], '  ',
                        ' data-Ext=', product['Ext'], '  ', ' data-note_detaials=',
                        product['note_detaials'], '  ',
                        '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                    )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c5 = row.insertCell(4);
                    let c6 = row.insertCell(5);
                    let c7 = row.insertCell(6);
                    let c8 = row.insertCell(7);
                    let c9 = row.insertCell(8);
                    let c10 = row.insertCell(9);
                    let c11 = row.insertCell(10);
                    let c12 = row.insertCell(11);

                    c1.innerText = data['id']
                    c2.innerText = product['loading']
                    c3.innerHTML = product['unloading']
                    c4.innerText = product['truck_data']
                    c5.innerText = product['invoice_number']
                    c6.innerText = product['polica_number']
                    c7.innerText = product['price_shipment']
                    c8.innerText = product['Daily']
                    c9.innerText = product['Ext']
                    c10.innerText = (product['Ext'] * 1) + (product['Daily'] * 1) + (
                        product['price_shipment'] * 1)
                    c11.innerText = product['note_detaials']
                    c12.innerHTML = update


                })
                let row = table.insertRow(-1); // We are adding at the end

                let c1 = row.insertCell(0);
                let c2 = row.insertCell(1);
                let c3 = row.insertCell(2);
                let c4 = row.insertCell(3);
                let c5 = row.insertCell(4);
                let c6 = row.insertCell(5);
                let c7 = row.insertCell(6);
                let c8 = row.insertCell(7);
                let c9 = row.insertCell(8);
                let c10 = row.insertCell(9);
                let c11 = row.insertCell(10);

                c1.innerText = '-'
                c2.innerText = '-'
                c3.innerHTML = '-'
                c4.innerText = '-'
                c5.innerText = '-'
                c6.innerText = '-'
                c7.innerText = '-'
                c8.innerText = '-'
                c9.innerText = '-'
                c10.innerText = i
                c11.innerText = '-'

                setTimeout(() => {
                    $('#loading_modale').modal('hide');

                }, 500);
            },
            error: function(response) {
                console.log(response['responseText'])
                Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: (response && response.responseJSON && response.responseJSON.message) || "{{ __('home.sorryerror') }}",
                        confirmButtonText: 'موافق'
                    })

            }
        })




    }



})


$("#formdata").on('submit', function(e) {
    e.preventDefault();
    // [تم الإصلاح] حماية من إرسال النموذج مرتين لو المستخدم دبس على زرار الحفظ أكتر من مرة.
    var $submitBtn = $(this).find('button[type="submit"], input[type="submit"]').first();
    if ($submitBtn.prop('disabled')) {
        return;
    }
    $submitBtn.prop('disabled', true);
    console.log('*********************')
    console.log($('#pay').val())
    var url = " {{ URL::to('add_describtion') }}";
    var token_search = $("#token_search").val(); {
        $('#loading_modale').modal().show();

        $.ajax({
            url: url,
            type: 'post',
            cache: false,

            contentType: false,
            processData: false,
            data: new FormData(this),
            complete: function() {
                $submitBtn.prop('disabled', false);
            },


            success: function(data) {
                $('#attachments').val('')
                $('#ext').val('')
                $('#delay').val('')
                $('#PRICE_SHIPMENT').val('')
                $('#polica_number').val('')
                $('#invoice_no').val('')
                $('#truck_no').val('')
                $('#Unloading').val('')
                $('#loading').val('')


                $('#transactions_id').val(data['id'])
                $('#Transactions_price_id_print').val(data['id'])


                let table = document.getElementById("example");
                var tableHeaderRowCount = 1;

                var rowCount = table.rows.length;

                for (var i = tableHeaderRowCount; i < rowCount; i++) {
                    table.deleteRow(tableHeaderRowCount);
                }

                i = 0;

                data['shipments_details'].forEach(async (product) => {

                    i = i + (product['Ext'] * 1) + (product['Daily'] * 1) + (product[
                        'price_shipment'] * 1);

                    let row = table.insertRow(-1); // We are adding at the end
                    update =
                        ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                    update = update.concat(product['id'], '  ', ' data-loading=',
                        product['loading'], '  ', ' data-unloading=', product[
                            'unloading'], '  ', ' data-truck_data=', product[
                            'truck_data'], '  ',
                        ' data-invoice_number=', product['invoice_number'], '  ',
                        ' data-price_shipment=', product['price_shipment'], '  ',
                        ' data-polica_number=', product['polica_number'], '  ',
                        ' data-Daily=', product['Daily'], '  ',
                                                ' data-date=', product['date'], '  ',

                        ' data-Ext=', product['Ext'], '  ', ' data-note_detaials=',
                        product['note_detaials'], '  ',
                        '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                    )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c5 = row.insertCell(4);
                    let c6 = row.insertCell(5);
                    let c7 = row.insertCell(6);
                    let c8 = row.insertCell(7);
                    let c9 = row.insertCell(8);
                    let c10 = row.insertCell(9);
                    let c11 = row.insertCell(10);
                    let c12 = row.insertCell(11);

                    c1.innerText = data['id']
                    c2.innerText = product['loading']
                    c3.innerHTML = product['unloading']
                    c4.innerText = product['truck_data']
                    c5.innerText = product['invoice_number']
                    c6.innerText = product['polica_number']
                    c7.innerText = product['price_shipment']
                    c8.innerText = product['Daily']
                    c9.innerText = product['Ext']
                    c10.innerText = (product['Ext'] * 1) + (product['Daily'] * 1) + (
                        product['price_shipment'] * 1)
                    c11.innerText = product['note_detaials']
                    c12.innerHTML = update

                })
                let row = table.insertRow(-1); // We are adding at the end

                let c1 = row.insertCell(0);
                let c2 = row.insertCell(1);
                let c3 = row.insertCell(2);
                let c4 = row.insertCell(3);
                let c5 = row.insertCell(4);
                let c6 = row.insertCell(5);
                let c7 = row.insertCell(6);
                let c8 = row.insertCell(7);
                let c9 = row.insertCell(8);
                let c10 = row.insertCell(9);
                let c11 = row.insertCell(10);

                c1.innerText = '-'
                c2.innerText = '-'
                c3.innerHTML = '-'
                c4.innerText = '-'
                c5.innerText = '-'
                c6.innerText = '-'
                c7.innerText = '-'
                c8.innerText = '-'
                c9.innerText = '-'
                c10.innerText = i
                c11.innerText = '-'

                setTimeout(() => {
                    $('#loading_modale').modal('hide');

                }, 500);
            },
            error: function(response) {
                console.log(response['responseText'])
                Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: (response && response.responseJSON && response.responseJSON.message) || "{{ __('home.sorryerror') }}",
                        confirmButtonText: 'موافق'
                    })

            }
        })




    }



})
</script>

<script>
    // [تم الإصلاح] زرار "{{ __('home.savedecoument') }}" ده بيبعت الفورم بشكل عادي (مش AJAX)
    // وماكانش فيه أي حماية - فلو المستخدم دبس عليه أكتر من مرة بسرعة قبل ما المتصفح ينتقل
    // للصفحة التانية، كانت بتتبعت أكتر من طلب للسيرفر وتتعمل نفس التصفية أكتر من مرة.
    $('#save_1').closest('form').on('submit', function() {
        var $btn = $('#save_1');
        if ($btn.prop('disabled')) {
            return false;
        }
        $btn.prop('disabled', true);
    });
</script>


@endsection