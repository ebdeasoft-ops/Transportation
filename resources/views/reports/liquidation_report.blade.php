@extends('layouts.master')
@section('css')
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
    
    
    .double {
      border: 3px solid grey;
      border-radius: 5px;
      width: 90%;
      font-size: 15px !important;

    }
    </style>



@endsection
@section('title')
{{__('home.liquidation')}} @stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class=" main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                     <div class="invoice-header" style="display: flex;justify-content:space-between;width:100%">

                        <div class="billed-from" style="width:33%;text-align: center;" >
                            <br>
                             <span style="font-size:25px">{{Nameen}}</span>
                            <br>
                            <p dir=ltr> {{describtionen}} </p>
                            <span dir=ltr>{{STen}} </span>
                            <p dir=ltr> {{Taxen}} </p>

                        </div>
                        <div class="row">
                        <?php
$logo=camplogo;

    ?>
    <a href="https://ebdeasoft.com/"><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 110px; height: 70px;"></a>

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
                    
                                                                                <center >  <p class="double"> {{__('home.liquidation')}} </p></center>

                        <div class="row mg-t-12">
                         
                        
                        
                        </div>
                      

                        <div class="card-body">
                    
                        @foreach($data as $product)
                                        @if($product->type==2)

                                                            <center >  <p class="double"> {{__('home.liquidation_purchase')}} </p></center>
@endif
                                  @if($product->type==1)

                                                            <center >  <p class="double"> {{__('home.liquidation_shipments')}} </p></center>
@endif

                          <table id="example" class="table key-buttons text-md-nowrap table-bordered table-striped text-center">
	                            <thead>
											<tr>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.no_liquidation') }}</th>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('users.username') }} </th>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.date') }}</th>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.branch') }}</th>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.status_liquidation') }}</th>
                                   <th style="color: #FF4F1F;font-size:12px" class="border-bottom-0">{{ __('home.total') }}</th>

											</tr>
										</thead>
                                        <tbody>
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
               
               
                                    </tr>
										
									</table>
@if($product->type==1)
								<div class="table-responsive">


                                  
<center>
    <h3>  {{ __('home.describtion_shipment') }}</h3>
</center>


                              <table id="example_shipment" class="table text-md-nowrap text-center our-table" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
                            <thead>
                                <tr>

                                    <th class="border-bottom-0"> {{ __('home.no_liquidation') }}</th>
                                    <th class="border-bottom-0"> {{ __('home.loading') }}</th>
                                    <th class="border-bottom-0">{{ __('home.Unloading') }}</th>
                                    <th class="border-bottom-0">{{ __('home.truck_no') }}</th>
                                    <th class="border-bottom-0">{{ __('home.invoice_no') }}</th>
                                    <th class="border-bottom-0">{{ __('home.polica_number') }}</th>
                                    <th class="border-bottom-0">{{ __('home.PRICE_SHIPMENT') }}</th>
                                    <th class="border-bottom-0">{{ __('home.delay') }}</th>
                                    <th class="border-bottom-0">{{ __('home.ext') }}</th>
                                    <th class="border-bottom-0">{{ __('home.total') }}</th>
                                    <th class="border-bottom-0">{{ __('home.notesClient') }}</th>
                                    <th class="border-bottom-0">{{__(key: 'home.attachments')}} </th>


                                </tr>
                            </thead>
                            <tbody>

                            <?php
                                $total=0;

                            $shipments_details=App\Models\shipments_details::where('Transactions_id',$product->id)->get()
                            ?>
                                @if($shipments_details!=NULL)
                               @foreach ($shipments_details as $item)
                               <?php  
$total+=$item->Ext+$item->Daily+$item->price_shipment;

?>
                               <tr>

                                    <td>{{ $item->Transactions_id }}</td>
                                    <td>{{ $item->loading }}</td>
                                    <td>{{ $item->unloading }}</td>
                                    <td>{{ $item->truck_data }}</td>
                                    <td>{{ $item->invoice_number }}</td>
                                    <td>{{ $item->polica_number }}</td>
                                    <td>{{ $item->price_shipment }}</td>
                                    <td>{{ $item->Daily}}</td>
                                    <td>{{ $item->Ext }}</td>
                                    <td>{{ $item->Ext+$item->Daily+$item->price_shipment  }}</td>
                                    <td>{{ $item->note_detaials }}</td>
      <td>                            @if($item['attachments_2']!=null)<a  target="_blank"
