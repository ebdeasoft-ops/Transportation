@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير الإجازات
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير الإجازات</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $extra = '<div class="col-lg-2 col-md-4 mb-2 d-flex align-items-end"><label class="mb-2" style="font-weight:700"><input type="checkbox" name="active_only" value="1"' . (request('active_only') ? ' checked' : '') . '> اللي ليهم إجازات بس</label></div>';
@endphp
@include('hr_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>الإجازات من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-calendar"></i>أيام إجازات معتمدة</span><b>{{ $summary['total'] }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-sun"></i>سنوية</span><b>{{ $summary['annual'] }}</b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-plus-medical"></i>مرضية</span><b>{{ $summary['sick'] }}</b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-hourglass"></i>طلبات معلقة</span><b>{{ $summary['pending'] }}</b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-minus-circle"></i>خصومات الإجازات</span><b>{{ $m($summary['deduction']) }} <small>ر.س</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>الموظف</th>@foreach ($types as $k => $v)<th>{{ $v }}</th>@endforeach<th>الإجمالي</th><th>معلقة</th><th>الخصم</th><th>المستخدم من السنوية {{ $year }}</th><th>الرصيد المتبقي</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['emp']->name_ar }}</td>
                        @foreach ($types as $k => $v)<td>{{ $r[$k] ?: '—' }}</td>@endforeach
                        <td style="font-weight:800">{{ $r['total'] }}</td>
                        <td>@if ($r['pending'])<span class="tr-tag on">{{ $r['pending'] }}</span>@else — @endif</td>
                        <td class="tr-num">{{ $m($r['deduction']) }}</td>
                        <td>{{ $r['used_year'] }}</td>
                        <td><span class="tr-tag {{ $r['balance'] > 5 ? 'ok' : ($r['balance'] > 0 ? 'on' : 'late') }}">{{ $r['balance'] }} يوم</span></td>
                    </tr>
                @empty <tr><td colspan="12" class="tr-empty">لا توجد بيانات</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection
