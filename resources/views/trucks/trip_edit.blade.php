@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
.te-head{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
.te-plate{ font-size:20px; font-weight:800; direction:ltr; }
.te-tag{ font-size:12px; font-weight:800; padding:4px 10px; border-radius:999px; }
.te-tag.loaded{ background:#FEF3C7; color:#B45309; } .te-tag.done{ background:#D1FAE5; color:#047857; }
.te-tag.own{ background:#DBEAFE; color:#1D4ED8; } .te-tag.external{ background:#EDE9FE; color:#6D28D9; } .te-tag.none{ background:#F1F5F9; color:#94A3B8; }
.te-sec{ font-weight:800; font-size:14px; color:#0F172A; margin:6px 0 12px; display:flex; align-items:center; gap:8px; }
.te-sec i{ color:#2F6FED; font-size:18px; }
.te-att{ display:flex; align-items:center; gap:10px; background:#F8FAFD; border:1px solid #E6EBF2; border-radius:10px; padding:8px 12px; margin-top:6px; font-size:13px; }
.te-actions{ display:flex; gap:10px; justify-content:center; margin:6px 0 20px; }
.te-actions .btn{ min-width:160px; }
</style>
@endsection
@section('title')
تعديل بيانات الشحنة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">تعديل بيانات الشحنة #{{ $trip->id }}</h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ $back }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back"></i> رجوع</a>
    </div>
</div>
@endsection
@section('content')
@php
    $lp  = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $dt  = function ($d) { return $d ? $d->format('Y-m-d\TH:i') : ''; };
    $v   = function ($f, $d = null) use ($trip) { return old($f, $d !== null ? $d : $trip->{$f}); };
    $done = $trip->status == 2;
@endphp

@if (count($errors) > 0)
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="post" action="{{ url($lp . '/trucks/trips/' . $trip->id . '/update') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="back" value="{{ $back }}">

    <div class="card"><div class="card-body">
        <div class="te-head">
            <i class="bx bxs-truck" style="font-size:28px;color:#2F6FED"></i>
            <span class="te-plate">{{ optional($trip->truck)->plate_number }}</span>
            <span class="te-tag {{ $trip->ownership ?: 'none' }}">{{ \App\Models\truck_trip::ownershipLabel($trip->ownership) }}</span>
            <span class="te-tag {{ $done ? 'done' : 'loaded' }}">{{ $done ? 'تم التفريغ' : 'محمّلة' }}</span>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <div class="te-sec"><i class="bx bx-map-alt"></i> المسار والمواعيد</div>
        <div class="row">
            <div class="col-md-3 mb-2"><label>من (منطقة التحميل) *<button type="button" class="rg-plus js-add-region" title="إضافة منطقة جديدة">+</button></label>
                <select name="from_region" class="form-control" required>@foreach ($regions as $r)<option value="{{ $r }}" {{ $v('from_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><label>مدينة التحميل</label><input name="from_city" class="form-control" value="{{ $v('from_city') }}"></div>
            <div class="col-md-3 mb-2"><label>إلى (منطقة التنزيل) *<button type="button" class="rg-plus js-add-region" title="إضافة منطقة جديدة">+</button></label>
                <select name="to_region" class="form-control" required>@foreach ($regions as $r)<option value="{{ $r }}" {{ $v('to_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><label>مدينة التنزيل</label><input name="to_city" class="form-control" value="{{ $v('to_city') }}"></div>

            <div class="col-md-3 mb-2"><label>معاد التحميل (التاريخ) *</label><input type="datetime-local" name="loading_at" class="form-control" required value="{{ old('loading_at', $dt($trip->loading_at)) }}"></div>
            <div class="col-md-3 mb-2"><label>معاد التنزيل المتوقع *</label><input type="datetime-local" name="expected_unloading_at" class="form-control" required value="{{ old('expected_unloading_at', $dt($trip->expected_unloading_at)) }}"></div>
            @if ($done)
                <div class="col-md-3 mb-2"><label>معاد التفريغ الفعلي *</label><input type="datetime-local" name="unloaded_at" class="form-control" required value="{{ old('unloaded_at', $dt($trip->unloaded_at)) }}"></div>
                <div class="col-md-3 mb-2"><label>ملاحظات التفريغ</label><input name="unload_notes" class="form-control" value="{{ $v('unload_notes') }}"></div>
            @endif
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <div class="te-sec"><i class="bx bx-package"></i> الحمولة والسائق والعميل</div>
        <div class="row">
            <div class="col-md-3 mb-2"><label>نوع التحميل *</label><input name="load_type" class="form-control" list="loadTypes" required value="{{ $v('load_type') }}">
                <datalist id="loadTypes"><option value="مواد بناء"><option value="مواد غذائية"><option value="حديد"><option value="أسمنت"><option value="أثاث"><option value="معدات"><option value="مبردات"><option value="مواد بترولية"><option value="بضائع عامة"></datalist></div>
            <div class="col-md-3 mb-2"><label>الوزن / الكمية</label><input name="load_weight" class="form-control" value="{{ $v('load_weight') }}"></div>
            <div class="col-md-3 mb-2"><label>السائق</label>
                <select name="driver_id" class="form-control js-s2"><option value="">—</option>@foreach ($drivers as $d)<option value="{{ $d->id }}" {{ $v('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}{{ $d->phone ? ' - ' . $d->phone : '' }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><label>اسم الشركة (العميل)</label>
                <select name="customer_account_id" class="form-control js-s2"><option value="">اختار العميل</option>@foreach ($customers as $c)<option value="{{ $c->id }}" {{ $v('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->account_number ? ' (' . $c->account_number . ')' : '' }}</option>@endforeach</select>
                @if (!$trip->customer_account_id && $trip->customer_name)
                    <label style="margin-top:6px !important;font-weight:600 !important;display:flex;gap:6px;align-items:center">
                        <input type="checkbox" name="keep_old_customer" value="1" checked> احتفظ بالاسم القديم: <b>{{ $trip->customer_name }}</b>
                    </label>
                @endif
            </div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <div class="te-sec"><i class="bx bx-receipt"></i> الفاتورة والسعر</div>
        <div class="row">
            <div class="col-md-3 mb-2"><label>رقم الفاتورة</label><input name="invoice_number" class="form-control" value="{{ $v('invoice_number') }}"></div>
            <div class="col-md-3 mb-2"><label>مرجع</label><input name="reference_no" class="form-control" value="{{ $v('reference_no') }}"></div>
            <div class="col-md-3 mb-2"><label>السعر (ر.س)</label><input type="number" step="any" min="0" name="price" class="form-control" value="{{ $v('price') > 0 ? $v('price') : '' }}"></div>
            <div class="col-md-3 mb-2"><label>رقم البوليصة</label><input name="waybill_no" class="form-control" value="{{ $v('waybill_no') }}"></div>
            <div class="col-md-6 mb-2"><label>المرفق {{ $trip->attachment ? '(اختار ملف جديد لو عايز تغيّره)' : '' }}</label>
                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                @if ($trip->attachment)
                    <div class="te-att">
                        <i class="bx bx-paperclip"></i>
                        <a href="{{ asset('assets/uploads/truck_trips/' . $trip->attachment) }}" target="_blank">عرض المرفق الحالي</a>
                        <label style="margin:0 !important;margin-inline-start:auto !important;display:flex;gap:6px;align-items:center;color:#DC2626 !important">
                            <input type="checkbox" name="remove_attachment" value="1"> حذف المرفق
                        </label>
                    </div>
                @endif
            </div>
            <div class="col-md-6 mb-2"><label>ملاحظات</label><input name="notes" class="form-control" value="{{ $v('notes') }}"></div>
        </div>
    </div></div>

    <div class="te-actions">
        <button class="btn btn-success"><i class="bx bx-save"></i> حفظ التعديلات</button>
        <a href="{{ $back }}" class="btn btn-secondary">إلغاء</a>
    </div>
</form>
@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    if (window.jQuery && $.fn.select2) { $('.js-s2').select2({ width: '100%', dir: 'rtl', allowClear: true, placeholder: '—' }); }
</script>
@include('trucks._region_add')
@endsection
