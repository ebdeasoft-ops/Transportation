<style>
.tr-kpis{ display:grid; grid-template-columns:repeat(var(--cols,5),minmax(0,1fr)); gap:12px; margin-bottom:16px; }
.tr-kpi{ background:#fff; border:1px solid #E6EBF2; border-radius:14px; padding:14px; box-shadow:0 8px 24px -16px rgba(15,23,42,.2); }
.tr-kpi span{ font-size:12px; color:#475569; font-weight:700; display:flex; align-items:center; gap:6px; }
.tr-kpi span i{ color:var(--c); font-size:17px; }
.tr-kpi b{ display:block; font-size:22px; font-weight:800; margin-top:4px; color:#0F172A; font-variant-numeric:tabular-nums; }
.tr-kpi b small{ font-size:12px; font-weight:700; color:#64748B; }
.tr-tag{ display:inline-flex; align-items:center; gap:4px; font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:999px; white-space:nowrap; }
.tr-tag.ok{ background:#D1FAE5; color:#047857; } .tr-tag.on{ background:#FEF3C7; color:#B45309; } .tr-tag.late{ background:#FEE2E2; color:#B91C1C; }
.tr-tag.blue{ background:#DBEAFE; color:#1D4ED8; } .tr-tag.gray{ background:#F1F5F9; color:#64748B; } .tr-tag.purple{ background:#EDE9FE; color:#6D28D9; }
.tr-num{ direction:ltr; text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
.tr-table th{ white-space:nowrap; font-size:12.5px; }
.tr-table td{ vertical-align:middle !important; font-size:13px; }
.tr-table tfoot td{ font-weight:800; background:#F8FAFC; }
.tr-bar{ height:8px; background:#EEF2F7; border-radius:99px; overflow:hidden; min-width:80px; }
.tr-bar span{ display:block; height:100%; background:linear-gradient(90deg,#2F6FED,#60A5FA); border-radius:99px; }
.tr-tabs{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
.tr-tabs a{ border:1px solid #E6EBF2; background:#fff; border-radius:999px; padding:7px 14px; font-size:13px; font-weight:700; color:#475569; display:inline-flex; align-items:center; gap:6px; }
.tr-tabs a.on, .tr-tabs a:hover{ border-color:#2F6FED; color:#2F6FED; background:#EAF1FF; text-decoration:none; }
.tr-print-head{ display:none; }
.tr-empty{ text-align:center; color:#94A3B8; padding:30px 0 !important; }
@media (max-width:1199px){ .tr-kpis{ grid-template-columns:repeat(3,minmax(0,1fr)); } }
@media (max-width:767px){ .tr-kpis{ grid-template-columns:1fr 1fr; } }
@media print{
    .app-sidebar, .main-header, .tr-filter, .no-print, .tr-tabs{ display:none !important; }
    .main-content, .app-content{ margin:0 !important; }
    .tr-print-head{ display:block; text-align:center; margin-bottom:10px; }
    .tr-kpi, .card{ box-shadow:none !important; }
    .tr-kpis{ grid-template-columns:repeat(var(--cols,5),1fr); }
}
</style>
