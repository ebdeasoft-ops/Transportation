<style>
/* ================= فاتورة - تصميم احترافي ================= */
.inv{ --ink:#0F172A; --ink2:#475569; --line:#CBD5E1; --soft:#F1F5F9; --brand:#1E3A8A; --brand2:#2F6FED; --acc:#C9A227;
      background:#fff; color:var(--ink); max-width:210mm; margin:0 auto; padding:18px 22px; font-size:13px; }
.inv *{ box-sizing:border-box; }
.inv .t-ltr{ direction:ltr; unicode-bidi:embed; }

/* الهيدر (جدول ثابت عشان يفضل مظبوط في الشاشة والطباعة وأي ثيم) */
.inv-head{ width:100%; border-collapse:collapse; table-layout:fixed; margin:0; }
.inv-head td{ border:0 !important; padding:0 0 12px !important; vertical-align:middle !important; background:transparent !important; }
.inv-head-line{ height:3px; background:var(--brand); margin:0; }
.inv-head-line2{ height:2px; background:var(--acc); margin:3px 0 0; }
.inv-co{ line-height:1.6; }
.inv-co b{ display:block; font-size:17px; font-weight:800; color:var(--brand); margin-bottom:3px; line-height:1.3; }
.inv-co span{ display:block; font-size:12.5px; color:var(--ink2); font-weight:700; }
.inv-co.ar{ text-align:right !important; direction:rtl; }
.inv-co.en{ text-align:left !important; direction:ltr; }
.inv-logo{ text-align:center !important; }
.inv-logo img{ width:105px; height:105px; object-fit:contain; display:inline-block; }
/* عنوان الفاتورة */
.inv-title{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin:18px 0 12px; }
.inv-title h1{ margin:0; font-size:19px; font-weight:800; color:#fff; background:var(--brand); padding:8px 18px; border-radius:8px; letter-spacing:.2px; }
.inv-title h1 small{ font-size:13px; font-weight:700; opacity:.85; margin:0 10px; }
.inv-no{ text-align:center; border:2px solid var(--brand); border-radius:8px; padding:4px 14px; }
.inv-no span{ display:block; font-size:10.5px; color:var(--ink2); font-weight:700; }
.inv-no b{ font-size:17px; color:var(--brand); }

/* جداول البيانات */
.inv-grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.inv-box{ border:1.5px solid var(--line); border-radius:8px; overflow:hidden; }
.inv-box h3{ margin:0; background:var(--soft); color:var(--brand); font-size:12.5px; font-weight:800; padding:6px 10px; border-bottom:1.5px solid var(--line); display:flex; justify-content:space-between; }
.inv-box h3 small{ color:var(--ink2); font-weight:700; }
.inv-kv{ width:100%; border-collapse:collapse; table-layout:fixed; }
.inv-kv th, .inv-kv td{ padding:5px 9px; border-bottom:1px solid #E2E8F0; vertical-align:middle; font-size:12.5px; word-break:break-word; }
.inv-kv tr:last-child th, .inv-kv tr:last-child td{ border-bottom:0; }
.inv-kv th{ width:42%; color:var(--ink2); font-weight:700; background:#FAFBFD; line-height:1.25; }
.inv-kv th small{ display:block; font-size:9.5px; color:#94A3B8; font-weight:700; letter-spacing:.3px; }
.inv-kv td{ font-weight:700; }

/* جدول الأصناف */
.inv-items{ width:100%; border-collapse:collapse; margin-bottom:12px; border:1.5px solid var(--brand); }
.inv-items thead th{ background:var(--brand); color:#fff; font-size:11.5px; font-weight:800; padding:7px 5px; text-align:center; line-height:1.3; border-inline-end:1px solid rgba(255,255,255,.18); }
.inv-items thead th small{ display:block; font-size:9.5px; font-weight:600; opacity:.85; }
.inv-items tbody td{ padding:6px 5px; text-align:center; font-size:12.5px; border-bottom:1px solid #E2E8F0; border-inline-end:1px solid #EEF2F7; }
.inv-items tbody tr:nth-child(even) td{ background:#F8FAFC; }
.inv-items td.name{ text-align:right; white-space:pre-wrap; word-break:break-word; font-weight:700; }
.inv-items td.num{ direction:ltr; font-variant-numeric:tabular-nums; }
.inv-items td.net{ font-weight:800; color:var(--brand); }

/* الإجماليات + QR */
.inv-bottom{ display:grid; grid-template-columns:1fr 1.15fr; gap:14px; align-items:start; }
.inv-qr{ display:flex; gap:12px; align-items:flex-start; }
.inv-qr .qr{ border:1.5px solid var(--line); border-radius:8px; padding:6px; background:#fff; line-height:0; }
.inv-bank{ flex:1; border:1.5px dashed var(--line); border-radius:8px; padding:8px 10px; font-size:11.5px; line-height:1.7; }
.inv-bank b{ color:var(--brand); display:block; }
.inv-tot{ width:100%; border-collapse:collapse; border:1.5px solid var(--line); border-radius:8px; overflow:hidden; }
.inv-tot th, .inv-tot td{ padding:6px 10px; font-size:12.5px; border-bottom:1px solid #E2E8F0; }
.inv-tot th{ text-align:right; color:var(--ink2); font-weight:700; background:#FAFBFD; }
.inv-tot th small{ color:#94A3B8; font-weight:700; font-size:10px; margin-inline-start:4px; }
.inv-tot td{ text-align:left; direction:ltr; font-weight:800; font-variant-numeric:tabular-nums; width:40%; }
.inv-tot tr.grand th, .inv-tot tr.grand td{ background:var(--brand); color:#fff; font-size:14.5px; border-bottom:0; }
.inv-tot tr.grand th small{ color:#C7D2FE; }
.inv-words{ margin-top:6px; font-size:11.5px; font-weight:700; color:#B91C1C; background:#FEF2F2; border-radius:6px; padding:5px 10px; text-align:center; }

.inv-note{ margin-top:12px; border-inline-start:4px solid var(--acc); background:#FFFBEB; padding:8px 12px; border-radius:6px; font-size:12.5px; }
.inv-sign{ display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-top:26px; text-align:center; font-size:12px; font-weight:700; color:var(--ink2); }
.inv-sign div{ border-top:1.5px solid var(--line); padding-top:6px; }
.inv-foot{ margin-top:16px; padding-top:8px; border-top:2px solid var(--brand); text-align:center; font-size:11px; color:var(--ink2); font-weight:600; }
.inv-foot span{ margin:0 6px; }

.inv-actions{ text-align:center; margin:4px 0 14px; }
.inv-actions .btn{ min-width:150px; font-weight:800; }

@media screen and (max-width:767px){
    .inv{ padding:10px; }
    .inv-head, .inv-head tbody, .inv-head tr, .inv-head td{ display:block; width:100% !important; }
    .inv-co.ar, .inv-co.en{ text-align:center !important; }
    .inv-grid, .inv-bottom{ grid-template-columns:1fr; }
    .inv-items-wrap{ overflow-x:auto; }
}

/* ================= الطباعة ================= */
@media print{
    @page{ size:A4; margin:8mm; }
    body{ background:#fff !important; }
    *{ -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
    .main-header, .main-sidebar, .app-sidebar, .main-footer, .breadcrumb-header, .inv-actions, #print_Button,
    .ahl-notif, .back-to-top, #back-to-top{ display:none !important; }
    .main-content, .main-content .container-fluid, .card, .card-body{ margin:0 !important; padding:0 !important; border:0 !important; box-shadow:none !important; background:#fff !important; }
    .main-content.app-content{ margin-right:0 !important; margin-left:0 !important; }
    .inv{ max-width:none; padding:0; font-size:11.5px; }
    .inv-items tr, .inv-bottom, .inv-sign, .inv-box{ page-break-inside:avoid; break-inside:avoid; }
    .inv-items thead{ display:table-header-group; }
}
</style>
