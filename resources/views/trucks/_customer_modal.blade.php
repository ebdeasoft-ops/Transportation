{{--
    إضافة عميل جديد (بيتسجل في شجرة الحسابات تحت العملاء)
    - أي زرار عليه data-customer-modal بيفتح النافذة
    - بعد الحفظ العميل بيتضاف لكل select عليه data-customers ويتختار في اللي كان مفتوح
--}}
@php $cmLp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(); @endphp
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bx bx-user-plus"></i> إضافة عميل جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form id="cmForm" autocomplete="off">
            <div class="modal-body">
                <div class="alert alert-danger" id="cmErr" style="display:none"></div>
                <div class="mb-2"><label>اسم العميل / الشركة *</label><input name="name" id="cmName" class="form-control" required maxlength="255"></div>
                <div class="row">
                    <div class="col-md-6 mb-2"><label>الجوال</label><input name="phone" class="form-control" dir="ltr" placeholder="05xxxxxxxx" inputmode="tel"></div>
                    <div class="col-md-6 mb-2"><label>الرقم الضريبي</label><input name="tax_no" class="form-control" dir="ltr"></div>
                </div>
                <div class="mb-2"><label>العنوان</label><input name="address" class="form-control"></div>
                <div class="mb-2"><label>ملاحظات</label><input name="notes" class="form-control"></div>
                <small class="text-muted"><i class="bx bx-info-circle"></i> العميل بيتسجل في شجرة الحسابات تحت العملاء، وبيظهر في كل الشاشات.</small>
            </div>
            <div class="modal-footer"><button class="btn btn-primary" id="cmSave"><i class="bx bx-save"></i> حفظ العميل</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
        </form>
    </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var URL_ = "{{ url($cmLp . '/trucks/customers') }}", CSRF = "{{ csrf_token() }}";
    var parentModal = null, targetSelect = null;

    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-customer-modal]');
        if (!b) return;
        e.preventDefault();
        var open = b.closest('.modal.show');
        parentModal = open ? $(open) : null;
        targetSelect = b.dataset.target ? document.querySelector(b.dataset.target) : (open ? open.querySelector('select[data-customers]') : null);
        document.getElementById('cmForm').reset();
        document.getElementById('cmErr').style.display = 'none';
        if (parentModal) {
            parentModal.one('hidden.bs.modal', function () { $('#customerModal').modal('show'); }).modal('hide');
        } else {
            $('#customerModal').modal('show');
        }
    });
    $('#customerModal').on('shown.bs.modal', function () { document.getElementById('cmName').focus(); });
    $('#customerModal').on('hidden.bs.modal', function () {
        if (parentModal) { var m = parentModal; parentModal = null; m.modal('show'); }
    });

    document.getElementById('cmForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = document.getElementById('cmSave'), err = document.getElementById('cmErr');
        btn.disabled = true; err.style.display = 'none';
        fetch(URL_, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new FormData(this) })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (res) {
                btn.disabled = false;
                if (!res.ok || !res.j.ok) {
                    var msgs = res.j.errors ? Object.values(res.j.errors).map(function (x) { return x[0]; }) : [res.j.message || 'حصل خطأ'];
                    err.innerHTML = msgs.join('<br>'); err.style.display = 'block'; return;
                }
                var c = res.j, label = c.name + (c.account_number ? ' (' + c.account_number + ')' : '');
                document.querySelectorAll('select[data-customers]').forEach(function (s) {
                    if (!s.querySelector('option[value="' + c.id + '"]')) s.add(new Option(label, c.id));
                });
                if (targetSelect) { targetSelect.value = c.id; if (window.jQuery) $(targetSelect).trigger('change'); }
                $('#customerModal').modal('hide');
                if (window.toastr) toastr.success(c.existed ? 'العميل موجود بالفعل واتختار' : 'تم إضافة العميل');
                else if (!targetSelect) alert(c.existed ? 'العميل موجود بالفعل' : 'تم إضافة العميل: ' + c.name);
            })
            .catch(function () { btn.disabled = false; err.textContent = 'حصل خطأ في الاتصال'; err.style.display = 'block'; });
    });
});
</script>
