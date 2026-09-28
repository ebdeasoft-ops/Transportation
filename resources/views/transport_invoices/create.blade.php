@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@include('transport_reports._style')
<style>
.ti-card{ background:#fff; border:1px solid #E6EBF2; border-radius:16px; padding:18px; box-shadow:0 10px 26px -18px rgba(15,23,42,.25); margin-bottom:16px; }
.ti-card h5{ font-weight:800; font-size:15px; margin:0 0 12px; display:flex; align-items:center; gap:8px; color:#0F172A; }
.ti-card h5 i{ color:#2F6FED; font-size:20px; }
.ti-trips td, .ti-trips th{ vertical-align:middle !important; }
.ti-trips tr.sel td{ background:#EFF6FF; }
.ti-trips input.pr{ width:120px; direction:ltr; text-align:right; font-weight:700; }
.ti-lines input{ font-weight:600; }
.ti-sum{ position:sticky; top:80px; }
.ti-sum table{ width:100%; }
.ti-sum th{ color:#475569; font-weight:700; padding:7px 0; font-size:13px; }
.ti-sum td{ text-align:left; direction:ltr; font-weight:800; font-variant-numeric:tabular-nums; font-size:14px; }
.ti-sum tr.grand th, .ti-sum tr.grand td{ font-size:18px; color:#1E3A8A; border-top:2px solid #1E3A8A; padding-top:10px; }
.ti-cust{ display:flex; flex-wrap:wrap; gap:8px 18px; font-size:13px; color:#475569; font-weight:700; }
.ti-cust b{ color:#0F172A; }
.ti-vat{ display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.ti-vat label{ border:2px solid #E6EBF2; border-radius:12px; padding:10px; text-align:center; cursor:pointer; margin:0; transition:.15s; }
.ti-vat label input{ display:none; }
.ti-vat label b{ display:block; font-size:20px; color:#0F172A; }
.ti-vat label small{ color:#64748B; font-weight:700; }
.ti-vat label.on{ border-color:#2F6FED; background:#EAF1FF; }
.ti-vat label.on b{ color:#2F6FED; }
.ti-sw{ display:flex; align-items:center; gap:8px; font-weight:700; font-size:13px; cursor:pointer; }
</style>
@endsection
@section('title')
فاتورة نقل ضريبية جديدة
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">فاتورة نقل ضريبية جديدة <small class="text-muted" style="font-size:13px">رقم متوقع #{{ $nextNo }}</small></h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ url('transport-invoices') }}" class="btn btn-secondary btn-sm"><i class="bx bx-list-ul"></i> الفواتير السابقة</a>
        <a href="{{ url('transport-reports/unbilled') }}" class="btn btn-outline-primary btn-sm"><i class="bx bx-receipt"></i> الأحمال غير المفوترة</a>
    </div>
</div>
@endsection
@section('content')
@php $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(); @endphp

@if ($errors->any())
    <div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

{{-- 1) اختيار العميل --}}
<div class="ti-card">
    <h5><i class="bx bx-user-circle"></i> العميل والأحمال</h5>
    <form method="get" action="{{ url($lp . '/transport-invoices/create') }}" id="custForm">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-2">
                <label>العميل (اسم الشركة)</label>
                <select name="customer_account_id" id="custSel" class="form-control">
                    <option value="">اختار العميل</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" {{ optional($account)->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->account_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3 mb-2"><label>أحمال من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ request('start_at') }}"></div>
            <div class="col-lg-2 col-md-3 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ request('end_at') }}"></div>
            <div class="col-lg-2 col-md-6 mb-2 d-flex align-items-end">
                <label class="ti-sw mb-2"><input type="checkbox" name="include_loaded" value="1" {{ request('include_loaded') ? 'checked' : '' }}> ضمّن المحمّلة (لسه ما اتفرغتش)</label>
            </div>
            <div class="col-lg-2 col-md-6 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block"><i class="bx bx-search"></i> عرض الأحمال</button></div>
        </div>
    </form>
</div>

@if ($account)
<form method="post" action="{{ url($lp . '/transport-invoices') }}" id="invForm">
    @csrf
    <input type="hidden" name="customer_account_id" value="{{ $account->id }}">
    <div class="row">
        <div class="col-xl-8">
            {{-- بيانات العميل --}}
            <div class="ti-card">
                <h5><i class="bx bx-buildings"></i> بيانات الفاتورة</h5>
                <div class="row">
                    <div class="col-md-4 mb-2"><label>تاريخ الفاتورة</label><input type="datetime-local" name="issue_date" class="form-control" required value="{{ old('issue_date', now('Asia/Riyadh')->format('Y-m-d\TH:i')) }}"></div>
                    <div class="col-md-4 mb-2"><label>فترة التوريد من</label><input type="date" name="supply_from" class="form-control" value="{{ old('supply_from', optional($trips->min('loading_at'))->format('Y-m-d')) }}"></div>
                    <div class="col-md-4 mb-2"><label>إلى</label><input type="date" name="supply_to" class="form-control" value="{{ old('supply_to', optional($trips->max('loading_at'))->format('Y-m-d')) }}"></div>
                    <div class="col-md-4 mb-2"><label>الرقم الضريبي للعميل <small class="text-muted">(15 رقم - لو فاضي الفاتورة مبسطة)</small></label><input type="text" name="customer_vat" class="form-control" dir="ltr" maxlength="15" value="{{ old('customer_vat', $info['vat']) }}"></div>
                    <div class="col-md-4 mb-2"><label>السجل التجاري للعميل</label><input type="text" name="customer_cr" class="form-control" dir="ltr" value="{{ old('customer_cr') }}"></div>
                    <div class="col-md-4 mb-2"><label>رقم أمر الشراء / المرجع</label><input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}"></div>
                    <div class="col-md-8 mb-2"><label>عنوان العميل</label><input type="text" name="customer_address" class="form-control" value="{{ old('customer_address', $info['address']) }}"></div>
                    <div class="col-md-4 mb-2"><label>جوال العميل</label><input type="text" name="customer_phone" class="form-control" dir="ltr" value="{{ old('customer_phone', $info['phone']) }}"></div>
                </div>
            </div>

            {{-- الأحمال --}}
            <div class="ti-card">
                <h5><i class="bx bxs-truck"></i> الأحمال غير المفوترة لـ {{ $account->name }} <span class="tr-tag blue">{{ $trips->count() }}</span>
                    @if ($trips->count())<label class="ti-sw mb-0" style="margin-inline-start:auto"><input type="checkbox" id="chkAll" checked> تحديد الكل</label>@endif
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover ti-trips tr-table mb-0">
                        <thead><tr><th></th><th>التاريخ</th><th>اللوحة</th><th>من</th><th>إلى</th><th>نوع التحميل</th><th>بوليصة / مرجع</th><th>الحالة</th><th>السعر</th></tr></thead>
                        <tbody>
                            @forelse ($trips as $t)
                                <tr class="sel">
                                    <td><input type="checkbox" class="chk" name="trip_ids[]" value="{{ $t->id }}" checked></td>
                                    <td style="white-space:nowrap">{{ optional($t->loading_at)->format('Y/m/d') }}</td>
                                    <td class="tr-num" style="font-weight:700">{{ optional($t->truck)->plate_number }}</td>
                                    <td>{{ $t->from_region }}{{ $t->from_city ? ' - ' . $t->from_city : '' }}</td>
                                    <td>{{ $t->to_region }}{{ $t->to_city ? ' - ' . $t->to_city : '' }}</td>
                                    <td>{{ $t->load_type }}</td>
                                    <td>{{ $t->waybill_no ?: ($t->reference_no ?: '—') }}</td>
                                    <td>@if ($t->status == 2)<span class="tr-tag ok">تم التفريغ</span>@else<span class="tr-tag on">محمّلة</span>@endif</td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm pr" name="trip_price[{{ $t->id }}]" value="{{ old('trip_price.' . $t->id, $t->price) }}"></td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="tr-empty">مفيش أحمال غير مفوترة للعميل ده في الفترة دي — تقدر تضيف بنود يدوية تحت</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- بنود يدوية --}}
            <div class="ti-card">
                <h5><i class="bx bx-edit"></i> بنود إضافية (اختياري) <button type="button" class="btn btn-sm btn-outline-primary" id="addLine" style="margin-inline-start:auto"><i class="bx bx-plus"></i> بند</button></h5>
                <table class="table ti-lines mb-0">
                    <thead><tr><th>الوصف</th><th style="width:110px">الكمية</th><th style="width:150px">سعر الوحدة</th><th style="width:40px"></th></tr></thead>
                    <tbody id="lines">
                        @php $oldDesc = old('line_desc', ['']); @endphp
                        @foreach ($oldDesc as $i => $d)
                            <tr>
                                <td><input type="text" name="line_desc[]" class="form-control" value="{{ $d }}" placeholder="مثال: رسوم انتظار / تحميل / تفريغ"></td>
                                <td><input type="number" step="0.01" min="0" name="line_qty[]" class="form-control lq" value="{{ old('line_qty.' . $i, 1) }}" dir="ltr"></td>
                                <td><input type="number" step="0.01" min="0" name="line_price[]" class="form-control lp" value="{{ old('line_price.' . $i) }}" dir="ltr"></td>
                                <td><button type="button" class="btn btn-sm btn-light delLine"><i class="bx bx-x"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ti-card">
                <label>ملاحظات تظهر على الفاتورة</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- الإجماليات --}}
        <div class="col-xl-4">
            <div class="ti-card ti-sum">
                <h5><i class="bx bx-calculator"></i> الإجماليات</h5>
                @php $pct = rtrim(rtrim(number_format($vatRate * 100, 2), '0'), '.'); $cat = old('vat_category', 'S'); @endphp
                <label>نوع الضريبة</label>
                <div class="ti-vat mb-2">
                    <label class="{{ $cat == 'S' ? 'on' : '' }}"><input type="radio" name="vat_category" value="S" {{ $cat == 'S' ? 'checked' : '' }}> <b>{{ $pct }}%</b><small>خاضعة للضريبة</small></label>
                    <label class="{{ $cat == 'Z' ? 'on' : '' }}"><input type="radio" name="vat_category" value="Z" {{ $cat == 'Z' ? 'checked' : '' }}> <b>0%</b><small>نسبة صفرية</small></label>
                </div>
                <div class="mb-3" id="zeroBox" style="{{ $cat == 'Z' ? '' : 'display:none' }}">
                    <label>سبب النسبة الصفرية <small class="text-muted">(بيظهر على الفاتورة)</small></label>
                    <select name="vat_exempt_code" class="form-control">
                        @foreach ($zeroReasons as $code => $label)
                            <option value="{{ $code }}" {{ old('vat_exempt_code', 'VATEX-SA-34-1') == $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="ti-sw mb-3" id="incVatWrap" style="{{ $cat == 'Z' ? 'display:none' : '' }}"><input type="checkbox" name="prices_include_vat" value="1" id="incVat" {{ old('prices_include_vat') ? 'checked' : '' }}> الأسعار المكتوبة شاملة الضريبة</label>
                <div class="mb-3"><label>خصم على الفاتورة (قبل الضريبة)</label><input type="number" step="0.01" min="0" name="discount" id="disc" class="form-control" dir="ltr" value="{{ old('discount', 0) }}"></div>
                <table>
                    <tr><th>عدد الأحمال المحددة</th><td id="sCount">0</td></tr>
                    <tr><th>الإجمالي قبل الضريبة</th><td id="sSub">0.00</td></tr>
                    <tr><th>الخصم</th><td id="sDisc">0.00</td></tr>
                    <tr><th>الخاضع للضريبة</th><td id="sTax">0.00</td></tr>
                    <tr><th>ضريبة القيمة المضافة <span id="sPct">{{ $cat == 'Z' ? 0 : $pct }}</span>%</th><td id="sVat">0.00</td></tr>
                    <tr class="grand"><th>الإجمالي شامل الضريبة</th><td id="sTotal">0.00</td></tr>
                </table>
                <div class="alert alert-info mt-3 mb-3" style="font-size:12.5px">
                    عند الحفظ هيترحّل القيد تلقائياً: <b>{{ $account->name }}</b> مدين بالإجمالي، و<b>إيرادات النقل</b> دائن بالصافي، و<b>ضريبة القيمة المضافة</b> دائن بالضريبة (لو الفاتورة صفرية مفيش سطر ضريبة).
                </div>
                <button class="btn btn-success btn-block btn-lg" id="saveBtn"><i class="bx bx-save"></i> حفظ الفاتورة وطباعتها</button>
            </div>
        </div>
    </div>
</form>
@else
    <div class="ti-card tr-empty"><i class="bx bx-user-circle" style="font-size:40px"></i><div>اختار العميل الأول عشان تظهر أحماله غير المفوترة</div></div>
@endif
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
(function () {
    if ($.fn.select2) $('#custSel').select2({ width: '100%', dir: 'rtl', placeholder: 'اختار العميل' });
    $('#custSel').on('change', function () { if (this.value) document.getElementById('custForm').submit(); });

    var baseRate = {{ $vatRate }}, rate = baseRate;
    function applyCat() {
        var z = $('input[name=vat_category]:checked').val() === 'Z';
        rate = z ? 0 : baseRate;
        $('#zeroBox').toggle(z); $('#incVatWrap').toggle(!z);
        if (z) $('#incVat').prop('checked', false);
        $('.ti-vat label').each(function () { $(this).toggleClass('on', $(this).find('input').is(':checked')); });
        $('#sPct').text(z ? 0 : Math.round(baseRate * 10000) / 100);
    }
    $(document).on('change', 'input[name=vat_category]', function () { applyCat(); calc(); });
    var f = function (n) { return (Math.round(n * 100) / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
    function calc() {
        var inc = $('#incVat').is(':checked'), sub = 0, cnt = 0;
        $('.ti-trips tbody tr').each(function () {
            var c = $(this).find('.chk');
            if (!c.length) return;
            $(this).toggleClass('sel', c.is(':checked'));
            if (c.is(':checked')) { cnt++; sub += parseFloat($(this).find('.pr').val()) || 0; }
        });
        $('#lines tr').each(function () {
            var q = parseFloat($(this).find('.lq').val()) || 0, p = parseFloat($(this).find('.lp').val()) || 0;
            if ($(this).find('input[name="line_desc[]"]').val().trim() !== '') sub += q * p;
        });
        if (inc) sub = sub / (1 + rate);
        var disc = Math.min(parseFloat($('#disc').val()) || 0, sub), tax = sub - disc, vat = Math.round(tax * rate * 100) / 100;
        $('#sCount').text(cnt); $('#sSub').text(f(sub)); $('#sDisc').text(f(disc)); $('#sTax').text(f(tax)); $('#sVat').text(f(vat)); $('#sTotal').text(f(tax + vat));
    }
    $(document).on('input change', '#invForm input', calc);
    $('#chkAll').on('change', function () { $('.chk').prop('checked', this.checked); calc(); });
    $('#addLine').on('click', function () {
        var r = $('#lines tr:first').clone(); r.find('input').val(''); r.find('.lq').val(1); $('#lines').append(r);
    });
    $(document).on('click', '.delLine', function () {
        if ($('#lines tr').length > 1) $(this).closest('tr').remove(); else $(this).closest('tr').find('input').val('');
        calc();
    });
    $('#invForm').on('submit', function () { $('#saveBtn').prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> جاري الحفظ...'); });
    applyCat(); calc();
})();
</script>
@endsection
