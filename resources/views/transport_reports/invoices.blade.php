@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير فواتير النقل والضريبة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير فواتير النقل وضريبة القيمة المضافة</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
    $opts = '<option value="">الكل</option>';
    foreach ($customers as $c) $opts .= '<option value="' . $c->id . '"' . (request('customer_account_id') == $c->id ? ' selected' : '') . '>' . e($c->name) . '</option>';
    $extra = '<div class="col-lg-3 col-md-4 mb-2"><label>العميل</label><select name="customer_account_id" class="form-control">' . $opts . '</select></div>';
    $maxC = max(1, (float) $byCustomer->max('total'));
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير فواتير النقل والضريبة من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-receipt"></i>فواتير سارية</span><b>{{ $summary['count'] }} <small>(ضريبية {{ $summary['standard'] }} / مبسطة {{ $summary['simple'] }})</small></b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-money"></i>الخاضع للضريبة</span><b>{{ $m($summary['taxable']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-coin-stack"></i>ضريبة المخرجات</span><b>{{ $m($summary['vat']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-wallet"></i>الإجمالي شامل الضريبة</span><b>{{ $m($summary['total']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-x-circle"></i>ملغاة</span><b>{{ $summary['cancelled'] }} <small>({{ $m($summary['cancelled_total']) }})</small></b></div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <div style="font-weight:800;margin-bottom:8px">إقرار ضريبة القيمة المضافة (مبيعات النقل) حسب الشهر</div>
            <table class="table tr-table mb-0">
                <thead><tr><th>الشهر</th><th>عدد الفواتير</th><th>خاضعة 15%</th><th>نسبة صفرية</th><th>ضريبة المخرجات</th><th>الإجمالي</th></tr></thead>
                <tbody>
                    @forelse ($months as $r)
                        <tr><td style="font-weight:700">{{ $r['month'] }}</td><td>{{ $r['count'] }}</td><td class="tr-num">{{ $m($r['std']) }}</td><td class="tr-num">{{ $m($r['zero']) }}</td><td class="tr-num">{{ $m($r['vat']) }}</td><td class="tr-num">{{ $m($r['total']) }}</td></tr>
                    @empty <tr><td colspan="6" class="tr-empty">لا توجد بيانات</td></tr> @endforelse
                </tbody>
                @if ($months->count())
                <tfoot><tr><td>الإجمالي</td><td>{{ $summary['count'] }}</td><td class="tr-num">{{ $m($summary['std_taxable']) }}</td><td class="tr-num">{{ $m($summary['zero_taxable']) }}</td><td class="tr-num">{{ $m($summary['vat']) }}</td><td class="tr-num">{{ $m($summary['total']) }}</td></tr></tfoot>
                @endif
            </table>
            <div class="text-muted mt-2" style="font-size:12px">المبيعات بنسبة صفرية: {{ $m($summary['zero_taxable']) }} ر.س ({{ $summary['zero_count'] }} فاتورة) — الخصومات على الفواتير: {{ $m($summary['discount']) }} ر.س — الفواتير الملغاة مش داخلة في الإقرار.</div>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <div style="font-weight:800;margin-bottom:8px">أعلى العملاء فوترة</div>
            <table class="table tr-table mb-0"><tbody>
                @forelse ($byCustomer as $r)
                    <tr><td style="font-weight:700">{{ $r['name'] }}</td><td style="width:35%"><div class="tr-bar"><span style="width:{{ round($r['total'] / $maxC * 100) }}%"></span></div></td><td class="tr-num">{{ $m($r['total']) }}</td></tr>
                @empty <tr><td class="tr-empty">لا توجد بيانات</td></tr> @endforelse
            </tbody></table>
        </div></div>
    </div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>رقم</th><th>التاريخ</th><th>العميل</th><th>الرقم الضريبي</th><th>النوع</th><th>قبل الخصم</th><th>الخصم</th><th>الخاضع</th><th>الضريبة</th><th>الإجمالي</th><th>الحالة</th></tr></thead>
            <tbody>
                @forelse ($invoices as $inv)
                    <tr style="{{ $inv->is_cancelled ? 'opacity:.55;text-decoration:line-through' : '' }}">
                        <td><a href="{{ url($lp . '/transport-invoices/' . $inv->id) }}" style="font-weight:800">#{{ $inv->invoice_no }}</a></td>
                        <td style="white-space:nowrap">{{ $inv->issue_date->format('Y/m/d') }}</td>
                        <td>{{ $inv->customer_name }}</td>
                        <td class="tr-num">{{ $inv->customer_vat ?: '—' }}</td>
                        <td>{{ $inv->is_standard ? 'ضريبية' : 'مبسطة' }}@if ($inv->is_zero_rated) <span class="tr-tag purple">صفرية</span>@endif</td>
                        <td class="tr-num">{{ $m($inv->subtotal) }}</td>
                        <td class="tr-num">{{ $m($inv->discount) }}</td>
                        <td class="tr-num">{{ $m($inv->taxable) }}</td>
                        <td class="tr-num">{{ $m($inv->vat_amount) }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($inv->total) }}</td>
                        <td>@if ($inv->is_cancelled)<span class="tr-tag late">ملغاة</span>@else<span class="tr-tag ok">سارية</span>@endif</td>
                    </tr>
                @empty <tr><td colspan="11" class="tr-empty">لا توجد فواتير</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection
