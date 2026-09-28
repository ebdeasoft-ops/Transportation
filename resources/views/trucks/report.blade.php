@extends('layouts.master')
@section('css')
<style>
.tr-kpis{ display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
.tr-money{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
.tr-att{ display:inline-flex; align-items:center; gap:3px; font-weight:700; font-size:12px; }
.tr-own{ font-size:11.5px; font-weight:800; padding:3px 9px; border-radius:999px; white-space:nowrap; }
.tr-own.own{ background:#DBEAFE; color:#1D4ED8; } .tr-own.external{ background:#EDE9FE; color:#6D28D9; } .tr-own.none{ background:#F1F5F9; color:#94A3B8; }
@media (max-width:767px){ .tr-money{ grid-template-columns:1fr; } }
.tr-kpi{ background:#fff; border:1px solid #E6EBF2; border-radius:14px; padding:14px; box-shadow:0 8px 24px -16px rgba(15,23,42,.2); }
.tr-kpi span{ font-size:12px; color:#475569; font-weight:700; display:flex; align-items:center; gap:6px; }
.tr-kpi span i{ color:var(--c); font-size:17px; }
.tr-kpi b{ display:block; font-size:24px; font-weight:800; margin-top:4px; color:#0F172A; }
.tr-two{ display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.tr-list{ list-style:none; padding:0; margin:0; }
.tr-list li{ display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #F0F3F8; font-size:13.5px; }
.tr-list li:last-child{ border-bottom:none; }
.tr-list .nm{ flex:1; font-weight:700; }
.tr-list .br{ flex:1.2; height:8px; background:#EEF2F7; border-radius:99px; overflow:hidden; }
.tr-list .br span{ display:block; height:100%; background:linear-gradient(90deg,#2F6FED,#60A5FA); border-radius:99px; }
.tr-list .ct{ width:36px; text-align:center; font-weight:800; }
.tr-tag{ display:inline-flex; align-items:center; gap:4px; font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:999px; white-space:nowrap; }
.tr-tag.ok{ background:#D1FAE5; color:#047857; } .tr-tag.on{ background:#FEF3C7; color:#B45309; } .tr-tag.late{ background:#FEE2E2; color:#B91C1C; }
.tr-print-head{ display:none; }
@media (max-width:1199px){ .tr-kpis{ grid-template-columns:repeat(3,minmax(0,1fr)); } }
@media (max-width:767px){ .tr-kpis{ grid-template-columns:1fr 1fr; } .tr-two{ grid-template-columns:1fr; } }
@media print{
    .app-sidebar, .tr-filter, .no-print{ display:none !important; }
    .main-content, .app-content{ margin:0 !important; }
    .tr-print-head{ display:block; text-align:center; margin-bottom:10px; }
    .tr-kpis{ grid-template-columns:repeat(6,1fr); }
    .tr-kpi, .card{ box-shadow:none !important; }
}
</style>
@endsection
@section('title')
تقرير الأحمال
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير الأحمال</h4></div>
    <div class="d-flex no-print" style="gap:8px">
        <a href="#" class="btn btn-warning btn-sm js-qload"><i class="bx bx-package"></i> شحنة جديدة</a>
        <a href="{{ url('trucks/board') }}" class="btn btn-secondary btn-sm"><i class="bx bxs-truck"></i> لوحة الشاحنات</a>
        <a class="btn btn-success btn-sm" href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['export' => 'csv'])) }}"><i class="bx bx-spreadsheet"></i> تصدير Excel</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button>
    </div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $fmt = function ($d) { return $d ? $d->format('Y/m/d h:i A') : '—'; };
    $maxRoute = max(1, (int) $routes->max('count'));
    $maxType  = max(1, (int) $types->max('count'));
@endphp

<div class="tr-print-head">
    <h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4>
    <div>تقرير الأحمال من {{ $from }} إلى {{ $to }}</div>
</div>

{{-- الفلاتر --}}
<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url($lp . '/trucks/report') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ $from }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ $to }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>الشاحنة</label>
                <select name="truck_id" class="form-control"><option value="">الكل</option>@foreach ($trucks as $t)<option value="{{ $t->id }}" {{ request('truck_id') == $t->id ? 'selected' : '' }}>{{ $t->plate_number }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>من منطقة</label>
                <select name="from_region" class="form-control"><option value="">الكل</option>@foreach ($regions as $r)<option value="{{ $r }}" {{ request('from_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى منطقة</label>
                <select name="to_region" class="form-control"><option value="">الكل</option>@foreach ($regions as $r)<option value="{{ $r }}" {{ request('to_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
            <div class="col-lg-1 col-md-4 mb-2"><label>الحالة</label>
                <select name="status" class="form-control"><option value="">الكل</option><option value="1" {{ request('status') == '1' ? 'selected' : '' }}>محمّلة</option><option value="2" {{ request('status') == '2' ? 'selected' : '' }}>تم التفريغ</option></select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>الملكية</label>
                <select name="ownership" class="form-control"><option value="">الكل</option><option value="own" {{ request('ownership') == 'own' ? 'selected' : '' }}>خاص بالمؤسسة</option><option value="external" {{ request('ownership') == 'external' ? 'selected' : '' }}>إيجار خارجي</option></select></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>اسم الشركة (العميل)</label>
                <select name="customer_account_id" class="form-control"><option value="">الكل</option>@foreach ($customers as $c)<option value="{{ $c->id }}" {{ request('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
        </div>
    </form>
</div></div>

{{-- الملخص --}}
<div class="tr-kpis">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-list-ul"></i>عدد الأحمال</span><b>{{ $summary['total'] }}</b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-package"></i>محمّلة الآن</span><b>{{ $summary['loaded'] }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-check-double"></i>تم التفريغ</span><b>{{ $summary['done'] }}</b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-error"></i>متأخرة حالياً</span><b>{{ $summary['overdue'] }}</b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-time-five"></i>اتفرغت متأخر</span><b>{{ $summary['late'] }}</b></div>
    <div class="tr-kpi" style="--c:#14B8A6"><span><i class="bx bx-timer"></i>متوسط مدة الرحلة</span><b>{{ $summary['avg_hours'] }} <small style="font-size:12px">ساعة</small></b></div>
</div>

<div class="tr-money">
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>إجمالي السعر</span><b>{{ number_format($summary['price'], 2) }} <small style="font-size:12px">ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-buildings"></i>شاحنات المؤسسة</span><b>{{ number_format($summary['price_own'], 2) }} <small style="font-size:12px">ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-transfer"></i>إيجار خارجي</span><b>{{ number_format($summary['price_ext'], 2) }} <small style="font-size:12px">ر.س</small></b></div>
</div>

<div class="tr-two">
    <div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أكثر المسارات</div>
        <ul class="tr-list">
            @forelse ($routes as $r)
                <li><span class="nm">{{ $r['from'] }} <i class="bx bx-left-arrow-alt"></i> {{ $r['to'] }}</span><span class="br"><span style="width:{{ round($r['count'] / $maxRoute * 100) }}%"></span></span><span class="ct">{{ $r['count'] }}</span></li>
            @empty <li style="color:#94A3B8">لا توجد بيانات</li> @endforelse
        </ul>
    </div></div>
    <div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أنواع التحميل</div>
        <ul class="tr-list">
            @forelse ($types as $r)
                <li><span class="nm">{{ $r['type'] }}</span><span class="br"><span style="width:{{ round($r['count'] / $maxType * 100) }}%"></span></span><span class="ct">{{ $r['count'] }}</span></li>
            @empty <li style="color:#94A3B8">لا توجد بيانات</li> @endforelse
        </ul>
    </div></div>
</div>

{{-- التفاصيل --}}
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr>
                <th>التاريخ</th><th>اسم السائق</th><th>رقم اللوحة</th><th>ايجار خارجي / خاص</th><th>من</th><th>الى</th>
                <th>رقم الفاتورة</th><th>مرجع</th><th>السعر</th><th>اسم الشركة</th><th>المرفق</th>
                <th>نوع التحميل</th><th>التنزيل المتوقع</th><th>التنزيل الفعلي</th><th>الحالة</th><th class="no-print"></th>
            </tr></thead>
            <tbody>
                @forelse ($trips as $tr)
                    @php $late = $tr->status == 2 && $tr->unloaded_at && $tr->expected_unloading_at && $tr->unloaded_at->gt($tr->expected_unloading_at); @endphp
                    <tr>
                        <td style="white-space:nowrap">{{ $fmt($tr->loading_at) }}</td>
                        <td>{{ $tr->driver_name ?: '—' }}@if (optional($tr->driver)->phone)<br><a href="tel:{{ preg_replace('/[^\d+]/', '', $tr->driver->phone) }}" dir="ltr" style="font-size:12px;font-weight:700">{{ $tr->driver->phone }}</a>@endif</td>
                        <td style="direction:ltr;text-align:center;font-weight:700;white-space:nowrap">{{ optional($tr->truck)->plate_number }}</td>
                        <td><span class="tr-own {{ $tr->ownership ?: 'none' }}">{{ \App\Models\truck_trip::ownershipLabel($tr->ownership) }}</span></td>
                        <td>{{ $tr->from_region }}{{ $tr->from_city ? ' - ' . $tr->from_city : '' }}</td>
                        <td>{{ $tr->to_region }}{{ $tr->to_city ? ' - ' . $tr->to_city : '' }}</td>
                        <td>{{ $tr->invoice_number ?: '—' }}</td>
                        <td>{{ $tr->reference_no ?: '—' }}</td>
                        <td style="font-weight:800;white-space:nowrap">{{ $tr->price > 0 ? number_format($tr->price, 2) : '—' }}</td>
                        <td>{{ $tr->customer_name ?: '—' }}</td>
                        <td style="white-space:nowrap">@if ($tr->attachment)<a class="tr-att" href="{{ asset('assets/uploads/truck_trips/' . $tr->attachment) }}" target="_blank"><i class="bx bx-paperclip"></i> الحمولة</a>@endif
                            @if ($tr->unload_attachment)@if ($tr->attachment)<br>@endif<a class="tr-att" style="color:#059669" href="{{ asset('assets/uploads/truck_trips/' . $tr->unload_attachment) }}" target="_blank"><i class="bx bx-check-double"></i> التفريغ</a>@endif
                            @if (!$tr->attachment && !$tr->unload_attachment) — @endif</td>
                        <td>{{ $tr->load_type }}{{ $tr->load_weight ? ' (' . $tr->load_weight . ')' : '' }}</td>
                        <td style="white-space:nowrap">{{ $fmt($tr->expected_unloading_at) }}</td>
                        <td style="white-space:nowrap">{{ $fmt($tr->unloaded_at) }}</td>
                        <td>
                            @if ($tr->status == 1)
                                @if ($tr->is_overdue)<span class="tr-tag late"><i class="bx bx-error"></i> محمّلة - متأخرة</span>
                                @else<span class="tr-tag on"><i class="bx bx-package"></i> محمّلة</span>@endif
                            @elseif ($late)<span class="tr-tag late"><i class="bx bx-time-five"></i> اتفرغت متأخر</span>
                            @else<span class="tr-tag ok"><i class="bx bx-check"></i> تم التفريغ</span>@endif
                        </td>
                        <td class="no-print" style="white-space:nowrap">@if ($tr->status == 1)<button type="button" class="qu-btn js-qunload" data-trip="{{ $tr->id }}" data-plate="{{ optional($tr->truck)->plate_number }}" data-to="{{ $tr->to_region }}" data-city="{{ $tr->to_city }}" title="تم التفريغ"><i class="bx bx-check-double"></i> تم التفريغ</button> @endif<a class="btn btn-sm btn-secondary" href="{{ url($lp . '/trucks/trips/' . $tr->id . '/edit') }}" title="تعديل"><i class="bx bx-edit"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="16" style="text-align:center;color:#94A3B8">لا توجد أحمال في الفترة دي</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@include('trucks._unload_quick')
@include('trucks._load_quick')
@endsection
