@extends('layouts.master')

@section('css')
<style>
/* ================= فاتورة - تصميم احترافي ================= */
.inv{ --ink:#0F172A; --ink2:#475569; --line:#CBD5E1; --soft:#F1F5F9; --brand:#1E3A8A; --brand2:#2F6FED; --acc:#C9A227;
      background:#fff; color:var(--ink); max-width:210mm; margin:0 auto; padding:18px 22px; font-size:13px; }
.inv *{ box-sizing:border-box; }
.inv .t-ltr{ direction:ltr; unicode-bidi:embed; }

/* الهيدر (جدول ثابت عشان يفضل مظبوط في الشاشة والطباعة وأي ثيم) */
.inv-head{ width:100%; border-collapse:collapse; table-layout:fixed; margin:0; }
.inv-head td{ border:0 !important; padding:0 0 12px !important; vertical-align:middle !important; background:transparent !important; }
.inv-head-line{ height:3px; background:var(--brand); margin:0; }
.inv-head-line2{ height:2px; background:var(--acc); margin:3px 0 0; }
.inv-co{ line-height:1.6; }
.inv-co b{ display:block; font-size:17px; font-weight:800; color:var(--brand); margin-bottom:3px; line-height:1.3; }
.inv-co span{ display:block; font-size:12.5px; color:var(--ink2); font-weight:700; }
.inv-co.ar{ text-align:right !important; direction:rtl; }
.inv-co.en{ text-align:left !important; direction:ltr; }
.inv-logo{ text-align:center !important; }
.inv-logo img{ width:105px; height:105px; object-fit:contain; display:inline-block; }
/* عنوان الفاتورة */
.inv-title{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin:18px 0 12px; }
.inv-title h1{ margin:0; font-size:19px; font-weight:800; color:#fff; background:var(--brand); padding:8px 18px; border-radius:8px; letter-spacing:.2px; }
.inv-title h1 small{ font-size:13px; font-weight:700; opacity:.85; margin:0 10px; }
.inv-no{ text-align:center; border:2px solid var(--brand); border-radius:8px; padding:4px 14px; }
.inv-no span{ display:block; font-size:10.5px; color:var(--ink2); font-weight:700; }
.inv-no b{ font-size:17px; color:var(--brand); }

/* جداول البيانات */
.inv-grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.inv-box{ border:1.5px solid var(--line); border-radius:8px; overflow:hidden; }
.inv-box h3{ margin:0; background:var(--soft); color:var(--brand); font-size:12.5px; font-weight:800; padding:6px 10px; border-bottom:1.5px solid var(--line); display:flex; justify-content:space-between; }
.inv-box h3 small{ color:var(--ink2); font-weight:700; }
.inv-kv{ width:100%; border-collapse:collapse; table-layout:fixed; }
.inv-kv th, .inv-kv td{ padding:5px 9px; border-bottom:1px solid #E2E8F0; vertical-align:middle; font-size:12.5px; word-break:break-word; }
.inv-kv tr:last-child th, .inv-kv tr:last-child td{ border-bottom:0; }
.inv-kv th{ width:42%; color:var(--ink2); font-weight:700; background:#FAFBFD; line-height:1.25; }
.inv-kv th small{ display:block; font-size:9.5px; color:#94A3B8; font-weight:700; letter-spacing:.3px; }
.inv-kv td{ font-weight:700; }

/* جدول الأصناف */
.inv-items{ width:100%; border-collapse:collapse; margin-bottom:12px; border:1.5px solid var(--brand); }
.inv-items thead th{ background:var(--brand); color:#fff; font-size:11.5px; font-weight:800; padding:7px 5px; text-align:center; line-height:1.3; border-inline-end:1px solid rgba(255,255,255,.18); }
.inv-items thead th small{ display:block; font-size:9.5px; font-weight:600; opacity:.85; }
.inv-items tbody td{ padding:6px 5px; text-align:center; font-size:12.5px; border-bottom:1px solid #E2E8F0; border-inline-end:1px solid #EEF2F7; }
.inv-items tbody tr:nth-child(even) td{ background:#F8FAFC; }
.inv-items td.name{ text-align:right; white-space:pre-wrap; word-break:break-word; font-weight:700; }
.inv-items td.num{ direction:ltr; font-variant-numeric:tabular-nums; }
.inv-items td.net{ font-weight:800; color:var(--brand); }

/* الإجماليات + QR */
.inv-bottom{ display:grid; grid-template-columns:1fr 1.15fr; gap:14px; align-items:start; }
.inv-qr{ display:flex; gap:12px; align-items:flex-start; }
.inv-qr .qr{ border:1.5px solid var(--line); border-radius:8px; padding:6px; background:#fff; line-height:0; }
.inv-bank{ flex:1; border:1.5px dashed var(--line); border-radius:8px; padding:8px 10px; font-size:11.5px; line-height:1.7; }
.inv-bank b{ color:var(--brand); display:block; }
.inv-tot{ width:100%; border-collapse:collapse; border:1.5px solid var(--line); border-radius:8px; overflow:hidden; }
.inv-tot th, .inv-tot td{ padding:6px 10px; font-size:12.5px; border-bottom:1px solid #E2E8F0; }
.inv-tot th{ text-align:right; color:var(--ink2); font-weight:700; background:#FAFBFD; }
.inv-tot th small{ color:#94A3B8; font-weight:700; font-size:10px; margin-inline-start:4px; }
.inv-tot td{ text-align:left; direction:ltr; font-weight:800; font-variant-numeric:tabular-nums; width:40%; }
.inv-tot tr.grand th, .inv-tot tr.grand td{ background:var(--brand); color:#fff; font-size:14.5px; border-bottom:0; }
.inv-tot tr.grand th small{ color:#C7D2FE; }
.inv-words{ margin-top:6px; font-size:11.5px; font-weight:700; color:#B91C1C; background:#FEF2F2; border-radius:6px; padding:5px 10px; text-align:center; }

.inv-note{ margin-top:12px; border-inline-start:4px solid var(--acc); background:#FFFBEB; padding:8px 12px; border-radius:6px; font-size:12.5px; }
.inv-sign{ display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-top:26px; text-align:center; font-size:12px; font-weight:700; color:var(--ink2); }
.inv-sign div{ border-top:1.5px solid var(--line); padding-top:6px; }
.inv-foot{ margin-top:16px; padding-top:8px; border-top:2px solid var(--brand); text-align:center; font-size:11px; color:var(--ink2); font-weight:600; }
.inv-foot span{ margin:0 6px; }

.inv-actions{ text-align:center; margin:4px 0 14px; }
.inv-actions .btn{ min-width:150px; font-weight:800; }

@media screen and (max-width:767px){
    .inv{ padding:10px; }
    .inv-head, .inv-head tbody, .inv-head tr, .inv-head td{ display:block; width:100% !important; }
    .inv-co.ar, .inv-co.en{ text-align:center !important; }
    .inv-grid, .inv-bottom{ grid-template-columns:1fr; }
    .inv-items-wrap{ overflow-x:auto; }
}

/* ================= الطباعة ================= */
@media print{
    @page{ size:A4; margin:8mm; }
    body{ background:#fff !important; }
    *{ -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
    .main-header, .main-sidebar, .app-sidebar, .main-footer, .breadcrumb-header, .inv-actions, #print_Button,
    .ahl-notif, .back-to-top, #back-to-top{ display:none !important; }
    .main-content, .main-content .container-fluid, .card, .card-body{ margin:0 !important; padding:0 !important; border:0 !important; box-shadow:none !important; background:#fff !important; }
    .main-content.app-content{ margin-right:0 !important; margin-left:0 !important; }
    .inv{ max-width:none; padding:0; font-size:11.5px; }
    .inv-items tr, .inv-bottom, .inv-sign, .inv-box{ page-break-inside:avoid; break-inside:avoid; }
    .inv-items thead{ display:table-header-group; }
}
</style>
@endsection

@section('title')
معاينة طباعة الفاتورة
@stop

@section('page-header')
<div class="breadcrumb-header justify-content-between"></div>
@endsection

@section('content')
@php
    $inv  = $data['invoiceData'];
    $cust = $inv->customer;
    $val = function ($v) { return ($v === null || $v === '' || $v === 0 || $v === '0') ? '-' : $v; };
    $isTaxInvoice = strlen((string) optional($cust)->tax_no) == 15;

    switch ($inv->Pay) {
        case 'Cash':          $pay = __('report.cash'); break;
        case 'Shabka':        $pay = __('report.shabka'); break;
        case 'Credit':        $pay = __('report.credit'); break;
        case 'Bank_transfer': $pay = __('home.Bank_transfer'); break;
        default:              $pay = __('home.Partition of the amount');
    }

    // ===== الأصناف والإجماليات (نفس طريقة الحساب القديمة) =====
    $avt = App\Models\Avt::find(1);
    $items = App\Models\sales::where('invoice_id', $inv->id)->where('quantity', '!=', 0)->get();
    $invoicetotal_addedvalue = 0; $total_withoud_tax_row = 0; $discountreturn = 0;
    $rows = [];
    foreach ($items as $product) {
        $total_row_befor_tax = round(($product->Unit_Price * $product->quantity) - $product->Discount_Value, 2);
        $added_value_row = round($total_row_befor_tax * $product->tax_rate, 2);
        $invoicetotal_addedvalue += $added_value_row;
        $total_withoud_tax_row += $total_row_befor_tax;
        $rows[] = [$product, $total_row_befor_tax, $added_value_row];
    }
    $invoicetotal_discount = $inv->discount + $discountreturn;

    // ===== QR (هيئة الزكاة - TLV) =====
    $tlv = function ($tag, $value) { $value = (string) $value; return pack('H*', sprintf('%02X', $tag)) . pack('H*', sprintf('%02X', strlen($value))) . $value; };
    $qrTime  = substr($inv->created_at, 0, 10) . 'T' . substr($inv->created_at, 11);
    $qrTotal = number_format(round($invoicetotal_addedvalue + $total_withoud_tax_row, 2), 2, '.', '');
    $qrTax   = number_format(round($invoicetotal_addedvalue, 2), 2, '.', '');
    $dataforQRcode = base64_encode($tlv(1, sallerQrCode) . $tlv(2, TaxQrCode) . $tlv(3, $qrTime) . $tlv(4, $qrTotal) . $tlv(5, $qrTax)
        . $tlv(6, '') . $tlv(7, '') . $tlv(8, '') . $tlv(9, ''));

    $money = function ($v) { return number_format(round((float) $v, 2), 2, '.', ','); };
    $logo = camplogo;
@endphp

<div class="inv-actions">
    <button class="btn btn-danger" id="print_Button" onclick="window.print()"><i class="mdi mdi-printer ml-1"></i> {{ __('home.print') }}</button>
</div>

<div class="card"><div class="card-body p-0">
<div class="inv" id="print" dir="rtl">
    <input type="number" name="show_invoice_number" id="show_invoice_number" value="{{ $inv->id }}" hidden>

    {{-- الهيدر --}}
    <table class="inv-head">
        <tr>
            <td class="inv-co ar" style="width:38%">
                <b>{{ Namear }}</b>
                <span>{{ describtionar }}</span>
                <span>{{ STar }}</span>
                <span>{{ Taxar }}</span>
            </td>
            <td class="inv-logo" style="width:24%">
                <img src="{{ asset('assets/img/brand/' . $logo) }}" alt="">
            </td>
            <td class="inv-co en" style="width:38%">
                <b>{{ Nameen }}</b>
                <span>{{ describtionen }}</span>
                <span>{{ STen }}</span>
                <span>{{ Taxen }}</span>
            </td>
        </tr>
    </table>
    <div class="inv-head-line"></div><div class="inv-head-line2"></div>

    {{-- عنوان الفاتورة --}}
    <div class="inv-title">
        <h1>{{ $isTaxInvoice ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة' }}<small class="t-ltr">{{ $isTaxInvoice ? 'Tax Invoice' : 'Simplified Tax Invoice' }}</small></h1>
        <div class="inv-no"><span>رقم الفاتورة · INVOICE NO</span><b class="t-ltr">#{{ $inv->id }}</b></div>
    </div>

    {{-- بيانات الفاتورة + العميل --}}
    <div class="inv-grid">
        <div class="inv-box">
            <h3>بيانات الفاتورة <small>INVOICE DETAILS</small></h3>
            <table class="inv-kv">
                <tr><th>تاريخ الفاتورة<small>INVOICE DATE</small></th><td class="t-ltr" style="text-align:right">{{ $inv->created_at }}</td></tr>
                <tr><th>طريقة الدفع<small>PAYMENT METHOD</small></th><td>{{ $pay }}</td></tr>
                <tr><th>الفرع<small>BRANCH</small></th><td>{{ optional($inv->branch)->name ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="inv-box">
            <h3>بيانات العميل <small>CUSTOMER DETAILS</small></h3>
            <table class="inv-kv">
                <tr><th>اسم العميل<small>CUSTOMER NAME</small></th><td>{{ optional($cust)->name ?? '-' }}</td></tr>
                <tr><th>رقم الجوال<small>PHONE</small></th><td class="t-ltr" style="text-align:right">{{ $val(optional($cust)->phone) }}</td></tr>
            </table>
        </div>
    </div>

    {{-- الأصناف --}}
    <div class="inv-items-wrap">
    <table class="inv-items">
        <thead>
            <tr>
                <th style="width:4%">#<small>NO</small></th>
                @if ($inv->display_number)<th style="width:10%">رقم المنتج<small>ITEM NO</small></th>@endif
                <th>اسم الصنف<small>ITEM NAME</small></th>
                <th style="width:9%">السعر<small>PRICE</small></th>
                <th style="width:7%">الكمية<small>QTY</small></th>
                <th style="width:10%">الإجمالي<small>TOTAL</small></th>
                <th style="width:8%">الخصم<small>DISCOUNT</small></th>
                <th style="width:8%">نسبة الضريبة<small>VAT %</small></th>
                <th style="width:9%">الضريبة<small>VAT</small></th>
                <th style="width:11%">الصافي<small>NET</small></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $r)
                @php [$product, $beforeTax, $vatRow] = $r; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    @if ($inv->display_number)<td class="num">{{ optional($product->productData)->Product_Code }}</td>@endif
                    <td class="name">{{ $product->product_name ?? optional($product->productData)->product_name }}</td>
                    <td class="num">{{ $money($product->Unit_Price) }}</td>
                    <td class="num">{{ $product->quantity }}</td>
                    <td class="num">{{ $money($product->Unit_Price * $product->quantity) }}</td>
                    <td class="num">{{ $money($product->Discount_Value) }}</td>
                    <td class="num">{{ number_format($product->tax_rate * 100, 2) }}%</td>
                    <td class="num">{{ $money($vatRow) }}</td>
                    <td class="num net">{{ $money($vatRow + $beforeTax) }}</td>
                </tr>
            @empty
                <tr><td colspan="10" style="color:#94A3B8">لا توجد أصناف</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- QR + البنك | الإجماليات --}}
    <div class="inv-bottom">
        <div class="inv-qr">
            <div class="qr">{!! QrCode::size(110)->generate($dataforQRcode) !!}</div>
            @if (Auth()->user()->branchs_id == 1)
                <div class="inv-bank">
                    <b>{{ bankname }}</b>
                    <div>رقم الحساب · Acc: <span class="t-ltr">{{ bank_acount_number }}</span></div>
                    <div>IBAN: <span class="t-ltr">{{ bank_acount_iban }}</span></div>
                </div>
            @endif
        </div>
        <div>
            <table class="inv-tot">
                <tr><th>الإجمالي<small>SUB TOTAL</small></th><td>{{ $money($total_withoud_tax_row) }}</td></tr>
                <tr><th>الخصم<small>DISCOUNT</small></th><td>{{ $money($invoicetotal_discount) }}</td></tr>
                <tr><th>الإجمالي بعد الخصم<small>AFTER DISCOUNT</small></th><td>{{ $money($total_withoud_tax_row) }}</td></tr>
                <tr><th>ضريبة القيمة المضافة<small>VAT</small></th><td>{{ $money($invoicetotal_addedvalue) }}</td></tr>
                <tr class="grand"><th>الإجمالي الكلي<small>NET TOTAL</small></th><td>{{ $money($total_withoud_tax_row + $invoicetotal_addedvalue) }} <small style="font-size:11px">SAR</small></td></tr>
            </table>
            @if (!empty($data['totatextlriyales']) || !empty($data['totatextlrihalala']))
                <div class="inv-words">{{ $data['totatextlriyales'] }} {{ $data['totatextlrihalala'] }}</div>
            @endif
        </div>
    </div>

    @if (!empty($inv->note))
        <div class="inv-note"><b>{{ __('home.notesClient') }}:</b> {!! $inv->note !!}</div>
    @endif

    <div class="inv-sign">
        <div>توقيع المستلم · Receiver</div>
        <div>الختم والتوقيع · Stamp &amp; Signature</div>
    </div>

    <div class="inv-foot"><span>{{ addressar }}</span>|<span class="t-ltr">{{ addressen }}</span></div>
</div>
</div></div>
@endsection

@section('js')
<script>
// طباعة تلقائية أول ما الصفحة تفتح، وبعد الطباعة (أو الإلغاء) الصفحة تتقفل
(function () {
    function go() {
        var printContents = document.getElementById('print').outerHTML;
        document.body.innerHTML = printContents;          // نطبع الفاتورة بس
        setTimeout(function () { window.print(); }, 500);
    }
    window.addEventListener('afterprint', function () { setTimeout(function () { window.close(); }, 300); });
    setTimeout(function () { window.close(); }, 60000);   // احتياطي لو afterprint ما اشتغلش
    if (document.readyState === 'complete') go(); else window.addEventListener('load', go);
})();
</script>
@endsection
