@extends('layouts.master')

@section('css')
@include('transport_invoices._print_style')
<style>
.inv{ position:relative; }
.inv-void{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; z-index:5; }
.inv-void span{ font-size:90px; font-weight:900; color:rgba(220,38,38,.16); border:10px solid rgba(220,38,38,.16); padding:0 40px; border-radius:20px; transform:rotate(-22deg); }
.inv-period{ font-size:11.5px; color:var(--ink2); font-weight:700; }
.inv-actions{ display:flex; justify-content:center; gap:8px; flex-wrap:wrap; }
.inv-cancel-info{ max-width:210mm; margin:0 auto 12px; }
</style>
@endsection

@section('title')
فاتورة نقل رقم {{ $inv->invoice_no }}
@stop

@section('page-header')
<div class="breadcrumb-header justify-content-between"></div>
@endsection

@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $money = function ($v) { return number_format(round((float) $v, 2), 2, '.', ','); };
    $val = function ($v) { return ($v === null || $v === '' || $v === '0') ? '-' : $v; };
    $ratePct = rtrim(rtrim(number_format($inv->vat_rate * 100, 2), '0'), '.');
@endphp

@if (session('inv_ok'))<div class="alert alert-success text-center no-print" style="max-width:210mm;margin:0 auto 12px">{{ session('inv_ok') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger text-center" style="max-width:210mm;margin:0 auto 12px">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
@if ($inv->is_cancelled)
    <div class="alert alert-danger inv-cancel-info no-print">
        <b>الفاتورة ملغاة</b> بتاريخ {{ optional($inv->cancelled_at)->format('Y/m/d h:i A') }} — السبب: {{ $inv->cancel_reason }}
    </div>
@endif

<div class="inv-actions">
    <button class="btn btn-danger" id="print_Button" onclick="window.print()"><i class="mdi mdi-printer ml-1"></i> طباعة</button>
    <a href="{{ url($lp . '/transport-invoices/create') }}" class="btn btn-success"><i class="bx bx-plus"></i> فاتورة جديدة</a>
    <a href="{{ url($lp . '/transport-invoices') }}" class="btn btn-secondary"><i class="bx bx-list-ul"></i> الفواتير السابقة</a>
    @if (!$inv->is_cancelled)
        <button class="btn btn-outline-danger" data-toggle="modal" data-target="#cancelModal"><i class="bx bx-x-circle"></i> إلغاء الفاتورة</button>
    @endif
</div>

<div class="card"><div class="card-body p-0">
<div class="inv" id="print" dir="rtl">
    @if ($inv->is_cancelled)<div class="inv-void"><span>ملغاة</span></div>@endif

    {{-- الهيدر --}}
    <table class="inv-head">
        <tr>
            <td class="inv-co ar" style="width:38%">
                <b>{{ $sys->name_ar ?? '' }}</b>
                <span>{{ $sys->descriptionarbic ?? '' }}</span>
                @if (!empty($sys->SR))<span>س.ت : {{ $sys->SR }}</span>@endif
                @if (!empty($sys->Tax))<span>الرقم الضريبي : {{ $sys->Tax }}</span>@endif
            </td>
            <td class="inv-logo" style="width:24%">
                @if (!empty($sys->logo))<img src="{{ asset('assets/img/brand/' . $sys->logo) }}" alt="">@endif
            </td>
            <td class="inv-co en" style="width:38%">
                <b>{{ $sys->name_en ?? '' }}</b>
                <span>{{ $sys->descriptionenglish ?? '' }}</span>
                @if (!empty($sys->SR))<span>C.R : {{ $sys->SR }}</span>@endif
                @if (!empty($sys->Tax))<span>VAT No : {{ $sys->Tax }}</span>@endif
            </td>
        </tr>
    </table>
    <div class="inv-head-line"></div><div class="inv-head-line2"></div>

    {{-- العنوان --}}
    <div class="inv-title">
        <h1>{{ $inv->is_standard ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة' }} - خدمات نقل{{ $inv->is_zero_rated ? ' (نسبة صفرية)' : '' }}<small class="t-ltr">{{ $inv->is_standard ? 'Tax Invoice' : 'Simplified Tax Invoice' }}</small></h1>
        <div class="inv-no"><span>رقم الفاتورة · INVOICE NO</span><b class="t-ltr">#{{ $inv->invoice_no }}</b></div>
    </div>

    <div class="inv-grid">
        <div class="inv-box">
            <h3>بيانات الفاتورة <small>INVOICE DETAILS</small></h3>
            <table class="inv-kv">
                <tr><th>تاريخ الإصدار<small>ISSUE DATE</small></th><td class="t-ltr" style="text-align:right">{{ $inv->issue_date->format('Y-m-d h:i A') }}</td></tr>
                <tr><th>فترة التوريد<small>SUPPLY PERIOD</small></th><td>
                    @if ($inv->supply_from || $inv->supply_to)
                        <span class="t-ltr">{{ optional($inv->supply_from)->format('Y-m-d') }}</span> → <span class="t-ltr">{{ optional($inv->supply_to)->format('Y-m-d') }}</span>
                    @else - @endif
                </td></tr>
                <tr><th>طريقة الدفع<small>PAYMENT</small></th><td>آجل</td></tr>
                <tr><th>أمر الشراء / المرجع<small>PO / REF</small></th><td>{{ $val($inv->po_number) }}</td></tr>
            </table>
        </div>
        <div class="inv-box">
            <h3>بيانات العميل <small>CUSTOMER DETAILS</small></h3>
            <table class="inv-kv">
                <tr><th>اسم العميل<small>CUSTOMER</small></th><td>{{ $inv->customer_name }}</td></tr>
                <tr><th>الرقم الضريبي<small>VAT NO</small></th><td class="t-ltr" style="text-align:right">{{ $val($inv->customer_vat) }}</td></tr>
                @if ($inv->customer_cr)<tr><th>السجل التجاري<small>C.R</small></th><td class="t-ltr" style="text-align:right">{{ $inv->customer_cr }}</td></tr>@endif
                <tr><th>العنوان<small>ADDRESS</small></th><td>{{ $val($inv->customer_address) }}</td></tr>
                <tr><th>الجوال<small>PHONE</small></th><td class="t-ltr" style="text-align:right">{{ $val($inv->customer_phone) }}</td></tr>
            </table>
        </div>
    </div>

    <div class="inv-items-wrap">
    <table class="inv-items">
        <thead>
            <tr>
                <th style="width:4%">#<small>NO</small></th>
                <th style="width:9%">التاريخ<small>DATE</small></th>
                <th>وصف الخدمة<small>DESCRIPTION</small></th>
                <th style="width:10%">رقم اللوحة<small>PLATE</small></th>
                <th style="width:9%">البوليصة<small>WAYBILL</small></th>
                <th style="width:6%">الكمية<small>QTY</small></th>
                <th style="width:10%">السعر<small>PRICE</small></th>
                <th style="width:7%">الضريبة %<small>VAT %</small></th>
                <th style="width:9%">الضريبة<small>VAT</small></th>
                <th style="width:11%">الإجمالي<small>TOTAL</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inv->items as $i => $it)
                @php $rowVat = round($it->amount * $inv->vat_rate, 2); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="num">{{ optional($it->trip_date)->format('Y-m-d') ?: '-' }}</td>
                    <td class="name">{{ $it->description }}</td>
                    <td class="num">{{ $it->plate_number ?: '-' }}</td>
                    <td class="num">{{ $it->waybill_no ?: '-' }}</td>
                    <td class="num">{{ rtrim(rtrim(number_format($it->qty, 2), '0'), '.') }}</td>
                    <td class="num">{{ $money($it->unit_price) }}</td>
                    <td class="num">{{ $ratePct }}%</td>
                    <td class="num">{{ $money($rowVat) }}</td>
                    <td class="num net">{{ $money($it->amount + $rowVat) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="inv-bottom">
        <div class="inv-qr">
            <div class="qr">{!! QrCode::size(110)->generate($qr) !!}</div>
            @if (!empty($sys->bankname) || !empty($sys->bank_acount_iban))
                <div class="inv-bank">
                    <b>{{ $sys->bankname }}</b>
                    @if ($sys->bank_acount_number)<div>رقم الحساب · Acc: <span class="t-ltr">{{ $sys->bank_acount_number }}</span></div>@endif
                    @if ($sys->bank_acount_iban)<div>IBAN: <span class="t-ltr">{{ $sys->bank_acount_iban }}</span></div>@endif
                </div>
            @endif
        </div>
        <div>
            <table class="inv-tot">
                <tr><th>الإجمالي قبل الضريبة<small>SUB TOTAL</small></th><td>{{ $money($inv->subtotal) }}</td></tr>
                <tr><th>الخصم<small>DISCOUNT</small></th><td>{{ $money($inv->discount) }}</td></tr>
                <tr><th>الإجمالي الخاضع للضريبة<small>TAXABLE</small></th><td>{{ $money($inv->taxable) }}</td></tr>
                <tr><th>ضريبة القيمة المضافة {{ $ratePct }}%<small>VAT</small></th><td>{{ $money($inv->vat_amount) }}</td></tr>
                <tr class="grand"><th>الإجمالي شامل الضريبة<small>TOTAL</small></th><td>{{ $money($inv->total) }} <small style="font-size:11px">SAR</small></td></tr>
            </table>
            @if ($words)<div class="inv-words">{{ $words }}</div>@endif
        </div>
    </div>

    @if ($inv->is_zero_rated)
        <div class="inv-note" style="border-color:#2F6FED;background:#EFF6FF"><b>خاضع لنسبة الصفر · Zero-rated:</b> {{ $inv->vat_exempt_reason }} <span class="t-ltr" style="color:#64748B">({{ $inv->vat_exempt_code }})</span></div>
    @endif

    @if ($inv->notes)
        <div class="inv-note"><b>ملاحظات:</b> {{ $inv->notes }}</div>
    @endif

    <div class="inv-sign">
        <div>توقيع المستلم · Receiver</div>
        <div>الختم والتوقيع · Stamp &amp; Signature</div>
    </div>

    <div class="inv-foot"><span>{{ $sys->address_ar ?? '' }}</span>|<span class="t-ltr">{{ $sys->address_en ?? '' }}</span>
        <div style="font-size:10px;margin-top:3px">أعدها: {{ optional($inv->user)->name }}</div>
    </div>
</div>
</div></div>

@if (!$inv->is_cancelled)
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="post" action="{{ url($lp . '/transport-invoices/' . $inv->id . '/cancel') }}">
            @csrf
            <div class="modal-header"><h5 class="modal-title">إلغاء الفاتورة #{{ $inv->invoice_no }}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div class="alert alert-warning" style="font-size:13px">الإلغاء هيعمل قيد عكسي بنفس المبالغ، والأحمال المرتبطة بالفاتورة هترجع "غير مفوترة". مينفعش ترجع في الإلغاء.</div>
                <label>سبب الإلغاء</label>
                <input type="text" name="cancel_reason" class="form-control" required maxlength="255">
            </div>
            <div class="modal-footer"><button class="btn btn-danger">تأكيد الإلغاء</button><button type="button" class="btn btn-light" data-dismiss="modal">رجوع</button></div>
        </form>
    </div></div>
</div>
@endif
@endsection
