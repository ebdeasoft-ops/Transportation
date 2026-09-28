@extends('layouts.master')

@section('css')
<!--  Owl-carousel css-->
<link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&family=Inter:wght@500;600;700;800&display=swap"
    rel="stylesheet">
<style>
    :root {
        --dash-bg: #EEF1F7;
        --dash-bg-alt: #F7F8FB;
        --dash-card: #FFFFFF;
        --dash-border: #E6E9F1;
        --dash-border-soft: #EEF0F6;
        --dash-ink: #171E2E;
        --dash-muted: #6C7488;
        --dash-muted-soft: #98A0B3;

        --dash-navy: #131C33;
        --dash-navy-2: #1D2B4C;
        --dash-navy-soft: #26365E;
        --dash-navy-glow: rgba(19, 28, 51, .28);

        --dash-gold: #B4893B;
        --dash-gold-2: #D3A85B;
        --dash-gold-soft: #FBF3E1;
        --dash-gold-glow: rgba(180, 137, 59, .28);

        --dash-green: #17875A;
        --dash-green-2: #22A472;
        --dash-green-soft: #E7F7EF;
        --dash-green-glow: rgba(23, 135, 90, .26);

        --dash-amber: #B4711A;
        --dash-amber-2: #D8912F;
        --dash-amber-soft: #FCF0DE;
        --dash-amber-glow: rgba(180, 113, 26, .26);

        --dash-teal: #147C93;
        --dash-teal-2: #1CA3BF;
        --dash-teal-soft: #E4F6F9;
        --dash-teal-glow: rgba(20, 124, 147, .26);

        --dash-radius: 16px;
        --dash-radius-lg: 20px;
        --dash-radius-sm: 10px;

        --dash-shadow-xs: 0 1px 2px rgba(23, 30, 46, .04);
        --dash-shadow-sm: 0 6px 16px -10px rgba(23, 30, 46, .18);
        --dash-shadow-md: 0 16px 32px -16px rgba(23, 30, 46, .22);
        --dash-shadow-lg: 0 26px 48px -20px rgba(23, 30, 46, .28);
    }

    .dashboard-wrap {
        font-family: 'Cairo', 'Inter', sans-serif;
        color: var(--dash-ink);
        background:
            radial-gradient(1100px 320px at 100% -10%, rgba(19, 28, 51, .05), transparent 60%),
            radial-gradient(900px 280px at -5% 0%, rgba(180, 137, 59, .06), transparent 55%),
            var(--dash-bg);
        padding: 28px clamp(14px, 2.4vw, 32px) 44px;
        border-radius: 22px;
        position: relative;
    }

    .dashboard-wrap * {
        box-sizing: border-box;
    }

    /* ===== Section shell ===== */
    .dash-section {
        margin-bottom: 26px;
        background: var(--dash-card);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius-lg);
        padding: 20px 20px 22px;
        box-shadow: var(--dash-shadow-xs);
        position: relative;
        overflow: hidden;
        animation: dashSectionIn .5s cubic-bezier(.22, .61, .36, 1) both;
    }

    .dash-section::before {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--dash-navy), var(--dash-navy-soft));
        opacity: .9;
    }

    .dash-section:has(.dash-section__bar.is-gold)::before {
        background: linear-gradient(180deg, var(--dash-gold), var(--dash-gold-2));
    }

    .dash-section:last-child {
        margin-bottom: 0;
    }

    .dash-section__head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-inline-start: 2px;
        flex-wrap: wrap;
    }

    .dash-section__bar {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(145deg, var(--dash-navy) 0%, var(--dash-navy-soft) 100%);
        box-shadow: 0 8px 16px -8px var(--dash-navy-glow);
        position: relative;
    }

    .dash-section__bar::after {
        content: "";
        position: absolute;
        inset: 0;
        margin: auto;
        width: 12px;
        height: 12px;
        border-radius: 3px;
        background: rgba(255, 255, 255, .92);
    }

    .dash-section__bar.is-gold {
        background: linear-gradient(145deg, var(--dash-gold) 0%, var(--dash-gold-2) 100%);
        box-shadow: 0 8px 16px -8px var(--dash-gold-glow);
    }

    .dash-section__title {
        font-size: 16.5px;
        font-weight: 800;
        margin: 0;
        color: var(--dash-navy);
        letter-spacing: .1px;
    }

    .dash-section__sub {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--dash-navy);
        background: var(--dash-gold-soft);
        border: 1px solid rgba(180, 137, 59, .28);
        padding: 4px 12px;
        border-radius: 999px;
        margin-inline-start: auto;
    }

    /* ===== Stats grid & cards ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 14px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--dash-bg-alt);
        border: 1px solid var(--dash-border-soft);
        border-radius: var(--dash-radius);
        padding: 16px 16px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        isolation: isolate;
        transition: transform .2s cubic-bezier(.22, .61, .36, 1), box-shadow .2s ease, border-color .2s ease, background .2s ease;
        animation: dashCardIn .5s cubic-bezier(.22, .61, .36, 1) both;
    }

    .stats-grid .stat-card:nth-child(1) { animation-delay: .02s; }
    .stats-grid .stat-card:nth-child(2) { animation-delay: .06s; }
    .stats-grid .stat-card:nth-child(3) { animation-delay: .10s; }
    .stats-grid .stat-card:nth-child(4) { animation-delay: .14s; }
    .stats-grid .stat-card:nth-child(5) { animation-delay: .18s; }
    .stats-grid .stat-card:nth-child(6) { animation-delay: .22s; }
    .stats-grid .stat-card:nth-child(7) { animation-delay: .26s; }
    .stats-grid .stat-card:nth-child(8) { animation-delay: .30s; }
    .stats-grid .stat-card:nth-child(n+9) { animation-delay: .34s; }

    .stat-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(120px 90px at 100% 0%, rgba(19, 28, 51, .07), transparent 70%);
        opacity: 0;
        transition: opacity .25s ease;
        z-index: -1;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: 10px;
        bottom: 10px;
        width: 3px;
        border-radius: 3px;
        background: var(--dash-navy);
        opacity: .55;
    }

    .stat-card:hover {
        border-color: transparent;
        background: var(--dash-card);
        transform: translateY(-3px);
        box-shadow: var(--dash-shadow-md);
        text-decoration: none;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card--gold::after { background: var(--dash-gold); }
    .stat-card--green::after { background: var(--dash-green); }
    .stat-card--amber::after { background: var(--dash-amber); }

    .stat-card__icon {
        flex: 0 0 auto;
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(150deg, var(--dash-navy) 0%, var(--dash-navy-soft) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-navy-glow);
        transition: transform .25s cubic-bezier(.22, .61, .36, 1);
    }

    .stat-card:hover .stat-card__icon {
        transform: scale(1.06) rotate(-2deg);
    }

    .stat-card__icon svg {
        width: 21px;
        height: 21px;
        fill: #fff;
    }

    .stat-card--gold .stat-card__icon {
        background: linear-gradient(150deg, var(--dash-gold) 0%, var(--dash-gold-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-gold-glow);
    }

    .stat-card--green .stat-card__icon {
        background: linear-gradient(150deg, var(--dash-green) 0%, var(--dash-green-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-green-glow);
    }

    .stat-card--amber .stat-card__icon {
        background: linear-gradient(150deg, var(--dash-amber) 0%, var(--dash-amber-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-amber-glow);
    }

    /* colored initial avatars for lists of many similarly-shaped items
       (bank/branch/custody accounts) so they read as distinct entities
       instead of one icon copy-pasted across every card */
    .stat-card__icon--avatar span {
        font-family: 'Cairo', 'Inter', sans-serif;
        font-size: 16px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .stat-card__icon--navy {
        background: linear-gradient(150deg, var(--dash-navy) 0%, var(--dash-navy-soft) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-navy-glow);
    }

    .stat-card__icon--gold {
        background: linear-gradient(150deg, var(--dash-gold) 0%, var(--dash-gold-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-gold-glow);
    }

    .stat-card__icon--green {
        background: linear-gradient(150deg, var(--dash-green) 0%, var(--dash-green-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-green-glow);
    }

    .stat-card__icon--amber {
        background: linear-gradient(150deg, var(--dash-amber) 0%, var(--dash-amber-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-amber-glow);
    }

    .stat-card__icon--teal {
        background: linear-gradient(150deg, var(--dash-teal) 0%, var(--dash-teal-2) 100%);
        box-shadow: 0 10px 18px -10px var(--dash-teal-glow);
    }

    .stat-card__body {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
    }

    .stat-card__label {
        font-size: 12px;
        font-weight: 700;
        color: var(--dash-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-card__value {
        font-family: 'Inter', 'Cairo', sans-serif;
        font-size: 20px;
        font-weight: 800;
        color: var(--dash-ink);
        direction: ltr;
        unicode-bidi: plaintext;
        letter-spacing: -.2px;
    }

    .stat-card__value .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: .15em;
        color: var(--dash-muted-soft);
    }

    /* ===== Clock (kept as .dash-clock, never .datetime, to dodge the theme's !important rules) ===== */
    .dash-clock {
        display: flex !important;
        align-items: center !important;
        gap: 15px;
        width: auto !important;
        height: auto !important;
        background: linear-gradient(135deg, var(--dash-navy) 0%, var(--dash-navy-2) 55%, var(--dash-navy-soft) 100%) !important;
        color: #fff !important;
        padding: 12px 24px !important;
        border-radius: 18px !important;
        font-family: 'Cairo', 'Inter', sans-serif;
        line-height: 1.3;
        box-shadow: 0 18px 34px -16px rgba(19, 28, 51, .55) !important;
        border: 1px solid rgba(255, 255, 255, .1) !important;
        text-align: start !important;
        position: relative;
        overflow: hidden;
    }

    .dash-clock::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(160px 90px at 100% 0%, rgba(211, 168, 91, .28), transparent 70%);
        pointer-events: none;
    }

    .dash-clock__icon {
        flex: 0 0 auto;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .14);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .16);
        position: relative;
        z-index: 1;
    }

    .dash-clock__icon svg {
        width: 22px;
        height: 22px;
    }

    .dash-clock__body {
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
    }

    .dash-clock__time {
        font-family: 'Inter', 'Cairo', sans-serif;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: .5px;
        font-variant-numeric: tabular-nums;
        direction: ltr;
        unicode-bidi: plaintext;
        display: flex;
        align-items: baseline;
        gap: 6px;
        background: linear-gradient(90deg, #fff, #E8D9BC);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .dash-clock__period {
        font-family: 'Cairo', 'Inter', sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: var(--dash-gold-2);
        background: rgba(211, 168, 91, .2);
        padding: 2px 8px;
        border-radius: 7px;
        -webkit-text-fill-color: var(--dash-gold-2);
    }

    .dash-clock__date {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #C7D0E0;
        margin-top: 3px;
    }

    .dash-clock__live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #34D399;
        flex: 0 0 auto;
        animation: dash-pulse 2s infinite;
    }

    @keyframes dash-pulse {
        0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, .55); }
        70% { box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
    }

    /* ===== Entrance animations ===== */
    @keyframes dashSectionIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes dashCardIn {
        from { opacity: 0; transform: translateY(8px) scale(.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ===== Charts ===== */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
    }

    .chart-card {
        background: var(--dash-bg-alt);
        border: 1px solid var(--dash-border-soft);
        border-radius: var(--dash-radius);
        padding: 18px 18px 12px;
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .chart-card:hover {
        box-shadow: var(--dash-shadow-sm);
        transform: translateY(-2px);
    }

    .chart-card::before {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        height: 3px;
        width: 100%;
        background: linear-gradient(90deg, var(--dash-navy), var(--dash-gold));
        opacity: .85;
    }

    .chart-card__title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--dash-navy);
        margin-bottom: 12px;
    }

    .chart-card__title::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 3px;
        background: var(--dash-gold);
        flex: 0 0 auto;
    }

    .chart-card__canvas-wrap {
        position: relative;
        height: 240px;
    }

    .chart-card__empty {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 16px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--dash-muted-soft);
        background: repeating-linear-gradient(135deg, rgba(23,30,46,.025) 0 10px, transparent 10px 20px);
        border-radius: 10px;
    }

    /* ===== Responsive ===== */
    @media (max-width: 576px) {
        .dashboard-wrap {
            padding: 18px 12px 30px;
            border-radius: 16px;
        }

        .dash-section {
            padding: 16px 14px 18px;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }

        .stat-card {
            padding: 13px 12px;
        }

        .dash-clock {
            padding: 10px 16px !important;
        }
    }
