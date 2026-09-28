@if (@isset($products) && !@empty($products) && count($products) >0 )
@php
$i=1;
@endphp
<div class="table-responsive">
  <table class="table text-md-nowrap text-center our-table" id="SearchProductTable" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
    
                                        <thead>
                                            <tr>
                                                <th class="border-bottom-0">#</th>
                                                <th class="border-bottom-0">{{ __('report.invoiceNo') }}</th>
                                                <th class="border-bottom-0"> {{ __('home.productNo') }}</th>
                                                <th class="border-bottom-0"> {{ __('home.product') }}</th>
                                                <th class="border-bottom-0">{{ __('report.date') }}</th>
                                                     <th class="border-bottom-0">{{ __('home.operationtype') }}</th>
                                                   <th class="border-bottom-0"> -</th>
                                                   <th class="border-bottom-0"> {{ __('home.quantity') }}</th> 
                                                     <th class="border-bottom-0">{{ __('home.price') }}</th>
                                               
                                             
                                                 <th style="color: #FF4F1F;font-size:11px" class="border-bottom-0">{{ __('home.operations') }}</th>

                                           

                 
    
    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                           ?>
                                            @foreach ($products as $invoice)
                                                <?php $i++;
               

                                                ?>
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>{{ $invoice['id'] }} </td>
                                                    <td dir='ltr'>{{ $invoice['Product_Code'] }} </td>
                                                    <td>{{ $invoice['product_name'] }}</td>
                                                    <td>{{ $invoice['created_at']}}</td>
                                                    
                                                            @if( $invoice['type'] ==1)
                                                    <td style="color:green">{{ $invoice['operation'] }}</td>
@endif            @if( $invoice['type'] ==3)
                                                    <td style="color:yello">{{ $invoice['operation'] }}</td>
@endif
                                                    @if($invoice['type']==2)

                                                    
<td style="color:red">{{ $invoice['operation'] }}</td>
@endif
                                                    <td>{{ $invoice['man'] }}</td>
                                                            <td>{{ $invoice['quantity'] }}</td>
                                                           <td>{{ $invoice['price'] }}</td>
                                            
                                                                                           @if( $invoice['type'] ==1)
                                                                                           
                                                                                           <td style="color:red">  <a  class="dropdown-item"
                                                                        href="purchasesShow/{{  $invoice['id'] }}"><i style="fill:#072c3c !important"
                                                                            class="fas fa-print"></i>&nbsp;&nbsp;
                                                                        {{ __('home.show') }}
                                                                    </a></td>
                                                                    
                         
@endif
                                                    @if($invoice['type']==2)

                                                    
                           <td style="color:green"><a style="color: #23395D" class="dropdown-item" href="showInvoiceRecent/{{  $invoice['id'] }}"><i style="fill:#072c3c !important" class=" fas fa-print"></i>&nbsp;&nbsp;
                        {{ __('home.show') }}
                    </a></td>
@endif
                                                           @if($invoice['type']==3)

                                                    
                           <td style="color:green"> <a style="color: #23395D" class="dropdown-item" href="print_Transfer_products/{{ $invoice['id'] }}"><i style="fill:#072c3c !important" class=" fas fa-print"></i>&nbsp;&nbsp;
                                                {{ __('home.show') }}
                                            </a></td>
@endif
    
                                            
                                                </tr>
                                            @endforeach
    
                                        </tbody>
                                    </table>


        @else
        <div class="alert alert-danger">
            {{__('home.notfounddata')}}
        </div>
        @endif