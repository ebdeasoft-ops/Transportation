@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    @include('waybills._style')
    <style>
        .wb-modal .modal-header { background: #2b2f8f; color: #fff; }
        .wb-modal .modal-header .close { color: #fff; }
        .wb-modal label { font-weight: 700; font-size: 13px; }
        .wb-modal .form-control { border-radius: 8px; }
        .wb-err { color: #c0392b; font-size: 13px; display: none; }
    </style>
@endsection
@section('title')
    بوليصة شحن
@stop
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $waybill ? 'تعديل بوليصة شحن رقم ' . $waybill->waybill_no : 'بوليصة شحن جديدة' }}</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ url('waybills') }}" class="btn btn-outline-primary btn-sm">البوليصات السابقة</a>
        </div>
    </div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $w  = $waybill;
    $v  = function ($field, $default = '') use ($w) {
        return old($field, $w ? $w->{$field} : $default);
    };
    $items = $w ? $w->items : collect();
    $rowsCount = max(7, $items->count());
    $dateVal = $v('date', date('Y-m-d'));
    if ($dateVal instanceof \DateTimeInterface) { $dateVal = $dateVal->format('Y-m-d'); }
@endphp

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button"><span aria-hidden="true">&times;</span></button>
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session()->has('waybill_saved'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('waybill_saved') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

<form method="post" action="{{ url($lp . '/waybills/store') }}" id="waybillForm" autocomplete="off">
    {{ csrf_field() }}
    <input type="hidden" name="waybill_id" value="{{ $w->id ?? '' }}">
    <input type="hidden" name="date_hijri" id="date_hijri" value="{{ $v('date_hijri') }}">

    <div class="wb-paper">
        {{-- رقم البوليصة تلقائي (بيتحدد وقت الحفظ) ، والتاريخ بيتختار من الهيدر فوق والهجري بيتحسب لوحده --}}
        @include('waybills._header', [
            'company'       => $company,
            'noHtml'        => '<span class="wb-no-auto" id="waybill_no_view">' . e($w ? $w->waybill_no : $nextNo) . '</span>'
                               . ($w ? '' : '<span class="wb-no-hint">يتحدد تلقائي عند الحفظ</span>'),
            'hijri'         => $v('date_hijri'),
            'dateInputHtml' => '<input type="date" name="date" id="wb_date" required value="' . e($dateVal) . '" title="اختار التاريخ">',
        ])

        {{-- المكرم / السادة --}}
        <div class="wb-row">
            <div class="wb-field wide">
                <label>المكرم / السادة :</label>
                <div class="wb-picker">
                    <select id="customer_id" name="customer_id" class="form-control wb-select" data-placeholder="اختر العميل">
                        <option value=""></option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ (string) $v('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->phone ? ' - ' . $c->phone : '' }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="wb-add" data-toggle="modal" data-target="#customerModal" title="عميل جديد">+</button>
                </div>
                <input type="text" class="wb-in" name="customer_name" id="customer_name" value="{{ $v('customer_name') }}" placeholder="اسم العميل كما يظهر في البوليصة">
                <span class="wb-suffix">المحترم</span>
            </div>
        </div>

        <div class="wb-row">
            <div class="wb-field">
                <label>المدينة المتجهة إليها البضاعة :</label>
                <input type="text" class="wb-in" name="destination_city" id="destination_city" value="{{ $v('destination_city') }}">
            </div>
        </div>

        {{-- السائق --}}
        <div class="wb-row">
            <div class="wb-field">
                <label>اسم السائق :</label>
                <div class="wb-picker">
                    <select id="driver_id" name="driver_id" class="form-control wb-select" data-placeholder="اختر السائق">
                        <option value=""></option>
                        @foreach ($drivers as $d)
                            <option value="{{ $d->id }}" {{ (string) $v('driver_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="wb-add" data-toggle="modal" data-target="#driverModal" title="سائق جديد">+</button>
                </div>
                <input type="hidden" name="driver_name" id="driver_name" value="{{ $v('driver_name') }}">
            </div>
            <div class="wb-field">
                <label>رقم رخصة القيادة :</label>
                <input type="text" class="wb-in" name="driver_license_number" id="driver_license_number" value="{{ $v('driver_license_number') }}">
            </div>
        </div>

        {{-- الشاحنة --}}
        <div class="wb-row">
            <div class="wb-field">
                <label>اسم مالك السيارة :</label>
                <input type="text" class="wb-in" name="owner_name" id="owner_name" value="{{ $v('owner_name') }}">
            </div>
            <div class="wb-field">
                <label>تاريخ صدورها :</label>
                <input type="text" class="wb-in" name="driver_license_issue_date" id="driver_license_issue_date" value="{{ $v('driver_license_issue_date') }}">
            </div>
        </div>

        <div class="wb-row">
            <div class="wb-field">
                <label>رقم السيارة :</label>
                <div class="wb-picker">
                    <select id="truck_id" name="truck_id" class="form-control wb-select" data-placeholder="اختر الشاحنة">
                        <option value=""></option>
                        @foreach ($trucks as $t)
                            <option value="{{ $t->id }}" {{ (string) $v('truck_id') === (string) $t->id ? 'selected' : '' }}>{{ $t->plate_number }}{{ $t->truck_type ? ' - ' . $t->truck_type : '' }}{{ $t->relationLoaded('activeTrip') && $t->activeTrip ? ' ⛔ عليها حمل (' . $t->activeTrip->from_region . ' ← ' . $t->activeTrip->to_region . ')' : '' }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="wb-add" data-toggle="modal" data-target="#truckModal" title="شاحنة جديدة">+</button>
                </div>
                <input type="hidden" name="plate_number" id="plate_number" value="{{ $v('plate_number') }}">
            </div>
            <div class="wb-field">
                <label>جهتها :</label>
                <input type="text" class="wb-in" name="plate_region" id="plate_region" value="{{ $v('plate_region') }}">
            </div>
        </div>

        <div class="wb-row">
            <div class="wb-field">
                <label>رقم رخصة التشغيل :</label>
                <input type="text" class="wb-in" name="operation_license_number" id="operation_license_number" value="{{ $v('operation_license_number') }}">
            </div>
            <div class="wb-field">
                <label>نوع السيارة :</label>
                <input type="text" class="wb-in" name="truck_type" id="truck_type" value="{{ $v('truck_type') }}">
            </div>
        </div>

        <div class="wb-row">
            <div class="wb-field">
                <label>جهة صدورها :</label>
                <input type="text" class="wb-in" name="operation_license_issuer" id="operation_license_issuer" value="{{ $v('operation_license_issuer') }}">
            </div>
            <div class="wb-field">
                <label>الحمولة الإجمالية :</label>
                <input type="text" class="wb-in" name="total_load" id="total_load" value="{{ $v('total_load') }}">
            </div>
        </div>

        {{-- جدول البضاعة --}}
        <div class="wb-table-wrap">
            <table class="wb-table" id="itemsTable">
                <thead>
                    <tr>
                        <th rowspan="2">م</th>
                        <th colspan="2">الراسل</th>
                        <th rowspan="2">اسم المرسل إليه</th>
                        <th rowspan="2">نوع البضاعة</th>
                        <th rowspan="2">وزن البضاعة</th>
                        <th rowspan="2" style="width:30px"></th>
                    </tr>
                    <tr>
                        <th>الاسم</th>
                        <th>الأجرة</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $rowsCount; $i++)
                        @php $it = $items[$i] ?? null; @endphp
                        <tr>
                            <td class="wb-idx">{{ $i + 1 }}</td>
                            <td><input type="text" name="sender_name[]" value="{{ old('sender_name.' . $i, $it->sender_name ?? '') }}"></td>
                            <td><input type="number" step="any" min="0" class="wb-fare" name="fare[]" value="{{ old('fare.' . $i, $it && $it->fare ? $it->fare : '') }}"></td>
                            <td><input type="text" name="receiver_name[]" value="{{ old('receiver_name.' . $i, $it->receiver_name ?? '') }}"></td>
                            <td><input type="text" name="goods_type[]" value="{{ old('goods_type.' . $i, $it->goods_type ?? '') }}"></td>
                            <td><input type="text" name="goods_weight[]" value="{{ old('goods_weight.' . $i, $it->goods_weight ?? '') }}"></td>
                            <td><button type="button" class="wb-del-row" title="حذف السطر">&times;</button></td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">إجمالي الأجرة</td>
                        <td id="totalFare">0</td>
                        <td colspan="4" style="text-align:left;padding:4px 8px">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addRow">+ إضافة سطر</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="wb-row">
            <div class="wb-field">
                <label>تاريخ المغادرة :</label>
                <input type="date" class="wb-in" name="departure_date" value="{{ $v('departure_date') instanceof \DateTimeInterface ? $v('departure_date')->format('Y-m-d') : $v('departure_date') }}">
            </div>
            <div class="wb-field">
                <label>توقيع المسؤول :</label>
                <span class="wb-val"></span>
            </div>
        </div>

        <div class="wb-note-strong">يجب تشريع البضاعة للمحافظة عليها</div>

        <div class="wb-row">
            <div class="wb-field">
                <label>تدفع الأجرة من قبل :</label>
                <input type="text" class="wb-in" name="fare_paid_by" value="{{ $v('fare_paid_by') }}">
            </div>
        </div>
        <div class="wb-row">
            <div class="wb-field">
                <label>يجب إيصال البضاعة خلال :</label>
                <input type="text" class="wb-in" name="delivery_within" value="{{ $v('delivery_within') }}">
            </div>
        </div>

        <div class="wb-note-strong">ملاحظة : أي نقصان أو تلف وأي تعطيل مسؤولية السائق</div>

        <div class="wb-row">
            <div class="wb-field">
                <label>ملاحظات إضافية :</label>
                <input type="text" class="wb-in" name="notes" value="{{ $v('notes') }}">
            </div>
        </div>

        <div class="wb-row">
            <div class="wb-field"><label>توقيع السائق :</label><span class="wb-val"></span></div>
            <div class="wb-field"><label>توقيع المستلم :</label><span class="wb-val"></span></div>
        </div>

        @include('waybills._footer', ['company' => $company])
    </div>

    <div class="wb-actions">
        <button type="submit" name="action" value="save" class="btn btn-success">حفظ</button>
        <button type="submit" name="action" value="print" class="btn btn-primary">حفظ وطباعة</button>
        @if ($w)
            <a href="{{ url('waybills/print/' . $w->id) }}" target="_blank" class="btn btn-info">طباعة فقط</a>
            <a href="{{ url('waybills/create') }}" class="btn btn-outline-secondary">بوليصة جديدة</a>
        @endif
    </div>
</form>

{{-- ============ مودال سائق جديد ============ --}}
<div class="modal fade wb-modal" id="driverModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة سائق جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form id="driverForm" data-url="{{ url($lp . '/waybills/drivers') }}" data-target="driver">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><label>اسم السائق *</label><input class="form-control" name="name" required></div>
                        <div class="col-md-6 mb-2"><label>الجوال *</label><input class="form-control" name="phone" required dir="ltr" placeholder="05xxxxxxxx" inputmode="tel" maxlength="10" pattern="05[0-9]{8}" title="رقم جوال سعودي 10 أرقام يبدأ بـ 05"></div>
                        <div class="col-md-6 mb-2"><label>رقم الهوية / الإقامة</label><input class="form-control" name="id_number"></div>
                        <div class="col-md-6 mb-2"><label>الجنسية</label><input class="form-control" name="nationality"></div>
                        <div class="col-md-6 mb-2"><label>رقم رخصة القيادة</label><input class="form-control" name="license_number"></div>
                        <div class="col-md-6 mb-2"><label>تاريخ صدور الرخصة</label><input class="form-control" name="license_issue_date" placeholder="مثال: 1445/05/10"></div>
                        <div class="col-md-12 mb-2"><label>ملاحظات</label><input class="form-control" name="notes"></div>
                    </div>
                    <div class="wb-err"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">حفظ السائق</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
            </form>
        </div>
    </div>
</div>

{{-- ============ مودال شاحنة جديدة ============ --}}
<div class="modal fade wb-modal" id="truckModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة شاحنة جديدة</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form id="truckForm" data-url="{{ url($lp . '/waybills/trucks') }}" data-target="truck">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><label>رقم السيارة (اللوحة) *</label><input class="form-control" name="plate_number" required></div>
                        <div class="col-md-6 mb-2"><label>جهتها</label><input class="form-control" name="plate_region"></div>
                        <div class="col-md-6 mb-2"><label>اسم مالك السيارة</label><input class="form-control" name="owner_name"></div>
                        <div class="col-md-6 mb-2"><label>نوع السيارة</label><input class="form-control" name="truck_type"></div>
                        <div class="col-md-6 mb-2"><label>رقم رخصة التشغيل</label><input class="form-control" name="operation_license_number"></div>
                        <div class="col-md-6 mb-2"><label>جهة صدورها</label><input class="form-control" name="operation_license_issuer"></div>
                        <div class="col-md-6 mb-2"><label>الحمولة الإجمالية</label><input class="form-control" name="total_load"></div>
                        <div class="col-md-6 mb-2"><label>السائق الافتراضي</label>
                            <select class="form-control" name="default_driver_id" id="truck_default_driver">
                                <option value="">-- بدون --</option>
                                @foreach ($drivers as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2"><label>ملاحظات</label><input class="form-control" name="notes"></div>
                    </div>
                    <div class="wb-err"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">حفظ الشاحنة</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
            </form>
        </div>
    </div>
</div>

{{-- ============ مودال عميل جديد ============ --}}
<div class="modal fade wb-modal" id="customerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة عميل جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form id="customerForm" data-url="{{ url($lp . '/waybills/customers') }}" data-target="customer">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><label>اسم العميل *</label><input class="form-control" name="name" required></div>
                        <div class="col-md-6 mb-2"><label>الجوال</label><input class="form-control" name="phone"></div>
                        <div class="col-md-6 mb-2"><label>المدينة</label><input class="form-control" name="city"></div>
                        <div class="col-md-6 mb-2"><label>الرقم الضريبي</label><input class="form-control" name="tax_no"></div>
                        <div class="col-md-12 mb-2"><label>العنوان</label><input class="form-control" name="address"></div>
                        <div class="col-md-12 mb-2"><label>ملاحظات</label><input class="form-control" name="notes"></div>
                    </div>
                    <div class="wb-err"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">حفظ العميل</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
(function () {
    // بيانات السائقين والشاحنات والعملاء للتعبئة التلقائية
    var WB = {
        driver:   @json($drivers->keyBy('id')),
        truck:    @json($trucks->keyBy('id')),
        customer: @json($customers->keyBy('id'))
    };
    var CSRF = '{{ csrf_token() }}';
    var isEdit = {{ $w ? 'true' : 'false' }};

    // ---------- select2 ----------
    function initSelect(el) {
        if ($.fn.select2) {
            $(el).select2({ width: '100%', dir: 'rtl', allowClear: true, placeholder: $(el).data('placeholder') });
        }
    }
    $('.wb-select').each(function () { initSelect(this); });

    // ---------- التعبئة التلقائية ----------
    function fillDriver(id) {
        var d = WB.driver[id];
        if (!d) { $('#driver_name').val(''); return; }
        $('#driver_name').val(d.name || '');
        $('#driver_license_number').val(d.license_number || '');
        $('#driver_license_issue_date').val(d.license_issue_date || '');
    }
    function fillTruck(id) {
        var t = WB.truck[id];
        if (!t) { $('#plate_number').val(''); return; }
        $('#plate_number').val(t.plate_number || '');
        $('#plate_region').val(t.plate_region || '');
        $('#owner_name').val(t.owner_name || '');
        $('#operation_license_number').val(t.operation_license_number || '');
        $('#operation_license_issuer').val(t.operation_license_issuer || '');
        $('#truck_type').val(t.truck_type || '');
        $('#total_load').val(t.total_load || '');
        // الشاحنة => سائقها (وتقدر تغيّره بعد كده)
        if (t.default_driver_id && !linking && String($('#driver_id').val()) !== String(t.default_driver_id)) {
            linking = true;
            $('#driver_id').val(String(t.default_driver_id)).trigger('change');
            linking = false;
        }
    }
    // السائق => الشاحنة بتاعته (لو ليه شاحنة مسجل عليها كسائق افتراضي)
    function truckOfDriver(driverId) {
        var ids = Object.keys(WB.truck).filter(function (k) { return String(WB.truck[k].default_driver_id) === String(driverId); });
        if (!ids.length) return null;
        // الأولوية للشاحنة الفاضية (اللي مش عليها حمل)
        var free = ids.filter(function (k) { var o = $('#truck_id option[value="' + k + '"]'); return o.length && o.text().indexOf('⛔') === -1; });
        return (free[0] || ids[0]);
    }
    var linking = false;
    function fillCustomer(id) {
        var c = WB.customer[id];
        if (!c) { return; }
        $('#customer_name').val(c.name || '');
        if (!$('#destination_city').val()) { $('#destination_city').val(c.city || ''); }
    }
    $('#driver_id').on('change', function () {
        fillDriver(this.value);
        if (linking || !this.value) return;
        var tid = truckOfDriver(this.value);
        if (tid && String($('#truck_id').val()) !== String(tid)) {
            linking = true;
            $('#truck_id').val(String(tid)).trigger('change');
            linking = false;
        }
    });
    $('#truck_id').on('change', function () { fillTruck(this.value); });
    $('#customer_id').on('change', function () { fillCustomer(this.value); });

    // ---------- التاريخ الهجري ----------
    function toHijri(iso) {
        if (!iso) return '';
        try {
            var p = iso.split('-');
            var d = new Date(Date.UTC(+p[0], +p[1] - 1, +p[2], 12));
            var parts = new Intl.DateTimeFormat('en-US-u-ca-islamic-umalqura', {
                day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC'
            }).formatToParts(d);
            var o = {};
            parts.forEach(function (x) { o[x.type] = x.value; });
            return (o.year || '').replace(/\D/g, '') + '/' + o.month + '/' + o.day;
        } catch (e) { return ''; }
    }
    function updateDates() {
        var iso = $('#wb_date').val();
        var h = toHijri(iso);
        $('#date_hijri').val(h);
        $('#hijri_view').text(h);
    }
    $('#wb_date').on('change', updateDates);
    updateDates();

    // ---------- جدول البضاعة ----------
    function renumber() {
        $('#itemsTable tbody tr').each(function (i) { $(this).find('.wb-idx').text(i + 1); });
    }
    function calcTotal() {
        var t = 0;
        $('.wb-fare').each(function () { t += parseFloat(this.value) || 0; });
        $('#totalFare').text(t.toLocaleString('en-US', { maximumFractionDigits: 2 }));
    }
    $('#addRow').on('click', function () {
        var $r = $('#itemsTable tbody tr:last').clone();
        $r.find('input').val('');
        $('#itemsTable tbody').append($r);
        renumber();
        $r.find('input:first').focus();
    });
    $('#itemsTable').on('click', '.wb-del-row', function () {
        var $rows = $('#itemsTable tbody tr');
        var $r = $(this).closest('tr');
        if ($rows.length > 1) { $r.remove(); } else { $r.find('input').val(''); }
        renumber(); calcTotal();
    });
    $('#itemsTable').on('input', '.wb-fare', calcTotal);
    calcTotal();

    // Enter ينتقل للحقل التالي بدل ما يحفظ الفورم
    $('#waybillForm').on('keydown', 'input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var $all = $('#waybillForm').find('input:visible, select').not('[type=hidden]');
            var idx = $all.index(this);
            if (idx > -1 && idx + 1 < $all.length) { $all.eq(idx + 1).focus(); }
        }
    });

    // ---------- حفظ سائق/شاحنة/عميل عن طريق Ajax ----------
    $('#driverForm, #truckForm, #customerForm').on('submit', function (e) {
        e.preventDefault();
        var $f = $(this), type = $f.data('target'), $err = $f.find('.wb-err').hide();
        var $btn = $f.find('[type=submit]').prop('disabled', true);
        $.ajax({
            url: $f.data('url'),
            type: 'POST',
            data: $f.serialize() + '&_token=' + encodeURIComponent(CSRF),
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            success: function (res) {
                var item = res.data;
                WB[type][item.id] = item;
                var label;
                if (type === 'driver') {
                    label = item.name;
                    $('#truck_default_driver').append(new Option(item.name, item.id));
                } else if (type === 'truck') {
                    label = item.plate_number + (item.truck_type ? ' - ' + item.truck_type : '');
                } else {
                    label = item.name + (item.phone ? ' - ' + item.phone : '');
                }
                var $sel = $('#' + type + '_id');
                $sel.append(new Option(label, item.id, true, true)).trigger('change');
                $f[0].reset();
                $f.closest('.modal').modal('hide');
            },
            error: function (xhr) {
                var msg = 'حدث خطأ أثناء الحفظ';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).map(function (a) { return a[0]; }).join('<br>');
                }
                $err.html(msg).show();
            },
            complete: function () { $btn.prop('disabled', false); }
        });
    });
})();
</script>
@endsection
