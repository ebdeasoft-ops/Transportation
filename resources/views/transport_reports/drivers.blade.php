@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير السائقين
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير أداء السائقين</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $maxT = max(1, (int) $rows->max('trips'));
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير السائقين من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter')

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-id-card"></i>سائقين اشتغلوا</span><b>{{ $rows->count() }}</b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-package"></i>إجمالي الرحلات</span><b>{{ $total['trips'] }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>الإيراد</span><b>{{ $m($total['revenue']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-time-five"></i>رحلات متأخرة</span><b>{{ $total['late'] }}</b></div>
    <div class="tr-kpi" style="--c:#14B8A6"><span><i class="bx bx-timer"></i>متوسط مدة الرحلة</span><b>{{ $total['avg_hours'] }} <small>ساعة</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>السائق</th><th>الجوال</th><th>الشاحنات</th><th>الرحلات</th><th style="width:14%"></th><th>تم التفريغ</th><th>محمّلة الآن</th><th>متأخرة</th><th>نسبة الالتزام</th><th>الإيراد</th><th>متوسط الرحلة</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    @php $ok = $r['trips'] ? round(($r['trips'] - $r['late']) / $r['trips'] * 100) : 0; @endphp
                    <tr>
                        <td style="font-weight:700">{{ $r['name'] }}</td>
                        <td>@if ($r['phone'])<a href="tel:{{ $r['phone'] }}" dir="ltr">{{ $r['phone'] }}</a>@else — @endif</td>
                        <td class="tr-num" style="font-size:12px">{{ $r['trucks'] ?: '—' }}</td>
                        <td style="font-weight:800">{{ $r['trips'] }}</td>
                        <td><div class="tr-bar"><span style="width:{{ round($r['trips'] / $maxT * 100) }}%"></span></div></td>
                        <td>{{ $r['done'] }}</td>
                        <td>{{ $r['loaded'] }}</td>
                        <td>@if ($r['late'])<span class="tr-tag late">{{ $r['late'] }}</span>@else 0 @endif</td>
                        <td><span class="tr-tag {{ $ok >= 90 ? 'ok' : ($ok >= 70 ? 'on' : 'late') }}">{{ $ok }}%</span></td>
                        <td class="tr-num">{{ $m($r['revenue']) }}</td>
                        <td>{{ $r['avg_hours'] }} س</td>
                    </tr>
                @empty <tr><td colspan="11" class="tr-empty">لا توجد رحلات في الفترة دي</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection
