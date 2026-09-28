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
<style>
  .modal {
    display: none; /* Hidden by default */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
  }



  .close {
    float: right;
    font-size: 24px;
    cursor: pointer;
  }

.modal {
  display: none;
}

.modal.show {
  display: block;
}

</style>

<!-- Internal Spectrum-colorpicker css -->
<link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">

<!-- Internal Select2 css -->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

@section('title')
{{ __('home.banks_transfer') }}@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <h4 class="content-title mb-0 my-auto">{{ __('home.banks_transfer') }}</h4>
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







                        {{-- 3 --}}


                        <br>


               <div class="row">

                                            <div class="col-lg-4" id="start_at">
                                                <label class="parent-label" for="exampleFormControlSelect1"> {{ __('home.enterinvoicenumber') }}</label>
                                                <input class="form-control" value="{{ $start_at ?? '' }}" id="invoiceid" placeholder="*" type="text" onchange="searchaboutinvoiceByIdfunction()" required>
                                            </div><!-- input-group -->
                                                           <div class="col-lg-4" id="start_at">
                                <label for="inputName" class="control-label parent-label">{{ __('users.branch') }} </label>

                                <select class="form-control select2" name="clientnamesearch" id="clientnamesearch">

  
                                    <option value="{{ Auth()->user()->branch->id }}"> {{ Auth()->user()->branch->name }}
                                    </option>
                                 @can(abilities: 'System setting')
                                    <option value="-" selected>{{ __(key: 'users.allbranchs') }}</option>
                                        @foreach (App\Models\branchs::where('id','!=',9)->get() as $branch)

                                            <option style="font-size:15px" value="{{ $branch->id }}"> {{ $branch->name }}</option>
                                        @endforeach
                                 @endcan
                                </select>
                            </div><!-- col-4 -->
                                            <div class="col-lg-4" id="start_at">
                                                <label class="parent-label" for="exampleFormControlSelect1"> {{ __('home.searchbydate') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control parent-input fc-datepicker" value="{{ $start_at ?? '' }}" id="date" placeholder="YYYY-MM-DD" type="text" onchange="searchaboutproductfunction()" required>
                                                </div><!-- input-group -->
                                                
                                         <!-- input-group -->
                             
                               </div><br>
                               
                        <?php $i = 0; ?>
                        <div class="col-xl-12">
                              <br>
                               
                             
                            <div class="card mg-b-20">
                        
                              
                                <div >
                                    <div class="table-responsive hoverable-table" id="ajax_responce_allinvoicesDiv">
                                        <table class="table text-md-nowrap text-center our-table" id="example1" data-page-length='50' style=" text-align: center;">
                                            <thead>
                                                <tr>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.sallerName') }} </th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.total') }}</th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.paymentmethod') }}</th>
                                                    <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.operations') }}</th>



                                                </tr>
                                            </thead>
                                            <tbody>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tbody>
                                        </table>
                                        <div>



                                        </div>
                                        <br>

                                    </div>
                                </div>
                            </div>

                            <br />

                </div>



            </div>
        </div>
        <!-- row closed -->
    </div>
    <!-- Container closed -->
