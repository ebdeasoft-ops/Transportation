@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
مسير الرواتب الشهري
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">مسير الرواتب الشهري — {{ $month }}</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php $m = function ($v) { return number_format((float) $v, 2); }; $t = $totals; @endphp
@include('hr_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>مسير الرواتب لشهر {{ $month }}</div></div>

<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}"><div class="row">
        <div class="col-lg-2 col-md-4 mb-2"><label>الشهر</label><input type="month" name="month" class="form-control" value="{{ $month }}"></div>
        <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
    </div></form>
</div></div>

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-group"></i>الموظفين</span><b>{{ $rows->count() }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>إجمالي المستحقات</span><b>{{ $m($t['gross']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-minus-circle"></i>الخصومات</span><b>{{ $m($t['deduction'] + $t['absence'] + $t['leave']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-credit-card"></i>السلف</span><b>{{ $m($t['loans']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#1E3A8A"><span><i class="bx bx-wallet"></i>صافي الرواتب</span><b>{{ $m($t['net']) }} <small>ر.س</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>الموظف</th><th>القسم</th><th>الأساسي</th><th>البدلات</th><th>مكافآت</th><th>الإجمالي</th><th>خصومات</th><th>خصم غياب</th><th>خصم إجازات</th><th>سلف</th><th>الصافي</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['emp']->name_ar }}</td>
                        <td>{{ $r['dept'] }}</td>
                        <td class="tr-num">{{ $m($r['basic']) }}</td>
                        <td class="tr-num">{{ $m($r['allow']) }}</td>
                        <td class="tr-num" style="color:#047857">{{ $m($r['bonus']) }}</td>
                        <td class="tr-num" style="font-weight:700">{{ $m($r['gross']) }}</td>
                        <td class="tr-num" style="color:#B91C1C">{{ $m($r['deduction']) }}</td>
                        <td class="tr-num" style="color:#B91C1C">{{ $m($r['absence']) }}</td>
                        <td class="tr-num" style="color:#B91C1C">{{ $m($r['leave']) }}</td>
                        <td class="tr-num" style="color:#B45309">{{ $m($r['loans']) }}</td>
                        <td class="tr-num" style="font-weight:800;color:#1E3A8A">{{ $m($r['net']) }}</td>
                    </tr>
                @empty <tr><td colspan="11" class="tr-empty">لا يوجد موظفين</td></tr> @endforelse
            </tbody>
            @if ($rows->count())
            <tfoot><tr><td colspan="2">الإجمالي</td>@foreach (['basic', 'allow', 'bonus', 'gross', 'deduction', 'absence', 'leave', 'loans', 'net'] as $k)<td class="tr-num">{{ $m($t[$k]) }}</td>@endforeach</tr></tfoot>
            @endif
        </table>
    </div>
    <div class="text-muted" style="font-size:12px">الصافي = الأساسي + البدلات + المكافآت − (الخصومات + خصم الغياب + خصم الإجازات بدون راتب) − السلف. قسيمة الراتب التفصيلية من «مستند الرواتب».</div>
</div></div>
@endsection
