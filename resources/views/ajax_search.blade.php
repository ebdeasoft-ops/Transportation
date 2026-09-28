
          @if (@isset($data) && !@empty($data) && count($data) >0 )
          @php
           $i=1;   
          @endphp
          <table class="table text-md-nowrap text-center  our-table"
                                             id="example2" width="100%"
                                             style="border: 2px solid rgba(0,0,0,.3);"
                                             >
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
                                                                                                                <th style="font-size: 15px" class="border-bottom-0" style="text-align:center">{{__('home.groups')}}</th>

                                                        <th style="font-size: 15px" class="border-bottom-0">{{__('home.productlocation')}}</th>
                                                        <th style="font-size: 15px" class="border-bottom-0">{{__('home.quantity')}}</th>
                                                       
      @can('System setting')
 <th style="font-size: 13px" class="border-bottom-0">{{__('home.purchaseproductwithouttax')}}</th>
                                                                        @endcan

                                                        <th style="font-size: 13px" class="border-bottom-0">{{__('home.sellingproduct without tax')}}</th>
                                                          <th style="font-size: 15px" class="border-bottom-0">{{__('home.refnumber')}}</th>
                                                        <th style="font-size: 13px" class="border-bottom-0">{{__('home.notesClient')}}</th>
            
            
            
                                                    </tr>
                                                </thead>
                                                <tbody class="">
                                                    <?php $i = 0;
                                                    ?>
            
                                                    @foreach ($data as $product)
                                                    <?php $i++ ?>
            
                                                    <tr id="<?php echo $product['id']; ?>">
                                                        <td id="tableData" style=""  dir=ltr>{{ $product->id }}</td>
                                                        <td id="tableData" style=""  dir=ltr>{{ $product->Product_Code }}</td>
                                                        <td id="tableData" style=""  data-target="product_name">{{ $product->product_name }}</td>

                                                        <td id="tableData" style=""  data-target="product_name">{{ $product->branch->name }}</td>
                                                        
                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->product_group_data->group_ar }}</td>
                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->Product_Location }}</td>
                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->numberofpice }}</td>
                                                                                                                     @can('System setting')

                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->purchasingـprice }}</td>

                                                                        @endcan

                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->sale_price }}</td>
                                                             <td id="tableData"  data-target="numberofpice">{{ $product->refnumber==null?__('home.notdata'):str_replace("+"," - ",$product->refnumber) }}</td>
                                                        <td id="tableData" style=""  data-target="numberofpice">{{ $product->notes }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
      <br>
  <div class="justify-content-start" id="ajax_pagination_in_search">
            {{ $data->links() }}
        </div>

         
       
           @else
           <div class="alert alert-danger">
           {{__('home.notfounddata')}}             </div>
                 @endif
