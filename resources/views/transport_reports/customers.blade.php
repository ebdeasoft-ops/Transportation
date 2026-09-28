@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير العملاء - النقل
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير العملاء (الأحمال والفوترة)</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
    $qs = http_build_query(['start_at' => $from, 'end_at' => $to]);
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير العملاء من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter')

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-group"></i>عملاء نشطين</span><b>{{ $rows->count() }}</b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-package"></i>عدد الأحمال</span><b>{{ $rows->sum('trips') }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>قيمة الأحمال</span><b>{{ $m($rows->sum('trips_value')) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-receipt"></i>المفوتر (شامل الضريبة)</span><b>{{ $m($rows->sum('invoiced')) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-error-circle"></i>غير مفوتر</span><b>{{ $m($rows->sum('unbilled_value')) }} <small>ر.س</small></b></div>
</div>

@if ($noCustomer->count())
    <div class="alert alert-warning no-print">فيه <b>{{ $noCustomer->count() }}</b> حمولة في الفترة دي مش مربوطة بعميل (قيمتها {{ $m($noCustomer->sum('price')) }} ر.س) — اربطها بعميل من «تعديل الشحنة» عشان تتفوتر.</div>
@endif

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>العميل</th><th>الأحمال</th><th>قيمة الأحمال</th><th>غير مفوترة</th><th>قيمة غير المفوترة</th><th>الفواتير</th><th>المفوتر شامل الضريبة</th><th>الضريبة</th><th>رصيد الحساب الحالي</th><th class="no-print"></th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['name'] }}</td>
                        <td>{{ $r['trips'] }}</td>
                        <td class="tr-num">{{ $m($r['trips_value']) }}</td>
                        <td>@if ($r['unbilled'])<span class="tr-tag on">{{ $r['unbilled'] }}</span>@else<span class="tr-tag ok">0</span>@endif</td>
                        <td class="tr-num">{{ $m($r['unbilled_value']) }}</td>
                        <td>{{ $r['invoices'] }}</td>
                        <td class="tr-num">{{ $m($r['invoiced']) }}</td>
                        <td class="tr-num">{{ $m($r['vat']) }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $r['balance'] === null ? '—' : $m($r['balance']) }}</td>
                        <td class="no-print" style="white-space:nowrap">
                            <a href="{{ url($lp . '/transport-reports/customers/' . $r['id']) }}?{{ $qs }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-spreadsheet"></i> كشف حساب</a>
                            @if ($r['unbilled'])<a href="{{ url($lp . '/transport-invoices/create') }}?customer_account_id={{ $r['id'] }}" class="btn btn-sm btn-success"><i class="bx bx-receipt"></i> فوترة</a>@endif
                        </td>
                    </tr>
                @empty <tr><td colspan="10" class="tr-empty">لا توجد بيانات في الفترة دي</td></tr> @endforelse
            </tbody>
            @if ($rows->count())
            <tfoot><tr><td>الإجمالي</td><td>{{ $rows->sum('trips') }}</td><td class="tr-num">{{ $m($rows->sum('trips_value')) }}</td><td>{{ $rows->sum('unbilled') }}</td><td class="tr-num">{{ $m($rows->sum('unbilled_value')) }}</td><td>{{ $rows->sum('invoices') }}</td><td class="tr-num">{{ $m($rows->sum('invoiced')) }}</td><td class="tr-num">{{ $m($rows->sum('vat')) }}</td><td></td><td class="no-print"></td></tr></tfoot>
            @endif
        </table>
    </div>
</div></div>
@endsection
