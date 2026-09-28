@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
إقرار ضريبة القيمة المضافة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">إقرار ضريبة القيمة المضافة</h4></div>
    <div class="d-flex no-print" style="gap:8px">
        <a href="{{ url('VAT') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-receipt"></i> تقرير ضريبة المبيعات والمشتريات (القديم)</a>
        @include('transport_reports._actions')
    </div>
</div>
@endsection
@section('content')
@php $m = function ($v) { return number_format((float) $v, 2); }; @endphp
@include('accounting_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>إقرار ضريبة القيمة المضافة من {{ $from }} إلى {{ $to }}</div></div>
@include('accounting_reports._filter')

<div class="tr-kpis" style="--cols:3">
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-up-arrow-alt"></i>ضريبة المخرجات (المستحقة)</span><b>{{ $m($output) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-down-arrow-alt"></i>ضريبة المدخلات (القابلة للخصم) / تسويات</span><b>{{ $m($input) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#1E3A8A"><span><i class="bx bx-wallet"></i>{{ $net >= 0 ? 'صافي الضريبة المستحقة للسداد' : 'رصيد ضريبة مسترد' }}</span><b>{{ $m(abs($net)) }} <small>ر.س</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px">ملخص المبيعات للإقرار</div>
    <table class="table table-bordered tr-table mb-0" style="max-width:900px">
        <thead><tr><th>البند</th><th>المبلغ الخاضع</th><th>الضريبة</th></tr></thead>
        <tbody>
            <tr><td>مبيعات النقل الخاضعة للنسبة الأساسية (فواتير النقل)</td><td class="ac-num">{{ $m($transport['std_taxable']) }}</td><td class="ac-num">{{ $m($transport['std_vat']) }}</td></tr>
            <tr><td>مبيعات النقل الخاضعة للنسبة الصفرية</td><td class="ac-num">{{ $m($transport['zero_taxable']) }}</td><td class="ac-num">0.00</td></tr>
            <tr><td>فواتير المبيعات الأخرى في النظام ({{ $sales['count'] }} فاتورة — الإجمالي شامل الضريبة)</td><td class="ac-num">{{ $m($sales['total']) }}</td><td class="ac-num">—</td></tr>
        </tbody>
    </table>
    <div class="text-muted mt-2" style="font-size:12px">الأرقام الأساسية للإقرار (المخرجات والمدخلات) من حركة حساب «ضريبة القيمة المضافة» في شجرة الحسابات، فبتشمل فواتير النقل وفواتير المبيعات والمشتريات وسندات الصرف اللي فيها ضريبة.</div>
</div></div>

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px">حركة حساب ضريبة القيمة المضافة ({{ $moves->count() }} حركة)</div>
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>التاريخ</th><th>البيان</th><th>العميل / المورد</th><th>مدين (مدخلات)</th><th>دائن (مخرجات)</th></tr></thead>
            <tbody>
                @forelse ($moves as $mv)
                    <tr><td style="white-space:nowrap">{{ substr($mv->created_at, 0, 10) }}</td><td>{{ $mv->note }}</td><td>{{ $mv->name }}</td>
                        <td class="ac-num">{{ (float) $mv->debtor ? $m($mv->debtor) : '' }}</td><td class="ac-num">{{ (float) $mv->creditor ? $m($mv->creditor) : '' }}</td></tr>
                @empty <tr><td colspan="5" class="tr-empty">لا توجد حركات ضريبة في الفترة دي</td></tr> @endforelse
            </tbody>
            <tfoot><tr><td colspan="3">الإجمالي</td><td class="ac-num">{{ $m($input) }}</td><td class="ac-num">{{ $m($output) }}</td></tr></tfoot>
        </table>
    </div>
</div></div>
@endsection
