{{--
    زرار «+ منطقة جديدة» جنب أي قائمة مناطق
    الاستخدام: <button type="button" class="js-add-region" data-target="#idOfSelect">+</button>
    بيضيف المنطقة ويحطها في كل قوائم المناطق اللي في الصفحة ويختارها في القائمة المطلوبة
--}}
@once
<style>
.rg-plus{ border:0; background:#EAF1FF; color:#2F6FED; border-radius:6px; padding:0 7px; font-weight:800; font-size:13px; line-height:20px; margin-inline-start:6px; cursor:pointer; }
.rg-plus:hover{ background:#2F6FED; color:#fff; }
</style>
<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest ? e.target.closest('.js-add-region') : null;
    if (!btn) return;
    e.preventDefault();
    var name = window.prompt('اسم المنطقة الجديدة:');
    if (!name || !name.trim()) return;
    var token = (document.querySelector('meta[name=csrf-token]') || {}).content || (document.querySelector('input[name=_token]') || {}).value || '';
    fetch(@json(url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/trucks/regions')), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ name: name.trim() })
    }).then(function (r) { return r.json(); }).then(function (d) {
        if (!d.ok) { alert(d.message || 'حصل خطأ'); return; }
        document.querySelectorAll('select[name=from_region], select[name=to_region], select[name=current_region], select[name=unload_region]').forEach(function (s) {
            var exists = Array.prototype.some.call(s.options, function (o) { return o.value === d.name; });
            if (!exists) s.add(new Option(d.name, d.name));
        });
        var t = btn.getAttribute('data-target') ? document.querySelector(btn.getAttribute('data-target'))
              : (btn.parentElement.parentElement ? btn.parentElement.parentElement.querySelector('select') : null);
        if (t) { t.value = d.name; t.dispatchEvent(new Event('change', { bubbles: true })); }
    }).catch(function () { alert('مقدرناش نضيف المنطقة، جرّب تاني'); });
});
</script>
@endonce
