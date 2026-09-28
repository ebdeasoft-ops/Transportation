@extends('layouts.master')
@section('css')
<style>
.tk-head{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
.tk-plate{ font-size:22px; font-weight:800; direction:ltr; }
.tk-tag{ font-size:12px; font-weight:800; padding:4px 10px; border-radius:999px; display:inline-flex; align-items:center; gap:4px; }
.tk-tag.empty{ background:#D1FAE5; color:#047857; } .tk-tag.loaded{ background:#FEF3C7; color:#B45309; } .tk-tag.info{ background:#F1F5F9; color:#475569; }
.tk-sec{ font-weight:800; font-size:14px; color:#0F172A; margin:4px 0 12px; display:flex; align-items:center; gap:8px; }
.tk-sec i{ color:#2F6FED; font-size:18px; }
.tk-own{ display:flex; gap:10px; }
.tk-own label{ flex:1; margin:0 !important; cursor:pointer; }
.tk-own input{ display:none; }
.tk-own span{ display:flex; align-items:center; justify-content:center; gap:6px; border:1.5px solid #E6EBF2; border-radius:10px; padding:11px; font-weight:800; color:#475569; transition:.15s; }
.tk-own input:checked + span{ border-color:#2F6FED; background:#EAF1FF; color:#2F6FED; }
.tk-doc{ border:1px solid #E6EBF2; border-radius:14px; padding:16px; height:100%; position:relative; }
.tk-doc.expired{ border-color:#FECACA; background:#FFF7F7; } .tk-doc.soon{ border-color:#FDE68A; background:#FFFDF5; }
.tk-doc-state{ position:absolute; top:14px; inset-inline-end:14px; font-size:11.5px; font-weight:800; padding:3px 10px; border-radius:999px; }
.tk-doc-state.expired{ background:#FEE2E2; color:#B91C1C; } .tk-doc-state.soon{ background:#FEF3C7; color:#B45309; } .tk-doc-state.ok{ background:#D1FAE5; color:#047857; } .tk-doc-state.none{ background:#F1F5F9; color:#94A3B8; }
.tk-actions{ display:flex; gap:10px; justify-content:center; margin:6px 0 20px; }
.tk-actions .btn{ min-width:160px; }
</style>
@endsection
@section('title')
بيانات الشاحنة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">بيانات الشاحنة</h4></div>
    <div class="d-flex" style="gap:8px"><a href="{{ url('trucks/board') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back"></i> لوحة الشاحنات</a></div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $v  = function ($f) use ($truck) { return old($f, $truck->{$f}); };
    $d  = function ($f) use ($truck) { return old($f, $truck->{$f} ? $truck->{$f}->format('Y-m-d') : ''); };
    $stLabel = ['expired' => 'منتهية', 'soon' => 'قربت تنتهي', 'ok' => 'سارية'];
    $daysLeft = function ($date) {
        if (!$date) return null;
        $today = \Carbon\Carbon::now('Asia/Riyadh')->startOfDay();
        return (int) $today->diffInDays(\Carbon\Carbon::parse($date->format('Y-m-d'), 'Asia/Riyadh'), false);
    };
    $insS = $truck->insurance_status; $istS = $truck->istimara_status;
    $insD = $daysLeft($truck->insurance_expiry); $istD = $daysLeft($truck->istimara_expiry);
@endphp

@if (count($errors) > 0)
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="post" action="{{ url($lp . '/trucks/' . $truck->id . '/update') }}">
    {{ csrf_field() }}

    <div class="card"><div class="card-body">
        <div class="tk-head">
            <i class="bx bxs-truck" style="font-size:30px;color:#2F6FED"></i>
            <span class="tk-plate">{{ $truck->plate_number }}</span>
            @if ($truck->activeTrip)
                <span class="tk-tag loaded"><i class="bx bx-package"></i> محمّلة: {{ $truck->activeTrip->from_region }} ← {{ $truck->activeTrip->to_region }}</span>
            @else
                <span class="tk-tag empty"><i class="bx bx-check"></i> فاضية</span>
            @endif
            <span class="tk-tag info"><i class="bx bx-list-ul"></i> {{ $trips }} رحلة</span>
        </div>
    </div></div>

    {{-- بيانات أساسية --}}
    <div class="card"><div class="card-body">
        <div class="tk-sec"><i class="bx bxs-truck"></i> بيانات الشاحنة</div>
        <div class="row">
            <div class="col-md-12 mb-3"><label>الملكية *</label>
                <div class="tk-own">
                    <label><input type="radio" name="ownership" value="own" required {{ $v('ownership') == 'own' ? 'checked' : '' }}><span><i class="bx bx-buildings"></i> ملك المؤسسة</span></label>
                    <label><input type="radio" name="ownership" value="external" {{ $v('ownership') == 'external' ? 'checked' : '' }}><span><i class="bx bx-transfer"></i> إيجار خارجي</span></label>
                </div>
            </div>
            <div class="col-md-3 mb-2"><label>رقم اللوحة *</label><input name="plate_number" class="form-control" required value="{{ $v('plate_number') }}"></div>
            <div class="col-md-3 mb-2"><label>جهة اللوحة</label><input name="plate_region" class="form-control" value="{{ $v('plate_region') }}"></div>
            <div class="col-md-3 mb-2"><label>نوع الشاحنة</label><input name="truck_type" class="form-control" value="{{ $v('truck_type') }}" placeholder="تريلا، سطحة، قلاب، براد..."></div>
            <div class="col-md-3 mb-2"><label>الحمولة الإجمالية</label><input name="total_load" class="form-control" value="{{ $v('total_load') }}"></div>
            <div class="col-md-3 mb-2"><label>اسم المالك</label><input name="owner_name" class="form-control" value="{{ $v('owner_name') }}"></div>
            <div class="col-md-3 mb-2"><label>رقم رخصة التشغيل</label><input name="operation_license_number" class="form-control" value="{{ $v('operation_license_number') }}"></div>
            <div class="col-md-3 mb-2"><label>جهة صدور رخصة التشغيل</label><input name="operation_license_issuer" class="form-control" value="{{ $v('operation_license_issuer') }}"></div>
            <div class="col-md-3 mb-2"><label class="d-flex justify-content-between" style="gap:6px">السائق الافتراضي
                    <span style="font-size:12px;white-space:nowrap"><a href="#" id="tkEditDrv" data-driver-modal data-target="#tkDriver" style="display:none"><i class="bx bx-edit"></i> تعديل</a> <a href="#" data-driver-modal data-target="#tkDriver">+ جديد</a></span></label>
                <select name="default_driver_id" id="tkDriver" class="form-control" data-drivers><option value="">—</option>@foreach ($drivers as $dr)<option value="{{ $dr->id }}" data-driver="{{ json_encode($dr->only(['id', 'name', 'phone', 'id_number', 'nationality', 'license_number', 'license_issue_date', 'notes']), JSON_UNESCAPED_UNICODE) }}" {{ $v('default_driver_id') == $dr->id ? 'selected' : '' }}>{{ $dr->name }}{{ $dr->phone ? ' - ' . $dr->phone : '' }}</option>@endforeach</select>
                <small id="tkDrvInfo" style="display:block;margin-top:4px;font-size:12px;font-weight:700"></small></div>
            <div class="col-md-3 mb-2"><label>مكانها الحالي (المنطقة)</label>
                <select name="current_region" class="form-control"><option value="">غير محدد</option>@foreach ($regions as $r)<option value="{{ $r }}" {{ $v('current_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><label>المدينة</label><input name="current_city" class="form-control" value="{{ $v('current_city') }}"></div>
            <div class="col-md-6 mb-2"><label>ملاحظات</label><input name="notes" class="form-control" value="{{ $v('notes') }}"></div>
        </div>
    </div></div>

    {{-- التأمين والاستمارة --}}
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card" style="height:100%"><div class="card-body">
                <div class="tk-doc {{ $insS }}">
                    <span class="tk-doc-state {{ $insS ?: 'none' }}">
                        @if ($insS) {{ $stLabel[$insS] }}{{ $insD !== null ? ($insD >= 0 ? ' · باقي ' . $insD . ' يوم' : ' من ' . abs($insD) . ' يوم') : '' }} @else مش متسجل @endif
                    </span>
                    <div class="tk-sec"><i class="bx bx-shield-quarter"></i> التأمين</div>
                    <div class="row">
                        <div class="col-md-6 mb-2"><label>شركة التأمين</label><input name="insurance_company" class="form-control" value="{{ $v('insurance_company') }}"></div>
                        <div class="col-md-6 mb-2"><label>رقم الوثيقة</label><input name="insurance_policy_no" class="form-control" value="{{ $v('insurance_policy_no') }}"></div>
                        <div class="col-md-6 mb-2"><label>تاريخ البداية</label><input type="date" name="insurance_start" class="form-control" value="{{ $d('insurance_start') }}"></div>
                        <div class="col-md-6 mb-2"><label>تاريخ الانتهاء</label><input type="date" name="insurance_expiry" class="form-control" value="{{ $d('insurance_expiry') }}"></div>
                        <div class="col-md-6 mb-2"><label>قيمة التأمين (ر.س)</label><input type="number" step="any" min="0" name="insurance_value" class="form-control" value="{{ $v('insurance_value') }}"></div>
                    </div>
                </div>
            </div></div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card" style="height:100%"><div class="card-body">
                <div class="tk-doc {{ $istS }}">
                    <span class="tk-doc-state {{ $istS ?: 'none' }}">
                        @if ($istS) {{ $stLabel[$istS] }}{{ $istD !== null ? ($istD >= 0 ? ' · باقي ' . $istD . ' يوم' : ' من ' . abs($istD) . ' يوم') : '' }} @else مش متسجلة @endif
                    </span>
                    <div class="tk-sec"><i class="bx bx-id-card"></i> الاستمارة</div>
                    <div class="row">
                        <div class="col-md-6 mb-2"><label>رقم الاستمارة</label><input name="istimara_no" class="form-control" value="{{ $v('istimara_no') }}"></div>
                        <div class="col-md-6 mb-2"><label>تاريخ انتهاء الاستمارة</label><input type="date" name="istimara_expiry" class="form-control" value="{{ $d('istimara_expiry') }}"></div>
                    </div>
                </div>
            </div></div>
        </div>
    </div>

    <div class="tk-actions">
        <button class="btn btn-success"><i class="bx bx-save"></i> حفظ بيانات الشاحنة</button>
        <a href="{{ url('trucks/board') }}" class="btn btn-secondary">إلغاء</a>
    </div>
</form>
@include('trucks._driver_modal')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // «تعديل» بيفتح بيانات السائق المختار، و«+ جديد» بيضيف سائق ويختاره (من غير ما الصفحة تتقفل)
    var sel = document.getElementById('tkDriver'), edit = document.getElementById('tkEditDrv'), info = document.getElementById('tkDrvInfo');
    function sync() {
        var o = sel.options[sel.selectedIndex], d = null;
        try { d = o && o.value ? JSON.parse(o.dataset.driver || 'null') : null; } catch (e) {}
        edit.style.display = d ? '' : 'none';
        jQuery(edit).data('driver', d || {});
        if (d && d.phone) {
            var p = d.phone.replace(/\D/g, '');
            info.innerHTML = '<a href="tel:' + p + '" dir="ltr">' + d.phone + '</a> · <a href="https://wa.me/966' + p.replace(/^0/, '') + '" target="_blank" style="color:#16A34A"><i class="bx bxl-whatsapp"></i> واتساب</a>';
        } else info.innerHTML = '';
    }
    sel.addEventListener('change', sync);
    jQuery(sel).on('change', sync);
    sync();
});
</script>
@endsection
