@if (@isset($data) && !@empty($data) && count($data) >0 )
@php
$i=1;
@endphp
<div class="table-responsive">
    <table class="table text-md-nowrap  text-center our-table" id="SearchProductTable" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
        <col style="width:2%">
        <col style="width:10%">
        <col style="width:31%">
        <col style="width:10%">
        <col style="width:7%">
        <col style="width:9%">
        <col style="width:20%">

        <thead>
            <tr>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.sallerName') }} </th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.clietName') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.paymentmethod') }}</th>

                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.total') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.operations') }}</th>


            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>

            @foreach ($data as $product)
            <?php $i++; ?>

            <tr id="<?php echo $product['id']; ?>">
                <td data-target="id">{{ $product->id }}</td>
                <td data-target="id">{{ $product->user->name??'' }}</td>
                <td dir="ltr" data-target="id">
                    {{ $product->customer->name??'' }}
                </td>
                <td data-target="numberofpice">{{ $product->created_at }}</td>
                <td data-target="numberofpice">{{ $product->branch->name }}
                </td>
                  <?php
                $pay = '';
                if ($product->Pay == 'Cash') {
                    $pay = __('report.cash');
                } elseif ($product->Pay == 'Shabka') {
                    $pay = __('report.shabka');
                } elseif ($product->Pay == "Credit") {
                    $pay = __('report.credit');
                } elseif ($product->Pay == "Bank_transfer") {
                    $pay = __('home.Bank_transfer');
                } else {
                    $pay = __('home.Partition of the amount');
                }

                ?>
                <td>{{$pay}}

                 
                </td>
                <td data-target="numberofpice">
                  
                    {{ round(($product->Price-$product->discount) ,2)  }}
                </td>

               
        
                
                <td>
                     <a style="color: #23395D" class="dropdown-item" href="showInvoiceRecentdelivery/{{ $product->id }}"><i style="fill:#072c3c !important" class=" fas fa-print"></i>&nbsp;&nbsp;
                        {{ __('home.show') }}
                    </a>
                    <form action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/' . ($page = 'return_sale_delivery')) }}" method="POST" role="search" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="col-lg-5 mg-t-20 mg-lg-t-0">


                            <input hidden  type="text" class="form-control parent-input" id="invoice_no" name="invoice_no" title="  يرجي ادخال رقم الفاتورة  " value="{{ $product->id }}" >

                        </div>





                        <div>

                            <br>

                            <div class="d-flex justify-content-center">
                                <button style="background-color: #419BB2" type="submit" class="btn btn-success p-1">
                        <i style=" height: 100;
                                                 
                                                 font-size:15px" class="las la-search"></i>
                                                                         {{ __('home.delivery_return') }}

                                </button>
                            </div>
                        </div>

                        <br>

                    </form>
 

                </td>

            </tr>
            @endforeach
    </table>
    <div>
        <br>
        <div class="justify-content-start" id="ajax_pagination_in_search">
            {{ $data->links() }}
        </div>



        @else
        <div class="alert alert-danger">
            {{__('home.notfounddata')}}
        </div>
        @endif