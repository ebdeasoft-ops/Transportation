@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير النقل الشامل
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير النقل الشامل</h4></div>
    <div class="d-flex no-print" style="gap:8px"><button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button></div>
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $maxM = max(1, (float) $months->max('revenue'), (float) $months->max('expenses'));
    $maxR = max(1, (int) $routes->max('count'));
    $maxTp = max(1, (int) $types->max('count'));
    $s = $summary;
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير النقل الشامل من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter')

<div class="tr-kpis" style="--cols:6">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-package"></i>الرحلات</span><b>{{ $s['trips'] }} <small>({{ $s['done'] }} اتفرغت)</small></b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>الإيراد (أسعار الأحمال)</span><b>{{ $m($s['revenue']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-wallet"></i>مصروفات الشاحنات</span><b>{{ $m($s['expenses']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:{{ $s['profit'] >= 0 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-trending-up"></i>صافي الربح</span><b>{{ $m($s['profit']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-receipt"></i>المفوتر ({{ $s['invoices'] }} فاتورة)</span><b>{{ $m($s['invoiced']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#F59E0B"><span><i class="bx bx-error-circle"></i>غير مفوتر</span><b>{{ $m($s['unbilled']) }} <small>ر.س</small></b></div>
</div>
<div class="tr-kpis" style="--cols:6">
    <div class="tr-kpi" style="--c:#1D4ED8"><span><i class="bx bx-buildings"></i>رحلات شاحنات المؤسسة</span><b>{{ $s['own_trips'] }} <small>({{ $m($s['own_revenue']) }})</small></b></div>
    <div class="tr-kpi" style="--c:#6D28D9"><span><i class="bx bx-transfer"></i>رحلات إيجار خارجي</span><b>{{ $s['ext_trips'] }} <small>({{ $m($s['ext_revenue']) }})</small></b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bxs-truck"></i>شاحنات اشتغلت</span><b>{{ $s['trucks'] }}</b></div>
    <div class="tr-kpi" style="--c:#14B8A6"><span><i class="bx bx-group"></i>عملاء</span><b>{{ $s['customers'] }}</b></div>
    <div class="tr-kpi" style="--c:#B91C1C"><span><i class="bx bx-time-five"></i>رحلات متأخرة</span><b>{{ $s['late'] }}</b></div>
    <div class="tr-kpi" style="--c:#64748B"><span><i class="bx bx-timer"></i>متوسط مدة الرحلة</span><b>{{ $s['avg_hours'] }} <small>ساعة</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px">الحركة الشهرية</div>
    <div class="table-responsive">
    <table class="table tr-table mb-0">
        <thead><tr><th>الشهر</th><th>الرحلات</th><th>الإيراد</th><th style="width:18%"></th><th>المصروفات</th><th style="width:18%"></th><th>صافي الربح</th><th>المفوتر</th></tr></thead>
        <tbody>
            @forelse ($months as $r)
                <tr>
                    <td style="font-weight:700">{{ $r['month'] }}</td><td>{{ $r['trips'] }}</td>
                    <td class="tr-num">{{ $m($r['revenue']) }}</td><td><div class="tr-bar"><span style="width:{{ round($r['revenue'] / $maxM * 100) }}%;background:linear-gradient(90deg,#10B981,#34D399)"></span></div></td>
                    <td class="tr-num">{{ $m($r['expenses']) }}</td><td><div class="tr-bar"><span style="width:{{ round($r['expenses'] / $maxM * 100) }}%;background:linear-gradient(90deg,#EF4444,#F87171)"></span></div></td>
                    <td class="tr-num" style="font-weight:800;color:{{ $r['revenue'] - $r['expenses'] >= 0 ? '#047857' : '#B91C1C' }}">{{ $m($r['revenue'] - $r['expenses']) }}</td>
                    <td class="tr-num">{{ $m($r['invoiced']) }}</td>
                </tr>
            @empty <tr><td colspan="8" class="tr-empty">لا توجد بيانات</td></tr> @endforelse
        </tbody>
    </table>
    </div>
</div></div>

<div class="row">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أكثر المسارات</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($routes as $r)
                <tr><td style="font-weight:700">{{ $r['name'] }}</td><td style="width:30%"><div class="tr-bar"><span style="width:{{ round($r['count'] / $maxR * 100) }}%"></span></div></td><td>{{ $r['count'] }}</td><td class="tr-num">{{ $m($r['revenue']) }}</td></tr>
            @empty <tr><td class="tr-empty">لا توجد بيانات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أنواع التحميل</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($types as $r)
                <tr><td style="font-weight:700">{{ $r['name'] }}</td><td style="width:30%"><div class="tr-bar"><span style="width:{{ round($r['count'] / $maxTp * 100) }}%"></span></div></td><td>{{ $r['count'] }}</td><td class="tr-num">{{ $m($r['revenue']) }}</td></tr>
            @empty <tr><td class="tr-empty">لا توجد بيانات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أكثر مناطق التحميل</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($regionsFrom as $k => $c)<tr><td style="font-weight:700">{{ $k }}</td><td>{{ $c }} رحلة</td></tr>
            @empty <tr><td class="tr-empty">لا توجد بيانات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div style="font-weight:800;margin-bottom:8px">أكثر مناطق التنزيل</div>
        <table class="table tr-table mb-0"><tbody>
            @forelse ($regionsTo as $k => $c)<tr><td style="font-weight:700">{{ $k }}</td><td>{{ $c }} رحلة</td></tr>
            @empty <tr><td class="tr-empty">لا توجد بيانات</td></tr> @endforelse
        </tbody></table>
    </div></div></div>
</div>
@endsection
