{{-- مودال إضافة / تعديل سائق (رقم الجوال إجباري) --}}
<div class="modal fade" id="driverModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="dmTitle">إضافة سائق</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" id="dmForm" action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/trucks/drivers') }}">
            {{ csrf_field() }}
            <input type="hidden" name="driver_id" id="dmId">
            <div class="modal-body">
                <div class="alert alert-warning" id="dmExists" style="display:none"></div>
                <div class="alert alert-danger" id="dmErr" style="display:none"></div>
                <div class="row">
                <div class="col-md-6 mb-2"><label>اسم السائق *</label><input name="name" id="dmName" class="form-control" required></div>
                <div class="col-md-6 mb-2"><label>رقم الجوال *</label><input name="phone" id="dmPhone" class="form-control" required dir="ltr" placeholder="05xxxxxxxx" inputmode="tel" maxlength="10" pattern="05[0-9]{8}" title="رقم جوال سعودي 10 أرقام يبدأ بـ 05">
                    <small id="dmPhoneMsg" style="display:block;margin-top:4px;font-weight:700;font-size:12px;color:#94A3B8">10 أرقام ويبدأ بـ 05</small></div>
                <div class="col-md-6 mb-2"><label>رقم الهوية / الإقامة</label><input name="id_number" id="dmIdNo" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>الجنسية</label><input name="nationality" id="dmNat" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>رقم رخصة القيادة</label><input name="license_number" id="dmLic" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>تاريخ صدور الرخصة</label><input name="license_issue_date" id="dmLicDate" class="form-control"></div>
                <div class="col-md-12 mb-2"><label>ملاحظات</label><input name="notes" id="dmNotes" class="form-control"></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-warning" id="dmUseExisting" style="display:none"><i class="bx bx-check"></i> اختار السائق الموجود</button><button class="btn btn-primary" id="dmSave"><i class="bx bx-save"></i> حفظ السائق</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
        </form>
    </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery) return;
    // التحقق من رقم الجوال وأنت بتكتب + تحويل الأرقام العربية
    jQuery(document).on('input', '#dmPhone', function () {
        var ar = '٠١٢٣٤٥٦٧٨٩', v = this.value.replace(/[٠-٩]/g, function (c) { return ar.indexOf(c); }).replace(/\D/g, '');
        if (v.indexOf('966') === 0) v = '0' + v.slice(3);
        if (v.length === 9 && v[0] === '5') v = '0' + v;
        this.value = v.slice(0, 10);
        var ok = /^05\d{8}$/.test(this.value), msg = jQuery('#dmPhoneMsg');
        if (!this.value) msg.css('color', '#94A3B8').text('10 أرقام ويبدأ بـ 05');
        else if (ok) msg.css('color', '#059669').html('✓ رقم صحيح - <a href="https://wa.me/966' + this.value.slice(1) + '" target="_blank">جرّب واتساب</a>');
        else msg.css('color', '#DC2626').text('✗ الرقم لازم يكون 10 أرقام ويبدأ بـ 05');
        this.setCustomValidity(ok ? '' : 'رقم جوال سعودي 10 أرقام يبدأ بـ 05');
    });
    // زرار "إضافة سائق" = فورم فاضي ، زرار "تعديل" = يملى البيانات
    // لو اتفتح من جوه نافذة تانية (إضافة شاحنة / تحميل) الحفظ بيتم من غير ما الصفحة تتقفل
    // والسائق بيتضاف ويتختار في قائمة السائقين اللي في النافذة دي
    var dmParent = null, dmTarget = null, dmFound = null;
    function dmReset() { jQuery('#dmExists, #dmErr, #dmUseExisting').hide(); dmFound = null; }
    jQuery(document).on('click', '[data-driver-modal]', function (e) {
        e.preventDefault();
        var d = jQuery(this).data('driver') || {};
        var open = this.closest('.modal.show');
        dmParent = open ? jQuery(open) : null;
        dmTarget = this.dataset.target ? document.querySelector(this.dataset.target) : (open ? open.querySelector('select[data-drivers]') : null);
        dmReset();
        jQuery('#dmTitle').text(d.id ? 'تعديل بيانات السائق' : 'إضافة سائق');
        jQuery('#dmId').val(d.id || '');
        jQuery('#dmName').val(d.name || ''); jQuery('#dmPhone').val(d.phone || '');
        jQuery('#dmIdNo').val(d.id_number || ''); jQuery('#dmNat').val(d.nationality || '');
        jQuery('#dmLic').val(d.license_number || ''); jQuery('#dmLicDate').val(d.license_issue_date || '');
        jQuery('#dmNotes').val(d.notes || '');
        jQuery('#dmPhone').trigger('input');
        if (dmParent) dmParent.one('hidden.bs.modal', function () { jQuery('#driverModal').modal('show'); }).modal('hide');
        else jQuery('#driverModal').modal('show');
    });
    jQuery('#driverModal').on('hidden.bs.modal', function () {
        if (dmParent) { var m = dmParent; dmParent = null; m.modal('show'); }
    });

    function dmPick(d) {
        var label = d.name + (d.phone ? ' - ' + d.phone : '');
        document.querySelectorAll('select[data-drivers]').forEach(function (s) {
            var o = s.querySelector('option[value="' + d.id + '"]');
            if (!o) { o = new Option(label, d.id); s.add(o); } else { o.text = label; }
            if (d.driver) o.dataset.driver = JSON.stringify(d.driver);
        });
        if (dmTarget) { dmTarget.value = d.id; jQuery(dmTarget).trigger('change'); }
    }
    jQuery('#dmUseExisting').on('click', function () {
        if (dmFound) dmPick(dmFound);
        jQuery('#driverModal').modal('hide');
    });

    document.getElementById('dmForm').addEventListener('submit', function (e) {
        if (!dmParent && !dmTarget) return;   // من صفحة السائقين / اللوحة: الحفظ العادي
        e.preventDefault();
        dmReset();
        var btn = document.getElementById('dmSave'); btn.disabled = true;
        fetch(this.action, { method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json' }, body: new FormData(this) })
            .then(function (r) { return r.json(); })
            .then(function (j) {
                btn.disabled = false;
                if (!j.ok) {
                    var m = j.errors ? Object.values(j.errors).map(function (x) { return x[0]; }) : [j.message || 'حصل خطأ'];
                    jQuery('#dmErr').html(m.join('<br>')).show(); return;
                }
                if (j.existed) {
                    // موجود مسبقاً: نعرض رسالة ونسيبه يختار الموجود
                    dmFound = j;
                    jQuery('#dmExists').html('<i class="bx bx-error"></i> <b>السائق موجود مسبقاً</b><br>' + j.name + ' - <span dir="ltr">' + j.phone + '</span>').show();
                    jQuery('#dmUseExisting').show();
                    return;
                }
                dmPick(j);
                jQuery('#driverModal').modal('hide');
                if (window.toastr) toastr.success(j.message);
            })
            .catch(function () { btn.disabled = false; jQuery('#dmErr').text('حصل خطأ في الاتصال').show(); });
    });
});
</script>
