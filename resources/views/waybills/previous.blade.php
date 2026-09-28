@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .wb-list thead th { background: #2b2f8f !important; color: #fff !important; font-size: 13px; white-space: nowrap; text-align: center; }
        .wb-list td { text-align: center; vertical-align: middle !important; font-size: 13.5px; }
        .wb-no { color: #c0392b; font-weight: 800; }
        .wb-filter label { font-weight: 700; font-size: 13px; }
        .wb-actions-cell { white-space: nowrap; }
        .wb-actions-cell .btn { padding: 3px 9px; font-size: 12px; }
        .wb-sum { font-weight: 800; font-size: 15px; }
    </style>
@endsection
@section('title')
    بوليصات الشحن السابقة
@stop
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0 my-auto">بوليصات الشحن السابقة</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ url('waybills/create') }}" class="btn btn-primary btn-sm">+ بوليصة جديدة</a>
        </div>
    </div>
@endsection
@section('content')
@php $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(); @endphp

    @if (session()->has('waybill_deleted'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('waybill_deleted') }}</strong>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ url($lp . '/waybills') }}" class="wb-filter">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label>بحث (رقم / عميل / سائق / لوحة / مدينة)</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <label>العميل</label>
                        <select name="customer_id" class="form-control wb-select">
                            <option value="">الكل</option>
                            @foreach ($customers as $c)<option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <label>السائق</label>
                        <select name="driver_id" class="form-control wb-select">
                            <option value="">الكل</option>
                            @foreach ($drivers as $d)<option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <label>الشاحنة</label>
                        <select name="truck_id" class="form-control wb-select">
                            <option value="">الكل</option>
                            @foreach ($trucks as $t)<option value="{{ $t->id }}" {{ request('truck_id') == $t->id ? 'selected' : '' }}>{{ $t->plate_number }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-6 mb-2">
                        <label>من</label>
                        <input type="date" name="start_at" class="form-control" value="{{ request('start_at') }}">
                    </div>
                    <div class="col-lg-1 col-md-6 mb-2">
                        <label>إلى</label>
                        <input type="date" name="end_at" class="form-control" value="{{ request('end_at') }}">
                    </div>
                    <div class="col-lg-1 col-md-12 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-block">بحث</button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center my-2">
                <span>عدد البوليصات: <b>{{ $waybills->total() }}</b></span>
                <span class="wb-sum">إجمالي الأجرة: {{ number_format($totalFare, 2) }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover wb-list">
                    <thead>
                        <tr>
                            <th>رقم البوليصة</th>
                            <th>التاريخ</th>
                            <th>الهجري</th>
                            <th>العميل</th>
                            <th>المدينة</th>
                            <th>السائق</th>
                            <th>رقم السيارة</th>
                            <th>عدد الأصناف</th>
                            <th>إجمالي الأجرة</th>
                            <th>المستخدم</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waybills as $w)
                            <tr>
                                <td class="wb-no">{{ $w->waybill_no }}</td>
                                <td>{{ $w->date ? \Carbon\Carbon::parse($w->date)->format('Y/m/d') : '' }}</td>
                                <td>{{ $w->date_hijri }}</td>
                                <td>{{ $w->customer_name }}</td>
                                <td>{{ $w->destination_city }}</td>
                                <td>{{ $w->driver_name }}</td>
                                <td>{{ $w->plate_number }}</td>
                                <td>{{ $w->items_count }}</td>
                                <td>{{ number_format($w->total_fare, 2) }}</td>
                                <td>{{ optional($w->user)->name }}</td>
                                <td class="wb-actions-cell">
                                    <a href="{{ url('waybills/print/' . $w->id) }}" target="_blank" class="btn btn-info">طباعة</a>
                                    <a href="{{ url('waybills/edit/' . $w->id) }}" class="btn btn-warning">تعديل</a>
                                    <form method="post" action="{{ url($lp . '/waybills/delete/' . $w->id) }}" style="display:inline"
                                          onsubmit="return confirm('حذف البوليصة رقم {{ $w->waybill_no }} ؟');">
                                        {{ csrf_field() }}
                                        <button class="btn btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11">لا توجد بوليصات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $waybills->links() }}</div>
        </div>
    </div>
@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    if ($.fn.select2) { $('.wb-select').select2({ width: '100%', dir: 'rtl' }); }
</script>
@endsection
