  @extends('layouts.master')
@section('css')
<style>
:root {
    --ts-primary: #1c2b39; --ts-primary-light: #2d4356; --ts-accent: #419BB2; --ts-accent-dark: #2f7d91;
    --ts-border: #e3e8ee; --ts-bg-soft: #f7f9fb; --ts-radius: 10px; --ts-shadow: 0 2px 10px rgba(20,40,60,.06);
}
.card-invoice { border: 1px solid var(--ts-border) !important; border-radius: var(--ts-radius) !important; box-shadow: var(--ts-shadow) !important; }
table.our-table thead th, table.table thead th { background: var(--ts-primary) !important; color: #fff !important; font-size: 12.5px !important; padding: 10px !important; }
table.our-table tbody td, table.table tbody td { padding: 9px !important; font-size: 13.5px !important; }
.btn-success { background: var(--ts-accent) !important; border-color: var(--ts-accent) !important; }
.btn-danger { background: #e74c3c !important; border-color: #e74c3c !important; }
.btn { border-radius: 8px !important; font-weight: 600 !important; }
.modal-content { border-radius: var(--ts-radius) !important; border: none !important; box-shadow: 0 10px 40px rgba(0,0,0,.15) !important; }

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

<?php
$logo=camplogo;
        $product = App\Models\Covenant_liquidation::where('id', $id)->first();

    ?>
<div class="row row-sm">
    <div class="col-md-12 col-xl-12">
        <div class=" main-content-body-invoice" id="print">
            <div class="card card-invoice">
                <div class="card-body">
                    <div class="invoice-header" style="display: flex;justify-content:space-between;width:100%">

                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>
                            <span style="font-size:25px">{{Nameen}}</span>
                            <br>
                            <p dir=ltr> {{describtionen}} </p>
                            <span dir=ltr>{{STen}} </span>
                            <p dir=ltr> {{Taxen}} </p>

                        </div>
                        <div class="row">

                            <a href="https://ebdeasoft.com/"><img src="{{ asset('assets/img/brand').'/'.$logo }}"
                                    class="logo-1" alt="logo" style="width: 110px; height: 70px;"></a>

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
                    <div class="row mg-t-12">



                    </div>


                    <div class="card-body">
                        <br><br>

                        <table id="example"
                            class="table key-buttons text-md-nowrap table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('home.no_liquidation') }}</th>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('users.username') }} </th>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('home.date') }}</th>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('home.branch') }}</th>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('home.status_liquidation') }}</th>
                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">
                                        {{ __('home.total') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-target="id">{{ $product->id }}</td>
                                    <td dir="ltr" data-target="id">{{ $product->user->name??'' }}</td>
                                    <td data-target="numberofpice">{{ $product->created_at }}</td>
                                    <td data-target="numberofpice">{{ $product->branch->name }}</td>
                                    @if($product->status==1)
                                    <td data-target="numberofpice"
                                        style="color:orange; font-weight: bold;font-size:20px">
                                        {{ __('home.data_complete') }}</td>
                                    @endif
                                    @if($product->status==2)
                                    <td data-target="numberofpice"
                                        style="color:green; font-weight: bold;font-size:20px">
                                        {{ __('home.confirm_done') }}</td>
                                    @endif    @if($product->status==3)
                                    <td data-target="numberofpice"
                                        style="color:green; font-weight: bold;font-size:20px">
                                        {{ __('home.pratiail_of_review') }}</td>
                                    @endif

                                    <td data-target="numberofpice">{{ $product->price_filtering }}</td>


                                </tr>

                        </table>
                        <br><br>
                        <div class="table-responsive">


                            <br>
                            <center>
                                <h3> {{ __('home.purchases_details') }}</h3>
                            </center>


                            <table id="example_shipment" class="table text-md-nowrap text-center our-table" width="100%"
                                style="border: 2px solid rgba(0,0,0,.3);">
                                <thead>
                                    <tr>

                                        <th class="border-bottom-0"> {{ __('home.Invoice_no') }}</th>
                                        <th class="border-bottom-0">{{ __('home.total') }}</th>
                                        <th class="border-bottom-0">{{ __('home.notesClient') }}</th>
                                        <th class="border-bottom-0">{{ __('home.attachments') }}</th>
                                        <th class="border-bottom-0">{{ __('home.operations') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                <input hidden type="text" class="form-control parent-input" name="id" id="id" hidden value="{{$id}}">

                                    <?php
                                $total=0;

                            $shipments_details=App\Models\purchase_liquidation::where('Transactions_id',$id)->get();
                            ?>
                                    @if($shipments_details!=NULL)
                                    @foreach ($shipments_details as $item)
                                    <?php  
$total+=$item->price_filtering;

?>
                                    <tr>

                                        <td>{{ $item->invoice_number }}</td>
                                        <td>{{ $item->price_filtering }}</td>
                                        <td>{{ $item->note_detaials }}</td>

                                        <td> @if($item['attachments']!=null)<a target="_blank"
                                                href="{{ url('/' . ($page = 'openfile') .'/'.$item['attachments']) }}">{{  __('home.show')}}</a>
                                            @endif
                                        </td>

                                        <td>
   
                                    
                                            @if(Auth()->user()->branchs_id==9&&$item->status==0)
                                            <a style="width:40px;height:20px"
                                                class="modal-effect btn btn-sm btn-warning mb-1"
                                                data-effect="effect-scale" data-id="{{ $item->id   }}"
                                                data-amount="{{ $item->price_filtering }}"
                                                data-note_detaials="{{ $item->note_detaials }}"
                                                data-invoice_number="{{ $item->invoice_number  }}" data-toggle="modal"
                                                href="#increaseProduct" title="تعديل"><i
                                                    class="las la-align-justify"></i></a>
                                            @else

                                            <a style="width:130px;height:20px"
                                                class="modal-effect btn btn-sm btn-success mb-1"
                                                data-effect="effect-scale" data-id="{{ $item->id }}" data-toggle="modal"
                                                href="" title="تعديل"><i
                                                    class="las la-align-justify">{{ __('home.confirm_done_1') }}</i></a>


                                            @endif

                                        </td>

                                    </tr>
                                    @endforeach
                                    <tr>

                                        <td>-</td>
                                        <td>{{ $total }}</td>

                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>

                                    </tr>

                                    @else




                                    @endif
                                </tbody>
                            </table>



                            <br>
                            <br>
                          <div class="d-flex justify-content-left">
                            @if($product->owner_confirm==0&&Auth()->user()->branchs_id==9)
                            <button style="background-color: #419BB2" id="confirm_liquidation_from_owner" name="confirm_liquidation_from_owner"
                                class="btn btn-success p-1">
                                {{ __('home.confirm_onwer') }}
                                <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                    <path fill="none"
                                        d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z">
                                    </path>
                                </svg>
                            </button>
                            @endif
                                         @if($product->owner_confirm==1)
                            <button id="confirm_done" style="background-color:green" class="btn btn-success p-1">
                                {{ __('home.confirm_done') }}
                              
                            </button>
                            @endif
                        </div>
                        </div>

                        <hr class="mg-b-40">


                        <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button"
                            onclick="printDiv()"> <i class="mdi mdi-printer ml-1"></i>{{__('home.print')}}</button>
                                           &nbsp;
                                <br>
                                        <a style="background-color: #419BB2;font-size:17px" class="btn btn-success p-1" href="{{ url('/' . ($page = 'export_Liquidation') . '/' . $product->id ) }}">
                              EXPORT EXCEL
                                <svg style="width: 20px !important" class="svg-icon-buttons" viewBox="0 0 20 20">
                                    <path d="M17.453,12.691V7.723 M17.453,12.691V7.723 M1.719,12.691V7.723 M18.281,12.691V7.723 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M7.309,13.312h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,13.312,7.309,13.312 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M16.625,6.066h-1.449V3.168c0-0.228-0.186-0.414-0.414-0.414H5.238c-0.228,0-0.414,0.187-0.414,0.414v2.898H3.375c-0.913,0-1.656,0.743-1.656,1.656v4.969c0,0.913,0.743,1.656,1.656,1.656h1.449v2.484c0,0.228,0.187,0.414,0.414,0.414h9.523c0.229,0,0.414-0.187,0.414-0.414v-2.484h1.449c0.912,0,1.656-0.743,1.656-1.656V7.723C18.281,6.81,17.537,6.066,16.625,6.066 M5.652,3.582h8.695v2.484H5.652V3.582zM14.348,16.418H5.652v-4.969h8.695V16.418z M17.453,12.691c0,0.458-0.371,0.828-0.828,0.828h-1.449v-2.484c0-0.228-0.186-0.414-0.414-0.414H5.238c-0.228,0-0.414,0.186-0.414,0.414v2.484H3.375c-0.458,0-0.828-0.37-0.828-0.828V7.723c0-0.458,0.371-0.828,0.828-0.828h13.25c0.457,0,0.828,0.371,0.828,0.828V12.691z M7.309,13.312h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,13.312,7.309,13.312M7.309,15.383h5.383c0.229,0,0.414-0.187,0.414-0.414s-0.186-0.414-0.414-0.414H7.309c-0.228,0-0.414,0.187-0.414,0.414S7.081,15.383,7.309,15.383 M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484 M12.691,12.484H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,12.484,12.691,12.484M12.691,14.555H7.309c-0.228,0-0.414,0.187-0.414,0.414s0.187,0.414,0.414,0.414h5.383c0.229,0,0.414-0.187,0.414-0.414S12.92,14.555,12.691,14.555">
                                    </path>
                                </svg>
                            </a>
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




<div class="modal fade product-selection"
    style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="loading" name="loading"
    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
    <div class="modal-dialog modal-xl"
        style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" role="document">
        <div class="modal-content">
            <div class="modal-body" style="justify-content: center;">
                <center><img style="width:250px;height:250px;" class="custom_img"
                        src="{{ asset('assets/admin/uploads/loading.png') }}">

                </center>
            </div>
        </div>
    </div>
</div>
 <input hidden=true class="form-control" id="branchs_id" name="branchs_id"
                                    value="{{Auth()->user()->branchs_id}}">

<div class="modal fade" id="increaseProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div style="margin: 5% !important;" class="modal-dialog modal-special" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('home.confirm_data_shipment') }}</h5>
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
                                id="Invoice_no_update" required readonly>
                        </div>
                        <div class="col">
                            <label class="control-label parent-label">{{ __('home.the amount') }}</label>
                            <input type="text" class="form-control parent-input" name="amount_update" id="amount_update"
                                required readonly>
                        </div>

                        <div class="col-lg-3 mg-t-20 mg-lg-t-0">

                            <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                            <input type="text" class="form-control parent-input" name="notes_update" id="notes_update"
                                required readonly>
                        </div>





                        <div class="form-group">
                            <label> {{__('home.attachments')}}</label>
                            <input autocomplete="off" onchange="readURL(this)" type="file" id="attachments_update"
                                name="attachments_update" class="form-control">

                        </div>
                    </div>



                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('home.cancel')}}</button>
                        <button type="button" id="confirmshipment" class="btn btn-danger"
                            data-dismiss="modal">{{ __('home.confirm') }}</button>
                    </div>

            </form>
        </div>
    </div>
</div>
<!-- main-content closed -->
@endsection
@section('js')
    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>
<!--Internal  Chart.bundle js -->
<script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>


<script type="text/javascript">

    $("#confirm_liquidation_from_owner").click(function(e) {

                e.preventDefault();
                var token_search = $("#token_search").val();
            $('#massagesave').modal().show();
console.log("{{ URL::to('/confirm_liquidation_from_owner') }}/" + $("#id").val() )
                    $.ajax({
                       url: "{{ URL::to('/confirm_liquidation_from_owner') }}/" + $("#id").val() ,
                       type: "GET",
                       dataType: "json",
                        success: function (data) {
                            
                    document.getElementById('confirm_liquidation_from_owner').hidden = true
                setTimeout(() => {
                    $('#massagesave').modal('hide');
                }, 500);
                            document.getElementById('confirm_done').hidden = false

                            
                            
                
                        },
                        error: function (response) {
                            console.log(response['responseText'])
                            Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: "{{ __('home.sorryerror') }}",
                        confirmButtonText: 'موافق'
                    })

                        }
                        
                        
                    })
                    })
                
                
                
                



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



$('#increaseProduct').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget)
    var id = button.data('id')
    var note_detaials = button.data('note_detaials')
    var invoice_number = button.data('invoice_number')
    var amount = button.data('amount')

    console.log(id)
    $('#notes_update').val(note_detaials);
    $('#amount_update').val(amount);
    $('#Invoice_no_update').val(invoice_number);
    $('#id_update').val(id);



})