</style>
@endsection
@section('title')
{{ __('home.home') }}
@stop
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1 welcoming">
                {{ __('home.welcome') }}{{ Auth()->User()->name }} !
            </h2>
        </div>
    </div>
    <div class="main-dashboard-header-right">
        <br>
        <div>
            <div class="main-star">
                <div class="dash-clock">
                    <span class="dash-clock__icon">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="7.5" stroke="#fff" stroke-width="1.3" />
                            <path d="M10 5.8V10l3 1.8" stroke="#fff" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <div class="dash-clock__body">
                        <div class="dash-clock__time">
                            <span class="dash-clock__time-value"></span>
                            <span class="dash-clock__period"></span>
                        </div>
                        <div class="dash-clock__date">
                            <span class="dash-clock__live-dot"></span>
                            <span class="dash-clock__date-value"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /breadcrumb -->
@endsection
@section('content')
@can('Home')
<div class="row row-sm parent-card" style="background-color: white;">
    <div class="dashboard-wrap" style="width:100%;">
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar"></span>
                <h3 class="dash-section__title">{{ __('home.salesdoday') }}</h3>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.salesdoday') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Transactions::whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M10.862,6.47H3.968v6.032h6.894V6.47z M10,11.641H4.83V7.332H10V11.641z M12.585,11.641h-0.861v0.861h0.861V11.641z M7.415,14.226h0.862v-0.862H7.415V14.226z M8.707,17.673h2.586c0.237,0,0.431-0.193,0.431-0.432c0-0.237-0.193-0.431-0.431-0.431H8.707c-0.237,0-0.431,0.193-0.431,0.431C8.276,17.479,8.47,17.673,8.707,17.673 M5.691,14.226h0.861v-0.862H5.691V14.226z M4.83,13.363H3.968v0.862H4.83V13.363z M16.895,4.746h-3.017V3.023h1.292c0.476,0,0.862-0.386,0.862-0.862V1.299c0-0.476-0.387-0.862-0.862-0.862H10c-0.476,0-0.862,0.386-0.862,0.862v0.862c0,0.476,0.386,0.862,0.862,0.862h1.293v1.723H3.106c-0.476,0-0.862,0.386-0.862,0.862v12.926c0,0.476,0.386,0.862,0.862,0.862h13.789c0.475,0,0.861-0.387,0.861-0.862V5.608C17.756,5.132,17.369,4.746,16.895,4.746 M10.862,2.161H10V1.299h0.862V2.161zM11.724,1.299h3.446v0.862h-3.446V1.299z M13.016,4.746h-0.861V3.023h0.861V4.746z M16.895,18.534H3.106v-2.585h13.789V18.534zM16.895,15.088H3.106v-9.48h13.789V15.088z M15.17,12.502h0.862v-0.861H15.17V12.502z M13.447,12.502h0.861v-0.861h-0.861V12.502zM15.17,10.778h0.862V9.917H15.17V10.778z M15.17,9.055h0.862V8.193H15.17V9.055z M16.032,6.47h-4.309v0.862h4.309V6.47zM14.309,8.193h-0.861v0.862h0.861V8.193z M12.585,8.193h-0.861v0.862h0.861V8.193z M13.447,14.226h2.585v-0.862h-2.585V14.226zM13.447,10.778h0.861V9.917h-0.861V10.778z M12.585,9.917h-0.861v0.861h0.861V9.917z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TODAYEARNINGS') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Transactions::whereDate('created_at', date('Y-m-d'))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.PRODUCT_number_SOLD') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Transactions::whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M5.109,8.392H4.249c-0.238,0-0.43,0.193-0.43,0.431c0,0.238,0.192,0.431,0.43,0.431h0.861c0.238,0,0.43-0.193,0.43-0.431C5.54,8.585,5.347,8.392,5.109,8.392 M4.249,4.088h11.19c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H4.249c-0.238,0-0.43,0.192-0.43,0.431C3.818,3.896,4.011,4.088,4.249,4.088 M2.527,5.81H17.16c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H2.527c-0.238,0-0.43,0.192-0.43,0.431C2.097,5.617,2.289,5.81,2.527,5.81 M18.452,6.67H1.236c-0.476,0-0.861,0.385-0.861,0.861v8.608c0,0.475,0.385,0.86,0.861,0.86h17.216c0.475,0,0.86-0.386,0.86-0.86V7.531C19.312,7.056,18.927,6.67,18.452,6.67 M1.666,7.531c0.238,0,0.431,0.192,0.431,0.431c0,0.238-0.192,0.43-0.431,0.43c-0.238,0-0.43-0.192-0.43-0.43C1.236,7.724,1.428,7.531,1.666,7.531 M1.666,16.14c-0.238,0-0.43-0.192-0.43-0.431c0-0.237,0.192-0.431,0.43-0.431c0.238,0,0.431,0.193,0.431,0.431C2.097,15.947,1.904,16.14,1.666,16.14 M18.021,16.14c-0.238,0-0.431-0.192-0.431-0.431c0-0.237,0.192-0.431,0.431-0.431s0.431,0.193,0.431,0.431C18.452,15.947,18.26,16.14,18.021,16.14 M18.452,14.496c-0.136-0.048-0.279-0.078-0.431-0.078c-0.714,0-1.291,0.578-1.291,1.291c0,0.151,0.03,0.295,0.078,0.431H2.878c0.048-0.136,0.079-0.279,0.079-0.431c0-0.713-0.579-1.291-1.292-1.291c-0.151,0-0.295,0.03-0.43,0.078V9.174c0.135,0.048,0.279,0.079,0.43,0.079c0.713,0,1.292-0.578,1.292-1.291c0-0.152-0.031-0.295-0.079-0.431h13.93C16.761,7.667,16.73,7.81,16.73,7.962c0,0.713,0.577,1.291,1.291,1.291c0.151,0,0.295-0.031,0.431-0.079V14.496z M18.021,8.392c-0.238,0-0.431-0.192-0.431-0.43c0-0.238,0.192-0.431,0.431-0.431s0.431,0.192,0.431,0.431C18.452,8.2,18.26,8.392,18.021,8.392 M15.438,14.418h-0.86c-0.238,0-0.431,0.192-0.431,0.43c0,0.238,0.192,0.431,0.431,0.431h0.86c0.238,0,0.431-0.192,0.431-0.431C15.869,14.61,15.677,14.418,15.438,14.418 M9.844,8.392c-1.901,0-3.443,1.542-3.443,3.443s1.542,3.443,3.443,3.443s3.443-1.542,3.443-3.443S11.745,8.392,9.844,8.392 M11.233,13.271c-0.071,0.162-0.169,0.297-0.292,0.403c-0.124,0.108-0.268,0.189-0.434,0.246c-0.166,0.058-0.295,0.089-0.488,0.097v0.4H9.673v-0.4c-0.208-0.004-0.35-0.037-0.522-0.099c-0.174-0.063-0.322-0.151-0.445-0.267s-0.219-0.257-0.286-0.424c-0.067-0.168-0.099-0.361-0.095-0.579h0.659c-0.003,0.256,0.052,0.459,0.168,0.608c0.115,0.147,0.257,0.226,0.522,0.233v-1.417c-0.158-0.042-0.265-0.094-0.422-0.154c-0.156-0.061-0.297-0.139-0.422-0.234c-0.125-0.095-0.226-0.215-0.303-0.36c-0.077-0.144-0.115-0.323-0.115-0.538c0-0.187,0.035-0.352,0.106-0.494c0.072-0.143,0.168-0.261,0.289-0.357c0.121-0.096,0.261-0.168,0.419-0.22C9.383,9.665,9.5,9.64,9.673,9.64V9.256h0.348V9.64c0.173,0,0.287,0.023,0.441,0.07c0.154,0.047,0.288,0.117,0.401,0.211c0.114,0.093,0.204,0.212,0.272,0.356c0.067,0.145,0.101,0.312,0.101,0.503h-0.659c-0.008-0.199-0.059-0.351-0.153-0.457c-0.095-0.105-0.197-0.158-0.404-0.158V11.4c0.173,0.048,0.293,0.103,0.459,0.165c0.166,0.062,0.312,0.142,0.439,0.239c0.127,0.098,0.229,0.219,0.306,0.363c0.077,0.144,0.116,0.321,0.116,0.532C11.341,12.919,11.305,13.109,11.233,13.271M10.458,12.332c-0.067-0.051-0.143-0.092-0.228-0.123c-0.085-0.031-0.123-0.06-0.21-0.082v1.363c0.208-0.016,0.329-0.076,0.462-0.185c0.133-0.107,0.199-0.277,0.199-0.512c0-0.109-0.02-0.2-0.061-0.275C10.581,12.444,10.526,12.383,10.458,12.332 M9.069,10.74c0,0.094,0.019,0.174,0.058,0.241c0.039,0.066,0.087,0.122,0.148,0.169c0.06,0.047,0.128,0.085,0.208,0.114s0.109,0.054,0.19,0.073v-1.171c-0.208,0-0.32,0.044-0.434,0.132C9.126,10.386,9.069,10.533,9.069,10.74">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TOTAL_EARNINGS_Month') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Transactions::whereDate('created_at', '>=', date('Y-m') . '-1')
                                            ->whereDate('created_at', '<=', date('Y-m-d '))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar is-gold"></span>
                <h3 class="dash-section__title">{{ __('home.liquitiondoday') }}</h3>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M10.862,6.47H3.968v6.032h6.894V6.47z M10,11.641H4.83V7.332H10V11.641z M12.585,11.641h-0.861v0.861h0.861V11.641z M7.415,14.226h0.862v-0.862H7.415V14.226z M8.707,17.673h2.586c0.237,0,0.431-0.193,0.431-0.432c0-0.237-0.193-0.431-0.431-0.431H8.707c-0.237,0-0.431,0.193-0.431,0.431C8.276,17.479,8.47,17.673,8.707,17.673 M5.691,14.226h0.861v-0.862H5.691V14.226z M4.83,13.363H3.968v0.862H4.83V13.363z M16.895,4.746h-3.017V3.023h1.292c0.476,0,0.862-0.386,0.862-0.862V1.299c0-0.476-0.387-0.862-0.862-0.862H10c-0.476,0-0.862,0.386-0.862,0.862v0.862c0,0.476,0.386,0.862,0.862,0.862h1.293v1.723H3.106c-0.476,0-0.862,0.386-0.862,0.862v12.926c0,0.476,0.386,0.862,0.862,0.862h13.789c0.475,0,0.861-0.387,0.861-0.862V5.608C17.756,5.132,17.369,4.746,16.895,4.746 M10.862,2.161H10V1.299h0.862V2.161zM11.724,1.299h3.446v0.862h-3.446V1.299z M13.016,4.746h-0.861V3.023h0.861V4.746z M16.895,18.534H3.106v-2.585h13.789V18.534zM16.895,15.088H3.106v-9.48h13.789V15.088z M15.17,12.502h0.862v-0.861H15.17V12.502z M13.447,12.502h0.861v-0.861h-0.861V12.502zM15.17,10.778h0.862V9.917H15.17V10.778z M15.17,9.055h0.862V8.193H15.17V9.055z M16.032,6.47h-4.309v0.862h4.309V6.47zM14.309,8.193h-0.861v0.862h0.861V8.193z M12.585,8.193h-0.861v0.862h0.861V8.193z M13.447,14.226h2.585v-0.862h-2.585V14.226zM13.447,10.778h0.861V9.917h-0.861V10.778z M12.585,9.917h-0.861v0.861h0.861V9.917z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TODAYEARNINGS_liquition') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.PRODUCT_number_SOLD_liquition') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('save', 1)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M5.109,8.392H4.249c-0.238,0-0.43,0.193-0.43,0.431c0,0.238,0.192,0.431,0.43,0.431h0.861c0.238,0,0.43-0.193,0.43-0.431C5.54,8.585,5.347,8.392,5.109,8.392 M4.249,4.088h11.19c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H4.249c-0.238,0-0.43,0.192-0.43,0.431C3.818,3.896,4.011,4.088,4.249,4.088 M2.527,5.81H17.16c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H2.527c-0.238,0-0.43,0.192-0.43,0.431C2.097,5.617,2.289,5.81,2.527,5.81 M18.452,6.67H1.236c-0.476,0-0.861,0.385-0.861,0.861v8.608c0,0.475,0.385,0.86,0.861,0.86h17.216c0.475,0,0.86-0.386,0.86-0.86V7.531C19.312,7.056,18.927,6.67,18.452,6.67 M1.666,7.531c0.238,0,0.431,0.192,0.431,0.431c0,0.238-0.192,0.43-0.431,0.43c-0.238,0-0.43-0.192-0.43-0.43C1.236,7.724,1.428,7.531,1.666,7.531 M1.666,16.14c-0.238,0-0.43-0.192-0.43-0.431c0-0.237,0.192-0.431,0.43-0.431c0.238,0,0.431,0.193,0.431,0.431C2.097,15.947,1.904,16.14,1.666,16.14 M18.021,16.14c-0.238,0-0.431-0.192-0.431-0.431c0-0.237,0.192-0.431,0.431-0.431s0.431,0.193,0.431,0.431C18.452,15.947,18.26,16.14,18.021,16.14 M18.452,14.496c-0.136-0.048-0.279-0.078-0.431-0.078c-0.714,0-1.291,0.578-1.291,1.291c0,0.151,0.03,0.295,0.078,0.431H2.878c0.048-0.136,0.079-0.279,0.079-0.431c0-0.713-0.579-1.291-1.292-1.291c-0.151,0-0.295,0.03-0.43,0.078V9.174c0.135,0.048,0.279,0.079,0.43,0.079c0.713,0,1.292-0.578,1.292-1.291c0-0.152-0.031-0.295-0.079-0.431h13.93C16.761,7.667,16.73,7.81,16.73,7.962c0,0.713,0.577,1.291,1.291,1.291c0.151,0,0.295-0.031,0.431-0.079V14.496z M18.021,8.392c-0.238,0-0.431-0.192-0.431-0.43c0-0.238,0.192-0.431,0.431-0.431s0.431,0.192,0.431,0.431C18.452,8.2,18.26,8.392,18.021,8.392 M15.438,14.418h-0.86c-0.238,0-0.431,0.192-0.431,0.43c0,0.238,0.192,0.431,0.431,0.431h0.86c0.238,0,0.431-0.192,0.431-0.431C15.869,14.61,15.677,14.418,15.438,14.418 M9.844,8.392c-1.901,0-3.443,1.542-3.443,3.443s1.542,3.443,3.443,3.443s3.443-1.542,3.443-3.443S11.745,8.392,9.844,8.392 M11.233,13.271c-0.071,0.162-0.169,0.297-0.292,0.403c-0.124,0.108-0.268,0.189-0.434,0.246c-0.166,0.058-0.295,0.089-0.488,0.097v0.4H9.673v-0.4c-0.208-0.004-0.35-0.037-0.522-0.099c-0.174-0.063-0.322-0.151-0.445-0.267s-0.219-0.257-0.286-0.424c-0.067-0.168-0.099-0.361-0.095-0.579h0.659c-0.003,0.256,0.052,0.459,0.168,0.608c0.115,0.147,0.257,0.226,0.522,0.233v-1.417c-0.158-0.042-0.265-0.094-0.422-0.154c-0.156-0.061-0.297-0.139-0.422-0.234c-0.125-0.095-0.226-0.215-0.303-0.36c-0.077-0.144-0.115-0.323-0.115-0.538c0-0.187,0.035-0.352,0.106-0.494c0.072-0.143,0.168-0.261,0.289-0.357c0.121-0.096,0.261-0.168,0.419-0.22C9.383,9.665,9.5,9.64,9.673,9.64V9.256h0.348V9.64c0.173,0,0.287,0.023,0.441,0.07c0.154,0.047,0.288,0.117,0.401,0.211c0.114,0.093,0.204,0.212,0.272,0.356c0.067,0.145,0.101,0.312,0.101,0.503h-0.659c-0.008-0.199-0.059-0.351-0.153-0.457c-0.095-0.105-0.197-0.158-0.404-0.158V11.4c0.173,0.048,0.293,0.103,0.459,0.165c0.166,0.062,0.312,0.142,0.439,0.239c0.127,0.098,0.229,0.219,0.306,0.363c0.077,0.144,0.116,0.321,0.116,0.532C11.341,12.919,11.305,13.109,11.233,13.271M10.458,12.332c-0.067-0.051-0.143-0.092-0.228-0.123c-0.085-0.031-0.123-0.06-0.21-0.082v1.363c0.208-0.016,0.329-0.076,0.462-0.185c0.133-0.107,0.199-0.277,0.199-0.512c0-0.109-0.02-0.2-0.061-0.275C10.581,12.444,10.526,12.383,10.458,12.332 M9.069,10.74c0,0.094,0.019,0.174,0.058,0.241c0.039,0.066,0.087,0.122,0.148,0.169c0.06,0.047,0.128,0.085,0.208,0.114s0.109,0.054,0.19,0.073v-1.171c-0.208,0-0.32,0.044-0.434,0.132C9.126,10.386,9.069,10.533,9.069,10.74">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.PRODUCT_number_SOLD_liquition') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('save', 1)->whereDate('created_at', '>=', date('Y-m') . '-1')
                                            ->whereDate('created_at', '<=', date('Y-m-d '))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar is-gold"></span>
                <h3 class="dash-section__title">{{ __('home.liquitiondoday_confirm') }} /
                    {{ __('home.liquitiondoday_notconfirm') }}</h3>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 2)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 1)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm_month') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 2)->where('save', 1)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm_month') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 1)->where('save', 1)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar"></span>
                <h3 class="dash-section__title">الحسابات المالية</h3>
            </div>
            <div class="stats-grid">
                @php
                    $sbAvatarPalette = ['navy', 'gold', 'green', 'amber', 'teal'];
                @endphp
                @foreach (App\Models\financial_accounts::where('id', '!=', 1258)->where('parent_account_number', 4)->where('branchs_id', NULL)->orwhere('branchs_id', 9)->where('parent_account_number', 4)->where('id', '!=', 1258)->get() as $section)
                <a href="#" class="stat-card">
                    <div class="stat-card__icon stat-card__icon--avatar stat-card__icon--{{ $sbAvatarPalette[$section->id % count($sbAvatarPalette)] }}">
                        <span>{{ mb_substr(trim($section->name), 0, 1) }}</span>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ $section->name }}</span>
                        <span class="stat-card__value" data-account-id="{{ $section->id }}">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </div>
                </a>
                @endforeach
                @foreach (App\Models\financial_accounts::where('parent_account_number', 82)->where('id', '!=', 1258)->get() as $section)
                <a href="#" class="stat-card">
                    <div class="stat-card__icon stat-card__icon--avatar stat-card__icon--{{ $sbAvatarPalette[$section->id % count($sbAvatarPalette)] }}">
                        <span>{{ mb_substr(trim($section->name), 0, 1) }}</span>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ $section->name }}</span>
                        <span class="stat-card__value" data-account-id="{{ $section->id }}">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </section>

        <?php
                    $chartLabels = [];
                    $chartSalesCounts = [];
                    $chartLiquidationCounts = [];
                    $chartConfirmedCounts = [];
                    $chartUnconfirmedCounts = [];
                    $arabicWeekdaysShort = ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'];
                    for ($i = 6; $i >= 0; $i--) {
                        $day = date('Y-m-d', strtotime("-{$i} days"));
                        $chartLabels[] = $arabicWeekdaysShort[date('w', strtotime($day))];

                        $chartSalesCounts[] = App\Models\Transactions::whereDate('created_at', $day)->count();

                        $chartLiquidationCounts[] = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('save', 1)->whereDate('created_at', $day)->count();

                        $chartConfirmedCounts[] = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 2)->where('save', 1)->whereDate('created_at', $day)->count();

                        $chartUnconfirmedCounts[] = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('status', 1)->where('save', 1)->whereDate('created_at', $day)->count();
                    }

                    // العهد = نفس حسابات "العهدة" (عهدة فرع الرياض / الدمام / جدة ...) الظاهرة فوق في قسم
                    // "الحسابات المالية"، فلترتهم من نفس الحسابات دي بحيث يبقوا بس اللي اسمهم فيه "عهدة".
                    $custodyAccounts = App\Models\financial_accounts::where('id', '!=', 1258)->where('parent_account_number', 4)->where('branchs_id', NULL)->orwhere('branchs_id', 9)->where('parent_account_number', 4)->where('id', '!=', 1258)->get()
                        ->merge(App\Models\financial_accounts::where('parent_account_number', 82)->where('id', '!=', 1258)->get())
                        ->filter(function ($account) {
                            return mb_strpos($account->name, 'عهدة') !== false;
                        })
                        ->values();
                    $chartCustodyLabels = $custodyAccounts->pluck('name')->values();
                    $chartCustodyIds = $custodyAccounts->pluck('id')->values();
                ?>

        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar"></span>
                <h3 class="dash-section__title">إحصائيات آخر 7 أيام</h3>
            </div>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-card__title">التحويلات مقابل التصفيات</div>
                    <div class="chart-card__canvas-wrap"><canvas id="chartSalesVsLiquidation"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-card__title">تصفيات متراجع عليها مقابل غير متراجع عليها</div>
                    <div class="chart-card__canvas-wrap"><canvas id="chartConfirmedVsUnconfirmed"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-card__title">العهد</div>
                    <div class="chart-card__canvas-wrap"><canvas id="chartCustody"></canvas></div>
                </div>
            </div>
        </section>

    </div>
</div>
@endcan

@if(Auth()->user()->id != 11)
<div class="row row-sm parent-card" style="background-color: white;">
    <div class="dashboard-wrap" style="width:100%;">
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar"></span>
                <h3 class="dash-section__title">{{ __('home.salesdoday') }}</h3>
                <span class="dash-section__sub">فرعي</span>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.salesdoday') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Transactions::where('branchs_id', Auth()->user()->branchs_id)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M10.862,6.47H3.968v6.032h6.894V6.47z M10,11.641H4.83V7.332H10V11.641z M12.585,11.641h-0.861v0.861h0.861V11.641z M7.415,14.226h0.862v-0.862H7.415V14.226z M8.707,17.673h2.586c0.237,0,0.431-0.193,0.431-0.432c0-0.237-0.193-0.431-0.431-0.431H8.707c-0.237,0-0.431,0.193-0.431,0.431C8.276,17.479,8.47,17.673,8.707,17.673 M5.691,14.226h0.861v-0.862H5.691V14.226z M4.83,13.363H3.968v0.862H4.83V13.363z M16.895,4.746h-3.017V3.023h1.292c0.476,0,0.862-0.386,0.862-0.862V1.299c0-0.476-0.387-0.862-0.862-0.862H10c-0.476,0-0.862,0.386-0.862,0.862v0.862c0,0.476,0.386,0.862,0.862,0.862h1.293v1.723H3.106c-0.476,0-0.862,0.386-0.862,0.862v12.926c0,0.476,0.386,0.862,0.862,0.862h13.789c0.475,0,0.861-0.387,0.861-0.862V5.608C17.756,5.132,17.369,4.746,16.895,4.746 M10.862,2.161H10V1.299h0.862V2.161zM11.724,1.299h3.446v0.862h-3.446V1.299z M13.016,4.746h-0.861V3.023h0.861V4.746z M16.895,18.534H3.106v-2.585h13.789V18.534zM16.895,15.088H3.106v-9.48h13.789V15.088z M15.17,12.502h0.862v-0.861H15.17V12.502z M13.447,12.502h0.861v-0.861h-0.861V12.502zM15.17,10.778h0.862V9.917H15.17V10.778z M15.17,9.055h0.862V8.193H15.17V9.055z M16.032,6.47h-4.309v0.862h4.309V6.47zM14.309,8.193h-0.861v0.862h0.861V8.193z M12.585,8.193h-0.861v0.862h0.861V8.193z M13.447,14.226h2.585v-0.862h-2.585V14.226zM13.447,10.778h0.861V9.917h-0.861V10.778z M12.585,9.917h-0.861v0.861h0.861V9.917z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TODAYEARNINGS') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Transactions::where('branchs_id', Auth()->user()->branchs_id)->whereDate('created_at', date('Y-m-d'))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.PRODUCT_number_SOLD') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Transactions::where('branchs_id', Auth()->user()->branchs_id)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M5.109,8.392H4.249c-0.238,0-0.43,0.193-0.43,0.431c0,0.238,0.192,0.431,0.43,0.431h0.861c0.238,0,0.43-0.193,0.43-0.431C5.54,8.585,5.347,8.392,5.109,8.392 M4.249,4.088h11.19c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H4.249c-0.238,0-0.43,0.192-0.43,0.431C3.818,3.896,4.011,4.088,4.249,4.088 M2.527,5.81H17.16c0.238,0,0.431-0.192,0.431-0.43c0-0.238-0.192-0.431-0.431-0.431H2.527c-0.238,0-0.43,0.192-0.43,0.431C2.097,5.617,2.289,5.81,2.527,5.81 M18.452,6.67H1.236c-0.476,0-0.861,0.385-0.861,0.861v8.608c0,0.475,0.385,0.86,0.861,0.86h17.216c0.475,0,0.86-0.386,0.86-0.86V7.531C19.312,7.056,18.927,6.67,18.452,6.67 M1.666,7.531c0.238,0,0.431,0.192,0.431,0.431c0,0.238-0.192,0.43-0.431,0.43c-0.238,0-0.43-0.192-0.43-0.43C1.236,7.724,1.428,7.531,1.666,7.531 M1.666,16.14c-0.238,0-0.43-0.192-0.43-0.431c0-0.237,0.192-0.431,0.43-0.431c0.238,0,0.431,0.193,0.431,0.431C2.097,15.947,1.904,16.14,1.666,16.14 M18.021,16.14c-0.238,0-0.431-0.192-0.431-0.431c0-0.237,0.192-0.431,0.431-0.431s0.431,0.193,0.431,0.431C18.452,15.947,18.26,16.14,18.021,16.14 M18.452,14.496c-0.136-0.048-0.279-0.078-0.431-0.078c-0.714,0-1.291,0.578-1.291,1.291c0,0.151,0.03,0.295,0.078,0.431H2.878c0.048-0.136,0.079-0.279,0.079-0.431c0-0.713-0.579-1.291-1.292-1.291c-0.151,0-0.295,0.03-0.43,0.078V9.174c0.135,0.048,0.279,0.079,0.43,0.079c0.713,0,1.292-0.578,1.292-1.291c0-0.152-0.031-0.295-0.079-0.431h13.93C16.761,7.667,16.73,7.81,16.73,7.962c0,0.713,0.577,1.291,1.291,1.291c0.151,0,0.295-0.031,0.431-0.079V14.496z M18.021,8.392c-0.238,0-0.431-0.192-0.431-0.43c0-0.238,0.192-0.431,0.431-0.431s0.431,0.192,0.431,0.431C18.452,8.2,18.26,8.392,18.021,8.392 M15.438,14.418h-0.86c-0.238,0-0.431,0.192-0.431,0.43c0,0.238,0.192,0.431,0.431,0.431h0.86c0.238,0,0.431-0.192,0.431-0.431C15.869,14.61,15.677,14.418,15.438,14.418 M9.844,8.392c-1.901,0-3.443,1.542-3.443,3.443s1.542,3.443,3.443,3.443s3.443-1.542,3.443-3.443S11.745,8.392,9.844,8.392 M11.233,13.271c-0.071,0.162-0.169,0.297-0.292,0.403c-0.124,0.108-0.268,0.189-0.434,0.246c-0.166,0.058-0.295,0.089-0.488,0.097v0.4H9.673v-0.4c-0.208-0.004-0.35-0.037-0.522-0.099c-0.174-0.063-0.322-0.151-0.445-0.267s-0.219-0.257-0.286-0.424c-0.067-0.168-0.099-0.361-0.095-0.579h0.659c-0.003,0.256,0.052,0.459,0.168,0.608c0.115,0.147,0.257,0.226,0.522,0.233v-1.417c-0.158-0.042-0.265-0.094-0.422-0.154c-0.156-0.061-0.297-0.139-0.422-0.234c-0.125-0.095-0.226-0.215-0.303-0.36c-0.077-0.144-0.115-0.323-0.115-0.538c0-0.187,0.035-0.352,0.106-0.494c0.072-0.143,0.168-0.261,0.289-0.357c0.121-0.096,0.261-0.168,0.419-0.22C9.383,9.665,9.5,9.64,9.673,9.64V9.256h0.348V9.64c0.173,0,0.287,0.023,0.441,0.07c0.154,0.047,0.288,0.117,0.401,0.211c0.114,0.093,0.204,0.212,0.272,0.356c0.067,0.145,0.101,0.312,0.101,0.503h-0.659c-0.008-0.199-0.059-0.351-0.153-0.457c-0.095-0.105-0.197-0.158-0.404-0.158V11.4c0.173,0.048,0.293,0.103,0.459,0.165c0.166,0.062,0.312,0.142,0.439,0.239c0.127,0.098,0.229,0.219,0.306,0.363c0.077,0.144,0.116,0.321,0.116,0.532C11.341,12.919,11.305,13.109,11.233,13.271M10.458,12.332c-0.067-0.051-0.143-0.092-0.228-0.123c-0.085-0.031-0.123-0.06-0.21-0.082v1.363c0.208-0.016,0.329-0.076,0.462-0.185c0.133-0.107,0.199-0.277,0.199-0.512c0-0.109-0.02-0.2-0.061-0.275C10.581,12.444,10.526,12.383,10.458,12.332 M9.069,10.74c0,0.094,0.019,0.174,0.058,0.241c0.039,0.066,0.087,0.122,0.148,0.169c0.06,0.047,0.128,0.085,0.208,0.114s0.109,0.054,0.19,0.073v-1.171c-0.208,0-0.32,0.044-0.434,0.132C9.126,10.386,9.069,10.533,9.069,10.74">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TOTAL_EARNINGS_Month') }}</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Transactions::where('branchs_id', Auth()->user()->branchs_id)->whereDate('created_at', '>=', date('Y-m') . '-1')
                                            ->whereDate('created_at', '<=', date('Y-m-d '))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar is-gold"></span>
                <h3 class="dash-section__title">{{ __('home.TODAYEARNINGS_liquition') }}</h3>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TODAYEARNINGS_liquition') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('type', '!=', 3)->where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('type', '!=', 3)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 2)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm') }}
                            ({{__('home.SAR')}})</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('type', '!=', 3)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 2)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('type', '!=', 3)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 1)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm') }}
                            ({{__('home.SAR')}})</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('type', '!=', 3)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 1)->where('save', 1)->whereDate('created_at', date('Y-m-d'))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
        <section class="dash-section">
            <div class="dash-section__head">
                <span class="dash-section__bar is-gold"></span>
                <h3 class="dash-section__title">{{ __('home.TOTAL_EARNINGS_Month_liquition') }}</h3>
            </div>
            <div class="stats-grid">
                <a href="#" class="stat-card stat-card--gold">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.TOTAL_EARNINGS_Month_liquition') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('branchs_id', Auth()->user()->branchs_id)->where('save', 1)->where('type', '!=', 3)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm_month') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 2)->where('save', 1)->where('type', '!=', 3)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--green">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_confirm_month') }}
                            ({{__('home.SAR')}})</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 2)->where('save', 1)->where('type', '!=', 3)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M9.941,4.515h1.671v1.671c0,0.231,0.187,0.417,0.417,0.417s0.418-0.187,0.418-0.417V4.515h1.672c0.229,0,0.417-0.187,0.417-0.418c0-0.23-0.188-0.417-0.417-0.417h-1.672V2.009c0-0.23-0.188-0.418-0.418-0.418s-0.417,0.188-0.417,0.418V3.68H9.941c-0.231,0-0.418,0.187-0.418,0.417C9.522,4.329,9.71,4.515,9.941,4.515 M17.445,15.479h0.003l1.672-7.52l-0.009-0.002c0.009-0.032,0.021-0.064,0.021-0.099c0-0.231-0.188-0.417-0.418-0.417H5.319L4.727,5.231L4.721,5.232C4.669,5.061,4.516,4.933,4.327,4.933H1.167c-0.23,0-0.418,0.188-0.418,0.417c0,0.231,0.188,0.418,0.418,0.418h2.839l2.609,9.729h0c0.036,0.118,0.122,0.214,0.233,0.263c-0.156,0.254-0.25,0.551-0.25,0.871c0,0.923,0.748,1.671,1.67,1.671c0.923,0,1.672-0.748,1.672-1.671c0-0.307-0.088-0.589-0.231-0.836h4.641c-0.144,0.247-0.231,0.529-0.231,0.836c0,0.923,0.747,1.671,1.671,1.671c0.922,0,1.671-0.748,1.671-1.671c0-0.32-0.095-0.617-0.252-0.871C17.327,15.709,17.414,15.604,17.445,15.479 M15.745,8.275h2.448l-0.371,1.672h-2.262L15.745,8.275z M5.543,8.275h2.77L8.5,9.947H5.992L5.543,8.275z M6.664,12.453l-0.448-1.671h2.375l0.187,1.671H6.664z M6.888,13.289h1.982l0.186,1.671h-1.72L6.888,13.289zM8.269,17.466c-0.461,0-0.835-0.374-0.835-0.835s0.374-0.836,0.835-0.836c0.462,0,0.836,0.375,0.836,0.836S8.731,17.466,8.269,17.466 M11.612,14.96H9.896l-0.186-1.671h1.901V14.96z M11.612,12.453H9.619l-0.186-1.671h2.18V12.453zM11.612,9.947H9.34L9.154,8.275h2.458V9.947z M14.162,14.96h-1.715v-1.671h1.9L14.162,14.96z M14.441,12.453h-1.994v-1.671h2.18L14.441,12.453z M14.72,9.947h-2.272V8.275h2.458L14.72,9.947z M15.79,17.466c-0.462,0-0.836-0.374-0.836-0.835s0.374-0.836,0.836-0.836c0.461,0,0.835,0.375,0.835,0.836S16.251,17.466,15.79,17.466 M16.708,14.96h-1.705l0.186-1.671h1.891L16.708,14.96z M15.281,12.453l0.187-1.671h2.169l-0.372,1.671H15.281z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm_month') }}</span>
                        <span class="stat-card__value">
                            {{ App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 1)->where('save', 1)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->count() }}
                            {{__('home.invoice')}}
                        </span>
                    </div>
                </a>
                <a href="#" class="stat-card stat-card--amber">
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 20 20">
                            <path fill="none"
                                d="M16.588,3.411h-4.466c0.042-0.116,0.074-0.236,0.074-0.366c0-0.606-0.492-1.098-1.099-1.098H8.901c-0.607,0-1.098,0.492-1.098,1.098c0,0.13,0.033,0.25,0.074,0.366H3.41c-0.606,0-1.098,0.492-1.098,1.098c0,0.607,0.492,1.098,1.098,1.098h0.366V16.59c0,0.808,0.655,1.464,1.464,1.464h9.517c0.809,0,1.466-0.656,1.466-1.464V5.607h0.364c0.607,0,1.1-0.491,1.1-1.098C17.688,3.903,17.195,3.411,16.588,3.411z M8.901,2.679h2.196c0.202,0,0.366,0.164,0.366,0.366S11.3,3.411,11.098,3.411H8.901c-0.203,0-0.366-0.164-0.366-0.366S8.699,2.679,8.901,2.679z M15.491,16.59c0,0.405-0.329,0.731-0.733,0.731H5.241c-0.404,0-0.732-0.326-0.732-0.731V5.607h10.983V16.59z M16.588,4.875H3.41c-0.203,0-0.366-0.164-0.366-0.366S3.208,4.143,3.41,4.143h13.178c0.202,0,0.367,0.164,0.367,0.366S16.79,4.875,16.588,4.875zM6.705,14.027h6.589c0.202,0,0.366-0.164,0.366-0.366s-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.165-0.366,0.367S6.502,14.027,6.705,14.027z M6.705,11.83h6.589c0.202,0,0.366-0.164,0.366-0.365c0-0.203-0.164-0.367-0.366-0.367H6.705c-0.203,0-0.366,0.164-0.366,0.367C6.339,11.666,6.502,11.83,6.705,11.83z M6.705,9.634h6.589c0.202,0,0.366-0.164,0.366-0.366c0-0.202-0.164-0.366-0.366-0.366H6.705c-0.203,0-0.366,0.164-0.366,0.366C6.339,9.47,6.502,9.634,6.705,9.634z">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">{{ __('home.liquitiondoday_notconfirm_month') }}
                            ({{__('home.SAR')}})</span>
                        <span class="stat-card__value">
                            <?php
                                        $cashamount = 0;
                                        $invoices = App\Models\Covenant_liquidation::where('type', '!=', 3)->where('type', '!=', 7)->where('type', '!=', 8)->where('type', '!=', 9)->where('type', '!=', 10)->where('branchs_id', Auth()->user()->branchs_id)->where('status', 1)->where('save', 1)->where('type', '!=', 3)->whereDate('created_at', '>=', date('Y-m') . '-1')->whereDate('created_at', '<=', date('Y-m-d '))->get();
                                        foreach ($invoices as $product) {
                                            $cashamount += $product->price_filtering;
                                        }
                                    ?>
                            {{ round($cashamount, 2)}} {{__('home.SAR')}}
                        </span>
                    </div>
                </a>
            </div>
        </section>
    </div>
