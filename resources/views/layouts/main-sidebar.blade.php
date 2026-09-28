@php
    $sbRtl   = App::getLocale() == 'ar';
    $sbSide  = $sbRtl ? 'right' : 'left';
    $sbOther = $sbRtl ? 'left' : 'right';
    $sbLogo  = defined('camplogo') ? camplogo : null;
    $sbName  = $sbRtl ? (defined('Namear') ? Namear : config('app.name')) : (defined('Nameen') ? Nameen : config('app.name'));
    $sbUser  = Auth::user();
        $t = function ($ar, $en) use ($sbRtl) { return $sbRtl ? $ar : $en; };
@endphp
<style>
/* =====================================================================
   Sidebar — Professional Navy theme
   ===================================================================== */
:root{
    --sb-bg-1:#0B1A33; --sb-bg-2:#10254A; --sb-bg-3:#0D1F3D;
    --sb-surface:rgba(255,255,255,.04); --sb-surface-2:rgba(255,255,255,.07);
    --sb-line:rgba(255,255,255,.08);
    --sb-text:#E8EEF8; --sb-text-2:#A9B7CE; --sb-text-3:#6F82A3;
    --sb-accent:#3B82F6; --sb-accent-2:#60A5FA; --sb-gold:#D6AE62; --sb-danger:#F0647A;
    --sb-w:240px; --sb-w-mini:80px; --sb-r:10px;
    --sb-font:'Cairo','IBM Plex Sans Arabic','Segoe UI',system-ui,sans-serif;
}

/* ---------- shell ---------- */
.app-sidebar{
    position:fixed !important; top:0; bottom:0; {{ $sbSide }}:0 !important; {{ $sbOther }}:auto !important;
    width:var(--sb-w) !important; height:100vh !important; z-index:1030;
    display:flex; flex-direction:column; overflow:hidden !important;
    background:linear-gradient(180deg,var(--sb-bg-1) 0%,var(--sb-bg-2) 60%,var(--sb-bg-3) 100%) !important;
    border-{{ $sbOther }}:1px solid var(--sb-line);
    box-shadow:0 0 40px rgba(3,10,25,.35);
    font-family:var(--sb-font); color:var(--sb-text);
    transition:width .25s ease, {{ $sbSide }} .25s ease;
}
.app-sidebar *{ box-sizing:border-box; }
/* إلغاء المسافات اللي بيضيفها الثيم فوق السايد بار */
.app-sidebar{ padding:0 !important; margin:0 !important; }
.app-sidebar .sb-scroll, .app-sidebar ul.side-menu{ margin-top:0 !important; }
.app-sidebar ul.side-menu{ padding-top:2px !important; }
.app-sidebar ul.side-menu > .side-menu__eyebrow:first-child, .app-sidebar ul.side-menu > .slide:first-child{ margin-top:0 !important; }

/* ---------- brand ---------- */
.sb-brand{
    flex:none; display:flex; align-items:center; gap:11px;
    padding:14px 16px 12px; border-bottom:1px solid var(--sb-line); position:relative;
    text-decoration:none !important;
}
.sb-brand::after{
    content:""; position:absolute; inset-inline:16px; bottom:-1px; height:2px; border-radius:2px;
    background:linear-gradient(90deg,transparent,var(--sb-gold),var(--sb-accent),transparent);
}
.sb-brand-logo{
    width:44px; height:44px; flex:none; border-radius:12px; background:#fff;
    display:flex; align-items:center; justify-content:center; overflow:hidden;
    box-shadow:0 4px 14px rgba(0,0,0,.3);
}
.sb-brand-logo img{ max-width:88%; max-height:88%; object-fit:contain; }
.sb-brand-text{ min-width:0; line-height:1.35; }
.sb-brand-name{ color:#fff; font-weight:800; font-size:13.5px; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; max-width:160px; }
.sb-brand-sub{ color:var(--sb-text-3); font-size:11px; font-weight:600; }

/* ---------- search ---------- */
.sb-search{ flex:none; padding:10px 14px 4px; position:relative; }
.sb-search input{
    width:100%; height:38px; border-radius:var(--sb-r); border:1px solid var(--sb-line);
    background:var(--sb-surface); color:var(--sb-text); font-family:var(--sb-font); font-size:12.5px;
    padding-inline-start:36px; padding-inline-end:10px; outline:none; transition:.18s;
}
.sb-search input::placeholder{ color:var(--sb-text-3); }
.sb-search input:focus{ border-color:var(--sb-accent); background:rgba(59,130,246,.08); box-shadow:0 0 0 3px rgba(59,130,246,.15); }
.sb-search i{ position:absolute; inset-inline-start:26px; top:50%; transform:translateY(-30%); color:var(--sb-text-3); font-size:16px; pointer-events:none; }
.sb-empty{ display:none; text-align:center; color:var(--sb-text-3); font-size:12px; padding:16px 0; }

/* ---------- scroll area ---------- */
.app-sidebar .sb-scroll{
    flex:1; min-height:0; overflow-y:auto; overflow-x:hidden; background:transparent !important;
    scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.12) transparent; margin:0 !important; padding:0 !important;
}
.app-sidebar .sb-scroll::-webkit-scrollbar{ width:5px; }
.app-sidebar .sb-scroll::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.12); border-radius:10px; }