$("#confirmshipment").click(function(e) {
    $('#loading').modal().show();

    e.preventDefault();
    var token_search = $("#token_search").val(); {
        $('#loading_modale').modal().show();

        $.ajax({
            url: "{{ URL::to('/confirm_data_purchases') }}/" + $("#id_update").val(),
            type: "GET",
            dataType: "json",


            success: function(data) {

                $('#transactions_id').val(data['id'])
                $('#Transactions_price_id_print').val(data['id'])


                let table = document.getElementById("example_shipment");
                var tableHeaderRowCount = 1;

                var rowCount = table.rows.length;

                for (var i = tableHeaderRowCount; i < rowCount; i++) {
                    table.deleteRow(tableHeaderRowCount);
                }

                i = 0;

                data['shipments_details'].forEach(async (product) => {

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
                    update1 =
                        ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-success mb-1" data-effect="effect-scale" data-id='
                    update1 = update1.concat(product['id'], '  ', ' data-amount=',
                        product['price_filtering'], '  ', ' data-note_detaials=',
                        product['note_detaials'], '  ',
                        ' data-invoice_number=', product['invoice_number'], '  ',
                        '  data-toggle="modal"   href=""   title="تعديل"><i class="las la-align-justify"></i></a>'
                    )
            
                var branchs_id = $("#branchs_id").val();


                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    let c3 = row.insertCell(2);
                    let c4 = row.insertCell(3);
                    let c5 = row.insertCell(4);
       link_attachment=" {{ URL::to('openfile') }}" + "/" + product['attachments'];


        attachment_1='<a  target="_blank"'+' '+' href= '+'"'+link_attachment+'"'+'>{{  __("home.show")}}</a>';
 

                    c1.innerText =product['id']
                    c2.innerText = product['price_filtering']
                    c3.innerHTML = product['note_detaials']
                    c4.innerHTML = attachment_1
                    if (product['status'] ==0&&branchs_id==9) {
                        c5.innerHTML = update

                    }else{
                        c5.innerHTML = update1

                    }

                })
                let row = table.insertRow(-1); // We are adding at the end

                let c1 = row.insertCell(0);
                let c2 = row.insertCell(1);
                let c3 = row.insertCell(2);
                let c4 = row.insertCell(3);
                let c5 = row.insertCell(4);


                c1.innerText = '-'
                c2.innerText = i
                c3.innerHTML =''
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
    setTimeout(() => {
        $('#loading').modal('hide');

    }, 500);
});
</script>

@endsection