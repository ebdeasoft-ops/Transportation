@extends('layouts.master')
@section('css')
@include('transport_reports._style')
<style>
.ub-group{ border:1px solid #E6EBF2; border-radius:14px; margin-bottom:14px; overflow:hidden; background:#fff; }
.ub-head{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; padding:12px 16px; background:#F8FAFC; border-bottom:1px solid #E6EBF2; }
.ub-head b{ font-size:15px; }
.ub-head .sp{ flex:1; }
</style>
@endsection
@section('title')
الأحمال غير المفوترة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">الأحمال غير المفوترة</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $m = function ($v) { return number_format((float) $v, 2); };
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>الأحمال غير المفوترة</div></div>

<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}">
        <div class="row">
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ request('start_at') }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ request('end_at') }}"></div>
            <div class="col-lg-3 col-md-4 mb-2"><label>العميل</label>
                <select name="customer_account_id" class="form-control"><option value="">الكل</option>@foreach ($customers as $c)<option value="{{ $c->id }}" {{ request('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>الحالة</label>
                <select name="status" class="form-control">
                    <option value="2" {{ $status == '2' ? 'selected' : '' }}>تم التفريغ (جاهزة للفوترة)</option>
                    <option value="1" {{ $status == '1' ? 'selected' : '' }}>محمّلة</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>الكل</option>
                </select></div>
            <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
        </div>
    </form>
</div></div>

<div class="tr-kpis" style="--cols:4">
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-package"></i>أحمال غير مفوترة</span><b>{{ $summary['count'] }}</b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-money"></i>قيمتها (قبل الضريبة)</span><b>{{ $m($summary['value']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-group"></i>عملاء</span><b>{{ $summary['customers'] }}</b></div>
    <div class="tr-kpi" style="--c:#64748B"><span><i class="bx bx-error"></i>من غير سعر</span><b>{{ $summary['zero'] }}</b></div>
</div>

@forelse ($groups as $g)
    <div class="ub-group">
        <div class="ub-head">
            <b>{{ $g['name'] }}</b>
            <span class="tr-tag on">{{ $g['count'] }} حمولة</span>
            <span class="tr-tag blue">{{ $m($g['value']) }} ر.س</span>
            @if ($g['zero'])<span class="tr-tag late">{{ $g['zero'] }} بدون سعر</span>@endif
            @if ($g['oldest'])<span class="tr-tag gray">أقدمها {{ \Carbon\Carbon::parse($g['oldest'])->diffForHumans() }}</span>@endif
            <span class="sp"></span>
            @if ($g['id'])
                <a href="{{ url($lp . '/transport-invoices/create') }}?customer_account_id={{ $g['id'] }}{{ $status == 'all' || $status == '1' ? '&include_loaded=1' : '' }}" class="btn btn-sm btn-success no-print"><i class="bx bx-receipt"></i> اعمل فاتورة</a>
            @else
                <span class="text-muted no-print" style="font-size:12px">اربط الأحمال دي بعميل من «تعديل الشحنة» عشان تتفوتر</span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover tr-table mb-0">
                <thead><tr><th>التاريخ</th><th>اللوحة</th><th>السائق</th><th>من</th><th>إلى</th><th>نوع التحميل</th><th>الحالة</th><th>السعر</th><th class="no-print"></th></tr></thead>
                <tbody>
                    @foreach ($g['trips'] as $t)
                        <tr>
                            <td style="white-space:nowrap">{{ optional($t->loading_at)->format('Y/m/d') }}</td>
                            <td class="tr-num" style="font-weight:700">{{ optional($t->truck)->plate_number }}</td>
                            <td>{{ $t->driver_name ?: '—' }}</td>
                            <td>{{ $t->from_region }}{{ $t->from_city ? ' - ' . $t->from_city : '' }}</td>
                            <td>{{ $t->to_region }}{{ $t->to_city ? ' - ' . $t->to_city : '' }}</td>
                            <td>{{ $t->load_type }}</td>
                            <td>@if ($t->status == 2)<span class="tr-tag ok">تم التفريغ</span>@else<span class="tr-tag on">محمّلة</span>@endif</td>
                            <td class="tr-num" style="font-weight:800">{{ (float) $t->price ? $m($t->price) : '—' }}</td>
                            <td class="no-print"><a href="{{ url($lp . '/trucks/trips/' . $t->id . '/edit') }}" class="btn btn-sm btn-light"><i class="bx bx-edit"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="card"><div class="card-body tr-empty"><i class="bx bx-check-circle" style="font-size:40px;color:#10B981"></i><div>مفيش أحمال غير مفوترة</div></div></div>
@endforelse
@endsection
