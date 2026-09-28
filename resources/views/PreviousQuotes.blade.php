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

    <style>
        .report-card {
            border: none;
            border-radius: 14px !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #2e3d50 0%, #3f5872 100%);
            border-radius: 0 !important;
            padding: 20px 26px;
        }

        .report-card .card-header h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 0;
        }

        .filter-label {
            font-weight: 600;
            font-size: 13px;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        #example12 thead th {
            background-color: #ecf0fa;
            font-size: 12px;
            font-weight: 700;
            color: #2e3d50 !important;
        }

        .btn-download {
            background-color: #28a745;
            border: none;
            border-radius: 30px;
            padding: 8px 18px;
            font-weight: 600;
            color: #fff;
        }

        .btn-download:hover {
            color: #fff;
            opacity: .9;
        }

        .btn-show {
            background-color: #419BB2;
            border: none;
            border-radius: 30px;
            padding: 8px 18px;
            font-weight: 600;
        }
    </style>
@stop

@section('title')
    {{ __('home.recentquotation') }}
@stop



@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطأ</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<br>
    <!-- row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card report-card mg-b-20">

                <div class="card-header">
                    <h5><i class="fas fa-file-invoice mr-2"></i>{{ __('home.recentquotation') }}</h5>
                </div>

                <div class="card-body">
                    <div class="row mg-b-20">
                        <div class="col-lg-6 mb-3">
                            <label class="filter-label" for="invoiceid">{{ __('home.enterinvoicenumber') }}</label>
                            <input class="form-control" value="{{ $start_at ?? '' }}" id="invoiceid" placeholder="00"
                                type="text" onchange="searchaboutinvoiceByIdfunction()">
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="filter-label" for="clientnamesearch">{{ __('home.chooseclient') }}</label>
                            <select class="form-control select2" name="clientnamesearch" id="clientnamesearch">
                                @foreach (App\Models\customers::get() as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->id == 1 ? __('home.Cash Custome') : $customer->name }} -
                                        {{ $customer->tax_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive hoverable-table" id="previous1uotestable">
                        <table class="table text-md-nowrap text-center our-table" id="example12"
                            data-page-length="50">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
                                    <th class="border-bottom-0">{{ __('home.clietName') }}</th>
                                    <th class="border-bottom-0">{{ __('home.date') }}</th>
                                    <th class="border-bottom-0">{{ __('home.branch') }}</th>
                                    <th class="border-bottom-0">{{ __('home.operations') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $product)
                                    <tr id="{{ $product->id }}">
                                        <td data-target="id">{{ $product->id }}</td>
                                        <td dir="ltr" data-target="id">{{ $product->customer->name }}</td>
                                        <td>{{ $product->created_at }}</td>
                                        <td>{{ $product->branch->name }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center flex-wrap" style="gap: 8px">
                                                <a class="btn btn-sm btn-download modal-effect" data-effect="effect-scale"
                                                    href="{{ url('generate_pdf_qoute/' . $product->id) }}" target="_blank">
                                                    {{ __('home.dwonloadpdf') }}
                                                    <i class="fa-solid fa-download ml-1"></i>
                                                </a>

                                                <form
                                                    action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/' . 'print_order_perice_to_customer') }}"
                                                    method="POST" role="search" autocomplete="off">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="OrderNoprint" value="{{ $product->id }}">
                                                    <button type="submit" class="btn btn-sm btn-show">
                                                        {{ __('home.show') }}
                                                        <i class="fas fa-eye ml-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="justify-content-start mt-3" id="ajax_pagination_in_search">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->

@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>

    <!-- Internal Datepicker js -->
    <script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!-- Internal jquery.maskedinput js -->
    <script src="{{ URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
    <!-- Internal spectrum-colorpicker js -->
    <script src="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- Internal Ion.rangeSlider.min js -->
    <script src="{{ URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <!-- Internal jquery-simple-datetimepicker js -->
    <script src="{{ URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js') }}"></script>
    <!-- Internal pickerjs js -->
    <script src="{{ URL::asset('assets/plugins/pickerjs/picker.min.js') }}"></script>
    <!-- Internal form-elements js -->
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>

    <script>
        $('.fc-datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        function searchaboutinvoiceByIdfunction() {
            var invoiceId = $('#invoiceid').val();
            if (invoiceId != '') {
                $.ajax({
                    url: "{{ URL::to('searchpreviousquotes') }}" + "/" + invoiceId,
                    type: "GET",
                    dataType: "html",
                    success: function(products) {
                        $("#previous1uotestable").html(products);
                    },
                });
            }
        }

        $('select[name="clientnamesearch"]').on('change', function() {
            var selectCustomer = $(this).val();
            if (selectCustomer != '') {
                $.ajax({
                    url: "{{ URL::to('getquotebycustomer') }}" + "/" + selectCustomer,
                    type: "GET",
                    dataType: "html",
                    success: function(products) {
                        $("#previous1uotestable").html(products);
                    },
                });
            }
        });
    </script>
@endsection