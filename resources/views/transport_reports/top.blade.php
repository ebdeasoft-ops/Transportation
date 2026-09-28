@extends('layouts.master')
@section('css')
@include('transport_reports._style')
<style>
.tp-card h6{ font-weight:800; font-size:15px; display:flex; align-items:center; gap:8px; margin-bottom:10px; }
.tp-card h6 i{ color:#F59E0B; font-size:20px; }
.tp-rank{ width:28px; height:28px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; background:#F1F5F9; color:#475569; }
.tp-rank.r1{ background:#FEF3C7; color:#B45309; } .tp-rank.r2{ background:#E2E8F0; color:#334155; } .tp-rank.r3{ background:#FFEDD5; color:#C2410C; }
.tp-sw a{ border:1px solid #E6EBF2; padding:5px 12px; border-radius:999px; font-weight:700; font-size:12.5px; color:#475569; }
.tp-sw a.on{ background:#2F6FED; color:#fff; border-color:#2F6FED; text-decoration:none; }
</style>
@endsection
@section('title')
الأكثر نقلاً
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">الأكثر نقلاً: الشاحنات والعملاء والسائقين والمسارات</h4></div>
    <div class="d-flex no-print" style="gap:8px"><button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button></div>
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $qs = function ($b) use ($from, $to) { return url()->current() . '?' . http_build_query(['start_at' => $from, 'end_at' => $to, 'by' => $b]); };
    $extra = '<input type="hidden" name="by" value="' . $by . '">';
    $lists = [
        ['bxs-truck', 'أكثر الشاحنات نقلاً', $topTrucks, true],
        ['bx-group', 'أكثر العملاء نقلاً', $topCustomers, false],
        ['bx-id-card', 'أكثر السائقين نقلاً', $topDrivers, false],
        ['bx-map-alt', 'أكثر المسارات', $topRoutes, false],
    ];
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>الأكثر نقلاً من {{ $from }} إلى {{ $to }} ({{ $by == 'revenue' ? 'حسب الإيراد' : 'حسب عدد الرحلات' }})</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="d-flex align-items-center mb-3 tp-sw no-print" style="gap:8px">
    <b style="font-size:13px">الترتيب حسب:</b>
    <a href="{{ $qs('trips') }}" class="{{ $by == 'trips' ? 'on' : '' }}">عدد الرحلات</a>
    <a href="{{ $qs('revenue') }}" class="{{ $by == 'revenue' ? 'on' : '' }}">الإيراد</a>
    <span class="text-muted" style="font-size:12.5px;margin-inline-start:auto">إجمالي الفترة: {{ $totals['trips'] }} رحلة — {{ $m($totals['revenue']) }} ر.س</span>
</div>

<div class="row">
    @foreach ($lists as [$icon, $title, $rows, $isTruck])
        @php $max = max(1, (float) $rows->max($by)); @endphp
        <div class="col-lg-6"><div class="card tp-card"><div class="card-body">
            <h6><i class="bx {{ $icon }}"></i>{{ $title }}</h6>
            <table class="table tr-table mb-0">
                <thead><tr><th></th><th>الاسم</th><th>الرحلات</th><th>الإيراد</th>@if ($isTruck)<th>المصروفات</th><th>الربح</th>@endif<th style="width:22%"></th></tr></thead>
                <tbody>
                    @forelse ($rows as $i => $r)
                        <tr>
                            <td><span class="tp-rank r{{ $i + 1 }}">{{ $i + 1 }}</span></td>
                            <td style="font-weight:700" class="{{ $isTruck ? 'tr-num' : '' }}">{{ $r['name'] }}</td>
                            <td style="font-weight:800">{{ $r['trips'] }}</td>
                            <td class="tr-num">{{ $m($r['revenue']) }}</td>
                            @if ($isTruck)
                                <td class="tr-num" style="color:#B91C1C">{{ $m($r['expenses']) }}</td>
                                <td class="tr-num" style="font-weight:800;color:{{ $r['profit'] >= 0 ? '#047857' : '#B91C1C' }}">{{ $m($r['profit']) }}</td>
                            @endif
                            <td><div class="tr-bar"><span style="width:{{ round($r[$by] / $max * 100) }}%"></span></div></td>
                        </tr>
                    @empty <tr><td colspan="7" class="tr-empty">لا توجد بيانات</td></tr> @endforelse
                </tbody>
            </table>
        </div></div></div>
    @endforeach
</div>
@endsection
