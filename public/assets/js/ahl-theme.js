/* AHL Theme — تحسينات بسيطة لعنوان الصفحة (بدون ما نغيّر أي منطق في الصفحات) */
(function () {
    function run() {
        var body = document.body;
        if (!body || !body.classList.contains('ahl-ui')) return;
        var rtl = (getComputedStyle(body).direction || 'rtl') === 'rtl';
        var homeText = rtl ? 'الرئيسية' : 'Home';
        var homeUrl = (window.AHL_HOME_URL || '/dashboard');

        // أيقونة حسب اسم الصفحة
        function pickIcon(t) {
            var map = [
                [/بوليص|waybill/i, 'bxs-truck'], [/تحويل|transfer/i, 'bx-transfer-alt'], [/تصفي|liquid/i, 'bx-file'],
                [/تسعير|عرض سعر|quot/i, 'bx-purchase-tag'], [/حساب|account|شجرة|tree/i, 'bx-wallet'], [/قيد|record|entry/i, 'bx-notepad'],
                [/سند|receipt|voucher/i, 'bx-receipt'], [/تقرير|report|كشف|statement/i, 'bx-bar-chart-alt-2'], [/مستخدم|user|صلاحي|role/i, 'bx-user'],
                [/فرع|branch/i, 'bx-buildings'], [/اعداد|إعداد|setting/i, 'bx-cog'], [/عميل|customer/i, 'bx-group'], [/مورد|supplier/i, 'bx-store'],
                [/موظف|employee|راتب|salary/i, 'bx-id-card']
            ];
            for (var i = 0; i < map.length; i++) if (map[i][0].test(t)) return map[i][1];
            return 'bx-layer';
        }

        var pageTitle = (document.title || '').trim();
        document.querySelectorAll('.main-content .breadcrumb-header').forEach(function (h) {
            if (h.dataset.ahlDone) return;
            h.dataset.ahlDone = '1';
            var titleEl = h.querySelector('.content-title, .main-content-title, h2, h4');
            var text = titleEl ? titleEl.textContent.trim() : '';

            // هيدر فاضي؟ نملاه من عنوان الصفحة، ولو مفيش نخفيه
            if (!text && !h.textContent.trim()) {
                if (!pageTitle) { h.classList.add('ahl-empty'); return; }
                var wrap = document.createElement('div');
                wrap.className = 'my-auto';
                wrap.innerHTML = '<h4 class="content-title mb-0 my-auto"></h4>';
                wrap.querySelector('h4').textContent = pageTitle;
                h.insertBefore(wrap, h.firstChild);
                titleEl = wrap.querySelector('h4');
                text = pageTitle;
            }
            if (!titleEl) return;
            if (h.closest('.card-invoice, .main-content-body-invoice')) return;

            // أيقونة + مسار (الرئيسية › الصفحة)
            if (!titleEl.querySelector('.ahl-title-ico')) {
                var ico = document.createElement('span');
                ico.className = 'ahl-title-ico';
                ico.innerHTML = '<i class="bx ' + pickIcon(text) + '"></i>';
                titleEl.insertBefore(ico, titleEl.firstChild);
            }
            if (!h.querySelector('.ahl-crumb')) {
                var crumb = document.createElement('div');
                crumb.className = 'ahl-crumb';
                var a = document.createElement('a'); a.href = homeUrl; a.textContent = homeText;
                var sep = document.createElement('i'); sep.className = 'bx ' + (rtl ? 'bx-chevron-left' : 'bx-chevron-right');
                var cur = document.createElement('span'); cur.textContent = text;
                crumb.appendChild(a); crumb.appendChild(sep); crumb.appendChild(cur);
                // نحط المسار تحت العنوان (مش جنبه) جوه نفس العمود بتاع العنوان
                var holder = titleEl;
                while (holder.parentNode && holder.parentNode !== h) holder = holder.parentNode;
                if (holder === titleEl) {
                    var col = document.createElement('div');
                    h.insertBefore(col, titleEl); col.appendChild(titleEl); holder = col;
                }
                holder.appendChild(crumb);
            }
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();
})();
