@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
كشف حساب {{ $account->name }}
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">كشف حساب عميل: {{ $account->name }}</h4></div>
    <div class="d-flex no-print" style="gap:8px">
        <a href="{{ url('transport-invoices/create') }}?customer_account_id={{ $account->id }}" class="btn btn-success btn-sm"><i class="bx bx-receipt"></i> فاتورة جديدة</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button>
    </div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
    $opts = '';
    foreach ($customers as $c) $opts .= '<option value="' . $c->id . '"' . ($account->id == $c->id ? ' selected' : '') . '>' . e($c->name) . '</option>';
    $extra = '<div class="col-lg-3 col-md-4 mb-2"><label>العميل</label><select class="form-control" onchange="location.href=\'' . url($lp . '/transport-reports/customers') . '/\'+this.value+\'?start_at=' . $from . '&end_at=' . $to . '\'">' . $opts . '</select></div>';
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>كشف حساب {{ $account->name }} ({{ $account->account_number }}) من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#64748B"><span><i class="bx bx-history"></i>رصيد أول المدة</span><b>{{ $m($opening) }}</b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-up-arrow-alt"></i>مدين (فواتير)</span><b>{{ $m($totals['debit']) }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-down-arrow-alt"></i>دائن (تحصيل / إلغاء)</span><b>{{ $m($totals['credit']) }}</b></div>
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-wallet"></i>الرصيد آخر المدة</span><b>{{ $m($totals['closing']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-error-circle"></i>أحمال غير مفوترة</span><b>{{ $unbilled->count() }} <small>({{ $m($unbilled->sum('price')) }})</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px">حركة الحساب</div>
    <div class="table-responsive">
        <table class="table tr-table">
            <thead><tr><th>التاريخ</th><th>البيان</th><th>مدين</th><th>دائن</th><th>الرصيد</th></tr></thead>
            <tbody>
                <tr style="background:#F8FAFC"><td>{{ $from }}</td><td style="font-weight:700">رصيد أول المدة</td><td></td><td></td><td class="tr-num" style="font-weight:800">{{ $m($opening) }}</td></tr>
                @forelse ($lines as $l)
                    <tr>
                        <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($l['date'])->format('Y/m/d') }}</td>
                        <td>{{ $l['note'] }}</td>
                        <td class="tr-num">{{ $l['debit'] ? $m($l['debit']) : '' }}</td>
                        <td class="tr-num">{{ $l['credit'] ? $m($l['credit']) : '' }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($l['balance']) }}</td>
                    </tr>
                @empty <tr><td colspan="5" class="tr-empty">لا توجد حركات في الفترة دي</td></tr> @endforelse
            </tbody>
            <tfoot><tr><td colspan="2">الإجمالي</td><td class="tr-num">{{ $m($totals['debit']) }}</td><td class="tr-num">{{ $m($totals['credit']) }}</td><td class="tr-num">{{ $m($totals['closing']) }}</td></tr></tfoot>
        </table>
    </div>
</div></div>

<div class="row">
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <div style="font-weight:800;margin-bottom:8px">فواتير النقل في الفترة</div>
            <table class="table tr-table mb-0">
                <thead><tr><th>رقم</th><th>التاريخ</th><th>الضريبة</th><th>الإجمالي</th><th>الحالة</th></tr></thead>
                <tbody>
                    @forelse ($invoices as $inv)
                        <tr><td><a href="{{ url($lp . '/transport-invoices/' . $inv->id) }}" style="font-weight:800">#{{ $inv->invoice_no }}</a></td><td>{{ $inv->issue_date->format('Y/m/d') }}</td><td class="tr-num">{{ $m($inv->vat_amount) }}</td><td class="tr-num">{{ $m($inv->total) }}</td>
                            <td>@if ($inv->is_cancelled)<span class="tr-tag late">ملغاة</span>@else<span class="tr-tag ok">سارية</span>@endif</td></tr>
                    @empty <tr><td colspan="5" class="tr-empty">لا توجد فواتير</td></tr> @endforelse
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <div style="font-weight:800;margin-bottom:8px">أحمال لسه ما اتفوترتش (كل الفترات)</div>
            <table class="table tr-table mb-0">
                <thead><tr><th>التاريخ</th><th>اللوحة</th><th>المسار</th><th>السعر</th></tr></thead>
                <tbody>
                    @forelse ($unbilled as $t)
                        <tr><td>{{ optional($t->loading_at)->format('Y/m/d') }}</td><td class="tr-num">{{ optional($t->truck)->plate_number }}</td><td>{{ $t->from_region }} ← {{ $t->to_region }}</td><td class="tr-num">{{ $m($t->price) }}</td></tr>
                    @empty <tr><td colspan="4" class="tr-empty">كل الأحمال متفوترة</td></tr> @endforelse
                </tbody>
            </table>
        </div></div>
    </div>
</div>
@endsection