</div>
<!-- main-content closed -->
</div>
<!-- enter payments -->
  <div class="modal fade" id="paymentmethod" tabindex="-1"   
            >
            <div style="margin: 5% !important;" class="modal-dialog modal-special" role="document">
                <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"> {{ __('home.add_describtion_shipment') }} </h6>
            </div>
            <div class="modal-body">
         
                     <form enctype="multipart/form-data" method="POST" role="search" name="form-name" id='formdata' autocomplete="off">
                        {{ csrf_field() }}

                <div class="row">

                    <div class="col">

                        <label class="control-label parent-label">{{ __('home.loading') }}</label>
                        <input type="text" class="form-control parent-input" name="loading" id="loading"  >
                    </div>
                    <div class="col">
                        <label class="control-label parent-label">{{ __('home.Unloading') }}</label>
                        <input type="text" class="form-control parent-input" name="Unloading" id="Unloading"  >
                    </div>
                    <div class="col">

                        <label class="control-label parent-label">{{ __('home.truck_no') }}</label>
                        <input type="text" class="form-control parent-input" name="truck_no" id="truck_no"  >
                    </div>
                    <div class="col">
                        <label class="control-label parent-label">{{ __('home.invoice_no') }}</label>
                        <input type="text" class="form-control parent-input" name="invoice_no" id="invoice_no"  >
                    </div>
  
            </div>
                     <div class="row">

                    <div class="col">

                        <label class="control-label parent-label">{{ __('home.delay') }}</label>
                        <input type="text" class="form-control parent-input" name="delay" id="delay"  >
                    </div>
                    <div class="col">
                        <label class="control-label parent-label">{{ __('home.ext') }}</label>
                        <input type="text" class="form-control parent-input" name="ext" id="ext"  >
                    </div>
                    <div class="col">

                        <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                        <input type="text" class="form-control parent-input" name="note" id="note"  >
                    </div>
                         <div class="form-group">
                  <label> {{__('home.attachments')}}</label>
                  <input autocomplete="off" onchange="readURL(this)" type="file" id="attachments" name="attachments" class="form-control">
                
               </div>
  
            </div>
             <div class="modal-footer">
                <button  class="btn btn-secondary" data-dismiss="modal">{{ __('home.cancel') }}</button>

                      <button  type='submit' data-dismiss="modal"  class="btn btn-success " id="button_1">
                            {{ __('home.savedecoument') }}
                          
                        </button>
            </div>
                                 </form>

        </div>
    </div>
</div>


<!-- delete -->

</div>
<div class="modal" id="updateCustomer">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title"> {{ __('home.updatecustome') }} </h6>
            </div>
            {{ csrf_field() }}


            <div class="modal-body">




                <input type="hidden" id="token_search" value="{{ csrf_token() }}">


                <div class="row">
                    <div class="col">


                        <label for="inputName" class="control-label parent-label">{{ __('home.Invoice_no') }}</label>
                        <input type="number" class="form-control parent-input" id="id" name="id" title="    رقم الفاتورة  " readonly>

                    </div>
                    <div class="col">


                        <label for="inputName" class="control-label parent-label">{{ __('home.clietName') }}</label>
                        <input type="text" class="form-control parent-input" id="customername" name="customername" title="   اسم  العميل  " readonly>

                    </div>
                </div>
                <br>
                <div class=" col">
                    <label> {{ __('home.chooseclient') }}</label>
                    <br>

                    <select class="form-control select2" style="width:300px" name='customerId' id='customerId' required>

                        @foreach (App\Models\customers::get() as $customer)
                        <option value="{{ $customer->id }}"> {{ $customer->id==1?__('home.Cash Custome'):$customer->name }}

                        </option>
                        @endforeach
                    </select>


                </div>







                <br>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('home.cancel')}}</button>
                <button type="submit" id="updatecustomer" data-dismiss="modal" class="btn btn-danger">{{ __('home.confirm') }}</button>
            </div>

        </div>
    </div>
</div>

    <div class="modal p-3" id="delete_quotation">
        <div style="margin: 0 9% !important;" class="modal-dialog modal-dialog-centered modal-special" role="document">
            <div class="modal-content modal-content-demo p-3">
                <form>
                    <div class="modal-header">
                        <h6 class="modal-title"> {{ __('home.alert') }} </h6><button aria-label="Close" class="close close-special" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    {{ csrf_field() }}
                    <div class="row mb-1">
                        <div class="col-lg-6 col-md-6 col-md-4 mb-2">
                            <label style="font-size: 22px;" for="inputName" class="control-label parent-label"> {{ __('home.Are_you_sure_delete') }}</label>
                        </div>


                    </div>

                        <input type="text" hidden class="form-control parent-input" name="delete_id" id="delete_id" >

                    <br>
                     <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('home.cancel') }}</button>
                <button id="delete_quotation_function" name="delete_quotation_function" data-dismiss="modal" class="btn btn-danger">{{ __('home.confirm') }}</button>
            </div>
            </div>

        </div>
    </div>
</div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Internal Data tables -->

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



