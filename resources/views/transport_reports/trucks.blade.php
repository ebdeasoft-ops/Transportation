@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير الشاحنات
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير أداء الشاحنات (الإيراد والمصروفات والربح)</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php
    $m = function ($v) { return number_format((float) $v, 2); };
    $own = request('ownership');
    $extra = '<div class="col-lg-2 col-md-4 mb-2"><label>الملكية</label><select name="ownership" class="form-control"><option value="">الكل</option>'
        . '<option value="own"' . ($own == 'own' ? ' selected' : '') . '>خاص بالمؤسسة</option>'
        . '<option value="external"' . ($own == 'external' ? ' selected' : '') . '>إيجار خارجي</option></select></div>'
        . '<div class="col-lg-2 col-md-4 mb-2 d-flex align-items-end"><label class="mb-2" style="font-weight:700"><input type="checkbox" name="active_only" value="1"' . (request('active_only') ? ' checked' : '') . '> اللي اشتغلت بس</label></div>';
    $maxR = max(1, (float) $rows->max('revenue'));
@endphp
@include('transport_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير الشاحنات من {{ $from }} إلى {{ $to }}</div></div>
@include('transport_reports._filter', ['extra' => $extra])

<div class="tr-kpis" style="--cols:6">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bxs-truck"></i>شاحنات اشتغلت</span><b>{{ $rows->where('trips', '>', 0)->count() }} <small>/ {{ $rows->count() }}</small></b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-package"></i>إجمالي الرحلات</span><b>{{ $total['trips'] }}</b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-money"></i>الإيراد</span><b>{{ $m($total['revenue']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-wallet"></i>المصروفات</span><b>{{ $m($total['expenses']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:{{ $total['profit'] >= 0 ? '#059669' : '#DC2626' }}"><span><i class="bx bx-trending-up"></i>صافي الربح</span><b>{{ $m($total['profit']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#14B8A6"><span><i class="bx bx-timer"></i>متوسط مدة الرحلة</span><b>{{ $total['avg_hours'] }} <small>ساعة</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>رقم اللوحة</th><th>الملكية</th><th>الرحلات</th><th>تم التفريغ</th><th>متأخرة</th><th>الإيراد</th><th style="width:12%"></th><th>المصروفات</th><th>صافي الربح</th><th>المفوتر</th><th>متوسط الرحلة</th><th>الحالة الآن</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    @php $t = $r['truck']; @endphp
                    <tr style="{{ $r['trips'] ? '' : 'opacity:.6' }}">
                        <td class="tr-num" style="font-weight:800;text-align:center">{{ $t->plate_number }}</td>
                        <td><span class="tr-tag {{ $t->ownership == 'own' ? 'blue' : ($t->ownership == 'external' ? 'purple' : 'gray') }}">{{ \App\Models\truck_trip::ownershipLabel($t->ownership) }}</span></td>
                        <td style="font-weight:800">{{ $r['trips'] }}</td>
                        <td>{{ $r['done'] }}</td>
                        <td>@if ($r['late'])<span class="tr-tag late">{{ $r['late'] }}</span>@else 0 @endif</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($r['revenue']) }}</td>
                        <td><div class="tr-bar"><span style="width:{{ round($r['revenue'] / $maxR * 100) }}%"></span></div></td>
                        <td class="tr-num"><a href="{{ url('trucks/expenses') }}?truck_id={{ $t->id }}&start_at={{ $from }}&end_at={{ $to }}" style="color:#B91C1C">{{ $m($r['expenses']) }}</a></td>
                        <td class="tr-num" style="font-weight:800;color:{{ $r['profit'] >= 0 ? '#047857' : '#B91C1C' }}">{{ $m($r['profit']) }}</td>
                        <td class="tr-num">{{ $m($r['invoiced']) }}</td>
                        <td>{{ $r['avg_hours'] }} س</td>
                        <td>@if ($t->activeTrip)<span class="tr-tag on">محمّلة → {{ $t->activeTrip->to_region }}</span>@else<span class="tr-tag ok">فاضية{{ $t->current_region ? ' - ' . $t->current_region : '' }}</span>@endif</td>
                    </tr>
                @empty <tr><td colspan="12" class="tr-empty">لا توجد شاحنات</td></tr> @endforelse
            </tbody>
            @if ($rows->count())
            <tfoot><tr><td colspan="2">الإجمالي</td><td>{{ $total['trips'] }}</td><td>{{ $total['done'] }}</td><td>{{ $total['late'] }}</td><td class="tr-num">{{ $m($total['revenue']) }}</td><td></td><td class="tr-num">{{ $m($total['expenses']) }}</td><td class="tr-num">{{ $m($total['profit']) }}</td><td class="tr-num">{{ $m($total['invoiced']) }}</td><td>{{ $total['avg_hours'] }} س</td><td></td></tr></tfoot>
            @endif
        </table>
    </div>
</div></div>
@endsection
