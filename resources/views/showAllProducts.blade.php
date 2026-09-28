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
{{ __('home.stock') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->

    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">&nbsp;&nbsp;{{ __('home.stock') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
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
    <input type="hidden" id="token_search" value="{{ csrf_token() }}">

    <!-- row -->
    <div class="row">

        <div class="col-xl-12">
            <div class="card mg-b-20">


                <div class="card-header pb-0">


                    {{ csrf_field() }}



                    <?php $i = 0; ?>
                    <div class="col-xl-12">
                        <div style="border-radius: 10px" class="card mg-b-20">
                            <div class="card-body p-5">


                                <div class="row">
                                   <div class="col-lg-3 mb-2">
                                    <label for="inputName" style="font-weight: bold" class="control-label parent-label"> {{__('home.searchaboutproduct')}} </label>
                                    <input dir="ltr" type="text" class="form-control parent-input" placeholder="{{ __('home.Search By Name or Product Number') }}" id="searchaboutproduct" name="searchaboutproduct" onkeyup="searchaboutproductfunction()">
                                </div>
                                   <div class="col-lg-3 mb-2">
                                <label for="inputName" class="control-label parent-label">{{ __('home.groups') }}</label>
                                <select style="width:100%!important" name="product_group" id="product_group" class="form-control select2" >
                                    <!--placeholder-->
                                   @foreach (App\Models\products_group::get() as $section)
                                    <option value="{{ $section->id }}"> {{ $section->group_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                            
                            
                      
                                <br>
                                <div id="ajax_responce_serarchDiv" class="table our-table border mb-0 table-responsive text-center" >
                                    <table class="table text-md-nowrap text-center our-table" id="example2" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
                                        <col style="width:5%">
                                        <col style="width:15%">
                                        <col style="width:20%">
                                        <col style="width:10%">
                                        <col style="width:10%">
                                        <col style="width:10%">
                                        <col style="width:15%">
                                        <col style="width:15%">


                                        <thead>
                                            <tr>
                                                <th style="font-size: 15px" class="border-bottom-0">#</th>
                                                <th style="font-size: 15px" class="border-bottom-0">{{__('home.productNo')}} </th>
                                                <th style="font-size: 15px" class="border-bottom-0" style="text-align:center">{{__('home.product')}}</th>
                                                <th style="font-size: 15px" class="border-bottom-0" style="text-align:center">{{__('home.branch')}}</th>
                                                <th style="font-size: 15px" class="border-bottom-0">{{__('home.productlocation')}}</th>
                                                <th style="font-size: 15px" class="border-bottom-0">{{__('home.quantity')}}</th>
                                                <th style="font-size: 13px" class="border-bottom-0">{{__('home.purchaseproductwithouttax')}}</th>
                                                <th style="font-size: 13px" class="border-bottom-0">{{__('home.sellingproduct without tax')}}</th>



                                            </tr>
                                        </thead>
                                        <tbody class="">
                                            <?php $i = 0;
                                            ?>

                                            <?php $i++ ?>

                                            <tr>
                                                <td id="tableData"  dir=ltr>-</td>
                                                <td id="tableData"  dir=ltr>-</td>
                                                <td id="tableData"  data-target="product_name">-</td>
                                                <td id="tableData"  data-target="product_name">-</td>
                                                <td id="tableData"  data-target="numberofpice">-</td>
                                                <td id="tableData"  data-target="numberofpice">-</td>
                                                <td id="tableData"  data-target="numberofpice">-</td>
                                                <td id="tableData"  data-target="numberofpice">-</td>
                                            </tr>
                                           
                                        </tbody>
                                    </table>
                                    
                                    <div>

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

<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
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
</script>



{{-- Update ( 24/4/2023 ) --}}

<script>


function searchaboutproductfunction() {
        searchtext = $('#searchaboutproduct').val();
        var token_search = $("#token_search").val();
console.log(searchtext)
console.log('-----------------*----------------')
        jQuery.ajax({
                url:  "{{ URL::to('searchAllproductpaginatenew_by_post')}}",
                type: 'post',
                cache: false,
                dataType: 'html',
                data: {
                    "_token": token_search,
                    "searchtext": searchtext,
                },
            success: function(data) {
                $("#ajax_responce_serarchDiv").html(data);
            },
           error: function(response) {
console.log(response)
            }
        });

    }



   
    $(document).on('click', '#ajax_pagination_in_search a ', function(e) {
        e.preventDefault();
        var search_by_text = $("#search_by_text").val();
        var url = $(this).attr("href");
        var token_search = $("#token_search").val();

        jQuery.ajax({
            url: url,
            type: 'post',
                cache: false,
                dataType: 'html',
                data: {
                    "_token": token_search,
                    "searchtext": searchtext,
                },
            success: function(data) {
                console.log(data)
                $("#ajax_responce_serarchDiv").html(data);
            },
            error: function() {

            }
        });
    });

   
</script>

{{-- End Update ( 24/4/2023 ) --}}







<script>
    $(document).ready(function() {



        // Update ( 24/4/2023 )

        $.ajax({
            url: " {{URL::to('Allproductpaginatenew')}}",
            type: "GET",
            dataType: "html",
            success: function(products) {
                $("#ajax_responce_serarchDiv").html(products);


            },
        });


        $('select[name="product_group"]').on('change', function() {
            var selectclientid = $(this).val();
            var token_search = $("#token_search").val();
            if (selectclientid) {

                $.ajax({
                    url: "{{ URL::to('product_group_ajax') }}",
                    type: 'post',
                cache: false,
                dataType: 'html',
                data: {
                    "_token": token_search,
                    "group_id": selectclientid,
                },
                    success: function(products) {
                        $("#ajax_responce_serarchDiv").html(products);

                    },
                });
            } else {
                console.log('AJAX load did not work');
            }
        });
    });

    $('select[name="searchproductNo"]').on('change', function() {
        console.log('AJAX load   work 0000');

        var selectclientid = $(this).val();
        if (selectclientid) {
            console.log('AJAX load   work');

            $.ajax({
                url: "{{ URL::to('getproduct') }}/" + selectclientid,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    console.log("success");
                    console.log(data['name']);
                    $('#quentity').val(data['numberofpice']);

                },
            });
        } else {
            console.log('AJAX load did not work');
        }
    });
</script>




<script>

</script>


@endsection