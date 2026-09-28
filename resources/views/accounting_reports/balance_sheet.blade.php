@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
الميزانية العمومية
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">الميزانية العمومية (قائمة المركز المالي) حتى {{ $to }}</h4></div>
    <div class="d-flex no-print" style="gap:8px"><button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button></div>
</div>
@endsection
@section('content')
@php $m = function ($v) { return number_format((float) $v, 2); }; $from = null; $asOf = true; @endphp
@include('accounting_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>الميزانية العمومية حتى {{ $to }}</div></div>
@include('accounting_reports._filter')

<div class="tr-kpis" style="--cols:4">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-building"></i>إجمالي الأصول</span><b>{{ $m($sections[1]['total']) }}</b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-credit-card"></i>إجمالي الخصوم</span><b>{{ $m($sections[2]['total']) }}</b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-user-check"></i>حقوق الملكية + صافي الربح</span><b>{{ $m($sections[5]['total'] + $profit) }}</b></div>
    <div class="tr-kpi" style="--c:{{ abs($diff) < 0.01 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-balance"></i>الفرق</span><b>{{ $m($diff) }} <small>{{ abs($diff) < 0.01 ? 'متوازنة' : 'غير متوازنة' }}</small></b></div>
</div>

<div class="row">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <table class="table table-bordered tr-table mb-0">
            <thead><tr><th>رقم</th><th>الأصول</th><th>المبلغ</th></tr></thead>
            <tbody>
                @forelse ($sections[1]['rows'] as $r)
                    <tr class="ac-row ac-l{{ $r['level'] }}"><td class="ac-num">{{ $r['number'] }}</td><td style="padding-inline-start:{{ 8 + ($r['level'] - 1) * 18 }}px">{{ $r['name'] }}</td><td class="ac-num">{{ $m($r['amount']) }}</td></tr>
                @empty <tr><td colspan="3" class="tr-empty">لا توجد أرصدة</td></tr> @endforelse
            </tbody>
            <tfoot><tr class="ac-grand"><td colspan="2">إجمالي الأصول</td><td class="ac-num">{{ $m($sections[1]['total']) }}</td></tr></tfoot>
        </table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <table class="table table-bordered tr-table mb-0">
            <thead><tr><th>رقم</th><th>الخصوم وحقوق الملكية</th><th>المبلغ</th></tr></thead>
            <tbody>
                @foreach ([2, 5] as $t)
                    <tr class="ac-sec"><td colspan="3">{{ $sections[$t]['label'] }}</td></tr>
                    @forelse ($sections[$t]['rows'] as $r)
                        <tr class="ac-row ac-l{{ $r['level'] }}"><td class="ac-num">{{ $r['number'] }}</td><td style="padding-inline-start:{{ 8 + ($r['level'] - 1) * 18 }}px">{{ $r['name'] }}</td><td class="ac-num">{{ $m($r['amount']) }}</td></tr>
                    @empty <tr><td colspan="3" class="tr-empty">لا توجد أرصدة</td></tr> @endforelse
                    <tr class="ac-tot"><td></td><td>إجمالي {{ $sections[$t]['label'] }}</td><td class="ac-num">{{ $m($sections[$t]['total']) }}</td></tr>
                @endforeach
                <tr class="ac-tot"><td></td><td>صافي {{ $profit >= 0 ? 'ربح' : 'خسارة' }} الفترة (الإيرادات {{ $m($rev) }} − المصروفات {{ $m($exp) }})</td><td class="ac-num">{{ $m($profit) }}</td></tr>
            </tbody>
            <tfoot><tr class="ac-grand"><td colspan="2">إجمالي الخصوم وحقوق الملكية</td><td class="ac-num">{{ $m($liabEq) }}</td></tr></tfoot>
        </table>
    </div></div></div>
</div>
@if (abs($diff) >= 0.01)
    <div class="alert alert-warning">الميزانية فيها فرق {{ $m($diff) }} — ده غالباً بسبب قيود متسجلة من طرف واحد أو حسابات مالهاش نوع في الشجرة. راجع «ميزان المراجعة» وشوف فرق الحركة.</div>
@endif
@endsection
