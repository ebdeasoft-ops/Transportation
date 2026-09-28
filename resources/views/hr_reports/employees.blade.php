@extends('layouts.master')
@section('css')
@include('transport_reports._style')
@endsection
@section('title')
تقرير الموظفين والرواتب
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تقرير الموظفين والرواتب</h4></div>
    @include('transport_reports._actions')
</div>
@endsection
@section('content')
@php $m = function ($v) { return number_format((float) $v, 2); }; $maxD = max(1, (float) $byDept->max('total')); @endphp
@include('hr_reports._tabs')
<div class="tr-print-head"><h4 style="margin:0">{{ defined('Namear') ? Namear : '' }}</h4><div>تقرير الموظفين والرواتب</div></div>

<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}"><div class="row">
        <div class="col-lg-3 col-md-4 mb-2"><label>القسم</label>
            <select name="department" class="form-control"><option value="">الكل</option>@foreach ($depts as $id => $n)<option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $n }}</option>@endforeach</select></div>
        <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
    </div></form>
</div></div>

<div class="tr-kpis" style="--cols:5">
    <div class="tr-kpi" style="--c:#2F6FED"><span><i class="bx bx-group"></i>عدد الموظفين</span><b>{{ $summary['count'] }}</b></div>
    <div class="tr-kpi" style="--c:#0EA5E9"><span><i class="bx bx-money"></i>الرواتب الأساسية</span><b>{{ $m($summary['basic']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#8B5CF6"><span><i class="bx bx-plus-circle"></i>البدلات</span><b>{{ $m($summary['allow']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#10B981"><span><i class="bx bx-wallet"></i>إجمالي الرواتب الشهرية</span><b>{{ $m($summary['total']) }} <small>ر.س</small></b></div>
    <div class="tr-kpi" style="--c:#EF4444"><span><i class="bx bx-bell"></i>تنبيهات وثائق / عهد</span><b>{{ $summary['alerts'] }} <small>/ {{ $summary['custody'] }} عهدة</small></b></div>
</div>

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px">حسب القسم</div>
    <table class="table tr-table mb-0"><tbody>
        @forelse ($byDept as $d)
            <tr><td style="font-weight:700">{{ $d['name'] }}</td><td>{{ $d['count'] }} موظف</td><td style="width:35%"><div class="tr-bar"><span style="width:{{ round($d['total'] / $maxD * 100) }}%"></span></div></td><td class="tr-num" style="font-weight:800">{{ $m($d['total']) }}</td></tr>
        @empty <tr><td class="tr-empty">لا يوجد موظفين</td></tr> @endforelse
    </tbody></table>
</div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover tr-table">
            <thead><tr><th>الموظف</th><th>القسم</th><th>الجنسية</th><th>رقم الهوية</th><th>الأساسي</th><th>البدلات</th><th>الإجمالي</th><th>نهاية العقد</th><th>تنبيهات</th><th>عهد</th></tr></thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['emp']->name_ar }}</td>
                        <td>{{ $r['dept'] }}</td>
                        <td>{{ $r['emp']->nationality }}</td>
                        <td class="tr-num">{{ $r['emp']->personal_identification }}</td>
                        <td class="tr-num">{{ $m($r['basic']) }}</td>
                        <td class="tr-num">{{ $m($r['allow']) }}</td>
                        <td class="tr-num" style="font-weight:800">{{ $m($r['total']) }}</td>
                        <td>{{ optional($r['contract'])->end_date ?: '—' }}</td>
                        <td>@foreach ($r['alerts'] as $a)<span class="tr-tag {{ str_contains($a, 'منتهية') ? 'late' : 'on' }}">{{ $a }}</span> @endforeach</td>
                        <td>{{ $r['custody'] ?: '—' }}</td>
                    </tr>
                @empty <tr><td colspan="10" class="tr-empty">لا يوجد موظفين</td></tr> @endforelse
            </tbody>
            @if ($rows->count())<tfoot><tr><td colspan="4">الإجمالي</td><td class="tr-num">{{ $m($summary['basic']) }}</td><td class="tr-num">{{ $m($summary['allow']) }}</td><td class="tr-num">{{ $m($summary['total']) }}</td><td colspan="3"></td></tr></tfoot>@endif
        </table>
    </div>
</div></div>
@endsection