/* ---------- menu ---------- */
.app-sidebar ul.side-menu{ list-style:none; margin:0; padding:4px 12px 18px; background:transparent !important; }
.app-sidebar .side-menu__eyebrow{
    display:flex; align-items:center; gap:8px; padding:16px 8px 6px;
    font-size:10.5px; font-weight:800; letter-spacing:.06em; color:var(--sb-text-3); text-transform:uppercase; white-space:nowrap;
}
.app-sidebar .side-menu__eyebrow::after{ content:""; flex:1; height:1px; background:var(--sb-line); }

.app-sidebar .slide{ position:relative; }
.app-sidebar .side-menu__item{
    display:flex !important; align-items:center; gap:11px; padding:8px 10px !important; margin:2px 0 !important;
    border-radius:var(--sb-r); color:var(--sb-text-2) !important; font-size:13.5px; font-weight:600;
    text-decoration:none !important; background:transparent !important; border:none !important; box-shadow:none !important; position:relative; white-space:nowrap; height:auto !important;
    transition:background .18s, color .18s;
}
.app-sidebar .side-menu__item:hover{ background:var(--sb-surface-2) !important; color:#fff !important; }
.app-sidebar .side-menu__icon{
    width:30px !important; height:30px !important; flex:none; padding:7px; border-radius:9px; margin:0 !important;
    color:var(--sb-text-2) !important; fill:currentColor; background:var(--sb-surface);
    display:inline-flex; align-items:center; justify-content:center; font-size:17px; transition:.18s;
}
.app-sidebar .side-menu__item:hover .side-menu__icon{ color:var(--sb-accent-2) !important; background:rgba(59,130,246,.14); }
.app-sidebar .side-menu__label{ flex:1; overflow:hidden; text-overflow:ellipsis; color:inherit !important; }
.app-sidebar .angle{ font-size:12px; color:var(--sb-text-3) !important; transition:transform .25s; margin:0 !important; }
.app-sidebar .slide.is-expanded > .side-menu__item{ color:#fff !important; background:var(--sb-surface) !important; }
.app-sidebar .slide.is-expanded > .side-menu__item .angle{ transform:rotate(180deg); }

/* active */
.app-sidebar .slide.active > .side-menu__item,
.app-sidebar .slide.is-expanded.active > .side-menu__item{
    background:linear-gradient(135deg,rgba(59,130,246,.22),rgba(59,130,246,.08)) !important; color:#fff !important;
}
.app-sidebar .slide.active > .side-menu__item .side-menu__icon{ background:var(--sb-accent); color:#fff !important; box-shadow:0 6px 14px -4px rgba(59,130,246,.7); }
.app-sidebar .slide.active > .side-menu__item::before{
    content:""; position:absolute; inset-inline-start:-12px; top:22%; bottom:22%; width:3px;
    border-radius:0 4px 4px 0; background:var(--sb-gold);
}

/* submenu */
.app-sidebar .slide-menu{
    list-style:none; padding:2px 0 4px !important; margin:0 !important; margin-inline-start:26px !important;
    border-inline-start:1px solid var(--sb-line); background:transparent !important;
    display:block !important; max-height:0; overflow:hidden; opacity:0;
    transition:max-height .3s ease, opacity .2s ease;
}
.app-sidebar .slide.is-expanded > .slide-menu{ max-height:900px; opacity:1; }
.app-sidebar .slide-menu li{ position:relative; }
.app-sidebar .slide-item{
    display:flex !important; align-items:center; gap:9px; padding:7px 12px !important; margin:1px 0 1px 0;
    margin-inline-start:10px; border-radius:8px; font-size:12.8px !important; font-weight:500;
    color:var(--sb-text-2) !important; text-decoration:none !important; background:transparent !important; border:none !important;
    transition:background .15s, color .15s; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.app-sidebar .slide-item::before{ display:none !important; }
.app-sidebar .slide-item i{ font-size:15px; width:16px; text-align:center; color:var(--sb-text-3); flex:none; transition:color .15s; margin:0 !important; }
.app-sidebar .slide-item:hover{ background:var(--sb-surface) !important; color:#fff !important; }
.app-sidebar .slide-item:hover i{ color:var(--sb-accent-2); }
.app-sidebar .slide-item.active{ color:#fff !important; background:rgba(59,130,246,.18) !important; font-weight:700; }
.app-sidebar .slide-item.active i{ color:var(--sb-accent-2); }
.app-sidebar .slide-item.active::after{
    content:""; position:absolute; inset-inline-start:-1px; top:50%; transform:translateY(-50%);
    width:5px; height:5px; border-radius:50%; background:var(--sb-accent-2); margin-inline-start:-2px;
}
.app-sidebar .sb-hidden{ display:none !important; }
.app-sidebar mark{ background:rgba(214,174,98,.3); color:#fff; padding:0 1px; border-radius:3px; }

/* ---------- footer (user) ---------- */
.sb-footer{ flex:none; border-top:1px solid var(--sb-line); padding:12px; background:rgba(0,0,0,.14); }
.sb-lang{ display:flex; gap:4px; background:rgba(0,0,0,.2); border-radius:999px; padding:3px; margin-bottom:10px; }
.sb-lang a{ flex:1; text-align:center; font-size:11.5px; font-weight:700; color:var(--sb-text-2) !important; padding:5px 0; border-radius:999px; text-decoration:none !important; transition:.15s; }
.sb-lang a:hover{ background:var(--sb-accent); color:#fff !important; }
.sb-user{ display:flex; align-items:center; gap:10px; }
.sb-avatar{ position:relative; width:40px; height:40px; flex:none; }
.sb-avatar img, .sb-avatar .sb-initial{
    width:40px; height:40px; border-radius:12px; object-fit:cover; border:2px solid rgba(59,130,246,.6);
}
.sb-avatar .sb-initial{ display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,var(--sb-accent),#1D4ED8); color:#fff; font-weight:800; font-size:16px; }
.sb-avatar::after{ content:""; position:absolute; bottom:-2px; inset-inline-end:-2px; width:11px; height:11px; border-radius:50%; background:#34D399; border:2px solid var(--sb-bg-3); }
.sb-user-info{ flex:1; min-width:0; line-height:1.3; }
.sb-user-name{ color:#fff; font-weight:700; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; }
.sb-user-mail{ color:var(--sb-text-3); font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; direction:ltr; text-align:start; }
.sb-icon-btn{
    width:34px; height:34px; flex:none; border-radius:9px; display:inline-flex; align-items:center; justify-content:center;
    color:var(--sb-text-2) !important; background:var(--sb-surface); border:1px solid var(--sb-line); font-size:17px; text-decoration:none !important; transition:.15s; cursor:pointer;
}
.sb-icon-btn:hover{ color:#fff !important; background:var(--sb-surface-2); }
.sb-icon-btn.sb-danger:hover{ background:rgba(240,100,122,.18); color:var(--sb-danger) !important; border-color:rgba(240,100,122,.35); }

/* focus */
.app-sidebar a:focus-visible, .sb-search input:focus-visible{ outline:2px solid var(--sb-accent-2); outline-offset:2px; }

/* ---------- desktop: collapsed (mini) mode ---------- */
@media (min-width:768px){
    .app.sidenav-toggled:not(.sidenav-toggled-open) .app-sidebar{ width:var(--sb-w-mini) !important; }
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-brand{ justify-content:center; padding-inline:0; }
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-brand-text,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-search,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .side-menu__label,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .angle,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .slide-menu,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-lang,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-user-info,
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-user .sb-icon-btn{ display:none !important; }
    .app.sidenav-toggled:not(.sidenav-toggled-open) .side-menu__eyebrow{ font-size:0; padding:12px 8px 4px; }
    .app.sidenav-toggled:not(.sidenav-toggled-open) .side-menu__item{ justify-content:center; }
    .app.sidenav-toggled:not(.sidenav-toggled-open) .sb-user{ justify-content:center; }
    .app.sidenav-toggled.sidenav-toggled-open .app-sidebar{ width:var(--sb-w) !important; }
}

/* ---------- mobile: off-canvas ---------- */
@media (max-width:767px){
    .app-sidebar{ {{ $sbSide }}:calc(-1 * var(--sb-w) - 20px) !important; }
    .app.sidenav-toggled .app-sidebar{ {{ $sbSide }}:0 !important; }
}
</style>

<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">

    {{-- ===== Brand ===== --}}
    <a class="sb-brand" href="{{ url('/dashboard') }}">
        <span class="sb-brand-logo">
            <img src="{{ $sbLogo ? asset('assets/img/brand/' . $sbLogo) : asset('assets/img/default-logo.svg') }}" alt="logo"
                 onerror="this.onerror=null;this.src='{{ asset('assets/img/default-logo.svg') }}'">
        </span>
        <span class="sb-brand-text">
            <span class="sb-brand-name" title="{{ $sbName }}">{{ $sbName }}</span>
            <span class="sb-brand-sub">{{ $t('نظام إدارة النقليات', 'Transport Management') }}</span>
        </span>
    </a>

    {{-- ===== Search ===== --}}
    <div class="sb-search">
        <i class="bx bx-search"></i>
        <input type="text" id="sbSearch" placeholder="{{ $t('ابحث في القائمة...', 'Search menu...') }}" autocomplete="off">
    </div>

    {{-- ===== Menu ===== --}}
    <div class="sb-scroll">
        <ul class="side-menu">

            <li class="slide">
                <a class="side-menu__item" href="{{ url('/dashboard') }}">
                    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"> <path d="M543.8 287.6c17 0 32-14 32-32.1c1-9-3-17-11-24L309.5 7c-6-5-14-7-21-7s-15 1-22 8L10 231.5c-7 7-10 15-10 24c0 18 14 32.1 32 32.1h32V448c0 35.3 28.7 64 64 64H480c35.3 0 64-28.7 64-64V287.6h-.2z"/> </svg>
                    <span class="side-menu__label">{{ __('home.home') }}</span>
                </a>
            </li>

            {{-- ============== OPERATIONS ============== --}}
            <div class="side-menu__eyebrow">{{ __('home.operations') }}</div>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 512" fill="currentColor"> <path d="M48 0C21.5 0 0 21.5 0 48V368c0 26.5 21.5 48 48 48H64c0 53 43 96 96 96s96-43 96-96H384c0 53 43 96 96 96s96-43 96-96h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V288 256 237.3c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7H416V48c0-26.5-21.5-48-48-48H48zM416 160h50.7L544 237.3V256H416V160zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/> </svg>
                    <span class="side-menu__label">{{ $t('بوليصات الشحن', 'Waybills') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('waybills/create') }}"><i class="bx bx-plus-circle"></i>{{ $t('بوليصة شحن جديدة', 'New Waybill') }}</a></li>
                    <li><a class="slide-item" href="{{ url('waybills') }}"><i class="bx bx-list-ul"></i>{{ $t('البوليصات السابقة', 'Previous Waybills') }}</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ url('trucks/board') }}?new=1">
                    <i class="bx bx-package side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('إضافة شحنة جديدة', 'New shipment') }}</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-map-alt side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('حركة الشاحنات', 'Truck movement') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('trucks/board') }}?new=1"><i class="bx bx-package"></i>{{ $t('إضافة شحنة جديدة', 'New shipment') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/board') }}"><i class="bx bxs-truck"></i>{{ $t('لوحة الشاحنات', 'Truck board') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/drivers') }}"><i class="bx bx-id-card"></i>{{ $t('السائقين', 'Drivers') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/expenses') }}"><i class="bx bx-wallet"></i>{{ $t('مصروفات الشاحنات', 'Truck expenses') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/regions') }}"><i class="bx bx-map-pin"></i>{{ $t('المناطق', 'Regions') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/report') }}"><i class="bx bx-bar-chart-alt-2"></i>{{ $t('تقرير الأحمال', 'Loads report') }}</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-receipt side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('فواتير النقل الضريبية', 'Transport tax invoices') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('transport-invoices/create') }}"><i class="bx bx-plus-circle"></i>{{ $t('فاتورة نقل جديدة', 'New invoice') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-invoices') }}"><i class="bx bx-list-ul"></i>{{ $t('الفواتير السابقة', 'Previous invoices') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/unbilled') }}"><i class="bx bx-error-circle"></i>{{ $t('أحمال غير مفوترة', 'Unbilled loads') }}</a></li>
                </ul>
            </li>

            @can('Sales products')
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 576 512" fill="currentColor"> <path d="M64 0C28.7 0 0 28.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V224H384c-17.7 0-32-14.3-32-32V0H64zM256 0V192H448L256 0zM80 320H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/> </svg>
                    <span class="side-menu__label">{{ __('home.banks_transfer') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('create_transfer') }}"><i class="bx bx-plus-circle"></i>{{ __('home.create_transfer') }}</a></li>
                    <li><a class="slide-item" href="{{ url('previousTransfers') }}"><i class="bx bx-list-ul"></i>{{ __('home.banks_transfer') }}</a></li>
                </ul>
            </li>
            @endcan

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 576 512" fill="currentColor"> <path d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H480c35.3 0 64-28.7 64-64V160H352c-17.7 0-32-14.3-32-32V0H96zM384 0V128H512L384 0zM216 232V334.1l31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31V232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/> </svg>
                    <span class="side-menu__label">{{ __('home.Covenant_liquidation') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('create_liquidation') }}"><i class="bx bx-plus-circle"></i>{{ __('home.create_liquidation') }}</a></li>
                    <li><a class="slide-item" href="{{ url('create_purchase') }}"><i class="bx bx-cart-add"></i>{{ __('home.create_purchase') }}</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 512 512" fill="currentColor"> <path d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/> </svg>
                    <span class="side-menu__label">{{ __('home.qualifiers_all') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('recent_liquidation') }}"><i class="bx bx-list-check"></i>{{ __('home.liquidation') }}</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 576 512" fill="currentColor"> <path d="M528 128h-96l-9.6-28.8C420.9 82.1 404 64 380.8 64H195.2c-23.2 0-40.1 18.1-41.6 35.2L144 128H48c-26.5 0-48 21.5-48 48s21.5 48 48 48h16v192c0 53 43 96 96 96H416c53 0 96-43 96-96V224h16c26.5 0 48-21.5 48-48s-21.5-48-48-48zM224 288c0-8.8 7.2-16 16-16h96c8.8 0 16 7.2 16 16s-7.2 16-16 16H240c-8.8 0-16-7.2-16-16z"/> </svg>
                    <span class="side-menu__label">{{ __('home.Quotations') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('getproductspricetocustomer') }}"><i class="bx bx-plus-circle"></i>{{ __('home.Offerـpricesـtoـcustomer') }}</a></li>
                    <li><a class="slide-item" href="{{ url('PreviousQuotes') }}"><i class="bx bx-history"></i>{{ __('home.recentquotation') }}</a></li>
                </ul>
            </li>

            {{-- ============== ACCOUNTING ============== --}}
            @can('Accounts')
            <div class="side-menu__eyebrow">{{ __('home.accounting') }}</div>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 384 512" fill="currentColor"> <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM96 64H288c17.7 0 32 14.3 32 32v32c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V96c0-17.7 14.3-32 32-32z"/> </svg>
                    <span class="side-menu__label">{{ __('home.accounting') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    @can('Receipt document')
                    <li><a class="slide-item" href="{{ url('account_type') }}"><i class="bx bx-category"></i>{{ __('home.account_type') }}</a></li>
                    @endcan
                    @can('enpenses_reason')
                    <li><a class="slide-item" href="{{ url('expenses_reason') }}"><i class="bx bx-purchase-tag"></i>{{ __('report.enpenses_reason') }}</a></li>
                    @endcan
                    <li><a class="slide-item" href="{{ url('financial_accounts') }}"><i class="bx bx-wallet"></i>{{ __('home.Financial_accounts') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Opening_entry') }}"><i class="bx bx-book-open"></i>{{ __('home.Opening_entry') }}</a></li>
                    <li><a class="slide-item" href="{{ url('create_acount') }}"><i class="bx bx-plus-circle"></i>{{ __('home.add_new_account') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Daily_record') }}"><i class="bx bx-notepad"></i>{{ __('home.Daily_record') }}</a></li>
                    <li><a class="slide-item" href="{{ url('tree') }}"><i class="bx bx-git-repo-forked"></i>{{ __('home.tree') }}</a></li>
                    @can('Receipt document')
                    <li><a class="slide-item" href="{{ url('reciept_decoument') }}"><i class="bx bx-receipt"></i>{{ __('home.Receipt document') }}</a></li>
                    @endcan
                    @can('Voucher')
                    <li><a class="slide-item" href="{{ url('voncher') }}"><i class="bx bx-money"></i>{{ __('home.voucher') }}</a></li>
                    @endcan
                    @can('List of customers')
                    <li><a class="slide-item" href="{{ url('Customerlist') }}"><i class="bx bx-group"></i>{{ __('home.customerList') }}</a></li>
                    @endcan
                    @can('List of suppliers')
                    <li><a class="slide-item" href="{{ url('supplierlist') }}"><i class="bx bx-store"></i>{{ __('report.Listofsupplier') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- ============== التقارير المحاسبية ============== --}}
            <div class="side-menu__eyebrow">{{ $t('التقارير المحاسبية', 'Accounting reports') }}</div>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-calculator side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('التقارير المحاسبية', 'Accounting reports') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('accounting-reports/trial-balance') }}"><i class="bx bx-spreadsheet"></i>{{ $t('ميزان المراجعة', 'Trial balance') }}</a></li>
                    <li><a class="slide-item" href="{{ url('accounting-reports/income-statement') }}"><i class="bx bx-trending-up"></i>{{ $t('قائمة الدخل (أرباح وخسائر)', 'Income statement') }}</a></li>
                    <li><a class="slide-item" href="{{ url('accounting-reports/balance-sheet') }}"><i class="bx bx-building"></i>{{ $t('الميزانية العمومية', 'Balance sheet') }}</a></li>
                    <li><a class="slide-item" href="{{ url('accounting-reports/vat') }}"><i class="bx bx-receipt"></i>{{ $t('إقرار ضريبة القيمة المضافة', 'VAT return') }}</a></li>
                    <li><a class="slide-item" href="{{ url('account_statement') }}"><i class="bx bx-user"></i>{{ $t('كشف حساب', 'Account statement') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Daily_record_report') }}"><i class="bx bx-notepad"></i>{{ $t('دفتر اليومية', 'Journal') }}</a></li>
                    <li><a class="slide-item" href="{{ url('tree') }}"><i class="bx bx-git-repo-forked"></i>{{ $t('شجرة الحسابات', 'Chart of accounts') }}</a></li>
                </ul>
            </li>

            {{-- ============== تقارير النقل ============== --}}
            <div class="side-menu__eyebrow">{{ $t('تقارير النقل', 'Transport reports') }}</div>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-line-chart side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('تقارير النقل', 'Transport reports') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('transport-reports/overview') }}"><i class="bx bx-line-chart"></i>{{ $t('تقرير النقل الشامل', 'Transport overview') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/report') }}"><i class="bx bx-bar-chart-alt-2"></i>{{ $t('تقرير الأحمال', 'Loads report') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/top') }}"><i class="bx bx-trophy"></i>{{ $t('الأكثر نقلاً (شاحنات / عملاء)', 'Top trucks & customers') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/invoices') }}"><i class="bx bx-receipt"></i>{{ $t('الفواتير والضريبة', 'Invoices & VAT') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/customers') }}"><i class="bx bx-group"></i>{{ $t('العملاء وكشف الحساب', 'Customers & statements') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/trucks') }}"><i class="bx bxs-truck"></i>{{ $t('الشاحنات (الإيراد والمصروف والربح)', 'Trucks profit') }}</a></li>
                    <li><a class="slide-item" href="{{ url('trucks/expenses') }}"><i class="bx bx-wallet"></i>{{ $t('مصروفات كل شاحنة', 'Truck expenses') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/drivers') }}"><i class="bx bx-id-card"></i>{{ $t('السائقين', 'Drivers') }}</a></li>
                    <li><a class="slide-item" href="{{ url('transport-reports/unbilled') }}"><i class="bx bx-error-circle"></i>{{ $t('الأحمال غير المفوترة', 'Unbilled loads') }}</a></li>
                </ul>
            </li>

            {{-- ============== الموارد البشرية ============== --}}
            <div class="side-menu__eyebrow">{{ $t('الموارد البشرية', 'Human resources') }}</div>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-user-pin side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('الموظفين', 'Employees') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('allEmployees') }}"><i class="bx bx-list-ul"></i>{{ __('hr.show_employees') }}</a></li>
                    <li><a class="slide-item" href="{{ url('createNewEmployee') }}"><i class="bx bx-user-plus"></i>{{ __('hr.add_new_employee') }}</a></li>
                    <li><a class="slide-item" href="{{ url('addnewDepartment') }}"><i class="bx bx-sitemap"></i>{{ __('hr.createdepartment') }}</a></li>
                    <li><a class="slide-item" href="{{ route('contracts.index') }}"><i class="bx bx-file"></i>{{ __('hr.contracts_management') }}</a></li>
                    <li><a class="slide-item" href="{{ route('documents.alerts') }}"><i class="bx bx-bell"></i>{{ $t('تنبيهات الإقامات والعقود', 'Document alerts') }}</a></li>
                    <li><a class="slide-item" href="{{ route('custodies.index') }}"><i class="bx bx-briefcase"></i>{{ $t('العهد', 'Custodies') }}</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-calendar-check side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('الحضور والإجازات', 'Attendance & leaves') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('attendances.index') }}"><i class="bx bx-time"></i>{{ __('hr.attendances_log') }}</a></li>
                    <li><a class="slide-item" href="{{ route('leaves.index') }}"><i class="bx bx-calendar-minus"></i>{{ __('hr.employee_leaves') }}</a></li>
                    <li><a class="slide-item" href="{{ route('leaves.balance_report') }}"><i class="bx bx-bar-chart-square"></i>{{ $t('رصيد الإجازات', 'Leave balance') }}</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-money-withdraw side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('الرواتب والمستحقات', 'Payroll') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('salarydecoument') }}"><i class="bx bx-spreadsheet"></i>{{ __('hr.salarydecoument') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Increaseـor_deduction') }}"><i class="bx bx-transfer-alt"></i>{{ $t('مكافأة / خصم', 'Bonus / deduction') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Loans') }}"><i class="bx bx-credit-card"></i>{{ $t('السلف', 'Loans') }}</a></li>
                    <li><a class="slide-item" href="{{ route('eos.index') }}"><i class="bx bx-exit"></i>{{ $t('مكافأة نهاية الخدمة', 'End of service') }}</a></li>
                    <li><a class="slide-item" href="{{ route('hr-settings.index') }}"><i class="bx bx-cog"></i>{{ __('hr.hr_settings') }}</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="bx bx-bar-chart-square side-menu__icon"></i>
                    <span class="side-menu__label">{{ $t('تقارير الموارد البشرية', 'HR reports') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('hr/reports/employees') }}"><i class="bx bx-user-pin"></i>{{ $t('الموظفين والرواتب', 'Employees & salaries') }}</a></li>
                    <li><a class="slide-item" href="{{ url('hr/reports/payroll') }}"><i class="bx bx-money-withdraw"></i>{{ $t('مسير الرواتب الشهري', 'Monthly payroll') }}</a></li>
                    <li><a class="slide-item" href="{{ url('hr/reports/attendance') }}"><i class="bx bx-time"></i>{{ $t('الحضور والانصراف', 'Attendance') }}</a></li>
                    <li><a class="slide-item" href="{{ url('hr/reports/leaves') }}"><i class="bx bx-calendar-minus"></i>{{ $t('الإجازات', 'Leaves') }}</a></li>
                </ul>
            </li>

            {{-- ============== REPORTS ============== --}}
            @can('Reports')
            <div class="side-menu__eyebrow">{{ __('home.reports') }}</div>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 576 512" fill="currentColor"> <path d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM256 0L384 128H256V0z"/> </svg>
                    <span class="side-menu__label">{{ __('home.reports') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    @can('Credit collection')
                    <li><a class="slide-item" href="{{ url('credit_collection') }}"><i class="bx bx-collection"></i>{{ __('report.creditcollection') }}</a></li>
                    <li><a class="slide-item" href="{{ url('Supplier_credit_payment') }}"><i class="bx bx-credit-card"></i>{{ __('report.Supplier credit payment') }}</a></li>
                    @endcan
                    @can('Sales products')
                    <li><a class="slide-item" href="{{ url('Daily_record_report') }}"><i class="bx bx-notepad"></i>{{ __('home.Daily_record') }}</a></li>
                    @endcan
                    <li><a class="slide-item" href="{{ url('shipments_report') }}"><i class="bx bx-transfer"></i>{{ __('home.banks_transfer') }}</a></li>
                    @can('Sales products')
                    <li><a class="slide-item" href="{{ url('cost_center') }}"><i class="bx bx-pie-chart-alt-2"></i>{{ __('home.cost_center') }}</a></li>
                    @endcan
                    @can('Supplier credit payment')
                    <li><a class="slide-item" href="{{ url('account_statement') }}"><i class="bx bx-spreadsheet"></i>{{ __('home.account_statement') }}</a></li>
                    @endcan
                    <li><a class="slide-item" href="{{ url('liquidation') }}"><i class="bx bx-list-check"></i>{{ __('home.liquidation') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- ============== ADMINISTRATION ============== --}}
            <div class="side-menu__eyebrow">{{ __('home.setting') }}</div>

            @can('User and branches')
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 512" fill="currentColor"> <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0z"/> </svg>
                    <span class="side-menu__label">{{ __('home.users') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    @can('add branch')
                    <li><a class="slide-item" href="{{ url('showallBranchs') }}"><i class="bx bx-buildings"></i>{{ __('report.allBranches') }}</a></li>
                    @endcan
                    @can('List of users')
                    <li><a class="slide-item" href="{{ url('users') }}"><i class="bx bx-user"></i>{{ __('users.usersList') }}</a></li>
                    @endcan
                    <li><a class="slide-item" href="{{ url('roles') }}"><i class="bx bx-shield-quarter"></i>{{ __('users.Userـpermissions') }}</a></li>
                </ul>
            </li>
            @endcan

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 640 512" fill="currentColor"> <path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5l-3.3-6.5c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8h-.7c-6.8 0-13.5 .4-20.1 1.2z"/> </svg>
                    <span class="side-menu__label">{{ __('home.setting') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('profile') }}"><i class="bx bx-user-circle"></i>{{ __('auth.setting') }}</a></li>
                    @can('AVT')
                    <li><a class="slide-item" href="{{ url('avt') }}"><i class="bx bx-slider-alt"></i>{{ __('home.AVTSHOW') }}</a></li>
                    @endcan
                    @can('System setting')
                    <li><a class="slide-item" href="{{ url('systemSetting') }}"><i class="bx bx-cog"></i>{{ __('home.systemSetting') }}</a></li>
                    @endcan
                </ul>
            </li>

            @can('Technical support')
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 512 512" fill="currentColor"> <path d="M256 48C141.1 48 48 141.1 48 256v40c0 13.3-10.7 24-24 24s-24-10.7-24-24V256C0 114.6 114.6 0 256 0S512 114.6 512 256V400.1c0 48.6-39.4 88-88.1 88L313.6 488c-8.3 14.3-23.8 24-41.6 24H240c-26.5 0-48-21.5-48-48s21.5-48 48-48h32c17.8 0 33.3 9.7 41.6 24l110.4 .1c22.1 0 40-17.9 40-40V256c0-114.9-93.1-208-208-208z"/> </svg>
                    <span class="side-menu__label">{{ __('home.For communication and technical support') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item" href="https://ebdeasoft.com/" target="_blank" rel="noopener noreferrer">
                            <i class="bx bx-globe"></i>{{ __('home.connectwithebdeasoft') }}
                        </a>
                    </li>
                    <li>
                        <a class="slide-item" target="_blank" rel="noopener noreferrer"
                           href="https://api.whatsapp.com/send/?phone=%3B+966(0)534544615&text=%D8%A7%D9%84%D8%B3%D9%84%D8%A7%D9%85+%D8%B9%D9%84%D9%8A%D9%83%D9%85+...+%D8%A3%D8%B1%D8%BA%D8%A8+%D8%A8%D8%AE%D8%AF%D9%85%D8%A9+%D8%AA%D8%B3%D9%88%D9%8A%D9%82+%D8%A7%D9%84%D9%86%D8%B4%D8%A7%D8%B7+%D8%A7%D9%84%D8%AA%D8%AC%D8%A7%D8%B1%D9%8A&type=phone_number&app_absent=0">
                            <i class="bx bxl-whatsapp" style="color:#25D366"></i>{{ __('home.whatsappcontact') }}
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

        </ul>
        <div class="sb-empty" id="sbEmpty">{{ $t('لا توجد نتائج', 'No results') }}</div>
    </div>

    {{-- ===== Footer: language + user ===== --}}
    <div class="sb-footer">
        <div class="sb-lang">
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                @if (app()->getLocale() != $localeCode)
                    <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <i class="bx bx-globe"></i> {{ $properties['native'] }}
                    </a>
                @endif
            @endforeach
        </div>
        <div class="sb-user">
            <span class="sb-avatar">
                <img src="{{ !empty($sbUser->profile_photo_path) ? URL::asset('storage/' . $sbUser->profile_photo_path) : asset('assets/img/default-user.svg') }}" alt="user"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/default-user.svg') }}'">
            </span>
            <span class="sb-user-info">
                <span class="sb-user-name">{{ $sbUser->name }}</span>
                <span class="sb-user-mail">{{ $sbUser->email }}</span>
            </span>
            <a class="sb-icon-btn" href="{{ url('profile') }}" title="{{ __('auth.setting') }}"><i class="bx bx-cog"></i></a>
            <a class="sb-icon-btn sb-danger" href="{{ route('logout') }}" title="{{ __('home.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-log-out"></i></a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sb = document.querySelector('.app-sidebar');
    if (!sb) return;

    /* ---- Active link (ignores the /ar or /en prefix) ---- */
    var locales = @json(array_keys(LaravelLocalization::getSupportedLocales()));
    function norm(href) {
        try {
            var p = new URL(href, location.origin).pathname.replace(/\/+$/, '').split('/').filter(Boolean);
            if (p.length && locales.indexOf(p[0]) > -1) p.shift();
            return '/' + p.join('/');
        } catch (e) { return ''; }
    }
    var here = norm(location.href), best = null, bestLen = -1;
    sb.querySelectorAll('.side-menu a[href]').forEach(function (a) {
        var h = a.getAttribute('href');
        if (!h || h === '#' || a.target === '_blank') return;
        var n = norm(h);
        if (n === '/') return;
        if ((here === n || here.indexOf(n + '/') === 0) && n.length > bestLen) { best = a; bestLen = n.length; }
    });
    if (best) {
        best.classList.add('active');
        var li = best.closest('.slide');
        if (li) {
            li.classList.add('active');
            if (li.querySelector('.slide-menu')) li.classList.add('is-expanded');
        }
        setTimeout(function () {
            var box = sb.querySelector('.sb-scroll');
            if (box && best.offsetTop > box.clientHeight - 60) box.scrollTop = best.offsetTop - 120;
        }, 50);
    }

    /* ---- Search filter ---- */
    var input = document.getElementById('sbSearch'), empty = document.getElementById('sbEmpty');
    var slides = Array.prototype.slice.call(sb.querySelectorAll('.side-menu > .slide'));
    var eyebrows = sb.querySelectorAll('.side-menu__eyebrow');
    slides.forEach(function (li) {
        li._wasExpanded = li.classList.contains('is-expanded');
        li.querySelectorAll('.slide-item, .side-menu__label').forEach(function (el) {
            el._text = el.textContent.trim().toLowerCase();
        });
    });
    function filter() {
        var q = (input.value || '').trim().toLowerCase(), any = false;
        eyebrows.forEach(function (e) { e.classList.toggle('sb-hidden', !!q); });
        slides.forEach(function (li) {
            var label = li.querySelector('.side-menu__label');
            var items = li.querySelectorAll('.slide-menu li');
            if (!q) {
                li.classList.remove('sb-hidden');
                items.forEach(function (it) { it.classList.remove('sb-hidden'); });
                li.classList.toggle('is-expanded', li._wasExpanded || li.classList.contains('active'));
                any = true; return;
            }
            var labelHit = label && label._text.indexOf(q) > -1, subHit = false;
            items.forEach(function (it) {
                var a = it.querySelector('.slide-item');
                var hit = labelHit || (a && a._text.indexOf(q) > -1);
                it.classList.toggle('sb-hidden', !hit);
                if (a && a._text.indexOf(q) > -1) subHit = true;
            });
            var show = labelHit || subHit;
            li.classList.toggle('sb-hidden', !show);
            if (items.length) li.classList.toggle('is-expanded', show);
            if (show) any = true;
        });
        empty.style.display = any ? 'none' : 'block';
    }
    input.addEventListener('input', filter);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { input.value = ''; filter(); input.blur(); }
        if (e.key === 'Enter') {
            var first = sb.querySelector('.side-menu > .slide:not(.sb-hidden) .slide-menu li:not(.sb-hidden) .slide-item, .side-menu > .slide:not(.sb-hidden) > .side-menu__item:not([data-toggle])');
            if (first) window.location = first.href;
        }
    });
    /* Ctrl + K للبحث */
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) { e.preventDefault(); input.focus(); input.select(); }
    });
});
</script>
<!-- main-sidebar -->
