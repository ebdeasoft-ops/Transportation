{{--
    «إضافة شحنة جديدة» من أي شاشة (الرئيسية / تقرير الأحمال) من غير ما تروح للوحة الشاحنات
    الاستخدام: أي زرار أو لينك عليه الكلاس js-qload
--}}
@php
    $qlLp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $qlTrucks = collect(); $qlDrivers = collect(); $qlCustomers = collect();
    try {
        $qlTrucks    = \App\Models\waybill_truck::whereDoesntHave('activeTrip')->where('ownership', 'own')->orderBy('plate_number')->get();
        $qlDrivers   = \App\Models\waybill_driver::orderBy('name')->get();
        $qlCustomers = \App\Models\financial_accounts::where('orginal_type', 1)->orderBy('name')->get(['id', 'name', 'account_number']);
    } catch (\Throwable $e) { report($e); }
    $qlRegions = \App\Models\truck_trip::REGIONS;
    $qlReopen  = old('_quick') && $errors->any();
    $qlOld = function ($k) use ($qlReopen) { return $qlReopen ? old($k) : null; };
@endphp
<style>
.ql-own{ display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:800; padding:3px 10px; border-radius:999px; }
.ql-own.own{ background:#DBEAFE; color:#1D4ED8; } .ql-own.external{ background:#EDE9FE; color:#6D28D9; } .ql-own.none{ background:#FEE2E2; color:#B91C1C; }
#qLoadModal label{ font-weight:700; font-size:13px; margin-bottom:3px; }
</style>

<div class="modal fade" id="qLoadModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bx bx-package"></i> إضافة شحنة جديدة</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" action="{{ url($qlLp . '/trucks/trips') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="_quick" value="1">
            <div class="modal-body">
                @if ($qlReopen)
                    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <div class="row">
                    <div class="col-md-8 mb-2"><label>الشاحنة (المتاحة - ملك المؤسسة) *</label>
                        <select name="truck_id" id="qlTruck" class="form-control" required>
                            <option value="">اختار الشاحنة</option>
                            @foreach ($qlTrucks as $t)
                                <option value="{{ $t->id }}" data-region="{{ $t->current_region }}" data-city="{{ $t->current_city }}" data-driver="{{ $t->default_driver_id }}" data-own="{{ $t->ownership }}" {{ $qlOld('truck_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->plate_number }}{{ $t->current_region ? ' - ' . $t->current_region : '' }}{{ $t->truck_type ? ' (' . $t->truck_type . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @if ($qlTrucks->isEmpty())<small class="text-danger">مفيش شاحنات متاحة (ملك المؤسسة) دلوقتي</small>@endif
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end"><span class="ql-own" id="qlOwn" style="display:none"></span></div>

                    <div class="col-md-6 mb-2"><label>من (منطقة التحميل) *</label>
                        <select name="from_region" id="qlFrom" class="form-control" required><option value="">اختار المنطقة</option>@foreach ($qlRegions as $r)<option value="{{ $r }}" {{ $qlOld('from_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label>إلى (منطقة التنزيل) *</label>
                        <select name="to_region" class="form-control" required><option value="">اختار المنطقة</option>@foreach ($qlRegions as $r)<option value="{{ $r }}" {{ $qlOld('to_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label>مدينة التحميل</label><input name="from_city" id="qlFromCity" class="form-control" value="{{ $qlOld('from_city') }}"></div>
                    <div class="col-md-6 mb-2"><label>مدينة التنزيل</label><input name="to_city" class="form-control" value="{{ $qlOld('to_city') }}"></div>
                    <div class="col-md-6 mb-2"><label>نوع التحميل *</label><input name="load_type" class="form-control" list="qlLoadTypes" required value="{{ $qlOld('load_type') }}" placeholder="مثال: مواد بناء، مواد غذائية، حديد...">
                        <datalist id="qlLoadTypes"><option value="مواد بناء"><option value="مواد غذائية"><option value="حديد"><option value="أسمنت"><option value="أثاث"><option value="معدات"><option value="مبردات"><option value="مواد بترولية"><option value="بضائع عامة"></datalist></div>
                    <div class="col-md-6 mb-2"><label>الوزن / الكمية</label><input name="load_weight" class="form-control" value="{{ $qlOld('load_weight') }}" placeholder="مثال: 25 طن"></div>
                    <div class="col-md-6 mb-2"><label>معاد التحميل *</label><input type="datetime-local" name="loading_at" id="qlLoadAt" class="form-control" required value="{{ $qlOld('loading_at') }}"></div>
                    <div class="col-md-6 mb-2"><label>معاد التنزيل المتوقع *</label><input type="datetime-local" name="expected_unloading_at" id="qlEta" class="form-control" required value="{{ $qlOld('expected_unloading_at') }}"></div>
                    <div class="col-md-6 mb-2"><label class="d-flex justify-content-between">السائق <a href="#" data-driver-modal style="font-size:12px">+ سائق جديد</a></label>
                        <select name="driver_id" id="qlDriver" class="form-control" data-drivers><option value="">—</option>@foreach ($qlDrivers as $d)<option value="{{ $d->id }}" {{ $qlOld('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}{{ $d->phone ? ' - ' . $d->phone : '' }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label class="d-flex justify-content-between">اسم الشركة (العميل) <a href="#" data-customer-modal style="font-size:12px">+ عميل جديد</a></label>
                        <select name="customer_account_id" id="qlCustomer" class="form-control" data-customers><option value="">اختار العميل</option>@foreach ($qlCustomers as $c)<option value="{{ $c->id }}" {{ $qlOld('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->account_number ? ' (' . $c->account_number . ')' : '' }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label>رقم الفاتورة</label><input name="invoice_number" class="form-control" value="{{ $qlOld('invoice_number') }}"></div>
                    <div class="col-md-6 mb-2"><label>مرجع</label><input name="reference_no" class="form-control" value="{{ $qlOld('reference_no') }}"></div>
                    <div class="col-md-6 mb-2"><label>السعر (ر.س)</label><input type="number" step="any" min="0" name="price" class="form-control" value="{{ $qlOld('price') }}"></div>
                    <div class="col-md-6 mb-2"><label>المرفق (فاتورة / صورة - PDF أو صورة)</label><input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp"></div>
                    <div class="col-md-6 mb-2"><label>رقم البوليصة</label><input name="waybill_no" class="form-control" value="{{ $qlOld('waybill_no') }}"></div>
                    <div class="col-md-6 mb-2"><label>ملاحظات</label><input name="notes" class="form-control" value="{{ $qlOld('notes') }}"></div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ url('trucks/board') }}" class="btn btn-link btn-sm me-auto">لوحة الشاحنات / إضافة شاحنة</a>
                <button class="btn btn-success" id="qlSubmit"><i class="bx bx-upload"></i> تسجيل الشحنة</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var truckEdit = "{{ url($qlLp . '/trucks') }}";
    function nowRiyadh(addH) {
        var p = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Riyadh', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false }).formatToParts(new Date(Date.now() + (addH || 0) * 3600e3));
        var o = {}; p.forEach(function (x) { o[x.type] = x.value; });
        return o.year + '-' + o.month + '-' + o.day + 'T' + (o.hour === '24' ? '00' : o.hour) + ':' + o.minute;
    }
    var sel = document.getElementById('qlTruck'), own = document.getElementById('qlOwn'), submit = document.getElementById('qlSubmit');

    // اختيار الشاحنة => يملا المكان والسائق ويعرض الملكية
    function onTruck(fill) {
        var o = sel.options[sel.selectedIndex];
        if (!o || !o.value) { own.style.display = 'none'; submit.disabled = false; return; }
        var v = o.dataset.own;
        own.style.display = '';
        if (v === 'own' || v === 'external') {
            own.className = 'ql-own ' + v;
            own.innerHTML = v === 'own' ? '<i class="bx bx-buildings"></i> خاص بالمؤسسة' : '<i class="bx bx-transfer"></i> إيجار خارجي';
            submit.disabled = false;
        } else {
            own.className = 'ql-own none';
            own.innerHTML = '<i class="bx bx-error"></i> حدد ملكية الشاحنة الأول - <a href="' + truckEdit + '/' + o.value + '/edit" style="color:inherit;text-decoration:underline">بيانات الشاحنة</a>';
            submit.disabled = true;
        }
        if (fill) {
            if (o.dataset.region) document.getElementById('qlFrom').value = o.dataset.region;
            document.getElementById('qlFromCity').value = o.dataset.city || '';
            document.getElementById('qlDriver').value = o.dataset.driver || '';
        }
    }
    sel.addEventListener('change', function () { onTruck(true); });

    // السائق => الشاحنة بتاعته (من الشاحنات المتاحة)، وتقدر تغيّرها
    var drv = document.getElementById('qlDriver');
    function onDriver() {
        if (!drv.value) return;
        var cur = sel.options[sel.selectedIndex];
        if (cur && cur.value && cur.dataset.driver === drv.value) return;   // نفس الشاحنة بتاعته
        var match = Array.prototype.find.call(sel.options, function (o) { return o.value && o.dataset.driver === drv.value; });
        if (match) {
            sel.value = match.value;
            if (window.jQuery && $(sel).data('select2')) $(sel).trigger('change.select2');
            onTruck(false);
            // المنطقة والمدينة من مكان الشاحنة
            if (match.dataset.region) document.getElementById('qlFrom').value = match.dataset.region;
            document.getElementById('qlFromCity').value = match.dataset.city || '';
        }
    }
    drv.addEventListener('change', onDriver);
    if (window.jQuery) $(drv).on('change', function (e) { if (!e.originalEvent) onDriver(); });

    document.addEventListener('click', function (e) {
        var b = e.target.closest('.js-qload');
        if (!b) return;
        e.preventDefault();
        if (!document.getElementById('qlLoadAt').value) document.getElementById('qlLoadAt').value = nowRiyadh(0);
        if (!document.getElementById('qlEta').value) document.getElementById('qlEta').value = nowRiyadh(24);
        $('#qLoadModal').modal('show');
    });

    if (window.jQuery && $.fn.select2) {
        $('#qlCustomer, #qlTruck').each(function () {
            $(this).select2({ width: '100%', dir: 'rtl', dropdownParent: $('#qLoadModal') });
        });
        $('#qlTruck').on('select2:select', function () { onTruck(true); });
    }

    @if ($qlReopen)
        onTruck(false);
        $('#qLoadModal').modal('show');
    @endif
});
</script>
@include('trucks._customer_modal')
@include('trucks._driver_modal')