</div>
@endif

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    function startDashboardClock() {
        var timeValueEl = document.querySelector('.dash-clock__time-value');
        var periodEl = document.querySelector('.dash-clock__period');
        var dateEl = document.querySelector('.dash-clock__date-value');
        if (!timeValueEl || !dateEl) return;

        var weekdays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        var months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر',
            'ديسمبر'
        ];

        function pad(n) {
            return n < 10 ? '0' + n : n;
        }

        function render() {
            var now = new Date();
            var hours24 = now.getHours();
            var period = hours24 >= 12 ? 'م' : 'ص';
            var hours12 = hours24 % 12;
            if (hours12 === 0) hours12 = 12;

            timeValueEl.textContent = pad(hours12) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
            if (periodEl) periodEl.textContent = period;
            dateEl.textContent = weekdays[now.getDay()] + '، ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now
                .getFullYear();
        }

        render();
        setInterval(render, 1000);
    }

    // تحريك الأرقام في الكروت بدل ما تظهر فجأة - لمسة احترافية بسيطة بدون
    // ما نلمس أي منطق PHP؛ بيشتغل على أي قيمة نصها بيبدأ برقم زي "12 فاتورة" أو "1,250.75 ريال".
    function animateStatValues(scope) {
        var root = scope || document;
        root.querySelectorAll('.stat-card__value').forEach(function(el) {
            if (el.dataset.animated === '1' || el.hasAttribute('data-account-id')) return;
            var text = el.textContent.trim();
            var match = text.match(/^-?\d+(\.\d+)?/);
            if (!match) return;

            el.dataset.animated = '1';
            var endValue = parseFloat(match[0]);
            var isDecimal = match[0].indexOf('.') !== -1;
            var suffix = text.slice(match[0].length);
            var startTime = null;
            var duration = 700;

            function step(ts) {
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var current = endValue * eased;
                el.textContent = (isDecimal ? current.toFixed(2) : Math.round(current)) + suffix;
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = match[0] + suffix;
                }
            }
            requestAnimationFrame(step);
        });
    }

    // الصفحة بتتحمّل أحيانًا بالـ pjax من غير reload كامل، فلو initDashboardCharts()
    // اتنادت أكتر من مرة على نفس الـ canvas، Chart.js بيرفض ويطلع خطأ "canvas is already
    // in use" ويسيب الرسم فاضي بالكامل. دايمًا بنتأكد إننا بنشيل أي رسم قديم قبل ما نعمل واحد جديد.
    function destroySbChart(canvasEl) {
        if (!canvasEl || typeof Chart === 'undefined' || typeof Chart.getChart !== 'function') return;
        var existing = Chart.getChart(canvasEl);
        if (existing) existing.destroy();
    }

    function showChartEmptyState(wrapEl, message) {
        if (!wrapEl) return;
        var note = document.createElement('div');
        note.className = 'chart-card__empty';
        note.textContent = message;
        wrapEl.appendChild(note);
    }

    function initDashboardCharts() {
        if (typeof Chart === 'undefined') return;

        var chartLabels = @json($chartLabels ?? []);
        var navy = '#131C33';
        var gold = '#B4893B';
        var green = '#17875A';
        var amber = '#B4711A';
        var teal = '#147C93';

        Chart.defaults.font.family = 'Cairo, Inter, sans-serif';

        var elSales = document.getElementById('chartSalesVsLiquidation');
        if (elSales) {
            destroySbChart(elSales);
            new Chart(elSales, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                            label: 'التحويلات',
                            data: @json($chartSalesCounts ?? []),
                            backgroundColor: navy,
                            borderRadius: 6,
                            maxBarThickness: 28
                        },
                        {
                            label: 'التصفيات',
                            data: @json($chartLiquidationCounts ?? []),
                            backgroundColor: gold,
                            borderRadius: 6,
                            maxBarThickness: 28
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    family: 'Cairo'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(23,30,46,.06)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        var elConfirm = document.getElementById('chartConfirmedVsUnconfirmed');
        if (elConfirm) {
            destroySbChart(elConfirm);
            new Chart(elConfirm, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                            label: 'متراجع عليها',
                            data: @json($chartConfirmedCounts ?? []),
                            borderColor: green,
                            backgroundColor: 'rgba(23,135,90,.15)',
                            pointBackgroundColor: green,
                            pointRadius: 3,
                            tension: .35,
                            fill: true
                        },
                        {
                            label: 'غير متراجع عليها',
                            data: @json($chartUnconfirmedCounts ?? []),
                            borderColor: amber,
                            backgroundColor: 'rgba(180,113,26,.15)',
                            pointBackgroundColor: amber,
                            pointRadius: 3,
                            tension: .35,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    family: 'Cairo'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(23,30,46,.06)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        var elCustody = document.getElementById('chartCustody');
        if (elCustody) {
            var custodyLabels = @json($chartCustodyLabels ?? []);
            var custodyIds = @json($chartCustodyIds ?? []);
            var custodyWrap = elCustody.closest('.chart-card__canvas-wrap');

            if (!custodyIds.length) {
                showChartEmptyState(custodyWrap, 'لا توجد حسابات عهدة لعرضها');
                return;
            }

            Promise.all(custodyIds.map(function(id) {
                return fetch(`/account-balance/${id}`)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        return Math.round(((data.debit || 0) - (data.credit || 0)) * 100) / 100;
                    })
                    .catch(function() {
                        return 0;
                    });
            })).then(function(custodyBalances) {
                destroySbChart(elCustody);
                new Chart(elCustody, {
                    type: 'bar',
                    data: {
                        labels: custodyLabels,
                        datasets: [{
                            label: 'العهد',
                            data: custodyBalances,
                            backgroundColor: teal,
                            borderRadius: 6,
                            maxBarThickness: 22
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(23,30,46,.06)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }).catch(function() {
                showChartEmptyState(custodyWrap, 'تعذّر تحميل بيانات العهد الآن');
            });
        }
    }

    function initDashboardWidgets() {
        // onload="" on a <div> never fires in any browser, so the old clock never started.
        // Use initClock() if the layout defines it, and always fall back to our own
        // self-contained clock either way.
        if (typeof initClock === 'function') {
            initClock();
        }
        startDashboardClock();
        initDashboardCharts();
        animateStatValues();

        document.querySelectorAll('[data-account-id]').forEach(function(el) {
            const accountId = el.dataset.accountId;
            fetch(`/account-balance/${accountId}`)
                .then(response => response.json())
                .then(data => {
                    var value = Math.round((data.debit - data.credit) * 100) / 100;
                    el.textContent = value;
                    el.style.opacity = '0';
                    el.style.transition = 'opacity .35s ease';
                    requestAnimationFrame(function() {
                        el.style.opacity = '1';
                    });
                })
                .catch(error => {
                    el.textContent = '-';
                    console.error(error);
                });
        });
    }

    // القالب بيحمّل صفحات المحتوى أحيانًا بالـ AJAX (pjax) لما تدوس على روابط السايد بار،
    // وساعتها حدث DOMContentLoaded يبقى خلاص حصل من زمان على أول تحميل للصفحة ومش هيتكرر تاني،
    // فلو اعتمدنا عليه بس الساعة والـ charts وأرصدة الحسابات هتفضل واقفة. عشان كده بنشغّل الكود
    // فورًا لو الصفحة خلصت تحميل أصلاً، ولو لسه بتتحمل بننتظر DOMContentLoaded زي المعتاد.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardWidgets);
    } else {
        initDashboardWidgets();
    }
</script>
@endsection