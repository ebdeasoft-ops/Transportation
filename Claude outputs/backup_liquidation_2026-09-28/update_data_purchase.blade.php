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
.form-control:focus { border-color: var(--ts-accent) !important; box-shadow: 0 0 0 3px rgba(65, 155, 178, 0.15) !important; }
label, .parent-label, .control-label { font-weight: 600 !important; color: var(--ts-primary-light) !important; font-size: 13px !important; margin-bottom: 6px !important; display: inline-block; }
.btn { border-radius: var(--ts-radius-sm) !important; font-weight: 600 !important; font-size: 13.5px !important; padding: 8px 18px !important; transition: transform .15s ease, box-shadow .15s ease; }
.btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(20,40,60,.10); }
.btn-success { background: var(--ts-accent) !important; border-color: var(--ts-accent) !important; }
.btn-success:hover { background: var(--ts-accent-dark) !important; border-color: var(--ts-accent-dark) !important; }
.btn-danger { background: var(--ts-danger) !important; border-color: var(--ts-danger) !important; }
.btn-secondary { background: #eef1f4 !important; border-color: #eef1f4 !important; color: var(--ts-primary) !important; }
.btn-warning { background: var(--ts-warning) !important; border-color: var(--ts-warning) !important; }
.table-responsive, .hoverable-table { border-radius: var(--ts-radius) !important; overflow: hidden !important; border: 1px solid var(--ts-border) !important; }
table.our-table, table.table { margin-bottom: 0 !important; }
table.our-table thead th, table.table thead th { background: var(--ts-primary) !important; color: #fff !important; font-weight: 600 !important; font-size: 12.5px !important; border: none !important; padding: 12px 10px !important; white-space: nowrap; }
table.our-table tbody td, table.table tbody td { padding: 10px !important; font-size: 13.5px !important; border-color: var(--ts-border) !important; vertical-align: middle !important; }
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
{{ __('home.update_data_purchase') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('home.update_data_purchase') }}</h4><span
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
                        <input hidden type="text" class="form-control parent-input" name="transactions_id"
                            id="transactions_id" value="0">

                        <div class="row">

                            <div class="col">

                                <label class="control-label parent-label">{{ __('home.Invoice_no') }}</label>
                                <input type="text" class="form-control parent-input" name="Invoice_no" id="Invoice_no"
                                    required>
                            </div>
                            <div class="col">
                                <label class="control-label parent-label">{{ __('home.the amount') }}</label>
                                <input type="text" class="form-control parent-input" name="amount" id="amount" required>
                            </div>

                            <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                                <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                                <input type="text" class="form-control parent-input" name="notes" id="notes" required>
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
                                                <th class="border-bottom-0"> {{ __('home.Invoice_no') }}</th>
                                                <th class="border-bottom-0">{{ __('home.total') }}</th>
                                                <th class="border-bottom-0">{{ __('home.notesClient') }}</th>
                                                <th class="border-bottom-0">{{ __('home.operations') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>


                                            <?php
                                $total=0;

                            $shipments_details=App\Models\purchase_liquidation::where('Transactions_id',$id)->get()
                            ?>
                                            @foreach ($shipments_details as $item)
                                            <?php  
$total+=$item->price_filtering;

?>
                                            <tr>

                                                <td>{{ $id }}</td>
                                                <td>{{ $item->invoice_number }}</td>
                                                <td>{{ $item->price_filtering }}</td>
                                                <td>{{ $item->note_detaials }}</td>


                                                <td><a style="width:40px;height:20px"
                                                        class="modal-effect btn btn-sm btn-warning mb-1"
                                                        data-effect="effect-scale" data-id="{{ $item->id   }}"
                                                        data-amount="{{ $item->price_filtering }}"
                                                        data-note_detaials="{{ $item->note_detaials }}"
                                                        data-invoice_number="{{ $item->invoice_number  }}"
                                                        data-toggle="modal" href="#increaseProduct" title="تعديل"><i
                                                            class="las la-align-justify"></i></a></td>

                                            </tr>
                                            @endforeach
                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ $total }}</td>
                                                <td>-</td>
                                                <td>-</td>

                                            </tr>

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
                        <form action="{{ '/' . ($page = 'save_liquidation_data') }}" method="POST" role="search"
                            autocomplete="off">
                            {{ csrf_field() }}



                            <div class='col ' id="printdiv">
                                <input hidden type="text" class="form-control parent-input"
                                    name="Transactions_price_id_print" id="Transactions_price_id_print">
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

                        <div class="col">

                            <label class="control-label parent-label">{{ __('home.Invoice_no') }}</label>
                            <input type="text" class="form-control parent-input" name="Invoice_no_update"
                                id="Invoice_no_update" required>
                        </div>
                        <div class="col">
                            <label class="control-label parent-label">{{ __('home.the amount') }}</label>
                            <input type="text" class="form-control parent-input" name="amount_update" id="amount_update"
                                required>
                        </div>

                        <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                            <input type="text" class="form-control parent-input" name="notes_update" id="notes_update"
                                required>
                        </div>





                        <div class="form-group">
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
    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>
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
    var note_detaials = button.data('note_detaials')
    var invoice_number = button.data('invoice_number')
    var amount = button.data('amount')

    var modal = $(this)
    console.log(id)
    modal.find('#notes_update').val(note_detaials);
    modal.find('#amount_update').val(amount);
    modal.find('#Invoice_no_update').val(invoice_number);
    modal.find('#id_update').val(id);


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
    // [تم الإصلاح] التأكد من المبلغ قبل الإرسال (حقل "amount_update" نوعه text بسبب تنسيق
    // الأرقام بـ JS فمكانش بيتفحص تلقائيًا من المتصفح).
    var amountUpdateVal = parseFloat(String($('#amount_update').val()).replace(/,/g, ''));
    if (isNaN(amountUpdateVal) ) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'من فضلك ادخل مبلغ صحيح أكبر من صفر',
            confirmButtonText: 'حسنًا'
        });
        return;
    }
    $submitBtn.prop('disabled', true);
    console.log('*********************')

    var url = " {{ URL::to('update_purchase_liquidation') }}";
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
                $('#amount_update').val('')
                $('#notes_update').val('')
                $('#Invoice_no_update').val('')


                $('#transactions_id').val(data['id'])
                $('#Transactions_price_id_print').val(data['id'])


                let table = document.getElementById("example");
                var tableHeaderRowCount = 1;

                var rowCount = table.rows.length;

                for (var i = tableHeaderRowCount; i < rowCount; i++) {
                    table.deleteRow(tableHeaderRowCount);
                }

                i = 0;

                data['purshase_list'].forEach(async (product) => {

                    i = i + (product['price_filtering'] * 1);

                    let row = table.insertRow(-1); // We are adding at the end
                    update =
                        ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                    update = update.concat(product['id'], '  ', ' data-amount=',
                        product['price_filtering'], '  ', ' data-note_detaials=',
                        product['note_detaials'], '  ',
                        ' data-invoice_number=', product['invoice_number'], '  ',
                        '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                    )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c5 = row.insertCell(4);


                    c1.innerText = data['id']
                    c2.innerText = product['invoice_number']
                    c3.innerHTML = product['price_filtering']
                    c4.innerText = product['note_detaials']
                    c5.innerHTML = update

                })
                let row = table.insertRow(-1); // We are adding at the end

                let c1 = row.insertCell(0);
                let c2 = row.insertCell(1);
                let c3 = row.insertCell(2);
                let c4 = row.insertCell(3);
                let c5 = row.insertCell(4);


                c1.innerText = '-'
                c2.innerText = '-'
                c3.innerHTML = i
                c4.innerText = '-'
                c5.innerText = '-'

                setTimeout(() => {
                    $('#loading_modale').modal('hide');

                }, 500);
            },
            error: function(response) {
                console.log(response['responseText'])
                Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: "{{ __('home.sorryerror') }}",
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
    // [تم الإصلاح] التأكد من المبلغ قبل الإرسال (حقل "amount" نوعه text بسبب تنسيق الأرقام بـ JS).
    var amountVal = parseFloat(String($('#amount').val()).replace(/,/g, ''));
    if (isNaN(amountVal) || amountVal <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'من فضلك ادخل مبلغ صحيح أكبر من صفر',
            confirmButtonText: 'حسنًا'
        });
        return;
    }
    $submitBtn.prop('disabled', true);
    console.log('*********************')
    console.log($('#pay').val())
    var url = " {{ URL::to('purchase_liquidation') }}";
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
                $('#amount').val('')
                $('#notes').val('')
                $('#Invoice_no').val('')


                $('#transactions_id').val(data['id'])
                $('#Transactions_price_id_print').val(data['id'])


                let table = document.getElementById("example");
                var tableHeaderRowCount = 1;

                var rowCount = table.rows.length;

                for (var i = tableHeaderRowCount; i < rowCount; i++) {
                    table.deleteRow(tableHeaderRowCount);
                }

                i = 0;

                data['purshase_list'].forEach(async (product) => {

                    i = i + (product['price_filtering'] * 1);

                    let row = table.insertRow(-1); // We are adding at the end
                    update =
                        ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                    update = update.concat(product['id'], '  ', ' data-amount=',
                        product['price_filtering'], '  ', ' data-note_detaials=',
                        product['note_detaials'], '  ',
                        ' data-invoice_number=', product['invoice_number'], '  ',
                        '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                    )
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c5 = row.insertCell(4);


                    c1.innerText = data['id']
                    c2.innerText = product['invoice_number']
                    c3.innerHTML = product['price_filtering']
                    c4.innerText = product['note_detaials']
                    c5.innerHTML = update

                })
                let row = table.insertRow(-1); // We are adding at the end

                let c1 = row.insertCell(0);
                let c2 = row.insertCell(1);
                let c3 = row.insertCell(2);
                let c4 = row.insertCell(3);
                let c5 = row.insertCell(4);


                c1.innerText = '-'
                c2.innerText = '-'
                c3.innerHTML = i
                c4.innerText = '-'
                c5.innerText = '-'

                setTimeout(() => {
                    $('#loading_modale').modal('hide');

                }, 500);
            },
            error: function(response) {
                console.log(response['responseText'])
                Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: "{{ __('home.sorryerror') }}",
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