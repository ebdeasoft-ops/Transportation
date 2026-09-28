{{--
    «تم التفريغ» بخطوات (من أي شاشة: اللوحة / الرئيسية / تقرير الأحمال)
    1) بيانات التفريغ  2) المرفق (اختياري)  3) مراجعة وتأكيد
    الاستخدام: زرار بالكلاس js-qunload وعليه data-trip / data-plate / data-to / data-city
--}}
@php
    $quLp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $quToast = $toast ?? true;
@endphp
<style>
.qu-btn{ display:inline-flex; align-items:center; gap:4px; background:#10B981; color:#fff !important; border:0; border-radius:9px; padding:5px 10px; font-size:12px; font-weight:800; cursor:pointer; white-space:nowrap; transition:.15s; }
.qu-btn:hover{ background:#059669; transform:translateY(-1px); }
.qu-btn i{ font-size:15px; }
.qu-toast{ position:fixed; bottom:22px; inset-inline-start:22px; z-index:2000; background:#10B981; color:#fff; padding:12px 18px; border-radius:12px; font-weight:800; box-shadow:0 10px 30px -10px rgba(16,185,129,.8); display:flex; align-items:center; gap:8px; animation:quIn .3s ease; }
@media (max-width:575px){ .db-trip{ flex-wrap:wrap; } }
@keyframes quIn{ from{ opacity:0; transform:translateY(10px); } to{ opacity:1; transform:none; } }

/* الخطوات */
.qu-steps{ display:flex; align-items:flex-start; justify-content:space-between; position:relative; margin:4px 6px 22px; }
.qu-steps:before{ content:""; position:absolute; top:17px; inset-inline:34px; height:3px; background:#E6EBF2; border-radius:3px; }
.qu-bar{ position:absolute; top:17px; inset-inline-start:34px; height:3px; background:#10B981; border-radius:3px; transition:width .35s ease; width:0; }
.qu-step{ position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; gap:6px; width:33%; text-align:center; }
.qu-dot{ width:36px; height:36px; border-radius:50%; background:#fff; border:3px solid #E6EBF2; color:#94A3B8; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; transition:.25s; }
.qu-step span{ font-size:12px; font-weight:800; color:#94A3B8; line-height:1.3; }
.qu-step span small{ display:block; font-weight:700; font-size:10.5px; }
.qu-step.active .qu-dot{ border-color:#10B981; color:#10B981; box-shadow:0 0 0 5px rgba(16,185,129,.15); }
.qu-step.active span{ color:#0F172A; }
.qu-step.done .qu-dot{ background:#10B981; border-color:#10B981; color:#fff; }
.qu-step.done span{ color:#047857; }
.qu-pane{ display:none; animation:quIn .25s ease; }
.qu-pane.on{ display:block; }
.qu-drop{ border:2px dashed #CBD5E1; border-radius:14px; padding:26px 16px; text-align:center; cursor:pointer; transition:.15s; background:#FAFBFD; display:block; margin:0; }
.qu-drop:hover, .qu-drop.over{ border-color:#10B981; background:#F0FDF9; }
.qu-drop i{ font-size:40px; color:#10B981; display:block; margin-bottom:6px; }
.qu-drop b{ display:block; font-size:14px; } .qu-drop small{ color:#64748B; font-weight:600; }
.qu-file{ display:none; align-items:center; gap:10px; border:1.5px solid #A7F3D0; background:#ECFDF5; border-radius:12px; padding:10px 12px; margin-top:10px; }
.qu-file.on{ display:flex; }
.qu-file img{ width:52px; height:52px; object-fit:cover; border-radius:8px; }
.qu-file .ic{ width:52px; height:52px; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; font-size:26px; color:#DC2626; }
.qu-file b{ display:block; font-size:13px; word-break:break-all; } .qu-file small{ color:#64748B; }
.qu-file button{ margin-inline-start:auto; border:0; background:#fff; color:#DC2626; border-radius:8px; padding:6px 10px; font-weight:800; cursor:pointer; }
.qu-review{ border:1px solid #E6EBF2; border-radius:12px; overflow:hidden; margin-bottom:12px; }
.qu-review div{ display:flex; justify-content:space-between; gap:10px; padding:8px 12px; border-bottom:1px solid #F1F5F9; font-size:13px; }
.qu-review div:last-child{ border-bottom:0; }
.qu-review span{ color:#64748B; font-weight:700; } .qu-review b{ text-align:end; }
.qu-err{ color:#DC2626; font-size:12px; font-weight:700; display:none; margin-top:4px; }
</style>

@if ($quToast && session('trip_ok'))
    <div class="qu-toast" id="quToast"><i class="bx bx-check-circle" style="font-size:20px"></i> {{ session('trip_ok') }}</div>
@endif

<div class="modal fade" id="qUnloadModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bx bx-check-double"></i> تم التفريغ - <span id="quPlate" dir="ltr"></span></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" id="quForm" action="" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="qu-steps">
                    <i class="qu-bar" id="quBar"></i>
                    <div class="qu-step" data-s="1"><div class="qu-dot">1</div><span>بيانات التفريغ</span></div>
                    <div class="qu-step" data-s="2"><div class="qu-dot">2</div><span>المرفق<small>اختياري</small></span></div>
                    <div class="qu-step" data-s="3"><div class="qu-dot">3</div><span>مراجعة وتأكيد</span></div>
                </div>

                {{-- 1) بيانات التفريغ --}}
                <div class="qu-pane" data-p="1">
                    <div class="mb-2"><label>معاد التفريغ الفعلي *</label><input type="datetime-local" name="unloaded_at" id="quAt" class="form-control">
                        <div class="qu-err" id="quAtErr">حدد معاد التفريغ</div></div>
                    <div class="mb-2"><label>منطقة التفريغ (الشاحنة هتبقى فاضية فيها)<button type="button" class="rg-plus js-add-region" title="إضافة منطقة جديدة">+</button></label>
                        <select name="unload_region" id="quRegion" class="form-control">@foreach (\App\Models\truck_trip::regions() as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach</select></div>
                    <div class="mb-2"><label>المدينة</label><input name="unload_city" id="quCity" class="form-control"></div>
                </div>

                {{-- 2) المرفق (اختياري) --}}
                <div class="qu-pane" data-p="2">
                    <label class="qu-drop" id="quDrop" for="quFileInput">
                        <i class="bx bx-cloud-upload"></i>
                        <b>ارفع إثبات التفريغ</b>
                        <small>سند استلام / صورة / PDF · حتى 5 ميجا · <u>اختياري</u></small>
                    </label>
                    <input type="file" name="unload_attachment" id="quFileInput" accept=".pdf,.jpg,.jpeg,.png,.webp" hidden>
                    <div class="qu-file" id="quFile">
                        <span id="quThumb"></span>
                        <div><b id="quFileName"></b><small id="quFileSize"></small></div>
                        <button type="button" id="quFileDel"><i class="bx bx-trash"></i> حذف</button>
                    </div>
                    <div class="qu-err" id="quFileErr"></div>
                </div>

                {{-- 3) مراجعة وتأكيد --}}
                <div class="qu-pane" data-p="3">
                    <div class="qu-review">
                        <div><span>الشاحنة</span><b id="rvPlate" dir="ltr"></b></div>
                        <div><span>معاد التفريغ</span><b id="rvAt" dir="ltr"></b></div>
                        <div><span>مكان التفريغ</span><b id="rvPlace"></b></div>
                        <div><span>المرفق</span><b id="rvFile"></b></div>
                    </div>
                    <div class="mb-1"><label>ملاحظات</label><input name="unload_notes" class="form-control" placeholder="اختياري"></div>
                    <small class="text-muted"><i class="bx bx-info-circle"></i> بعد التأكيد الشاحنة هتبقى فاضية في منطقة التفريغ.</small>
                </div>
            </div>
            <div class="modal-footer" style="justify-content:space-between">
                <button type="button" class="btn btn-light" id="quPrev"><i class="bx bx-right-arrow-alt"></i> السابق</button>
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" id="quSkip">تخطي</button>
                    <button type="button" class="btn btn-primary" id="quNext">التالي <i class="bx bx-left-arrow-alt"></i></button>
                    <button type="submit" class="btn btn-success" id="quSubmit"><i class="bx bx-check-double"></i> تأكيد التفريغ</button>
                </div>
            </div>
        </form>
    </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var base = "{{ url($quLp . '/trucks/trips') }}";
    var $ = window.jQuery, step = 1, MAX = 5 * 1024 * 1024;
    function el(id) { return document.getElementById(id); }
    function nowRiyadh() {
        var p = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Riyadh', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false }).formatToParts(new Date());
        var o = {}; p.forEach(function (x) { o[x.type] = x.value; });
        return o.year + '-' + o.month + '-' + o.day + 'T' + (o.hour === '24' ? '00' : o.hour) + ':' + o.minute;
    }
    function fmtAt(v) { if (!v) return '—'; var d = v.split('T'); return d[0].split('-').reverse().join('/') + ' ' + (d[1] || ''); }

    function go(s) {
        step = s;
        document.querySelectorAll('#qUnloadModal .qu-pane').forEach(function (p) { p.classList.toggle('on', +p.dataset.p === s); });
        document.querySelectorAll('#qUnloadModal .qu-step').forEach(function (x) {
            var n = +x.dataset.s;
            x.classList.toggle('active', n === s); x.classList.toggle('done', n < s);
            x.querySelector('.qu-dot').innerHTML = n < s ? '<i class="bx bx-check"></i>' : n;
        });
        el('quBar').style.width = 'calc((100% - 68px) * ' + ((s - 1) / 2) + ')';
        el('quPrev').style.visibility = s > 1 ? 'visible' : 'hidden';
        el('quSkip').style.display = (s === 2 && !el('quFileInput').files.length) ? '' : 'none';
        el('quNext').style.display = s < 3 ? '' : 'none';
        el('quSubmit').style.display = s === 3 ? '' : 'none';
        if (s === 3) {
            var f = el('quFileInput').files[0];
            el('rvPlate').textContent = el('quPlate').textContent;
            el('rvAt').textContent = fmtAt(el('quAt').value);
            el('rvPlace').textContent = el('quRegion').value + (el('quCity').value ? ' - ' + el('quCity').value : '');
            el('rvFile').innerHTML = f ? '<i class="bx bx-paperclip"></i> ' + f.name : '<span style="color:#94A3B8">بدون مرفق</span>';
        }
    }
    function valid1() {
        var ok = !!el('quAt').value;
        el('quAtErr').style.display = ok ? 'none' : 'block';
        return ok;
    }
    el('quNext').addEventListener('click', function () { if (step === 1 && !valid1()) return; go(step + 1); });
    el('quPrev').addEventListener('click', function () { if (step > 1) go(step - 1); });
    el('quSkip').addEventListener('click', function () { clearFile(); go(3); });
    el('quForm').addEventListener('submit', function (e) {
        if (step !== 3) { e.preventDefault(); if (step === 1 && !valid1()) return; go(step + 1); return; }
        if (!valid1()) { e.preventDefault(); go(1); return; }
        el('quSubmit').disabled = true;
        el('quSubmit').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> جاري الحفظ...';
    });

    // ---------- المرفق ----------
    function clearFile() { el('quFileInput').value = ''; showFile(); }
    function showFile() {
        var f = el('quFileInput').files[0], box = el('quFile'), err = el('quFileErr');
        err.style.display = 'none';
        if (f && f.size > MAX) { err.textContent = 'حجم الملف أكبر من 5 ميجا'; err.style.display = 'block'; el('quFileInput').value = ''; f = null; }
        if (f && !/\.(pdf|jpe?g|png|webp)$/i.test(f.name)) { err.textContent = 'الملف لازم يكون PDF أو صورة'; err.style.display = 'block'; el('quFileInput').value = ''; f = null; }
        box.classList.toggle('on', !!f);
        el('quDrop').style.display = f ? 'none' : '';
        el('quSkip').style.display = (step === 2 && !f) ? '' : 'none';
        if (!f) return;
        el('quFileName').textContent = f.name;
        el('quFileSize').textContent = (f.size / 1024 > 1024 ? (f.size / 1048576).toFixed(1) + ' ميجا' : Math.ceil(f.size / 1024) + ' كيلو');
        if (/^image\//.test(f.type)) {
            var r = new FileReader(); r.onload = function () { el('quThumb').innerHTML = '<img src="' + r.result + '">'; }; r.readAsDataURL(f);
        } else el('quThumb').innerHTML = '<span class="ic"><i class="bx bxs-file-pdf"></i></span>';
    }
    el('quFileInput').addEventListener('change', showFile);
    el('quFileDel').addEventListener('click', clearFile);
    var drop = el('quDrop');
    ['dragenter', 'dragover'].forEach(function (t) { drop.addEventListener(t, function (e) { e.preventDefault(); drop.classList.add('over'); }); });
    ['dragleave', 'drop'].forEach(function (t) { drop.addEventListener(t, function (e) { e.preventDefault(); drop.classList.remove('over'); }); });
    drop.addEventListener('drop', function (e) { if (e.dataTransfer.files.length) { el('quFileInput').files = e.dataTransfer.files; showFile(); } });

    // ---------- فتح النافذة ----------
    document.addEventListener('click', function (e) {
        var b = e.target.closest('.js-qunload');
        if (!b) return;
        e.preventDefault();
        el('quForm').reset();
        el('quSubmit').disabled = false;
        el('quSubmit').innerHTML = '<i class="bx bx-check-double"></i> تأكيد التفريغ';
        el('quPlate').textContent = b.dataset.plate || '';
        el('quForm').action = base + '/' + b.dataset.trip + '/unload';
        el('quRegion').value = b.dataset.to || el('quRegion').options[0].value;
        el('quCity').value = b.dataset.city || '';
        el('quAt').value = nowRiyadh();
        el('quAtErr').style.display = 'none';
        clearFile(); go(1);
        $('#qUnloadModal').modal('show');
    });
    var t = el('quToast');
    if (t) setTimeout(function () { t.style.transition = 'opacity .4s'; t.style.opacity = 0; setTimeout(function () { t.remove(); }, 400); }, 4000);
});
</script>
@include('trucks._region_add')