href="{{ url('/' . ($page = 'openfile') .'/'.$item['attachments_2']) }}"
                                    >{{  __('home.show')}}</a>
                                    @endif
                                </td>                                  

                               </tr>
                               @endforeach 
   <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>{{ $total }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>

                                @else



                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
@endif
                            </tbody>
                        </table>



                                    <br>
                               
                                    </div>
                                    @endif
@endforeach
                        <hr class="mg-b-40">


                        <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button" onclick="printDiv()"> <i
                                class="mdi mdi-printer ml-1"></i>{{__('home.print')}}</button>
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
                    style="background-color: rgba(0, 0, 0, 0)!important;color: rgba(0, 0, 0, 0)!important;" id="loading"
                    name="loading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" dir='rtl' aria-hidden="true">
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


<div class="modal fade" id="increaseProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div style="margin: 5% !important;" class="modal-dialog modal-special" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('home.confirm_data_shipment') }}</h5>
                <button type="button" class="close choose-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
          
                   <form enctype="multipart/form-data" method="POST" role="search" name="form-name" id='formdata_update' autocomplete="off">
                        {{ csrf_field() }}
                                                 <input hidden type="text" class="form-control parent-input" name="id_update" id="id_update"  >
<div style="width:99%">
                <div class="row">

<div style="width:1%">
</div>
                    <div class="col mg-t-20 mg-lg-t-0">

                        <label class="control-label parent-label">{{ __('home.loading') }}</label>
                        <input type="text" class="form-control parent-input" name="loading_update" id="loading_update" readonly required >
                    </div>
                    <div class="col mg-t-20 mg-lg-t-0">
                        <label class="control-label parent-label">{{ __('home.Unloading') }}</label>
                        <input type="text" class="form-control parent-input" name="Unloading_update" id="Unloading_update" readonly required >
                    </div>
                    <div class="col mg-t-20 mg-lg-t-0">

                        <label class="control-label parent-label">{{ __('home.truck_no') }}</label>
                        <input type="text" class="form-control parent-input" name="truck_no_update" id="truck_no_update" readonly  required >
                    </div>
                    <div class="col mg-t-20 mg-lg-t-0">
                        <label class="control-label parent-label">{{ __('home.invoice_no') }}</label>
                        <input type="text" class="form-control parent-input" name="invoice_no_update" id="invoice_no_update" readonly required >
                    </div>
   <div class="col mg-t-20 mg-lg-t-0">
                        <label class="control-label parent-label">{{ __('home.polica_number') }}</label>
                        <input type="text" class="form-control parent-input" name="polica_number_update" id="polica_number_update" readonly required >
                    </div>
            </div>
                     <div class="row">
<div style="width:1%">
</div>
  <div class="col">

                                    <label class="control-label parent-label">{{ __('home.PRICE_SHIPMENT') }}</label>
                                    <input type="number" class="form-control parent-input" name="PRICE_SHIPMENT_update"
                                        id="PRICE_SHIPMENT_update" readonly  required>
                                </div>
                    <div class="col mg-t-20 mg-lg-t-0">

                        <label class="control-label parent-label">{{ __('home.delay') }}</label>
                        <input type="text" class="form-control parent-input" name="delay_update" id="delay_update" readonly  required >
                    </div>
                    <div class="col mg-t-20 mg-lg-t-0">
                        <label class="control-label parent-label">{{ __('home.ext') }}</label>
                        <input type="text" class="form-control parent-input" name="ext_update" id="ext_update" readonly required  >
                    </div>
                    <div class="col mg-t-20 mg-lg-t-0">

                        <label class="control-label parent-label">{{ __('home.notesClient') }}</label>
                        <input type="text" class="form-control parent-input" name="note_update" id="note_update" readonly required  >
                    </div>
                 
              
  
            </div>
            </div>
         
      

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('home.cancel')}}</button>
            <button type="button" id="confirmshipment"  class="btn btn-danger"   data-dismiss="modal">{{ __('home.confirm') }}</button>
        </div>

        </form>
    </div>