<script>
   $('#delete_quotation').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        $('#delete_id').val(id)
    });
    
    $("#delete_quotation_function").click(function(e) {


            var selectCustomer = $('#delete_id').val();
        if (selectCustomer != '') {
            $.ajax({
                url: " {{URL::to('delete_transction')}}" + "/" + selectCustomer,
                type: "GET",
                dataType: "html",
                success: function(products) {
                $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        } else {
        
        }

        });
        
        
        
        
        
        
        
        
    
       $("#formdata").on('submit',function(e) {
        e.preventDefault();
        // [تم الإصلاح] حماية من إرسال النموذج مرتين لو المستخدم دبس على زرار الحفظ أكتر من مرة.
        var $submitBtn = $(this).find('button[type="submit"], input[type="submit"]').first();
        if ($submitBtn.prop('disabled')) {
            return;
        }

       $('#paymentmethod').modal('hide');

        $('#loading').modal().show();

        var url = " {{ URL::to('add_describtion') }}";
            var token_search = $("#token_search").val();
            if ($('#clientnamesearch').val() == '-') {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.enterclienname')}}",
                        confirmButtonText: 'حسنًا'
                    })
            } else if ($('#loading').val() == '') {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.loading')}}",
                        confirmButtonText: 'حسنًا'
                    })

            }else if ($('#Unloading').val() == '') {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.Unloading')}}",
                        confirmButtonText: 'حسنًا'
                    })

            }else if ($('#truck_no').val() == '') {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.truck_no')}}",
                        confirmButtonText: 'حسنًا'
                    })

            }else if ($('#invoice_no').val() == 0) {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.invoice_no')}}",
                        confirmButtonText: 'حسنًا'
                    })

            }else if ($('#ext').val() == 0) {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.ext')}}",
                        confirmButtonText: 'حسنًا'
                    })

            }else if ($('#delay').val() == 0) {
                Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: "{{__('home.delay')}}",
                        confirmButtonText: 'حسنًا'
                    })

            } else {
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'post',
                    cache: false,
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    },

                  contentType:false,
                 processData:false,
                data:new FormData(this),


                    success: function(data) {
                                            console.log(data)


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






    function searchaboutproductfunction() {
        date = $('#date').val();
                branch = $('#clientnamesearch').val();

        console.log(" {{URL::to('searchTransctionbyDate')}}" + "/" + date+ "/" + branch)
        $.ajax({
            url: " {{URL::to('searchTransctionbyDate')}}" + "/" + date+ "/" + branch,
            type: "GET",
            dataType: "html",
            success: function(products) {
                console.log(products)
                $("#ajax_responce_allinvoicesDiv").html(products);


            },
        });
    }
        $('select[name="clientnamesearch"]').on('change', function() {
            console.log('AJAX load   work 0000');

            var selectCustomer = $(this).val();
        if (selectCustomer != '') {
            $.ajax({
                url: " {{URL::to('getTransctionbyBransh')}}" + "/" + selectCustomer,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        } else {
            $.ajax({
                url: " {{URL::to('getpreviousTransfersajax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }

        });
        
        $(document).ready(function () {
        console.log(" {{URL::to('getpreviousTransfersajax')}}")
  $.ajax({
                url: " {{URL::to('getpreviousTransfersajax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
            });
    function searchaboutinvoiceByIdfunction() {
        date = $('#invoiceid').val();
        branch = $('#clientnamesearch').val();
        if (date != '') {

            $.ajax({
                url: " {{URL::to('searchaboutTransctionByIdfunction')}}" + "/" + date+ "/" + branch,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        } else {
            $.ajax({
                url: " {{URL::to('getpreviousTransfersajax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }
    }

    $("#clientnamesearch").click(function(e) {
        branch = $('#clientnamesearch').val();
        if (date != '') {

            $.ajax({
                url: " {{URL::to('searchaboutTransctionByBranch')}}" + "/" + branch,
                type: "GET",
                dataType: "html",
                success: function(products) {
                    console.log(products)
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        } else {
            $.ajax({
                url: " {{URL::to('getpreviousTransfersajax')}}",
                type: "GET",
                dataType: "html",
                success: function(products) {
                    $("#ajax_responce_allinvoicesDiv").html(products);


                },
            });
        }
    })

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
    $('select[name="paymodal"]').on('change', function() {

        var selectCustomer = $(this).val();
        if ($('#cashamount').val() != 0) {
            value = $('#cashamount').val()

        } else if ($('#bankamount').val() != 0) {
            value = $('#bankamount').val();

        } else if ($('#Bank_transfer').val() != 0) {
            value = $('#Bank_transfer').val();

        } else {
            value = $('#creaditamount').val();
        }


        if (selectCustomer == 'Cash') {
            $('#cashamount').val(value)
            $('#bankamount').val(0)
            $('#creaditamount').val(0)
            $('#Bank_transfer').val(0)
            document.getElementById("bankamount").readOnly = true;
            document.getElementById("cashamount").readOnly = true;
            document.getElementById("Bank_transfer").readOnly = true;


        } else if (selectCustomer == 'Shabka') {
            $('#cashamount').val(0)
            $('#bankamount').val(value)
            $('#creaditamount').val(0)
            $('#Bank_transfer').val(0)

            document.getElementById("bankamount").readOnly = true;
            document.getElementById("cashamount").readOnly = true;
            document.getElementById("Bank_transfer").readOnly = true;

        } else if (selectCustomer == 'Credit') {
            $('#cashamount').val(0)
            $('#bankamount').val(0)
            $('#Bank_transfer').val(0)
            $('#creaditamount').val(value)
            document.getElementById("bankamount").readOnly = true;
            document.getElementById("cashamount").readOnly = true;
            document.getElementById("Bank_transfer").readOnly = true;

        } else if (selectCustomer == 'Bank_transfer') {


            $('#cashamount').val(0)
            $('#bankamount').val(0)
            $('#creaditamount').val(0)
            $('#Bank_transfer').val(value)
            document.getElementById("bankamount").readOnly = true;
            document.getElementById("cashamount").readOnly = true;
            document.getElementById("Bank_transfer").readOnly = true;

        } else {
            $('#cashamount').val(value)
            $('#bankamount').val(0)
            $('#creaditamount').val(0)
            $('#Bank_transfer').val(0)
            document.getElementById("bankamount").readOnly = false;
            document.getElementById("cashamount").readOnly = false;
            document.getElementById("Bank_transfer").readOnly = false;


        }





    });
    $('#updateCustomer').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        $('#cashamount').val(0)
        $('#Bank_transfer').val(0)
        $('#creaditamount').val(0)
        $('#bankamount').val(0)

        var customername = button.data('customername')


        var modal = $(this)

        modal.find('.modal-body #customername').val(customername);
        modal.find('.modal-body #id').val(id);

    })
</script>

<script>
    $('#paymentmethod').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        // var id = button.data('id')
        // var invoice = button.data('totalinvoice')
        // $('#invoiceid').val(id)
        // document.getElementById('totalvalue').innerHTML = invoice;
        // $('#cashamount').val(invoice)

    });

    $(document).ready(function() {


        $('select[name="clientnamesearch"]').on('change', function() {
            console.log('AJAX load   work 0000');

            var selectclientid = $(this).val();
            if (selectclientid) {
                console.log('AJAX load   work');

                $.ajax({
                    url: "{{ URL::to('getcustomer') }}/" + selectclientid,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log("success");
                        console.log(data['name']);
                        $('#clientName').val(data['name']);
                        $('#address').val(data['address']);
                        $('#phonenumber').val(data['phone']);
                        $('#notes').val(data['notes']);
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
    $(document).ready(function() {
        $.ajax({
            url: " {{URL::to('getpreviousTransfersajax')}}",
            type: "GET",
            dataType: "html",
            success: function(products) {
                $("#ajax_responce_allinvoicesDiv").html(products);


            },
        });
        $('#invoice_number').hide();

        $('input[type="radio"]').click(function() {
            if ($(this).attr('id') == 'type_div') {
                $('#invoice_number').hide();
                $('#type').show();
                $('#start_at').show();
                $('#end_at').show();
            } else {
                $('#invoice_number').show();
                $('#type').hide();
                $('#start_at').hide();
                $('#end_at').hide();
            }
        });
    });
</script>


@endsection