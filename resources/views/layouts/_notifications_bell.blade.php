{{-- جرس الإشعارات (شاحنات / تحويلات / تصفيات) - بيتحدث كل 30 ثانية --}}
@php
    $nbLp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $nbPushKey = \App\Services\AppNotifier::pushEnabled() ? config('services.webpush.public_key') : '';
@endphp
<style>
.nb-wrap{ position:relative; }
.nb-btn{ position:relative; display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:12px; color:#475569; cursor:pointer; transition:.15s; }
.nb-btn:hover{ background:#F1F5FB; color:#2F6FED; }
.nb-btn svg{ width:22px; height:22px; }
.nb-count{ position:absolute; top:3px; inset-inline-end:2px; min-width:18px; height:18px; padding:0 5px; border-radius:999px; background:#EF4444; color:#fff; font-size:11px; font-weight:800; display:none; align-items:center; justify-content:center; border:2px solid #fff; }
.nb-count.on{ display:flex; animation:nbPop .35s ease; }
@keyframes nbPop{ 0%{ transform:scale(.4); } 70%{ transform:scale(1.2); } 100%{ transform:scale(1); } }
@keyframes nbRing{ 0%,100%{ transform:rotate(0); } 20%{ transform:rotate(14deg); } 40%{ transform:rotate(-12deg); } 60%{ transform:rotate(8deg); } 80%{ transform:rotate(-4deg); } }
.nb-btn.ring svg{ animation:nbRing .9s ease; transform-origin:50% 10%; }
.nb-panel{ position:fixed; top:64px; left:8px; width:370px; max-width:92vw; background:#fff; border:1px solid #E6EBF2; border-radius:16px; box-shadow:0 24px 60px -20px rgba(11,26,51,.35); z-index:2000; display:none; overflow:hidden; font-family:'Cairo',sans-serif; }
.nb-panel.open{ display:block; animation:nbIn .2s ease; }
@keyframes nbIn{ from{ opacity:0; transform:translateY(-6px); } to{ opacity:1; transform:none; } }
.nb-head{ display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:linear-gradient(135deg,#0B1A33,#15335E); color:#fff; }
.nb-head b{ font-size:14px; }
.nb-head a{ color:#BFD2FA !important; font-size:12px; font-weight:700; cursor:pointer; }
.nb-list{ max-height:420px; overflow-y:auto; }
.nb-item{ display:flex; gap:10px; padding:11px 14px; border-bottom:1px solid #F0F3F8; text-decoration:none !important; color:#0F172A !important; transition:background .12s; position:relative; }
.nb-item:hover{ background:#F5F8FF; }
.nb-item.unread{ background:#F8FBFF; }
.nb-item.unread::after{ content:""; position:absolute; top:16px; inset-inline-end:12px; width:8px; height:8px; border-radius:50%; background:#2F6FED; }
.nb-ico{ width:36px; height:36px; flex:none; border-radius:11px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px; }
.nb-txt{ min-width:0; flex:1; padding-inline-end:12px; }
.nb-title{ font-size:13px; font-weight:800; line-height:1.4; }
.nb-body{ font-size:12px; color:#475569; margin-top:2px; line-height:1.4; }
.nb-ago{ font-size:11px; color:#94A3B8; margin-top:3px; }
.nb-empty{ text-align:center; color:#94A3B8; padding:30px 10px; font-size:13px; }
.nb-foot{ display:flex; gap:8px; padding:10px 14px; background:#FAFBFD; }
.nb-push{ padding:8px 14px; font-size:12px; font-weight:700; display:none; align-items:center; gap:8px; border-top:1px solid #F0F3F8; }
.nb-push.on{ display:flex; }
.nb-push .ok{ color:#047857; flex:1; } .nb-push .off{ color:#B45309; flex:1; }
.nb-push a{ color:#2F6FED !important; cursor:pointer; text-decoration:none !important; }
.nb-foot a{ flex:1; text-align:center; font-size:12.5px; font-weight:800; padding:7px; border-radius:9px; color:#2F6FED !important; background:#EAF1FF; text-decoration:none !important; }
.nb-toast{ position:fixed; bottom:24px; inset-inline-start:24px; z-index:3000; width:340px; max-width:90vw; background:#0B1A33; color:#fff; border-radius:14px; padding:12px 14px; display:flex; gap:10px; box-shadow:0 20px 50px -15px rgba(0,0,0,.5); animation:nbToast .35s ease; cursor:pointer; font-family:'Cairo',sans-serif; }
.nb-toast .nb-body{ color:#BFD2FA; }
@keyframes nbToast{ from{ opacity:0; transform:translateY(20px); } to{ opacity:1; transform:none; } }
</style>

<div class="nav-item nb-wrap" id="nbWrap">
    <a class="nb-btn" id="nbBtn" title="الإشعارات">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        <span class="nb-count" id="nbCount">0</span>
    </a>
    <div class="nb-panel" id="nbPanel">
        <div class="nb-head"><b>الإشعارات</b><a id="nbReadAll">تعليم الكل كمقروء</a></div>
        <div class="nb-list" id="nbList"><div class="nb-empty">جاري التحميل...</div></div>
        <div class="nb-push" id="nbPushBar"></div>
        <div class="nb-foot">
            <a href="{{ url('notifications') }}">كل الإشعارات</a>
            <a href="#" id="nbPerm" style="display:none">🔔 تفعيل إشعارات المتصفح</a>
        </div>
    </div>
</div>

<script>
(function () {
    var FEED = "{{ url('notifications/feed') }}", READ_ALL = "{{ url($nbLp . '/notifications/read-all') }}", CSRF = "{{ csrf_token() }}";
    var btn = document.getElementById('nbBtn'), panel = document.getElementById('nbPanel'), list = document.getElementById('nbList'),
        cnt = document.getElementById('nbCount'), perm = document.getElementById('nbPerm');
    var KEY = 'ahl_nb_last_{{ auth()->id() }}', lastSeen = 0;
    try { lastSeen = parseInt(localStorage.getItem(KEY) || '0', 10); } catch (e) {}
    var first = true;

    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    function beep() {
        try {
            var C = window.AudioContext || window.webkitAudioContext; if (!C) return;
            var c = new C(), o = c.createOscillator(), g = c.createGain();
            o.type = 'sine'; o.frequency.value = 880; o.connect(g); g.connect(c.destination);
            g.gain.setValueAtTime(.001, c.currentTime); g.gain.exponentialRampToValueAtTime(.2, c.currentTime + .02);
            g.gain.exponentialRampToValueAtTime(.001, c.currentTime + .5); o.start(); o.stop(c.currentTime + .5);
        } catch (e) {}
    }
    function toast(n) {
        var t = document.createElement('div'); t.className = 'nb-toast';
        t.innerHTML = '<span class="nb-ico" style="background:' + esc(n.color) + '"><i class="bx ' + esc(n.icon) + '"></i></span><div class="nb-txt"><div class="nb-title">' + esc(n.title) + '</div><div class="nb-body">' + esc(n.body) + '</div></div>';
        t.onclick = function () { location = n.open; };
        document.body.appendChild(t); setTimeout(function () { t.remove(); }, 7000);
    }
    function browserNotify(n) {
        try {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;
            if (pushOn()) return;   // الـ Service Worker هو اللي بيطلّع الإشعار، منكررهوش
            var x = new Notification(n.title, { body: n.body || '', tag: 'ahl-' + n.id });
            x.onclick = function () { window.focus(); location = n.open; };
        } catch (e) {}
    }
    function render(d) {
        cnt.textContent = d.unread > 99 ? '99+' : d.unread;
        cnt.classList.toggle('on', d.unread > 0);
        if (!d.items.length) { list.innerHTML = '<div class="nb-empty"><i class="bx bx-bell-off" style="font-size:28px"></i><br>مفيش إشعارات</div>'; return; }
        list.innerHTML = d.items.map(function (n) {
            return '<a class="nb-item ' + (n.read ? '' : 'unread') + '" href="' + n.open + '">' +
                '<span class="nb-ico" style="background:' + esc(n.color) + '"><i class="bx ' + esc(n.icon) + '"></i></span>' +
                '<div class="nb-txt"><div class="nb-title">' + esc(n.title) + '</div>' + (n.body ? '<div class="nb-body">' + esc(n.body) + '</div>' : '') +
                '<div class="nb-ago">' + esc(n.ago) + '</div></div></a>';
        }).join('');
        // إشعارات جديدة من آخر مرة => صوت + تنبيه
        var fresh = d.items.filter(function (n) { return !n.read && n.id > lastSeen; });
        if (fresh.length) {
            var maxId = Math.max.apply(null, d.items.map(function (n) { return n.id; }));
            if (!first || lastSeen > 0) {
                beep(); btn.classList.remove('ring'); void btn.offsetWidth; btn.classList.add('ring');
                fresh.slice(0, 3).reverse().forEach(function (n) { toast(n); browserNotify(n); });
            }
            lastSeen = maxId; try { localStorage.setItem(KEY, String(maxId)); } catch (e) {}
        }
        first = false;
    }
    function load() {
        fetch(FEED, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; }).then(function (d) { if (d) render(d); }).catch(function () {});
    }
    function place() {
        // اللوحة دايماً جوه الشاشة تحت الجرس
        var r = btn.getBoundingClientRect(), w = Math.min(370, window.innerWidth - 16);
        var rtl = getComputedStyle(document.body).direction === 'rtl';
        var left = rtl ? r.left : r.right - w;
        left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
        panel.style.left = left + 'px'; panel.style.top = (r.bottom + 8) + 'px'; panel.style.width = w + 'px';
    }
    window.addEventListener('resize', function () { if (panel.classList.contains('open')) place(); });
    btn.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); panel.classList.toggle('open'); if (panel.classList.contains('open')) { place(); load(); } });
    document.addEventListener('click', function (e) { if (!document.getElementById('nbWrap').contains(e.target)) panel.classList.remove('open'); });
    document.getElementById('nbReadAll').addEventListener('click', function () {
        fetch(READ_ALL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, credentials: 'same-origin' }).then(load);
    });
    // ================= Web Push =================
    var PUSH_KEY = "{{ $nbPushKey }}", SUB_URL = "{{ url($nbLp . '/push/subscribe') }}",
        UNSUB_URL = "{{ url($nbLp . '/push/unsubscribe') }}", TEST_URL = "{{ url($nbLp . '/push/test') }}",
        bar = document.getElementById('nbPushBar');
    var pushSupported = !!(PUSH_KEY && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window);

    function post(url, body) {
        return fetch(url, { method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body || {}) });
    }
    // المفتاح العام جاي base64-url ولازم يتحول لـ bytes
    function keyToBytes(b64) {
        var pad = '='.repeat((4 - b64.length % 4) % 4), raw = atob((b64 + pad).replace(/-/g, '+').replace(/_/g, '/'));
        var out = new Uint8Array(raw.length); for (var i = 0; i < raw.length; i++) out[i] = raw.charCodeAt(i); return out;
    }
    function setPushFlag(on) { try { localStorage.setItem('ahl_push_on', on ? '1' : '0'); } catch (e) {} }
    function pushOn() { try { return localStorage.getItem('ahl_push_on') === '1'; } catch (e) { return false; } }

    function showBar(state) {
        bar.classList.add('on');
        if (state === 'on') {
            bar.innerHTML = '<span class="ok">✓ الإشعارات مفعّلة على الجهاز ده (حتى لو المتصفح مقفول)</span><a id="nbTest">تجربة</a> · <a id="nbOff">إيقاف</a>';
            document.getElementById('nbTest').onclick = function () { post(TEST_URL).then(function () { bar.querySelector('.ok').textContent = '✓ اتبعت إشعار تجريبي - هيوصلك خلال ثواني'; }); };
            document.getElementById('nbOff').onclick = disablePush;
        } else if (state === 'denied') {
            bar.innerHTML = '<span class="off">🔕 الإشعارات محظورة - فعّلها من 🔒 جنب الرابط ← Notifications ← Allow</span>';
        } else {
            bar.innerHTML = '<span class="off">🔔 فعّل الإشعارات عشان توصلك حتى لو المتصفح مقفول</span><a id="nbOn">تفعيل</a>';
            document.getElementById('nbOn').onclick = enablePush;
        }
    }

    function enablePush() {
        navigator.serviceWorker.register('/sw.js')                          // 1) تسجيل الـ Service Worker
            .then(function () { return Notification.requestPermission(); }) // 2) طلب الإذن
            .then(function (p) {
                if (p !== 'granted') { showBar(p === 'denied' ? 'denied' : 'off'); throw 'no'; }
                return navigator.serviceWorker.ready;
            })
            .then(function (reg) {                                          // 3) الاشتراك في خدمة الـ Push
                return reg.pushManager.getSubscription().then(function (old) {
                    return old || reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: keyToBytes(PUSH_KEY) });
                });
            })
            .then(function (sub) { return post(SUB_URL, sub.toJSON()); })    // 4) حفظ الاشتراك على السيرفر
            .then(function (r) { if (r.ok) { setPushFlag(true); showBar('on'); } else { showBar('off'); } })
            .catch(function (e) { if (e !== 'no') { console.error(e); showBar('off'); } });
    }

    function disablePush() {
        navigator.serviceWorker.ready.then(function (reg) { return reg.pushManager.getSubscription(); })
            .then(function (sub) {
                if (!sub) return;
                return post(UNSUB_URL, { endpoint: sub.endpoint }).then(function () { return sub.unsubscribe(); });
            })
            .then(function () { setPushFlag(false); showBar('off'); });
    }

    if (pushSupported) {
        // نشوف الجهاز ده مشترك ولا لأ
        navigator.serviceWorker.register('/sw.js').then(function (reg) { return reg.pushManager.getSubscription(); })
            .then(function (sub) {
                if (Notification.permission === 'denied') { setPushFlag(false); showBar('denied'); }
                else if (sub) {
                    setPushFlag(true); showBar('on');
                    post(SUB_URL, sub.toJSON());                               // نحدّث الاشتراك (لو اتغير المستخدم على نفس الجهاز)
                } else { setPushFlag(false); showBar('off'); }
            }).catch(function () { showBar('off'); });
    } else if ('Notification' in window && Notification.permission === 'default') {
        // مفيش Web Push (مفاتيح VAPID مش متظبطة أو المتصفح قديم): إشعارات التاب المفتوح بس
        perm.style.display = 'block';
        perm.addEventListener('click', function (e) { e.preventDefault(); Notification.requestPermission().then(function () { perm.style.display = 'none'; }); });
    }
    load(); setInterval(load, 30000);
})();
</script>
