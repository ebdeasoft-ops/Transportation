@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
قائمة الدخل
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">قائمة الدخل (الأرباح والخسائر)</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php $m = function ($v) { return number_format((float) $v, 2); }; @endphp
@include('accounting_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>قائمة الدخل من {{ $from }} إلى {{ $to }}</div></div>
@include('accounting_reports._filter')

<div class="tr-kpis" style="--cols:3">
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-plus-circle"></i>إجمالي الإيرادات</span><b>{{ $m($sections[3]['total']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-minus-circle"></i>إجمالي المصروفات</span><b>{{ $m($sections[4]['total']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:{{ $net >= 0 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-trending-up"></i>{{ $net >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}</span><b>{{ $m(abs($net)) }} <small>ر.س</small></b></div>
</div>

<div class="card"><div class="card-body">
    <table class="table table-bordered tr-table" style="max-width:900px;margin:auto">
        <thead><tr><th style="width:18%">رقم الحساب</th><th>البيان</th><th style="width:22%">المبلغ</th></tr></thead>
        <tbody>
        @foreach ($sections as $s)
            <tr class="ac-sec"><td colspan="3">{{ $s['label'] }}</td></tr>
            @forelse ($s['rows'] as $r)
                <tr class="ac-row ac-l{{ $r['level'] }}"><td class="ac-num">{{ $r['number'] }}</td><td style="padding-inline-start:{{ 8 + ($r['level'] - 1) * 18 }}px">{{ $r['name'] }}</td><td class="ac-num">{{ $m($r['amount']) }}</td></tr>
            @empty <tr><td colspan="3" class="tr-empty">لا توجد حركات</td></tr> @endforelse
            <tr class="ac-tot"><td></td><td>إجمالي {{ $s['label'] }}</td><td class="ac-num">{{ $m($s['total']) }}</td></tr>
        @endforeach
        </tbody>
        <tfoot><tr class="ac-grand"><td></td><td>{{ $net >= 0 ? 'صافي الربح' : 'صافي الخسارة' }} (الإيرادات − المصروفات)</td><td class="ac-num">{{ $m($net) }}</td></tr></tfoot>
    </table>
</div></div>
@endsection