</div>
</div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>


    <script type="text/javascript">
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
        var loading = button.data('loading')
        var unloading = button.data('unloading')
        var truck_data = button.data('truck_data')
        var invoice_number = button.data('invoice_number')
        var price_shipment = button.data('priceshipment')
        var polica_number = button.data('polica_number')
        var Daily = button.data('daily')
        var Ext = button.data('ext')
        var note_detaials = button.data('note_detaials')
        var modal = $(this)

        var modal = $(this)


        modal.find('#id_update').val(id);
        modal.find('#loading_update').val(loading);
        modal.find('#Unloading_update').val(unloading);
        modal.find('#truck_no_update').val(truck_data);
        modal.find('#invoice_no_update').val(invoice_number);
        modal.find('#PRICE_SHIPMENT_update').val(price_shipment);
        modal.find('#delay_update').val(Daily);
        modal.find('#polica_number_update').val(polica_number);
        modal.find('#ext_update').val(Ext);
        modal.find('#note_update').val(note_detaials);

    })




        $("#confirmshipment").click(function(e) {
                    $('#loading').modal().show();

                e.preventDefault();
                var token_search = $("#token_search").val();
                {
                            $('#loading_modale').modal().show();

                    $.ajax({
                       url: "{{ URL::to('/confirm_data_shipment') }}/" + $("#id_update").val() ,
                       type: "GET",
                       dataType: "json",


                        success: function (data) {
                     
                                $('#transactions_id').val(data['id'])
                                $('#Transactions_price_id_print').val(data['id'])


                            let table = document.getElementById("example_shipment");
                            var tableHeaderRowCount = 1;

                            var rowCount = table.rows.length;

                            for (var i = tableHeaderRowCount; i < rowCount; i++) {
                                table.deleteRow(tableHeaderRowCount);
                            }

                           i=0;

                            data['shipments_details'].forEach(async (product) => {

                            i=i+(product['Ext']*1)+( product['Daily'] *1)+(  product['price_shipment'] *1);

                                let row = table.insertRow(-1); // We are adding at the end
                                update = ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-warning mb-1" data-effect="effect-scale" data-id='
                                update = update.concat(product['id'], '  ', ' data-loading=', product['loading'], '  ',' data-unloading=', product['unloading'], '  ',' data-truck_data=', product['truck_data'], '  ',
                                ' data-invoice_number=', product['invoice_number'], '  ',' data-price_shipment=', product['price_shipment'], '  ', ' data-polica_number=', product['polica_number'],'  ',' data-Daily=', product['Daily'], '  ',
                                ' data-Ext=', product['Ext'], '  ',' data-note_detaials=', product['note_detaials'], '  ',
                                    '  data-toggle="modal"   href="#increaseProduct"   title="تعديل"><i class="las la-align-justify"></i></a>'
                                )
 update1= ' <a style="width:40px;height:20px" class="modal-effect btn btn-sm btn-success mb-1" data-effect="effect-scale" data-id='
                                update1 = update1.concat(product['id'], '  ', ' data-loading=', product['loading'], '  ',' data-unloading=', product['unloading'], '  ',' data-truck_data=', product['truck_data'], '  ',
                                ' data-invoice_number=', product['invoice_number'], '  ',' data-price_shipment=', product['price_shipment'], '  ', ' data-polica_number=', product['polica_number'],'  ',' data-Daily=', product['Daily'], '  ',
                                ' data-Ext=', product['Ext'], '  ',' data-note_detaials=', product['note_detaials'], '  ',
                                    '  data-toggle="modal"   href=""   title="تعديل"><i class="las la-align-justify"></i></a>'
                                )
                                  link_attachment=" {{ URL::to('openfile') }}" + "/" + product['attachments_2'];


                                        attachment_1='<a  target="_blank"'+' '+' href= '+'"'+link_attachment+'"'+'>{{  __('home.show')}}</a>';
 


                                   let c1 = row.insertCell(0);
                                let c2 = row.insertCell(1);
                                let c3 = row.insertCell(2);
                                let c4 = row.insertCell(3);
                                let c5 = row.insertCell(4);
                                let c6 = row.insertCell(5);
                                let c7 = row.insertCell(6);
                                let c8= row.insertCell(7);
                                let c9 = row.insertCell(8);
                                let c10 = row.insertCell(9);
                                let c11= row.insertCell(10);
                                let c12= row.insertCell(11);
                                let c13= row.insertCell(11);
 
                                c1.innerText = data['id']
                                c2.innerText = product['loading']
                                c3.innerHTML = product['unloading']
                                c4.innerText = product['truck_data']
                                c5.innerText = product['invoice_number']
                                c6.innerText = product['polica_number']
                                c7.innerText = product['price_shipment']
                                c8.innerText = product['Daily']
                                c9.innerText = product['Ext']
                                c10.innerText =(product['Ext']*1)+( product['Daily'] *1)+(  product['price_shipment'] *1)
                                c11.innerText = product['note_detaials']
                                c12.innerHTML= attachment_1
                                if(product['status']==0){
                                c13.innerHTML= update

                                }else{
                                c13.innerHTML= update1

                                }
                        
                            })
                                                            let row = table.insertRow(-1); // We are adding at the end

                             let c1 = row.insertCell(0);
                                let c2 = row.insertCell(1);
                                let c3 = row.insertCell(2);
                                let c4 = row.insertCell(3);
                                let c5 = row.insertCell(4);
                                let c6 = row.insertCell(5);
                                let c7 = row.insertCell(6);
                                let c8= row.insertCell(7);
                                let c9 = row.insertCell(8);
                                let c10 = row.insertCell(9);
                                let c11= row.insertCell(10);
                                let c12= row.insertCell(11);
                                let c13= row.insertCell(12);
 
                                c1.innerText = '-'
                                c2.innerText = '-'
                                c3.innerHTML = '-'
                                c4.innerText = '-'
                                c5.innerText ='-'
                                c6.innerText = '-'
                                c7.innerText = '-'
                                c8.innerText = '-'
                                c9.innerText ='-'
                                c10.innerText = i
                                c11.innerText= '-'
                                c12.innerText= '-'
                                c13.innerText= '-'
                        
                            setTimeout(() => {
                                $('#loading_modale').modal('hide');

                            }, 500);
                        },
                        error: function (response) {
                            console.log(response['responseText'])
                            alert("{{ __('home.sorryerror') }}")

                        }
                    })




                }
   setTimeout(() => {
                                    $('#loading').modal('hide');

                                }, 500);
        });
        

    





       




    </script>

@endsection
