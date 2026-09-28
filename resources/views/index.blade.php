@extends('layouts.master')
@section('css')
<style>
/* =====================================================================
   Dashboard — Professional (Navy / Blue / Gold) + light animations
   ===================================================================== */
.db{
    --db-navy:#0B1A33; --db-navy-2:#15335E; --db-blue:#3B82F6; --db-blue-2:#60A5FA;
    --db-gold:#D6AE62; --db-green:#10B981; --db-amber:#F59E0B; --db-red:#EF4444; --db-teal:#14B8A6;
    --db-ink:#0F172A; --db-ink-2:#475569; --db-ink-3:#94A3B8;
    --db-card:#FFFFFF; --db-line:#E7ECF3; --db-bg:#F4F7FB;
    --db-r:16px; --db-shadow:0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.12);
    font-family:'Cairo','IBM Plex Sans Arabic','Segoe UI',sans-serif; color:var(--db-ink);
    padding:4px 0 30px;
}
.db *{ box-sizing:border-box; }
.db a{ text-decoration:none !important; }

/* ---------- animations ---------- */
@keyframes dbUp{ from{ opacity:0; transform:translateY(18px); } to{ opacity:1; transform:none; } }
@keyframes dbFloat{ 0%,100%{ transform:translate(0,0) scale(1); } 50%{ transform:translate(-18px,14px) scale(1.06); } }
@keyframes dbRoad{ from{ background-position:0 0; } to{ background-position:-120px 0; } }
@keyframes dbPulse{ 0%{ box-shadow:0 0 0 0 rgba(16,185,129,.55); } 70%{ box-shadow:0 0 0 8px rgba(16,185,129,0); } 100%{ box-shadow:0 0 0 0 rgba(16,185,129,0); } }
@keyframes dbShimmer{ from{ background-position:-200px 0; } to{ background-position:200px 0; } }
.db-anim{ opacity:0; animation:dbUp .6s cubic-bezier(.2,.7,.2,1) forwards; animation-delay:calc(var(--i,0) * 70ms); }
@media (prefers-reduced-motion: reduce){
    .db-anim{ animation:none; opacity:1; }
    .db-hero__blob, .db-hero__truck, .db-hero__road{ animation:none !important; }
}

/* ---------- hero ---------- */
.db-hero{
    position:relative; overflow:hidden; border-radius:20px; padding:26px 28px 62px; color:#fff;
    background:linear-gradient(125deg,var(--db-navy) 0%,#10254A 45%,var(--db-navy-2) 100%);
    box-shadow:0 18px 40px -18px rgba(11,26,51,.6);
}
.db-hero__blob{ position:absolute; border-radius:50%; filter:blur(40px); opacity:.45; animation:dbFloat 12s ease-in-out infinite; pointer-events:none; }
.db-hero__blob.b1{ width:260px; height:260px; background:#2563EB; top:-90px; inset-inline-start:-60px; }
.db-hero__blob.b2{ width:200px; height:200px; background:var(--db-gold); bottom:-120px; inset-inline-end:18%; opacity:.25; animation-duration:15s; animation-delay:-4s; }
.db-hero__grid{ position:absolute; inset:0; opacity:.07; pointer-events:none;
    background-image:linear-gradient(rgba(255,255,255,.6) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.6) 1px,transparent 1px);
    background-size:34px 34px; mask-image:linear-gradient(180deg,#000,transparent 85%); -webkit-mask-image:linear-gradient(180deg,#000,transparent 85%); }
.db-hero__row{ position:relative; display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; }
.db-hero__hello{ font-size:13px; font-weight:700; color:var(--db-gold); letter-spacing:.02em; display:flex; align-items:center; gap:8px; }
.db-hero__title{ font-size:26px; font-weight:800; margin:4px 0 6px; line-height:1.3; color:#fff; }
.db-hero__sub{ color:#B9C6DC; font-size:13.5px; margin:0; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.db-scope{ display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:700; color:#fff; padding:3px 10px; border-radius:999px; background:rgba(214,174,98,.22); border:1px solid rgba(214,174,98,.45); }
.db-clock{ position:relative; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(6px);
    border-radius:14px; padding:12px 18px; min-width:230px; text-align:center; }
.db-clock__time{ font-size:28px; font-weight:800; letter-spacing:1px; direction:ltr; font-variant-numeric:tabular-nums; }
.db-clock__date{ font-size:12px; color:#B9C6DC; display:flex; gap:6px; justify-content:center; align-items:center; flex-wrap:wrap; }
.db-live{ width:8px; height:8px; border-radius:50%; background:var(--db-green); animation:dbPulse 2s infinite; display:inline-block; }
.db-actions{ position:relative; display:flex; gap:10px; flex-wrap:wrap; margin-top:20px; }
.db-action{ display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:11px; font-weight:700; font-size:13px;
    color:#fff !important; background:rgba(255,255,255,.09); border:1px solid rgba(255,255,255,.14); transition:.2s; }
.db-action i{ font-size:17px; }
.db-action:hover{ background:rgba(255,255,255,.18); transform:translateY(-2px); }
.db-action.primary{ background:var(--db-blue); border-color:var(--db-blue); box-shadow:0 8px 20px -8px rgba(59,130,246,.8); }
.db-action.primary:hover{ background:#2563EB; }
.db-action--ship{ background:#10B981 !important; border-color:#10B981 !important; box-shadow:0 8px 20px -8px rgba(16,185,129,.8) !important; }
.db-action--ship:hover{ background:#059669 !important; }
.db-hero__road{ position:absolute; left:0; right:0; bottom:18px; height:2px; opacity:.25;
    background:repeating-linear-gradient(90deg,#fff 0 40px,transparent 40px 60px); animation:dbRoad 1.6s linear infinite; }
.db[dir="ltr"] .db-hero__road{ animation-direction:reverse; }
.db-hero__truck{ position:absolute; bottom:19px; font-size:34px; color:rgba(255,255,255,.9); pointer-events:none; }
/* RTL: الشاحنة داخلة من اليمين وماشية شمال */
.db[dir="rtl"] .db-hero__truck{ right:-60px; animation:dbDriveRtl 16s linear infinite; }
@keyframes dbDriveRtl{ from{ transform:translateX(0) scaleX(-1); } to{ transform:translateX(-130vw) scaleX(-1); } }
/* LTR: داخلة من الشمال وماشية يمين */
.db[dir="ltr"] .db-hero__truck{ left:-60px; animation:dbDriveLtr 16s linear infinite; }
@keyframes dbDriveLtr{ from{ transform:translateX(0); } to{ transform:translateX(130vw); } }

/* ---------- section ---------- */
.db-sec{ margin-top:26px; }
.db-sec__head{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; }
.db-sec__title{ display:flex; align-items:center; gap:10px; font-size:16px; font-weight:800; margin:0; color:var(--db-ink); }
.db-sec__title::before{ content:""; width:4px; height:18px; border-radius:4px; background:linear-gradient(180deg,var(--db-blue),var(--db-gold)); }
.db-chip{ font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:999px; background:#EEF4FF; color:#1D4ED8; }
.db-link{ font-size:12.5px; font-weight:700; color:var(--db-blue) !important; }

/* ---------- KPI cards ---------- */
.db-kpis{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
.db-kpi{
    position:relative; overflow:hidden; background:var(--db-card); border:1px solid var(--db-line); border-radius:var(--db-r);
    padding:18px 18px 12px; box-shadow:var(--db-shadow); transition:transform .25s, box-shadow .25s;
}
.db-kpi:hover{ transform:translateY(-4px); box-shadow:0 20px 40px -20px rgba(15,23,42,.28); }
.db-kpi::after{ content:""; position:absolute; inset-inline-start:0; top:0; bottom:0; width:4px; background:var(--c,var(--db-blue)); }
.db-kpi__top{ display:flex; align-items:center; justify-content:space-between; gap:10px; }
.db-kpi__icon{ width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px;
    color:var(--c,var(--db-blue)); background:color-mix(in srgb, var(--c,var(--db-blue)) 12%, #fff); transition:transform .3s; }
.db-kpi:hover .db-kpi__icon{ transform:rotate(-8deg) scale(1.08); }
.db-kpi__label{ font-size:12.5px; color:var(--db-ink-2); font-weight:700; margin:12px 0 2px; }
.db-kpi__value{ font-size:26px; font-weight:800; line-height:1.2; font-variant-numeric:tabular-nums; }
.db-kpi__unit{ font-size:12px; color:var(--db-ink-3); font-weight:700; margin-inline-start:4px; }
.db-kpi__foot{ display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:6px; font-size:12px; color:var(--db-ink-3); }
.db-delta{ font-weight:800; font-size:11.5px; padding:2px 8px; border-radius:999px; display:inline-flex; align-items:center; gap:3px; }
.db-delta.up{ color:#047857; background:#D1FAE5; } .db-delta.down{ color:#B91C1C; background:#FEE2E2; } .db-delta.flat{ color:#475569; background:#F1F5F9; }
.db-spark{ height:38px; margin-top:6px; }

/* ---------- grid layouts ---------- */
.db-grid-2{ display:grid; grid-template-columns:1.6fr 1fr; gap:16px; }
.db-grid-3{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
.db-card{ background:var(--db-card); border:1px solid var(--db-line); border-radius:var(--db-r); box-shadow:var(--db-shadow); padding:18px; }
.db-card__title{ font-size:14px; font-weight:800; margin:0 0 4px; display:flex; justify-content:space-between; align-items:center; }
.db-card__sub{ font-size:12px; color:var(--db-ink-3); margin-bottom:12px; }
.db-canvas{ position:relative; height:260px; }
.db-canvas.sm{ height:210px; }
.db-empty{ height:100%; display:flex; align-items:center; justify-content:center; color:var(--db-ink-3); font-size:13px; }

/* liquidation status */
.db-status{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:6px; }
.db-status__item{ border:1px solid var(--db-line); border-radius:12px; padding:12px; }
.db-status__label{ font-size:12px; color:var(--db-ink-2); font-weight:700; display:flex; align-items:center; gap:6px; }
.db-dot{ width:8px; height:8px; border-radius:50%; display:inline-block; }
.db-status__val{ font-size:22px; font-weight:800; margin-top:4px; }
.db-bar{ height:6px; background:#EEF2F7; border-radius:99px; overflow:hidden; margin-top:8px; }
.db-bar > span{ display:block; height:100%; width:0; border-radius:99px; transition:width 1.2s cubic-bezier(.2,.7,.2,1); }

/* accounts */
.db-accounts{ display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:12px; align-content:start; align-self:start; }
.db-acc{ display:flex; align-items:center; gap:12px; padding:14px; border:1px solid var(--db-line); border-radius:14px; background:#fff; transition:.2s; }
.db-acc:hover{ border-color:#C7D7F3; transform:translateY(-2px); box-shadow:var(--db-shadow); }
.db-acc__av{ width:40px; height:40px; flex:none; border-radius:11px; display:flex; align-items:center; justify-content:center; font-weight:800; color:#fff; font-size:16px; }
.db-acc__name{ font-size:12.5px; color:var(--db-ink-2); font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-acc__bal{ font-size:17px; font-weight:800; font-variant-numeric:tabular-nums; min-height:24px; }
.db-acc__bal.neg{ color:var(--db-red); }
.db-skel{ display:inline-block; width:80px; height:14px; border-radius:6px; vertical-align:middle;
    background:linear-gradient(90deg,#EEF2F7 0,#F8FAFC 50%,#EEF2F7 100%); background-size:400px 100%; animation:dbShimmer 1.2s linear infinite; }

/* tables */
.db-table{ width:100%; border-collapse:separate; border-spacing:0; font-size:13px; }
.db-table th{ font-size:11.5px; color:var(--db-ink-3); font-weight:800; text-align:start; padding:8px 10px; border-bottom:1px solid var(--db-line); white-space:nowrap; }
.db-table td{ padding:10px; border-bottom:1px solid #F1F4F9; white-space:nowrap; }
.db-table tr:last-child td{ border-bottom:none; }
.db-table tbody tr{ transition:background .15s; }
.db-table tbody tr:hover{ background:#F8FAFD; }
.db-no{ color:#C0392B; font-weight:800; }
.db-route{ display:inline-flex; align-items:center; gap:6px; color:var(--db-ink-2); }
.db-to{ font-weight:700; color:var(--db-ink); display:flex; align-items:center; gap:6px; }
.db-to i{ color:var(--db-blue); font-size:16px; }
.db-from{ font-size:11.5px; color:var(--db-ink-3); margin-top:2px; padding-inline-start:22px; white-space:nowrap; }
.db-by{ color:var(--db-ink-3); }
.db-tag{ display:inline-flex; align-items:center; gap:4px; font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:999px; background:#F1F5F9; color:#475569; white-space:nowrap; }
.db-tag i{ font-size:14px; }
.db-tag.green{ background:#D1FAE5; color:#047857; } .db-tag.amber{ background:#FEF3C7; color:#B45309; }
.db-tag.blue{ background:#DBEAFE; color:#1D4ED8; } .db-tag.violet{ background:#EDE9FE; color:#6D28D9; }
.db-link i{ font-size:18px; }
.db-route i{ color:var(--db-ink-3); }
.db-money{ font-weight:800; font-variant-numeric:tabular-nums; }

/* branch */
/* fleet */
.db-fleet-kpis{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
.db-fk{ display:flex; align-items:center; gap:12px; background:#fff; border:1px solid var(--db-line); border-radius:var(--db-r); padding:14px 16px; box-shadow:var(--db-shadow); color:var(--db-ink) !important; transition:transform .2s; }
.db-fk:hover{ transform:translateY(-3px); }
.db-fk i{ width:44px; height:44px; flex:none; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; color:var(--c); background:color-mix(in srgb,var(--c) 12%,#fff); }
.db-fk b{ display:block; font-size:24px; font-weight:800; line-height:1.1; }
.db-fk span{ font-size:12.5px; color:var(--db-ink-2); font-weight:700; }
.db-regions{ display:grid; grid-template-columns:repeat(auto-fill,minmax(118px,1fr)); gap:10px; margin-top:12px; }
.db-region{ border:1px solid var(--db-line); border-radius:12px; padding:10px; text-align:center; background:#FAFBFD; transition:.2s; }
.db-region.has{ background:#ECFDF5; border-color:#A7F3D0; }
.db-region:hover{ transform:translateY(-2px); }
.db-region__n{ display:block; font-size:22px; font-weight:800; color:var(--db-ink-3); }
.db-region.has .db-region__n{ color:#047857; }
.db-region__name{ font-size:12px; font-weight:700; color:var(--db-ink-2); }
.db-loaded{ margin-top:10px; }
.db-trip{ display:flex; align-items:center; gap:12px; padding:10px 12px; border:1px solid var(--db-line); border-radius:12px; margin-bottom:8px; border-inline-start:4px solid var(--db-amber); }
.db-trip.late{ border-inline-start-color:var(--db-red); background:#FFF7F7; }
.db-trip__plate{ font-weight:800; direction:ltr; min-width:90px; }
.db-trip__route{ flex:1; min-width:0; display:flex; align-items:center; gap:6px; flex-wrap:wrap; font-size:13px; }
.db-trip__route i{ color:var(--db-amber); font-size:18px; }
.db-trip__route small{ width:100%; color:var(--db-ink-3); font-size:11.5px; }
.db-call{ width:32px; height:32px; flex:none; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#2F6FED; color:#fff !important; font-size:16px; transition:transform .15s; }
.db-call.wa{ background:#25D366; }
.db-call:hover{ transform:translateY(-2px); }
.db-trip__eta{ font-size:12px; color:var(--db-ink-2); font-weight:700; text-align:end; white-space:nowrap; }
@media (max-width:1199px){ .db-fleet-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
.db-branch{ background:linear-gradient(180deg,#F8FAFD,#fff); }

@media (max-width:1199px){ .db-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); } .db-grid-2{ grid-template-columns:1fr; } .db-grid-3{ grid-template-columns:1fr 1fr; } }
@media (max-width:767px){
    .db-hero{ padding:20px 18px 54px; } .db-hero__title{ font-size:20px; }
    .db-clock{ width:100%; }
    .db-kpis, .db-grid-3{ grid-template-columns:1fr; }
    .db-kpi__value{ font-size:22px; }
    .db-table-wrap{ overflow-x:auto; }
}
</style>
@endsection

@section('title')
{{ __('home.home') }}
@stop

@section('page-header')
<div style="height:14px"></div>
@endsection

@section('content')
@php
    // لو الصفحة اتفتحت من غير DashboardController (مثلاً /index) نرجّعها للداشبورد
    $dbDirect = !isset($all);
    if ($dbDirect) {
        $all = array_fill_keys(['trx_today_count','trx_today_amount','trx_yest_count','trx_month_count','trx_month_amount','liq_today_count','liq_today_amount','liq_yest_count','liq_month_count','liq_month_amount','liq_today_confirmed','liq_today_unconfirmed','liq_month_confirmed','liq_month_unconfirmed'], 0);
        $chart = null; $accounts = collect(); $custody = collect(); $recentTrx = collect(); $recentLiq = collect(); $branch = null; $isAdminBranch = false; $fleet = null;
        $wb = ['enabled' => false, 'today' => 0, 'month' => 0, 'month_fare' => 0, 'recent' => collect()];
    }
    $ar  = App::getLocale() == 'ar';
    $t   = function ($a, $e) use ($ar) { return $ar ? $a : $e; };
    $sar = __('home.SAR');
    $h   = (int) date('G');
    $greet = $h < 12 ? $t('صباح الخير', 'Good morning') : ($h < 18 ? $t('مساء الخير', 'Good afternoon') : $t('مساء النور', 'Good evening'));
    $delta = function ($now, $prev) {
        if ($prev == 0 && $now == 0) return ['flat', '0%'];
        if ($prev == 0) return ['up', '+100%'];
        $p = round((($now - $prev) / $prev) * 100);
        return [$p > 0 ? 'up' : ($p < 0 ? 'down' : 'flat'), ($p > 0 ? '+' : '') . $p . '%'];
    };
    $palette = ['#3B82F6', '#D6AE62', '#10B981', '#F59E0B', '#14B8A6', '#8B5CF6', '#EF4444'];
@endphp

@if ($dbDirect)<script>location.replace('{{ url('dashboard') }}');</script>@endif
<div class="db" dir="{{ $ar ? 'rtl' : 'ltr' }}">

    {{-- ================= HERO ================= --}}
    <div class="db-hero db-anim" style="--i:0">
        <span class="db-hero__blob b1"></span>
        <span class="db-hero__blob b2"></span>
        <span class="db-hero__grid"></span>

        <div class="db-hero__row">
            <div>
                <div class="db-hero__hello"><i class="bx bx-sun"></i> {{ $greet }}</div>
                <h2 class="db-hero__title">{{ $t('أهلاً', 'Welcome') }}، {{ Auth::user()->name }} 👋</h2>
                <p class="db-hero__sub">{{ $t('ده ملخص سريع لأداء النشاط النهارده', "Here's a quick overview of today's activity") }}
                    <span class="db-scope"><i class="bx {{ ($isAdminBranch ?? false) ? 'bx-globe' : 'bx-buildings' }}"></i> {{ ($isAdminBranch ?? false) ? $t('بيانات كل الفروع', 'All branches') : $t('بيانات فرعك فقط', 'Your branch only') }}</span></p>
            </div>
            <div class="db-clock">
                <div class="db-clock__time" id="dbTime">--:--:--</div>
                <div class="db-clock__date"><span class="db-live"></span><span id="dbDate"></span><span>·</span><span id="dbHijri"></span></div>
            </div>
        </div>

        <div class="db-actions">
            <a class="db-action primary db-action--ship js-qload" href="{{ url('trucks/board') }}?new=1"><i class="bx bx-package"></i>{{ $t('إضافة شحنة جديدة', 'New Shipment') }}</a>
            <a class="db-action" href="{{ url('waybills/create') }}"><i class="bx bx-plus-circle"></i>{{ $t('بوليصة شحن جديدة', 'New Waybill') }}</a>
            @can('Sales products')
            <a class="db-action" href="{{ url('create_transfer') }}"><i class="bx bx-transfer-alt"></i>{{ __('home.create_transfer') }}</a>
            @endcan
            <a class="db-action" href="{{ url('create_liquidation') }}"><i class="bx bx-file"></i>{{ __('home.create_liquidation') }}</a>
            <a class="db-action" href="{{ url('recent_liquidation') }}"><i class="bx bx-list-check"></i>{{ __('home.liquidation') }}</a>
        </div>

        <span class="db-hero__road"></span>
        <i class="bx bxs-truck db-hero__truck"></i>
    </div>

    {{-- ================= حالة الأسطول (لكل المستخدمين) ================= --}}
    @if (!empty($fleet))
    <div class="db-sec">
        <div class="db-sec__head">
            <h3 class="db-sec__title">{{ $t('حالة الشاحنات', 'Fleet status') }}</h3>
            <a class="db-link" href="{{ url('trucks/board') }}">{{ $t('لوحة الشاحنات', 'Truck board') }} <i class="bx {{ $ar ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i></a>
        </div>
        <div class="db-fleet-kpis">
            <a href="{{ url('trucks/board') }}" class="db-fk db-anim" style="--i:1;--c:#2F6FED"><i class="bx bxs-truck"></i><div><b data-count="{{ $fleet['total'] }}">0</b><span>{{ $t('كل الشاحنات', 'All trucks') }}</span></div></a>
            <a href="{{ url('trucks/board') }}?new=1" class="db-fk db-anim" style="--i:2;--c:#10B981"><i class="bx bx-check-circle"></i><div><b data-count="{{ $fleet['empty'] }}">0</b><span>{{ $t('متاحة (ملك المؤسسة)', 'Available (own)') }}</span></div></a>
            <a href="{{ url('trucks/board') }}" class="db-fk db-anim" style="--i:3;--c:#F59E0B"><i class="bx bx-package"></i><div><b data-count="{{ $fleet['loaded'] }}">0</b><span>{{ $t('محمّلة', 'Loaded') }}</span></div></a>
            <a href="{{ url('trucks/board') }}" class="db-fk db-anim" style="--i:4;--c:#EF4444"><i class="bx bx-time-five"></i><div><b data-count="{{ $fleet['overdue'] }}">0</b><span>{{ $t('متأخرة عن التنزيل', 'Overdue') }}</span></div></a>
        </div>
        <div class="db-grid-2" style="margin-top:16px">
            <div class="db-card db-anim" style="--i:5">
                <div class="db-card__title">{{ $t('الشاحنات المتاحة في كل منطقة (ملك المؤسسة)', 'Available own trucks by region') }}</div>
                <div class="db-regions">
                    @foreach ($fleet['byRegion'] as $reg => $cnt)
                        @if ($reg !== 'غير محدد' || $cnt > 0)
                        <div class="db-region {{ $cnt ? 'has' : '' }}">
                            <span class="db-region__n">{{ $cnt }}</span>
                            <span class="db-region__name">{{ $reg }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="db-card db-anim" style="--i:6">
                <div class="db-card__title">{{ $t('الشاحنات المحمّلة', 'Loaded trucks') }} <span class="db-chip">{{ $fleet['loaded'] }}</span>
                    <a href="#" class="qu-btn js-qload" style="margin-inline-start:auto"><i class="bx bx-plus"></i> {{ $t('شحنة جديدة', 'New shipment') }}</a></div>
                <div class="db-loaded">
                    @forelse ($fleet['loadedList']->take(8) as $trip)
                        <div class="db-trip {{ $trip->is_overdue ? 'late' : '' }}">
                            <div class="db-trip__plate">{{ optional($trip->truck)->plate_number }}</div>
                            <div class="db-trip__route">
                                <b>{{ $trip->from_region }}</b>
                                <i class="bx {{ $ar ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
                                <b>{{ $trip->to_region }}</b>
                                <small>{{ $trip->load_type }}{{ $trip->driver_name ? ' · ' . $trip->driver_name : '' }}{{ optional($trip->driver)->phone ? ' · ' . $trip->driver->phone : '' }}</small>
                            </div>
                            @if (optional($trip->driver)->phone)
                                <a class="db-call" href="tel:{{ preg_replace('/[^\d+]/', '', $trip->driver->phone) }}" title="{{ $trip->driver->name }} - {{ $trip->driver->phone }}"><i class="bx bxs-phone"></i></a>
                                <a class="db-call wa" href="https://wa.me/{{ \App\Models\waybill_driver::intlPhone($trip->driver->phone) }}" target="_blank" rel="noopener" title="واتساب"><i class="bx bxl-whatsapp"></i></a>
                            @endif
                            <div class="db-trip__eta" title="{{ $t('معاد التنزيل المتوقع', 'Expected unloading') }}">
                                @if ($trip->is_overdue)<span class="db-tag amber" style="background:#FEE2E2;color:#B91C1C"><i class="bx bx-error"></i> {{ $t('متأخرة', 'Overdue') }}</span>@endif
                                {{ optional($trip->expected_unloading_at)->format('m/d h:i A') }}
                            </div>
                            <button type="button" class="qu-btn js-qunload" data-trip="{{ $trip->id }}" data-plate="{{ optional($trip->truck)->plate_number }}" data-to="{{ $trip->to_region }}" data-city="{{ $trip->to_city }}" title="تم التفريغ"><i class="bx bx-check-double"></i> تم التفريغ</button>
                        </div>
                    @empty
                        <div class="db-empty" style="height:auto;padding:24px 0">{{ $t('مفيش شاحنات محمّلة دلوقتي', 'No loaded trucks right now') }}</div>
                    @endforelse
                    @if ($fleet['loaded'] > 8)
                        <a class="db-link" href="{{ url('trucks/board') }}" style="display:block;text-align:center;margin-top:8px">{{ $t('عرض الكل', 'View all') }} ({{ $fleet['loaded'] }})</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @can('Home')
    {{-- ================= KPIs ================= --}}
    @php
        $d1 = $delta($all['trx_today_count'], $all['trx_yest_count']);
        $d2 = $delta($all['liq_today_count'], $all['liq_yest_count']);
    @endphp
    <div class="db-sec">
        <div class="db-kpis">
            <div class="db-kpi db-anim" style="--i:1;--c:#3B82F6">
                <div class="db-kpi__top">
                    <span class="db-kpi__icon"><i class="bx bx-transfer-alt"></i></span>
                    <span class="db-delta {{ $d1[0] }}" title="{{ $t('مقارنة بأمس', 'vs yesterday') }}">{{ $d1[1] }}</span>
                </div>
                <div class="db-kpi__label">{{ __('home.salesdoday') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $all['trx_today_count'] }}">0</span><span class="db-kpi__unit">{{ __('home.invoice') }}</span></div>
                <div class="db-kpi__foot"><span>{{ __('home.TODAYEARNINGS') }}</span><span class="db-money"><span data-count="{{ $all['trx_today_amount'] }}" data-dec="2">0</span> {{ $sar }}</span></div>
                <div class="db-spark"><canvas data-spark="trx" data-color="#3B82F6"></canvas></div>
            </div>

            <div class="db-kpi db-anim" style="--i:2;--c:#10B981">
                <div class="db-kpi__top">
                    <span class="db-kpi__icon"><i class="bx bx-line-chart"></i></span>
                    <span class="db-chip">{{ $t('الشهر', 'Month') }}</span>
                </div>
                <div class="db-kpi__label">{{ __('home.TOTAL_EARNINGS_Month') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $all['trx_month_amount'] }}" data-dec="2">0</span><span class="db-kpi__unit">{{ $sar }}</span></div>
                <div class="db-kpi__foot"><span>{{ __('home.PRODUCT_number_SOLD') }}</span><span class="db-money"><span data-count="{{ $all['trx_month_count'] }}">0</span> {{ __('home.invoice') }}</span></div>
                <div class="db-spark"><canvas data-spark="trx_amount" data-color="#10B981"></canvas></div>
            </div>

            <div class="db-kpi db-anim" style="--i:3;--c:#D6AE62">
                <div class="db-kpi__top">
                    <span class="db-kpi__icon"><i class="bx bx-file"></i></span>
                    <span class="db-delta {{ $d2[0] }}" title="{{ $t('مقارنة بأمس', 'vs yesterday') }}">{{ $d2[1] }}</span>
                </div>
                <div class="db-kpi__label">{{ __('home.liquitiondoday') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $all['liq_today_count'] }}">0</span><span class="db-kpi__unit">{{ __('home.invoice') }}</span></div>
                <div class="db-kpi__foot"><span>{{ __('home.TODAYEARNINGS_liquition') }}</span><span class="db-money"><span data-count="{{ $all['liq_today_amount'] }}" data-dec="2">0</span> {{ $sar }}</span></div>
                <div class="db-spark"><canvas data-spark="liq" data-color="#D6AE62"></canvas></div>
            </div>

            <div class="db-kpi db-anim" style="--i:4;--c:#8B5CF6">
                <div class="db-kpi__top">
                    <span class="db-kpi__icon"><i class="bx bxs-truck"></i></span>
                    <span class="db-chip">{{ $t('بوليصات الشحن', 'Waybills') }}</span>
                </div>
                <div class="db-kpi__label">{{ $t('بوليصات اليوم', 'Waybills today') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $wb['today'] }}">0</span><span class="db-kpi__unit">{{ $t('بوليصة', 'waybills') }}</span></div>
                <div class="db-kpi__foot"><span>{{ $t('الشهر', 'Month') }}: <b data-count="{{ $wb['month'] }}">0</b></span><span class="db-money"><span data-count="{{ $wb['month_fare'] }}" data-dec="2">0</span> {{ $sar }}</span></div>
                <div class="db-spark" style="display:flex;align-items:flex-end">
                    <a class="db-link" href="{{ url('waybills') }}">{{ $t('عرض البوليصات', 'View waybills') }} <i class="bx bx-left-arrow-alt"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Charts ================= --}}
    <div class="db-sec">
        <div class="db-grid-2">
            <div class="db-card db-anim" style="--i:5">
                <div class="db-card__title">{{ $t('التحويلات مقابل التصفيات', 'Transfers vs Liquidations') }} <span class="db-chip">{{ $t('آخر 7 أيام', 'Last 7 days') }}</span></div>
                <div class="db-card__sub">{{ $t('عدد العمليات اليومية', 'Daily operations count') }}</div>
                <div class="db-canvas"><canvas id="dbMainChart"></canvas></div>
            </div>

            <div class="db-card db-anim" style="--i:6">
                @php
                    $tc = $all['liq_today_confirmed']; $tu = $all['liq_today_unconfirmed']; $tt = max(1, $tc + $tu);
                    $mc = $all['liq_month_confirmed']; $mu = $all['liq_month_unconfirmed']; $mt = max(1, $mc + $mu);
                @endphp
                <div class="db-card__title">{{ __('home.liquitiondoday_confirm') }} / {{ __('home.liquitiondoday_notconfirm') }}</div>
                <div class="db-card__sub">{{ $t('حالة التصفيات اليوم والشهر', 'Liquidation status today & this month') }}</div>
                <div class="db-canvas sm"><canvas id="dbDonut"></canvas></div>
                <div class="db-status">
                    <div class="db-status__item">
                        <div class="db-status__label"><span class="db-dot" style="background:#10B981"></span>{{ __('home.liquitiondoday_confirm') }}</div>
                        <div class="db-status__val" data-count="{{ $tc }}">0</div>
                        <div class="db-bar"><span data-w="{{ round($tc / $tt * 100) }}" style="background:#10B981"></span></div>
                        <div class="db-card__sub" style="margin:6px 0 0">{{ __('home.liquitiondoday_confirm_month') }}: <b>{{ $mc }}</b></div>
                    </div>
                    <div class="db-status__item">
                        <div class="db-status__label"><span class="db-dot" style="background:#F59E0B"></span>{{ __('home.liquitiondoday_notconfirm') }}</div>
                        <div class="db-status__val" data-count="{{ $tu }}">0</div>
                        <div class="db-bar"><span data-w="{{ round($tu / $tt * 100) }}" style="background:#F59E0B"></span></div>
                        <div class="db-card__sub" style="margin:6px 0 0">{{ __('home.liquitiondoday_notconfirm_month') }}: <b>{{ $mu }}</b></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Accounts ================= --}}
    @if ($accounts->count())
    <div class="db-sec">
        <div class="db-sec__head">
            <h3 class="db-sec__title">{{ $t('الحسابات المالية', 'Financial accounts') }}</h3>
            <span class="db-chip">{{ $accounts->count() }}</span>
        </div>
        <div class="db-grid-2">
            <div class="db-accounts">
                @foreach ($accounts as $k => $acc)
                    <div class="db-acc db-anim" style="--i:{{ 7 + min($k, 10) }}">
                        <span class="db-acc__av" style="background:{{ $palette[$acc->id % count($palette)] }}">{{ mb_substr(trim($acc->name), 0, 1) }}</span>
                        <div style="min-width:0">
                            <div class="db-acc__name" title="{{ $acc->name }}">{{ $acc->name }}</div>
                            <div class="db-acc__bal" data-account-id="{{ $acc->id }}"><span class="db-skel"></span></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="db-card db-anim" style="--i:8">
                <div class="db-card__title">{{ $t('العهد', 'Custody') }}</div>
                <div class="db-card__sub">{{ $t('أرصدة حسابات العهدة', 'Custody account balances') }}</div>
                <div class="db-canvas"><canvas id="dbCustody"></canvas></div>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= Recent ================= --}}
    <div class="db-sec">
        <div class="db-grid-2">
            @if ($wb['enabled'])
            <div class="db-card db-anim" style="--i:9">
                <div class="db-card__title"><span>{{ $t('أحدث بوليصات الشحن', 'Latest waybills') }} <span class="db-chip">{{ ($isAdminBranch ?? false) ? $t('كل الفروع', 'All branches') : $t('فرعك', 'Your branch') }}</span> </span> <a class="db-link" href="{{ url('waybills') }}">{{ $t('عرض الكل', 'View all') }}</a></div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead><tr><th>#</th><th>{{ $t('العميل', 'Customer') }}</th><th>{{ $t('السائق', 'Driver') }}</th><th>{{ $t('المدينة', 'City') }}</th><th>{{ $t('الأجرة', 'Fare') }}</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($wb['recent'] as $w)
                                <tr>
                                    <td class="db-no">{{ $w->waybill_no }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($w->customer_name, 22) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($w->driver_name, 18) }}</td>
                                    <td>{{ $w->destination_city }}</td>
                                    <td class="db-money">{{ number_format($w->total_fare, 2) }}</td>
                                    <td><a class="db-link" href="{{ url('waybills/print/' . $w->id) }}" target="_blank"><i class="bx bx-printer"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;color:#94A3B8">{{ $t('لا توجد بوليصات بعد', 'No waybills yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="db-card db-anim" style="--i:10">
                <div class="db-card__title"><span>{{ $t('أحدث التحويلات', 'Latest transfers') }} <span class="db-chip">{{ ($isAdminBranch ?? false) ? $t('كل الفروع', 'All branches') : $t('فرعك', 'Your branch') }}</span> </span> <a class="db-link" href="{{ url('previousTransfers') }}">{{ $t('عرض الكل', 'View all') }}</a></div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead><tr><th>#</th><th>{{ $t('المحوَّل له', 'Transferred to') }}</th><th>{{ $t('المبلغ', 'Amount') }}</th><th>{{ $t('التاريخ', 'Date') }}</th></tr></thead>
                        <tbody>
                            @forelse ($recentTrx as $tr)
                                <tr>
                                    <td class="db-no">{{ $tr->id }}</td>
                                    <td>
                                        <div class="db-to"><i class="bx bx-user-check"></i> {{ \Illuminate\Support\Str::limit($tr->to_name ?: '—', 28) }}</div>
                                        @if ($tr->from_name || $tr->by_name)
                                        <div class="db-from">
                                            @if ($tr->from_name){{ $t('من', 'From') }}: {{ \Illuminate\Support\Str::limit($tr->from_name, 24) }}@endif
                                            @if ($tr->by_name)<span class="db-by">· {{ $t('بواسطة', 'by') }} {{ $tr->by_name }}</span>@endif
                                        </div>
                                        @endif
                                    </td>
                                    <td class="db-money">{{ number_format((float) $tr->price, 2) }}</td>
                                    <td style="color:#94A3B8">{{ $tr->date ? \Carbon\Carbon::parse($tr->date)->format('Y/m/d') : optional($tr->created_at)->format('Y/m/d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;color:#94A3B8">{{ $t('لا توجد تحويلات', 'No transfers') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- ================= Recent liquidations (الإدارة) ================= --}}
    <div class="db-sec">
        <div class="db-card db-anim" style="--i:11">
            <div class="db-card__title"><span>{{ $t('أحدث التصفيات', 'Latest liquidations') }} <span class="db-chip">{{ ($isAdminBranch ?? false) ? $t('كل الفروع', 'All branches') : $t('فرعك', 'Your branch') }}</span> </span> <a class="db-link" href="{{ url('recent_liquidation') }}">{{ $t('عرض الكل', 'View all') }}</a></div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead><tr>
                        <th>#</th><th>{{ $t('النوع', 'Type') }}</th><th>{{ $t('الفرع', 'Branch') }}</th><th>{{ $t('بواسطة', 'By') }}</th>
                        <th>{{ $t('المبلغ', 'Amount') }}</th><th>{{ $t('الحالة', 'Status') }}</th><th>{{ $t('التاريخ', 'Date') }}</th><th></th>
                    </tr></thead>
                    <tbody>
                        @forelse ($recentLiq ?? [] as $lq)
                            <tr>
                                <td class="db-no">{{ $lq->id }}</td>
                                <td>
                                    @if ($lq->type == 1) <span class="db-tag blue">{{ __('home.liquidation_shipments') }}</span>
                                    @elseif ($lq->type == 2) <span class="db-tag violet">{{ __('home.liquidation_purchase') }}</span>
                                    @else <span class="db-tag">—</span> @endif
                                </td>
                                <td>{{ optional($lq->branch)->name ?? '—' }}</td>
                                <td>{{ optional($lq->user)->name ?? '—' }}</td>
                                <td class="db-money">{{ number_format((float) $lq->price_filtering, 2) }}</td>
                                <td>
                                    @if ($lq->status == 2) <span class="db-tag green"><i class="bx bx-check-circle"></i> {{ __('home.confirm_done') }}</span>
                                    @elseif ($lq->status == 3) <span class="db-tag blue"><i class="bx bx-search-alt"></i> {{ __('home.pratiail_of_review') }}</span>
                                    @else <span class="db-tag amber"><i class="bx bx-time-five"></i> {{ __('home.data_complete') }}</span> @endif
                                </td>
                                <td style="color:#94A3B8">{{ optional($lq->created_at)->format('Y/m/d') }}</td>
                                <td>
                                    @if ($lq->type == 1)
                                        <a class="db-link" href="{{ url('print_transfers_after_full/' . $lq->id) }}" title="{{ __('home.show') }}"><i class="bx bx-show"></i></a>
                                    @elseif ($lq->type == 2)
                                        <a class="db-link" href="{{ url('print_full_purchase/' . $lq->id) }}" title="{{ __('home.show') }}"><i class="bx bx-show"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center;color:#94A3B8">{{ $t('لا توجد تصفيات', 'No liquidations') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan

    {{-- ================= Branch ================= --}}
    @if ($branch)
    <div class="db-sec">
        <div class="db-sec__head">
            <h3 class="db-sec__title">{{ $t('أداء فرعي', 'My branch') }}</h3>
            <span class="db-chip">{{ $t('فرعي', 'Branch') }}</span>
        </div>
        <div class="db-kpis">
            <div class="db-kpi db-branch db-anim" style="--i:11;--c:#3B82F6">
                <div class="db-kpi__top"><span class="db-kpi__icon"><i class="bx bx-transfer-alt"></i></span><span class="db-chip">{{ $t('اليوم', 'Today') }}</span></div>
                <div class="db-kpi__label">{{ __('home.salesdoday') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $branch['trx_today_count'] }}">0</span><span class="db-kpi__unit">{{ __('home.invoice') }}</span></div>
                <div class="db-kpi__foot"><span>{{ __('home.TODAYEARNINGS') }}</span><span class="db-money"><span data-count="{{ $branch['trx_today_amount'] }}" data-dec="2">0</span> {{ $sar }}</span></div>
            </div>
            <div class="db-kpi db-branch db-anim" style="--i:12;--c:#10B981">
                <div class="db-kpi__top"><span class="db-kpi__icon"><i class="bx bx-calendar"></i></span><span class="db-chip">{{ $t('الشهر', 'Month') }}</span></div>
                <div class="db-kpi__label">{{ __('home.TOTAL_EARNINGS_Month') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $branch['trx_month_amount'] }}" data-dec="2">0</span><span class="db-kpi__unit">{{ $sar }}</span></div>
                <div class="db-kpi__foot"><span>{{ __('home.PRODUCT_number_SOLD') }}</span><span class="db-money"><span data-count="{{ $branch['trx_month_count'] }}">0</span></span></div>
            </div>
            <div class="db-kpi db-branch db-anim" style="--i:13;--c:#D6AE62">
                <div class="db-kpi__top"><span class="db-kpi__icon"><i class="bx bx-file"></i></span><span class="db-chip">{{ $t('اليوم', 'Today') }}</span></div>
                <div class="db-kpi__label">{{ __('home.liquitiondoday') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $branch['liq_today_count'] }}">0</span><span class="db-kpi__unit">{{ __('home.invoice') }}</span></div>
                <div class="db-kpi__foot">
                    <span><span class="db-dot" style="background:#10B981"></span> {{ $branch['liq_today_confirmed'] }}</span>
                    <span><span class="db-dot" style="background:#F59E0B"></span> {{ $branch['liq_today_unconfirmed'] }}</span>
                </div>
            </div>
            <div class="db-kpi db-branch db-anim" style="--i:14;--c:#8B5CF6">
                <div class="db-kpi__top"><span class="db-kpi__icon"><i class="bx bx-check-double"></i></span><span class="db-chip">{{ $t('الشهر', 'Month') }}</span></div>
                <div class="db-kpi__label">{{ __('home.PRODUCT_number_SOLD_liquition') }}</div>
                <div class="db-kpi__value"><span data-count="{{ $branch['liq_month_count'] }}">0</span><span class="db-kpi__unit">{{ __('home.invoice') }}</span></div>
                <div class="db-kpi__foot">
                    <span><span class="db-dot" style="background:#10B981"></span> {{ $branch['liq_month_confirmed'] }}</span>
                    <span><span class="db-dot" style="background:#F59E0B"></span> {{ $branch['liq_month_unconfirmed'] }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@include('trucks._unload_quick')
@include('trucks._load_quick')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    var AR = {{ $ar ? 'true' : 'false' }};
    var CH = @json($chart ?? null);
    var CUSTODY = {!! json_encode(isset($custody) ? $custody->map(function ($a) { return ['id' => $a->id, 'name' => $a->name]; })->values() : [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var fmt = function (v, dec) { return Number(v).toLocaleString('en-US', { minimumFractionDigits: dec || 0, maximumFractionDigits: dec || 0 }); };

    /* ---------- clock ---------- */
    function clock() {
        var d = new Date(), p = function (n) { return (n < 10 ? '0' : '') + n; };
        var el = document.getElementById('dbTime'); if (!el) return;
        el.textContent = p(d.getHours()) + ':' + p(d.getMinutes()) + ':' + p(d.getSeconds());
        try {
            document.getElementById('dbDate').textContent = d.toLocaleDateString(AR ? 'ar-EG' : 'en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('dbHijri').textContent = d.toLocaleDateString((AR ? 'ar' : 'en') + '-SA-u-ca-islamic-umalqura', { day: 'numeric', month: 'long', year: 'numeric' });
        } catch (e) {}
    }

    /* ---------- count up ---------- */
    function countUp(el) {
        var target = parseFloat(el.dataset.count) || 0, dec = parseInt(el.dataset.dec || 0, 10);
        if (reduce || target === 0) { el.textContent = fmt(target, dec); return; }
        var start = null, dur = 1100;
        function step(ts) {
            if (!start) start = ts;
            var k = Math.min(1, (ts - start) / dur), e = 1 - Math.pow(1 - k, 3);
            el.textContent = fmt(target * e, dec);
            if (k < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    /* ---------- charts ---------- */
    function charts() {
        if (typeof Chart === 'undefined' || !CH) return;
        Chart.defaults.font.family = "'Cairo', sans-serif";
        Chart.defaults.color = '#64748B';

        // sparklines
        document.querySelectorAll('canvas[data-spark]').forEach(function (c) {
            var key = c.dataset.spark, col = c.dataset.color, data = CH[key] || [];
            new Chart(c, {
                type: 'line',
                data: { labels: CH.labels, datasets: [{ data: data, borderColor: col, borderWidth: 2, tension: .45, pointRadius: 0, fill: true,
                    backgroundColor: function (ctx) { var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 40); g.addColorStop(0, col + '40'); g.addColorStop(1, col + '00'); return g; } }] },
                options: { responsive: true, maintainAspectRatio: false, animation: { duration: reduce ? 0 : 1200 },
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }, scales: { x: { display: false, reverse: AR }, y: { display: false, beginAtZero: true } } }
            });
        });

        // main area chart
        var m = document.getElementById('dbMainChart');
        if (m) {
            var grad = function (col) { return function (ctx) { var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260); g.addColorStop(0, col + '55'); g.addColorStop(1, col + '00'); return g; }; };
            new Chart(m, {
                type: 'line',
                data: { labels: CH.labels, datasets: [
                    { label: AR ? 'التحويلات' : 'Transfers', data: CH.trx, borderColor: '#3B82F6', backgroundColor: grad('#3B82F6'), fill: true, tension: .4, borderWidth: 2.5, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff' },
                    { label: AR ? 'التصفيات' : 'Liquidations', data: CH.liq, borderColor: '#D6AE62', backgroundColor: grad('#D6AE62'), fill: true, tension: .4, borderWidth: 2.5, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff' }
                ] },
                options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    animation: { duration: reduce ? 0 : 1300, easing: 'easeOutQuart' },
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: { backgroundColor: '#0B1A33', padding: 10, cornerRadius: 8, rtl: AR } },
                    scales: { x: { reverse: AR, grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(15,23,42,.06)' } } } }
            });
        }

        // donut
        var dn = document.getElementById('dbDonut');
        if (dn) {
            var c1 = {{ (int) ($all['liq_month_confirmed'] ?? 0) }}, c2 = {{ (int) ($all['liq_month_unconfirmed'] ?? 0) }};
            if (c1 + c2 === 0) {
                dn.parentNode.innerHTML = '<div class="db-empty">' + (AR ? 'لا توجد تصفيات هذا الشهر' : 'No liquidations this month') + '</div>';
            } else {
                new Chart(dn, {
                    type: 'doughnut',
                    data: { labels: [AR ? 'متراجع عليها' : 'Confirmed', AR ? 'غير متراجع عليها' : 'Pending'], datasets: [{ data: [c1, c2], backgroundColor: ['#10B981', '#F59E0B'], borderWidth: 0, hoverOffset: 8 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '72%', animation: { animateRotate: !reduce, duration: reduce ? 0 : 1300 },
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }, tooltip: { rtl: AR } } },
                    plugins: [{ id: 'center', afterDraw: function (ch) {
                        var a = ch.chartArea, x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2, ctx = ch.ctx;
                        ctx.save(); ctx.textAlign = 'center'; ctx.fillStyle = '#0F172A'; ctx.font = "800 22px Cairo, sans-serif";
                        ctx.fillText(Math.round(c1 / (c1 + c2) * 100) + '%', x, y + 4);
                        ctx.fillStyle = '#94A3B8'; ctx.font = "600 11px Cairo, sans-serif"; ctx.fillText(AR ? 'الشهر' : 'Month', x, y + 22); ctx.restore();
                    } }]
                });
            }
        }
    }

    /* ---------- balances ---------- */
    function balances() {
        var cache = {};
        var jobs = Array.prototype.map.call(document.querySelectorAll('[data-account-id]'), function (el) {
            var id = el.dataset.accountId;
            return fetch('/account-balance/' + id).then(function (r) { return r.json(); }).then(function (d) {
                var v = Math.round(((d.debit || 0) - (d.credit || 0)) * 100) / 100;
                cache[id] = v;
                el.dataset.count = v; el.dataset.dec = 2;
                el.classList.toggle('neg', v < 0);
                countUp(el);
            }).catch(function () { el.textContent = '-'; });
        });

        var cc = document.getElementById('dbCustody');
        if (!cc) return;
        Promise.all(jobs).then(function () {
            if (!CUSTODY.length || typeof Chart === 'undefined') {
                cc.parentNode.innerHTML = '<div class="db-empty">' + (AR ? 'لا توجد حسابات عهدة' : 'No custody accounts') + '</div>';
                return;
            }
            var vals = CUSTODY.map(function (a) { return cache[a.id] || 0; });
            new Chart(cc, {
                type: 'bar',
                data: { labels: CUSTODY.map(function (a) { return a.name; }), datasets: [{ data: vals, borderRadius: 8, maxBarThickness: 22,
                    backgroundColor: vals.map(function (v) { return v < 0 ? '#EF4444' : '#14B8A6'; }) }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, animation: { duration: reduce ? 0 : 1200 },
                    plugins: { legend: { display: false }, tooltip: { rtl: AR } },
                    scales: { x: { beginAtZero: true, reverse: AR, grid: { color: 'rgba(15,23,42,.06)' } }, y: { position: AR ? 'right' : 'left', grid: { display: false } } } }
            });
        });
    }

    function init() {
        clock(); setInterval(clock, 1000);
        // count-up when visible
        var nums = document.querySelectorAll('.db [data-count]:not([data-account-id])');
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (es) {
                es.forEach(function (e) { if (e.isIntersecting) { countUp(e.target); io.unobserve(e.target); } });
            }, { threshold: .3 });
            nums.forEach(function (n) { io.observe(n); });
        } else { nums.forEach(countUp); }
        setTimeout(function () { document.querySelectorAll('.db-bar > span[data-w]').forEach(function (b) { b.style.width = b.dataset.w + '%'; }); }, 400);
        charts();
        balances();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
@endsection
