@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
فواتير النقل الضريبية
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">فواتير النقل الضريبية</h4></div>
    <div class="d-flex no-print" style="gap:8px">
        <a href="{{ url('transport-invoices/create') }}" class="btn btn-success btn-sm"><i class="bx bx-plus-circle"></i> فاتورة جديدة</a>
        <a href="{{ url('transport-reports/invoices') }}" class="btn btn-outline-primary btn-sm"><i class="bx bx-bar-chart-alt-2"></i> تقرير الفواتير والضريبة</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button>
    </div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
@endphp
@if (session('inv_ok'))<div class="alert alert-success">{{ session('inv_ok') }}</div>@endif

<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>فواتير النقل من {{ $from }} إلى {{ $to }}</div></div>

<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url($lp . '/transport-invoices') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ $from }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ $to }}"></div>
            <div class="col-lg-3 col-md-4 mb-2"><label>العميل</label>
                <select name="customer_account_id" class="form-control"><option value="">الكل</option>@foreach ($customers as $c)<option value="{{ $c->id }}" {{ request('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>الحالة</label>
                <select name="status" class="form-control"><option value="">الكل</option><option value="1" {{ request('status') == '1' ? 'selected' : '' }}>سارية</option><option value="2" {{ request('status') == '2' ? 'selected' : '' }}>ملغاة</option></select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>بحث (رقم / عميل / مرجع)</label><input type="text" name="q" class="form-control" value="{{ request('q') }}"></div>
            <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
        </div>
    </form>
</div></div>

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-receipt"></i>فواتير سارية</span><b>{{ $summary['count'] }}</b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-money"></i>الصافي قبل الضريبة</span><b>{{ $m($summary['taxable']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-coin-stack"></i>ضريبة القيمة المضافة</span><b>{{ $m($summary['vat']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-wallet"></i>الإجمالي شامل الضريبة</span><b>{{ $m($summary['total']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-x-circle"></i>ملغاة</span><b>{{ $summary['cancelled'] }}</b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>رقم الفاتورة</th><th>التاريخ</th><th>العميل</th><th>الرقم الضريبي</th><th>النوع</th><th>قبل الضريبة</th><th>الضريبة</th><th>الإجمالي</th><th>الحالة</th><th>أعدها</th><th class="no-print"></th></tr></thead>
            <tbody>
                @forelse ($invoices as $inv)
                    <tr style="{{ $inv->is_cancelled ? 'opacity:.6' : '' }}">
                        <td style="font-weight:800">#{{ $inv->invoice_no }}</td>
                        <td style="white-space:nowrap">{{ $inv->issue_date->format('Y/m/d') }}</td>
                        <td>{{ $inv->customer_name }}</td>
                        <td class="tr-num">{{ $inv->customer_vat ?: '—' }}</td>
                        <td>@if ($inv->is_standard)<span class="tr-tag blue">ضريبية</span>@else<span class="tr-tag gray">مبسطة</span>@endif @if ($inv->is_zero_rated)<span class="tr-tag purple">0%</span>@endif</td>
                        <td class="tr-num">{{ $m($inv->taxable) }}</td>
                        <td class="tr-num">{{ $m($inv->vat_amount) }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($inv->total) }}</td>
                        <td>@if ($inv->is_cancelled)<span class="tr-tag late">ملغاة</span>@else<span class="tr-tag ok">سارية</span>@endif</td>
                        <td>{{ optional($inv->user)->name }}</td>
                        <td class="no-print"><a href="{{ url($lp . '/transport-invoices/' . $inv->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-printer"></i> عرض / طباعة</a></td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="tr-empty">لا توجد فواتير في الفترة دي</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection
