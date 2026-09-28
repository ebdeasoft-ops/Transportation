@extends('layouts.master')
@section('css')
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
{{ __('home.tree') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->

    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">&nbsp;&nbsp;{{ __('home.tree') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                </span>
            </div>
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


                    {{ csrf_field() }}
                    <div class="card-header pb-0">

                        <div class="col-sm-3 col-md-4 mb-3">
                            <a class="btn btn-primary print-style p-1 py-2" href="{{ url('/' . ($page = 'create_acount')) }}">
                                {{ __('home.add_new_account') }}
                                <svg style="width: 17px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                    <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                                </svg>
                            </a>
                        </div>

                    
                    </div>
                    </div>

                    <br>
                    <?php $i = 0; ?>
                    <div class="col-xl-12">
                        <div style="border-radius: 10px" class="card mg-b-20">
                            <div class="card-body p-5">


                                <br>
                                <div class="table-responsive hoverable-table" id="ajax_responce_allinvoicesDiv11">
         
    <ul>
                                <div>
     @foreach(App\Models\acounts_type::get()  as $type)
        <li style="font-size: 22px;color:black;  font-weight: bold;
"> {{ app()->getLocale() == 'ar' ? $type->name_ar : $type->name_en }}</li>

<ul>

     @foreach(App\Models\financial_accounts::where('account_type',$type->id)->where('parent_account_number',null)->get()  as $v)
<li style="font-size: 20px;color:green;  font-weight: 800;
">({{ $v->account_number}}) :{{ app()->getLocale() == 'ar' ? $v->name : $v->name }}</li>


    <ul>
  @foreach(App\Models\financial_accounts::where('parent_account_number',$v->id)->get()  as $v)
 

        <li style="font-size: 18px;color:blue;  font-weight: 700">({{ $v->account_number}}) :- {{ app()->getLocale() == 'ar' ? $v->name : $v->name }}</li>
           <ul>
  @foreach(App\Models\financial_accounts::where('parent_account_number',$v->id)->get()  as $v)
 

            <li style="font-size: 17px;color:red;  font-weight: 600;
">({{ $v->account_number}}) :- {{ app()->getLocale() == 'ar' ? $v->name : $v->name }}</li>
                         <ul>
  @foreach(App\Models\financial_accounts::where('parent_account_number',$v->id)->get()  as $v)
 

            <li style="font-size: 17px;color:grey">({{ $v->account_number}}) :- {{ app()->getLocale() == 'ar' ? $v->name : $v->name }}</li>
                                                            

            @endforeach
           </ul>                                             

            @endforeach
           </ul>   

            @endforeach
    </ul>

            @endforeach
</ul>   
</ul>   

            @endforeach
                                    </div>



                                </div>

                            </div>
                        </div>
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
@endsection
@section('js')
<!-- Internal Data tables -->

<!--Internal  Datatable js -->
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal  Datatable js -->
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
<!-- Internal Spectrum-colorpicker css -->
<link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">

<!-- Internal Select2 css -->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<script>
    var date = $('.fc-datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    }).val();
</script>



<script>

$('select[name="searchaboutaccountBytype_function"]').on('change', function() {
        console.log('AJAX load   work 0000');

        var selectclientid = $(this).val();
        if (selectclientid) {
            console.log("{{ URL::to('searchaboutaccountBytype_function') }}/" + selectclientid);

            $.ajax({
                url: "{{ URL::to('searchaboutaccountBytype_function') }}/" + selectclientid,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }
    });
    $('select[name="Master_account"]').on('change', function() {
        console.log('AJAX load   work 0000');

        var selectclientid = $(this).val();
        if (selectclientid) {
            console.log("{{ URL::to('searchMaster_account_function') }}/" + selectclientid);

            $.ajax({
                url: "{{ URL::to('searchMaster_account_function') }}/" + selectclientid,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }
    });
    function searchaboutinvoiceByIdfunction() {
        date = $('#invoiceid').val();
        if (date != '') {

            $.ajax({
                url: " {{URL::to('searchaboutaccountByname_numberfunction')}}" + "/" + date,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        } else {
            $.ajax({
                url: " {{URL::to('getAllinvicesajax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }
    }

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
                search_by_text: search_by_text,
                "_token": token_search
            },
            success: function(data) {
                console.log(data)
                $("#ajax_responce_allinvoicesDiv").html(data);
            },
            error: function() {

            }
        });
    });

  
</script>






















<script>
    $(document).ready(function() {
        $.ajax({
            url: " {{URL::to('getAllaccountsajax')}}",
            type: "GET",
            dataType: "html",
            success: function(products) {
                $("#ajax_responce_allinvoicesDiv").html(products);


            },
        });
    })
</script>



@endsection