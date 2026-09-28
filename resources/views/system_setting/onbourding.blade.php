@extends('layouts.master')
@section('css')
    <!--- Internal Select2 css-->
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

@endsection
@section('title')
    {{ __('home.onbourding') }}@stop

@section('page-header')
    <div class="main-parent">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between parent-heading">
            <div class="my-auto">
                <div class="d-flex">
                    <h4 class="content-title mb-0 my-auto">{{ __('home.onbourding') }}</h4>
                </div>
            </div>
        </div>
        <!-- breadcrumb -->
    @endsection
    @section('content')
    <br>

        @if (session()->has('newcustomer'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <br>

                <strong>{{ session()->get('newcustomer') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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

            <div class="col-lg-12 col-md-12">
                <div style="border-end-end-radius: 10px;border-end-start-radius:10px" class="card pt-5">
                    <div class="card-body pb-0">
                        <form
                            action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/' . ($page = 'onbourding')) }}"
                            method="post" enctype="multipart/form-data" autocomplete="off">
                            {{ csrf_field() }}
                   

                   
                                                            <div class="row mb-3">
                                       <div class="col-lg-3 " >
                                <label for="inputName" style="width:250px"  class="control-label parent-label">{{ __('home.invoicetype') }}</label>

                                <select class="form-control parent-input" style="width:250px"  name="invoicetype" id="invoicetype">


                                    <option value="1100">{{__('home.Both')}}   </option>
                                    <option value="0100">{{__('home.B2B')}}   </option>
                                    <option value="1000">{{__('home.B2C')}}   </option>

                                
                                </select>
                           
                           
                              </div>
                        
                              </div>
                              <br>
                              <div class="col-lg-3">
                                <label for="inputName" style="width:250px"   class="control-label parent-label">{{ __('home.typeconnect') }}</label>

                                <select class="form-control parent-input " style="width:250px"   name="typeconnect" id="typeconnect">
                                <option value="1">{{__('home.coreumlation')}}   </option>

                                <option value="0">{{__('home.connectsumlation')}}   </option>

                                
                                </select>
                           
                           
                              </div>

                              <br>
                              <br>

                <a target="_blank"  href="https://login.zatca.gov.sa/saml2/idp/sso?SAMLRequest=fVHRTsJAEPyVy77THm1t8UIxKBpJFBpaePBtLUe5pNzV2ysxfr21QMQXk3257MzO3Mz47vNQs6O0pIxOYehxYFKXZqt0lcK6eBqM4G4yJjzUQSOmrdvrlfxoJTnWETWJ0yaF1mphkBQJjQdJwpUin76%2BiMDjorHGmdLUwKZE0rpO6sFoag%2FS5tIeVSnXq5cU9s41JHwfG7VDZ4xF7wtdiV5ljh6hiKLQx86B%2F6Pp5%2FkS2KwzojS63vyFX5tK6T%2FUnhH4atv4RAbYk7Gl7D%2BTwg5rksDmsxRweBMnYRhFCd9XybYqRzvO4%2BQd1e2wjDsMZUikjvKXRdTKuSaH2qUQ8OBmwINuimEgeCjCyItv%2BRuw7JzAvdKnZP%2BL6%2F0EIvFcFNkgW%2BYFsM2loQ4A5z5Er26vi%2Fj%2FMF7Sh8njfLFZLu6z1XKWZ8HYvz44OT%2F%2F9j35Bg%3D%3D&SigAlg=http%3A%2F%2Fwww.w3.org%2F2000%2F09%2Fxmldsig%23rsa-sha1&Signature=EoXAG7jS%2BjhzvlIBeJmqcKuKNic%2FqptrT5duOPBJQNPrNpN%2BOExa2qOyn%2BfBAg9%2Fnuor0MsC7EXnVeDjNyh8bd6RVKNIs5DYR7dquyFZxj3tBwPcaUW2Wm422lQlpyDb6uCbx%2FZItMwMoECvz8NOKqHtYQyhsqee93SoQHH9zaBA5EH436i0NEbO7dSZehYdrOJBVfkJQOZ%2FqWbMmttQX5a4t0GFhErCzVlCNPgvVXsQTlt75IH9QLWXJ%2FRoDnfn2Q8vhMz8lAVZ8sYNpTUt1axND8IuXbleQIhVCk9N4aDr3KgdYklLOcTRxWZ9Mw8x2W%2FihcMK58thfd1fxh%2BLpw%3D%3D&login=eivft">
                    <span class="control-label parent-label" style="color:black;  font-size: 16px;  font-weight: bold;

">{{__('home.bering_confirm_number')}}</span>

                   

            </a>
            <a target="_blank" class="side-menu__item" href="https://login.zatca.gov.sa/saml2/idp/sso?SAMLRequest=fVHRTsJAEPyVy77THm1t8UIxKBpJFBpaePBtLUe5pNzV2ysxfr21QMQXk3257MzO3Mz47vNQs6O0pIxOYehxYFKXZqt0lcK6eBqM4G4yJjzUQSOmrdvrlfxoJTnWETWJ0yaF1mphkBQJjQdJwpUin76%2BiMDjorHGmdLUwKZE0rpO6sFoag%2FS5tIeVSnXq5cU9s41JHwfG7VDZ4xF7wtdiV5ljh6hiKLQx86B%2F6Pp5%2FkS2KwzojS63vyFX5tK6T%2FUnhH4atv4RAbYk7Gl7D%2BTwg5rksDmsxRweBMnYRhFCd9XybYqRzvO4%2BQd1e2wjDsMZUikjvKXRdTKuSaH2qUQ8OBmwINuimEgeCjCyItv%2BRuw7JzAvdKnZP%2BL6%2F0EIvFcFNkgW%2BYFsM2loQ4A5z5Er26vi%2Fj%2FMF7Sh8njfLFZLu6z1XKWZ8HYvz44OT%2F%2F9j35Bg%3D%3D&SigAlg=http%3A%2F%2Fwww.w3.org%2F2000%2F09%2Fxmldsig%23rsa-sha1&Signature=EoXAG7jS%2BjhzvlIBeJmqcKuKNic%2FqptrT5duOPBJQNPrNpN%2BOExa2qOyn%2BfBAg9%2Fnuor0MsC7EXnVeDjNyh8bd6RVKNIs5DYR7dquyFZxj3tBwPcaUW2Wm422lQlpyDb6uCbx%2FZItMwMoECvz8NOKqHtYQyhsqee93SoQHH9zaBA5EH436i0NEbO7dSZehYdrOJBVfkJQOZ%2FqWbMmttQX5a4t0GFhErCzVlCNPgvVXsQTlt75IH9QLWXJ%2FRoDnfn2Q8vhMz8lAVZ8sYNpTUt1axND8IuXbleQIhVCk9N4aDr3KgdYklLOcTRxWZ9Mw8x2W%2FihcMK58thfd1fxh%2BLpw%3D%3D&login=eivft">

                   
                    <img id="uploadedimg" src="{{ asset('assets\img\brand\zatca.png') }}" style="width: 250px; height: 40px;">

            </a>
                              <br>
                              <br>


                            
                                   <div class="col-lg-2 ">
                                    <label for="inputName" class="control-label parent-label"> {{ __('home.otp') }}</label>
                                    <input type="number" class="form-control parent-input" id="otp" name="otp" placeholder="--- ---" 
                                    required >
                               
 

                              </div>
                     
 

                       
                         
                                <br>
                      
                            <div class="d-flex justify-content-center">
                                <button style="background-color: #419BB2" type="submit" class="btn btn-primary">
                                    {{ __('supprocesses.save_data') }}
                                    <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                        <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                                    </svg>
                                </button>
                            </div>


                        </form>
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
    <!-- Internal Select2 js-->
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
        function timeout_periodـinـdaysConvert() {
            var input = document.getElementById("timeout_periodـinـdays");
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
        function TaxـNumberConvert() {
            var input = document.getElementById("TaxـNumber");
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
        function credit_limitConvert() {
            var input = document.getElementById("credit_limit");
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
        function phoneConvert() {
            var input = document.getElementById("phone");
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
        var date = $('.fc-datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        }).val();
    </script>

    <script>
        $(document).ready(function() {
            

            $(function() {
var timeout = 4000; // in miliseconds (3*1000)
$('.alert').delay(timeout).fadeOut(500);
});
    
     
            $('select[name="Section"]').on('change', function() {
                var SectionId = $(this).val();
                if (SectionId) {
                    $.ajax({
                        url: "{{ URL::to('section') }}/" + SectionId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('select[name="product"]').empty();
                            $.each(data, function(key, value) {
                                $('select[name="product"]').append('<option value="' +
                                    value + '">' + value + '</option>');
                            });
                        },
                    });

                } else {
                    console.log('AJAX load did not work');
                }
            });

        });
    </script>


    <script>
        function myFunction() {

            var Amount_Commission = parseFloat(document.getElementById("Amount_Commission").value);
            var Discount = parseFloat(document.getElementById("Discount").value);
            var Rate_VAT = parseFloat(document.getElementById("Rate_VAT").value);
            var Value_VAT = parseFloat(document.getElementById("Value_VAT").value);

            var Amount_Commission2 = Amount_Commission - Discount;


            if (typeof Amount_Commission === 'undefined' || !Amount_Commission) {

                alert('يرجي ادخال مبلغ العمولة ');

            } else {
                var intResults = Amount_Commission2 * Rate_VAT / 100;

                var intResults2 = parseFloat(intResults + Amount_Commission2);

                sumq = parseFloat(intResults).toFixed(2);

                sumt = parseFloat(intResults2).toFixed(2);

                document.getElementById("Value_VAT").value = sumq;

                document.getElementById("Total").value = sumt;

            }

        }
    </script>


@endsection
