@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير الحضور والانصراف
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير الحضور والانصراف</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $extra = '<div class="col-lg-2 col-md-4 mb-2 d-flex align-items-end"><label class="mb-2" style="font-weight:700"><input type="checkbox" name="active_only" value="1"' . (request('active_only') ? ' checked' : '') . '> اللي ليهم سجلات بس</label></div>';
@endphp
@include('hr_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>الحضور والانصراف من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-check"></i>أيام حضور</span><b>{{ $summary['present'] }}</b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-time-five"></i>أيام تأخير</span><b>{{ $summary['late'] }}</b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-x"></i>أيام غياب</span><b>{{ $summary['absent'] }}</b></div>
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-timer"></i>ساعات إضافي</span><b>{{ $summary['overtime'] }}</b></div>
    <div class="tr-kpi" style="--c:#B91C1C"><span><i class="bx bx-minus-circle"></i>خصومات الغياب</span><b>{{ $m($summary['deduction']) }} <small>ر.س</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>الموظف</th><th>حاضر</th><th>متأخر</th><th>غائب</th><th>نسبة الحضور</th><th>ساعات إضافي</th><th>خصومات الغياب</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['emp']->name_ar }}</td>
                        <td>{{ $r['present'] }}</td>
                        <td>@if ($r['late'])<span class="tr-tag on">{{ $r['late'] }}</span>@else 0 @endif</td>
                        <td>@if ($r['absent'])<span class="tr-tag late">{{ $r['absent'] }}</span>@else 0 @endif</td>
                        <td>@if ($r['rate'] === null) — @else<span class="tr-tag {{ $r['rate'] >= 95 ? 'ok' : ($r['rate'] >= 85 ? 'on' : 'late') }}">{{ $r['rate'] }}%</span>@endif</td>
                        <td>{{ $r['overtime'] }}</td>
                        <td class="tr-num">{{ $m($r['deduction']) }}</td>
                    </tr>
                @empty <tr><td colspan="7" class="tr-empty">لا توجد بيانات</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection
