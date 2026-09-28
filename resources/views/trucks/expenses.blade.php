@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@include('transport_reports._style')
@endsection
@section('title')
مصروفات الشاحنات
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">مصروفات الشاحنات</h4></div>
    <div class="d-flex no-print" style="gap:8px">
        <a href="{{ url('transport-reports/trucks') }}" class="btn btn-outline-primary btn-sm"><i class="bx bx-line-chart"></i> ربح كل شاحنة</a>
        @include('transport_reports._actions')
    </div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
    $maxT = max(1, (float) $byTruck->max('total'));
@endphp
@if (session('trip_ok'))<div class="alert alert-success">{{ session('trip_ok') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>مصروفات الشاحنات من {{ $from }} إلى {{ $to }}</div></div>

{{-- تسجيل مصروف --}}
<div class="card no-print"><div class="card-body">
    <div style="font-weight:800;margin-bottom:10px"><i class="bx bx-plus-circle" style="color:#2F6FED"></i> تسجيل مصروف على شاحنة</div>
    <form method="post" action="{{ url($lp . '/trucks/expenses') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="start_at" value="{{ $from }}"><input type="hidden" name="end_at" value="{{ $to }}">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-2"><label>الشاحنة *</label>
                <select name="truck_id" id="exTruck" class="form-control" required><option value="">اختار الشاحنة</option>
                    @foreach ($trucks as $t)<option value="{{ $t->id }}" {{ old('truck_id', request('truck_id')) == $t->id ? 'selected' : '' }}>{{ $t->plate_number }} ({{ \App\Models\truck_trip::ownershipLabel($t->ownership) }})</option>@endforeach
                </select></div>
            <div class="col-lg-2 col-md-6 mb-2"><label>التاريخ *</label><input type="date" name="expense_date" class="form-control" required value="{{ old('expense_date', date('Y-m-d')) }}"></div>
            <div class="col-lg-2 col-md-6 mb-2"><label>النوع *</label>
                <select name="type" class="form-control" required>@foreach ($types as $k => $l)<option value="{{ $k }}" {{ old('type') == $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-6 mb-2"><label>المبلغ *</label><input type="number" step="0.01" min="0.01" name="amount" class="form-control" dir="ltr" required value="{{ old('amount') }}"></div>
            <div class="col-lg-3 col-md-6 mb-2"><label>مدفوع من (ترحيل القيد)</label>
                <select name="pay_account_id" class="form-control">
                    <option value="">بدون ترحيل (تسجيل بس)</option>
                    @foreach ($payAccounts as $a)<option value="{{ $a->id }}" {{ old('pay_account_id') == $a->id ? 'selected' : '' }}>{{ $a->parent_account_number == 78 ? 'بنك: ' : 'خزينة: ' }}{{ $a->name }}</option>@endforeach
                </select></div>
            <div class="col-lg-4 col-md-6 mb-2"><label>البيان</label><input type="text" name="description" class="form-control" maxlength="255" value="{{ old('description') }}" placeholder="مثال: تغيير 4 كفرات / تعبئة ديزل"></div>
            <div class="col-lg-3 col-md-6 mb-2"><label>المورد / المحطة / الورشة</label><input type="text" name="vendor" class="form-control" maxlength="255" value="{{ old('vendor') }}"></div>
            <div class="col-lg-3 col-md-6 mb-2"><label>مرفق (فاتورة / صورة)</label><input type="file" name="attachment" class="form-control" accept=".pdf,image/*"></div>
            <div class="col-lg-2 col-md-6 mb-2 d-flex align-items-end"><button class="btn btn-success btn-block"><i class="bx bx-save"></i> حفظ المصروف</button></div>
        </div>
        <div class="text-muted" style="font-size:12px">لو اخترت «مدفوع من»: القيد بيترحّل تلقائياً — «مصروفات الشاحنات» مدين والخزينة/البنك دائن. الحذف بيعكس القيد.</div>
    </form>
</div></div>

{{-- الفلتر --}}
<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}">
        <div class="row">
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ $from }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ $to }}"></div>
            <div class="col-lg-3 col-md-4 mb-2"><label>الشاحنة</label>
                <select name="truck_id" class="form-control"><option value="">الكل</option>@foreach ($trucks as $t)<option value="{{ $t->id }}" {{ request('truck_id') == $t->id ? 'selected' : '' }}>{{ $t->plate_number }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>النوع</label>
                <select name="type" class="form-control"><option value="">الكل</option>@foreach ($types as $k => $l)<option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
        </div>
    </form>
</div></div>

<div class="tr-kpis" style="--cols:3">
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-wallet"></i>إجمالي المصروفات</span><b>{{ $m($total) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-receipt"></i>عدد المصروفات</span><b>{{ $expenses->count() }}</b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bxs-truck"></i>شاحنات عليها مصروفات</span><b>{{ $byTruck->count() }}</b></div>
</div>

<div class="row">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">مصروفات كل شاحنة</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($byTruck as $r)
                <tr><td class="tr-num" style="font-weight:800">{{ $r['plate'] }}</td><td>{{ $r['count'] }} مصروف</td><td style="width:35%"><div class="tr-bar"><span style="width:{{ round($r['total'] / $maxT * 100) }}%;background:linear-gradient(90deg,#EF4444,#F87171)"></span></div></td><td class="tr-num" style="font-weight:800">{{ $m($r['total']) }}</td></tr>
            @empty <tr><td class="tr-empty">لا توجد مصروفات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">حسب نوع المصروف</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($byType as $r)
                <tr><td style="font-weight:700">{{ $r['type'] }}</td><td class="tr-num">{{ $m($r['total']) }}</td><td style="width:20%" class="tr-num">{{ $total ? round($r['total'] / $total * 100) : 0 }}%</td></tr>
            @empty <tr><td class="tr-empty">لا توجد مصروفات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>التاريخ</th><th>الشاحنة</th><th>النوع</th><th>البيان</th><th>المورد</th><th>المبلغ</th><th>مدفوع من</th><th>المرفق</th><th>سجلها</th><th class="no-print"></th></tr></thead>
            <tbody>
                @forelse ($expenses as $e)
                    <tr>
                        <td style="white-space:nowrap">{{ $e->expense_date->format('Y/m/d') }}</td>
                        <td class="tr-num" style="font-weight:800">{{ optional($e->truck)->plate_number }}</td>
                        <td><span class="tr-tag gray">{{ \App\Models\truck_expense::typeLabel($e->type) }}</span></td>
                        <td>{{ $e->description ?: '—' }}</td>
                        <td>{{ $e->vendor ?: '—' }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($e->amount) }}</td>
                        <td>@if ($e->posted)<span class="tr-tag ok">{{ optional($e->payAccount)->name }}</span>@else<span class="tr-tag gray">بدون ترحيل</span>@endif</td>
                        <td>@if ($e->attachment)<a href="{{ asset('assets/uploads/truck_expenses/' . $e->attachment) }}" target="_blank"><i class="bx bx-paperclip"></i> عرض</a>@else — @endif</td>
                        <td>{{ optional($e->user)->name }}</td>
                        <td class="no-print">
                            <form method="post" action="{{ url($lp . '/trucks/expenses/' . $e->id . '/delete') }}" onsubmit="return confirm('حذف المصروف{{ $e->posted ? ' وعكس القيد' : '' }}؟')">@csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
                        </td>
                    </tr>
                @empty <tr><td colspan="10" class="tr-empty">لا توجد مصروفات في الفترة دي</td></tr> @endforelse
            </tbody>
            @if ($expenses->count())<tfoot><tr><td colspan="5">الإجمالي</td><td class="tr-num">{{ $m($total) }}</td><td colspan="4"></td></tr></tfoot>@endif
        </table>
    </div>
</div></div>
@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>if ($.fn.select2) $('#exTruck').select2({ width: '100%', dir: 'rtl', placeholder: 'اختار الشاحنة' });</script>
@endsection
