@extends('layouts.master')
@section('css')
<style>
    /* تنسيقات الطباعة الاحترافية */
    @media print {
        .no-print {
        display: none !important;
    }
    }

    .invoice-wrapper { padding: 20px; color: #000; direction: ltr; }

    /* الهيدر: العربي يمين - اللوجو وسط - الإنجليزي يسار */
    .header-container { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-start; 
        width: 100%; 
        margin-bottom: 10px;
    }
    .header-part { width: 38%; }
    .header-logo { width: 24%; text-align: center; }
    
    .text-ar { text-align: right; direction: rtl; font-size: 14px; line-height: 1.4; }
    .text-en { text-align: left; direction: ltr; font-size: 14px; line-height: 1.4; }

    /* عنوان الوثيقة وبيانات العرض */
    .doc-info { text-align: center; margin-bottom: 15px; }
    .price-list-tag { 
        border: 2px solid #000; 
        display: inline-block; 
        padding: 4px 20px; 
        font-weight: bold; 
        font-size: 16px;
        margin-bottom: 5px;
    }

    /* بيانات السادة / Gentlemen */
    .client-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }

    /* الجدول الرئيسي بمظهر نظيف */
    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th, .items-table td { 
        border: 1px solid #000 !important; 
        padding: 6px; 
        text-align: center; 
        font-size: 13px; 
    }
    .items-table th { background-color: #f8f8f8 !important; font-weight: bold; }

    /* قسم الملاحظات */
    .notes-section { margin-top: 20px; font-size: 12px; }
    .note-line { display: flex; justify-content: space-between; margin-bottom: 6px; align-items: center; }
    .note-en { width: 60%; font-weight: bold; text-align: left; text-decoration: underline; }
    .note-ar { width: 38%; font-weight: bold; text-align: right; direction: rtl; }

    /* النص القانوني الطويل */
    .legal-notice { 
        margin-top: 15px; 
        font-size: 11px; 
        text-decoration: underline; 
        font-weight: bold; 
        line-height: 1.4; 
        text-align: justify;
    }

    /* التواقيع في أسفل الصفحة */
    .signature-area { 
        margin-top: 40px; 
        display: flex; 
        justify-content: space-between; 
        text-align: center; 
    }
    <style>
    /* تحسينات بصرية */
    .dot-indicator {
        min-width: 8px;
        height: 8px;
        background-color: #419BB2;
        border-radius: 50%;
        display: inline-block;
        margin-inline-end: 12px;
    }
    .note-content p {
        margin: 0;
        line-height: 1.6;
        font-size: 14px;
    }
    .badge {
        font-size: 0.9rem;
        padding: 4px 8px;
        font-weight: 600;
    }

</style>
@endsection

@section('content')
     <?php
     $offer_price_to_customer=[];
     if($itemsRequest!=[]){
     
$offer_price_to_customer=App\Models\offer_price_to_customer::find($id);
}

?>
<div class="row row-sm">
    <div class="col-md-12">
        <div class="card card-invoice" id="print">
            <div class="invoice-wrapper">
                
                <button class="btn btn-danger float-left no-print mb-2" id="print_Button" onclick="printDiv()">
                    <i class="mdi mdi-printer ml-1"></i> طباعة العرض
                </button>

        <div class="invoice-header margintop" style="display: flex;justify-content:space-between;width:100%" dir="ltr">


            <div class="billed-from" style="width:33%;text-align: center;">
              <br>
              <span style="font-size:17px">{{Nameen}}</span>
              <br>
              <p dir=ltr > {{describtionen}} </p>
              <p dir=ltr>{{STen}} </p>
              <p dir=ltr> {{Taxen}} </p>

            </div>



            <div class="row">
              <?php
$logo=camplogo;
    ?>
              <a href=""><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1"
                  alt="logo" style="width: 170px; height: 180px;"></a>

            </div>

            <div class="billed-from" style="width:33%;text-align: center;">
              <br>

              <span style="font-size:16px">{{Namear}}</span>
              <br>
              <p> {{describtionar}}</p>
              <p>{{STar}}</p>
              <p>{{Taxar}}</p>

            </div><!-- billed-from -->

          </div><!-- invoice-header -->
 <center> <p  class="double"> عرض تسعيرة للعميل <br> Quote to the customer</p></center>

<div style="direction: rtl; text-align: right; margin-bottom: 20px; border-right: 4px solid #419BB2; padding-right: 15px;">
    <h6 style="font-weight: bold;">السلام عليكم ورحمة الله وبركاته ،،</h6>
    <p style="font-size: 15px; color: #333; margin: 0;">
        السادة الأعزاء، يشرفنا تقديم هذا العرض المالي تلبيةً لاحتياجاتكم بكفاءة وموثوقية، آملين أن ينال رضاكم وأن يكون بداية لتعاون مثمر ينسجم مع معايير الجودة لدينا.
    </p>
</div>

<div style="direction: ltr; text-align: left; margin-bottom: 20px; border-left: 4px solid #419BB2; padding-left: 15px;">
    <h6 style="font-weight: bold;">Peace be upon you,</h6>
    <p style="font-size: 14px; color: #333; margin: 0;">
Dear Sirs,
In appreciation of your interest, we are pleased to present the following price quotation, designed to meet your requirements with efficiency and reliability, and reflecting the level of professionalism we uphold in our services..    </p>
</div>
          <div class='row' style="justify-content: space-around;">
                        <table style="border:2px solid rgba(0,0,0,.3);width:40%" class="table text-md-nowrap mb-0 table-striped invoice-table text-center">
                            <thead>
                                <tr class="row12"  >
                                     <th class="tx-16" >{{$offer_price_to_customer->customer->name}}</th>

<th >CLIENT NAME <br>اسم العميل  </th>



                                </tr>
                                
                                 <tr   >
                                                                         <th class="tx-16">{{$offer_price_to_customer->customer->tax_no}}</th>

                                    <th class="tx-16">TAX NUMBER<br> 
                                    الرقم الضريبي </th>

                                                                    </tr>
                           
                            </thead>
                           
                        </table>
                                          
                                              <table style="border:2px solid rgba(0,0,0,.3);width:40%" class="table text-md-nowrap mb-0 table-striped invoice-table text-center">
                            <thead>
         
  
      
                                   <tr>
                                                                                  <th class="tx-16">{{ $offer_price_to_customer->created_at}}</th>

                                         <th class="tx-16"> Quote DATE<br>تاريخ التسعيرة </th>
                                    </tr>
                                  <tr>
                                                                          <th class="tx-16">{{ $offer_price_to_customer->id}}</th>

                                        <th class="tx-16"> Quote NUMBER<br>رقم التسعيرة</th>
                                </tr>
                            </thead>
                          
                        </table>
                
                                </div>

         



<br>
      <table dir="rtl"
                                            style="border:2px solid rgba(0,0,0,.3);width:100%; border-radius: 5px;"
                                            class="table double">                     <thead>
                        <tr>
                            <th>S/N<br>م</th>
                            <th>Loading<br>التحميل</th>
                            <th>Unloading<br>التنزيل</th>
          

                            <th>Type Truck<br>نوع الشاحنة</th>
                                                       @if($offer_price_to_customer->numbershowstatus)
<th>WEIGHT (TON)<br>الوزن (طن)</th>
@endif
                            <th>Price<br>السعر</th>
                            <th>NOTES<br>الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @php
    use Stichoza\GoogleTranslate\GoogleTranslate;
    $tr = new GoogleTranslate('en'); // تحديد لغة الهدف: الإنجليزية
@endphp



                        @foreach ($itemsRequest as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->loading }}</td>
                            <td>{{ $product->Unloading }}</td>
                            <td>{{ $product->truck_type }}</td>
                                                                 @if($offer_price_to_customer->numbershowstatus)

                            <td>{{ number_format($product->quantity, 2) }}</td>
                                                                 @endif

                            <td>{{ number_format($product->PriceWithoudTax, 2) }}</td>
                            <td>{{ $product->note}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
<br>
<br>
<br>
<div class="notes-section mt-4" style="direction: rtl; text-align: right;">
    {{-- عنوان القسم --}}
    <h6 class="fw-bold mb-3 text-dark d-flex align-items-center" style="font-size: 16px;">
        <i class="fas fa-info-circle ml-2 text-info"></i>
        <span>ملاحظات وشروط النقل</span>
        <span class="mx-2 text-muted" style="font-size: 12px; font-weight: normal;">/ Transport Terms & Notes</span>
    </h6>

    <div class="notes-content">
        
        {{-- فترة السداد --}}
        @if(!empty($offer_price_to_customer->payment_per_day))
        <div class="mb-2 border rounded shadow-sm" style="background-color: #ffffff; border-right: 5px solid #419BB2 !important; overflow: hidden;">
            <div class="p-2">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-check-circle text-info ml-2" style="font-size: 12px;"></i>
                    <strong class="text-dark" style="font-size: 14px;">يتم السداد خلال مدة لا تتجاوز ({{ $offer_price_to_customer->payment_per_day }}) يوماً من تاريخ تسليم الفاتورة.</strong>
                </div>
                <div style="direction: ltr; text-align: left; padding-left: 20px;">
                    <small class="text-muted" style="font-style: italic;">Payment shall be made within ({{ $offer_price_to_customer->payment_per_day }}) days from invoice delivery date.</small>
                </div>
            </div>
        </div>
        @endif
        
        {{-- رسوم الانتظار --}}
        @if(!empty($offer_price_to_customer->note1))
        <div class="mb-2 border rounded shadow-sm" style="background-color: #ffffff; border-right: 5px solid #419BB2 !important;">
            <div class="p-2">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-clock text-info ml-2" style="font-size: 12px;"></i>
                    <strong class="text-dark" style="font-size: 14px;">في حال انتظار الشاحنة لدى العميل لمدة 24 ساعة، تفرض رسوم يومية قدرها ({{ $offer_price_to_customer->waiting }}) ريال.</strong>
                </div>
                <div style="direction: ltr; text-align: left; padding-left: 20px;">
                    <small class="text-muted" style="font-size: 14px;">If the truck waits for 24 hours, a daily fee of ({{ $offer_price_to_customer->waiting }}) SAR shall apply.</small>
                </div>
            </div>
        </div>
        @endif

        {{-- المواقع الإضافية --}}
        @if(!empty($offer_price_to_customer->note2))
        <div class="mb-2 border rounded shadow-sm" style="background-color: #ffffff; border-right: 5px solid #419BB2 !important;">
            <div class="p-2">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-map-marker-alt text-info ml-2" style="font-size: 12px;"></i>
                    <strong class="text-dark" style="font-size: 14px;">يتم احتساب ({{ $offer_price_to_customer->converted }}) ريال عن كل موقع إضافي داخل المنطقة بخلاف الموقع الرئيسي.</strong>
                </div>
                <div style="direction: ltr; text-align: left; padding-left: 20px;">
                    <small class="text-muted" style="font-size: 14px;">({{ $offer_price_to_customer->converted }}) SAR will be charged for each extra location other than the main location.</small>
                </div>
            </div>
        </div>
        @endif

        {{-- الملاحظات الديناميكية من 3 إلى 5 --}}
        @for($i = 3; $i <= 8; $i++)
            @php $field = 'note' . $i; @endphp
            @if(!empty($offer_price_to_customer->$field))
                @php $is_important = ($i > 3); @endphp
                <div class="mb-2 border rounded shadow-sm" style="background-color: #ffffff; border-right: 5px solid {{ $is_important ? '#dc3545' : '#419BB2' }} !important;">
                    <div class="p-2">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-exclamation-circle {{ $is_important ? 'text-danger' : 'text-info' }} ml-2" style="font-size: 12px;"></i>
                            <strong class="{{ $is_important ? 'text-danger' : 'text-dark' }}" style="font-size: 14px;">{{ __('home.note' . $i, [], 'ar') }}</strong>
                        </div>
                        <div style="direction: ltr; text-align: left; padding-left: 20px;">
                            <small class="{{ $is_important ? 'text-danger' : 'text-muted' }}" style="font-size: 14px;">{{ __('home.note' . $i, [], 'en') }}</small>
                        </div>
                    </div>
                </div>
            @endif
        @endfor
        
        {{-- ملاحظات إضافية حرة --}}
        @if(!empty($offer_price_to_customer->notes))
        <div class="mt-3 p-3 border rounded shadow-sm" style="background-color: #fff9e6; border-right: 5px solid #ffc107 !important;">
            <div class="text-dark fw-bold" style="white-space: pre-line; font-size: 16px;">{{ $offer_price_to_customer->notes }}</div>
        </div>
        @endif

    </div>
</div>
                </div>

<div class="signature-section" style="margin-top: 40px; direction: rtl;">
    
    <div class="electronic-approval" style="text-align: center; padding: 10px; border: 1px solid #419BB2; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 30px;">
        <strong style="font-size: 14px; color: #2c3e50; display: block;">تُعد هذه الوثيقة معتمدة إلكترونيًا ولا تتطلب توقيعًا أو ختمًا يدويًا</strong>
        <small style="direction: ltr; display: block; color: #7f8c8d; font-weight: bold; text-transform: uppercase; margin-top: 5px;">
            This document is electronically approved and does not require a handwritten signature or manual stamp
        </small>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        
        {{-- جهة مدير النقليات --}}
        <div class="sig-block" style="text-align: center; flex: 1;">
            <strong style="font-size: 16px; border-bottom: 2px solid #333; padding-bottom: 5px;">مدير النقليات / Transport Manager</strong>
            <div class="sig-space" style="height: 80px; margin-top: 10px;">
                {{-- هنا يمكن وضع صورة التوقيع أو الختم الخاص بالشركة --}}
            </div>
        </div>

        {{-- جهة ختم وتوقيع العميل --}}
        <div class="customer-sig-block" style="text-align: center; flex: 1; margin-right: 50px;">
            <strong style="font-size: 16px; border-bottom: 2px solid #333; padding-bottom: 5px;">ختم وتوقيع العميل / Customer Stamp & Signature</strong>
            <div class="sig-space" style="height: 100px; border: 1px dashed #ccc; margin-top: 15px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <span style="color: #ccc; font-size: 12px;">محل الختم / STAMP HERE</span>
            </div>
        </div>

    </div>
</div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function printDiv() {
        var printContents = document.getElementById('print').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endsection