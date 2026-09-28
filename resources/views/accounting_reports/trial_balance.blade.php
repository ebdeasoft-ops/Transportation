@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
ميزان المراجعة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">ميزان المراجعة</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return abs($v) < 0.005 ? '—' : number_format((float) $v, 2); };
    $extra = '<div class="col-lg-2 col-md-4 mb-2"><label>الحسابات الصفرية</label><select name="hide_zero" class="form-control">'
        . '<option value="1"' . ($hideZero ? ' selected' : '') . '>إخفاء</option><option value="0"' . (!$hideZero ? ' selected' : '') . '>إظهار</option></select></div>';
    $diffMove  = round($totals['move_d'] - $totals['move_c'], 2);
    $diffClose = round($totals['close_d'] - $totals['close_c'], 2);
@endphp
@include('accounting_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>ميزان المراجعة من {{ $from }} إلى {{ $to }}</div></div>
@include('accounting_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:4">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-up-arrow-alt"></i>إجمالي حركة المدين</span><b>{{ number_format($totals['move_d'], 2) }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-down-arrow-alt"></i>إجمالي حركة الدائن</span><b>{{ number_format($totals['move_c'], 2) }}</b></div>
    <div class="tr-kpi" style="--c:{{ abs($diffMove) < 0.01 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-check-shield"></i>فرق الحركة</span><b>{{ number_format($diffMove, 2) }}</b></div>
    <div class="tr-kpi" style="--c:{{ abs($diffClose) < 0.01 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-balance"></i>فرق الأرصدة</span><b>{{ number_format($diffClose, 2) }} <small>{{ abs($diffClose) < 0.01 ? 'متوازن' : 'غير متوازن' }}</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered tr-table">
            <thead>
                <tr><th rowspan="2">رقم الحساب</th><th rowspan="2">اسم الحساب</th><th colspan="2" class="text-center">رصيد أول المدة</th><th colspan="2" class="text-center">حركة الفترة</th><th colspan="2" class="text-center">الرصيد آخر المدة</th></tr>
                <tr><th>مدين</th><th>دائن</th><th>مدين</th><th>دائن</th><th>مدين</th><th>دائن</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr class="ac-row ac-l{{ $r['level'] }}">
                        <td class="ac-num">{{ $r['number'] }}</td>
                        <td class="ac-name" style="padding-inline-start:{{ 8 + ($r['level'] - 1) * 18 }}px">{{ $r['name'] }}</td>
                        <td class="ac-num">{{ $m($r['open_nd']) }}</td><td class="ac-num">{{ $m($r['open_nc']) }}</td>
                        <td class="ac-num">{{ $m($r['move_d']) }}</td><td class="ac-num">{{ $m($r['move_c']) }}</td>
                        <td class="ac-num">{{ $m($r['close_d']) }}</td><td class="ac-num">{{ $m($r['close_c']) }}</td>
                    </tr>
                @empty <tr><td colspan="8" class="tr-empty">لا توجد حركات</td></tr> @endforelse
            </tbody>
            <tfoot>
                <tr class="ac-grand"><td colspan="2">الإجمالي</td>
                    <td class="ac-num">{{ number_format($totals['open_nd'], 2) }}</td><td class="ac-num">{{ number_format($totals['open_nc'], 2) }}</td>
                    <td class="ac-num">{{ number_format($totals['move_d'], 2) }}</td><td class="ac-num">{{ number_format($totals['move_c'], 2) }}</td>
                    <td class="ac-num">{{ number_format($totals['close_d'], 2) }}</td><td class="ac-num">{{ number_format($totals['close_c'], 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
    <div class="text-muted" style="font-size:12px">الأرقام من دفتر الحركات (القيود المعتمدة بس). رصيد الحساب الرئيسي = مجموع حركاته + حركات الحسابات اللي تحته.</div>
</div></div>
@endsection
