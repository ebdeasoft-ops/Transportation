@if (@isset($data) && !@empty($data) && count($data) >0 )
@php
$i=1;
@endphp
<div class="table-responsive">
    <table class="table text-md-nowrap  text-center our-table" id="SearchProductTable" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
        <col style="width:2%">
        <col style="width:10%">
        <col style="width:25%">
        <col style="width:10%">
        <col style="width:7%">
        <col style="width:9%">
        <col style="width:20%">

        <thead>
            <tr>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.no_liquidation') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('users.username') }} </th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.status_liquidation') }}</th>

                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.amount_liquition') }}</th>
                                                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.current balance') }}</th>

                <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.operations') }}</th>
            </tr>
        </thead>
        <tbody>
                 <?php $i = 0; 
            $total_before=0;
            $total_after=0;
            $total=0;
            ?>
            @foreach ($data as $product)
            <?php $i++; ?>
    <?php
                    $pay = __('home.Bank_transfer');
                ?>
            <tr >
                <td data-target="id">{{ $product->id }}</td>
                <td dir="ltr" data-target="id">{{ $product->user->name??'' }}</td>
                <td data-target="numberofpice">{{ $product->created_at }}</td>
                <td data-target="numberofpice">{{ $product->branch->name }}</td>
                @if($product->status==1)
                <td data-target="numberofpice" style="color:orange; font-weight: bold;font-size:20px">{{ __('home.data_complete') }}</td>
                @endif
                @if($product->status==2)
                <td data-target="numberofpice" style="color:green; font-weight: bold;font-size:20px">{{ __('home.confirm_done') }}</td>
                @endif

                <td data-target="numberofpice">{{ $product->price_filtering }}</td>
               
                                              <td data-target="numberofpice">{{ $product->currentblance==0?'-': $product->currentblance}}</td>


    <?php $i = 0; 
            $total_before=0;
            $total_after=$product->currentblance==0?'-': $product->currentblance;
            $total+=$product->price_filtering ;
            ?>

               
                <td>
                    <div class="row">
                 <a class="btn btn-danger print-style float-left mt-3 mr-2" href="print_full_purchase/{{ $product->id }}"><i style="fill:#072c3c !important" class=" fas fa-print"></i>&nbsp;&nbsp;{{ __('home.show') }}</a>
               @if($product->status==1)

                <a class="btn btn-danger print-style float-left mt-3 mr-2" href="update_data_purchase/{{ $product->id }}" >{{ __('home.update_data_purchase') }}<i class="las la-pen"></i></a>


              @endif
    @if($product->status==2&&Auth()->user()->branchs_id==9)
                    <a class="btn btn-danger  float-left mt-3 mr-2" data-effect="effect-scale" data-id="{{ $product->id }}"  data-toggle="modal" href="#cancel_confirm_modolla" title="تعديل طريقة الدفع">{{ __('home.cancel_confirm') }}<i class="las la-pen"></i></a>
                @endif

                    </div>
             
                </td>

            </tr>
            @endforeach
            
                                      <tr >
                <td data-target="id">-</td>
                <td dir="ltr" data-target="id">-</td>
                <td data-target="numberofpice">-</td>
                <td data-target="numberofpice" style="color:green; font-weight: bold;font-size:20px">-</td>
                <td data-target="numberofpice">-</td>
                <td data-target="numberofpice">{{ $total}}</td>
                <td data-target="numberofpice">{{$total_after}}</td>
                <td>-</td>
            </tr>
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