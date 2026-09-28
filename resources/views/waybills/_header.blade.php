{{-- هيدر البوليصة - البيانات من الإعدادات (system_setting / settings) --}}
@php
    $sys = $company['sys'] ?? null;
    $set = $company['set'] ?? null;
    $wbMobile = '0551559610'; // رقم الجوال في البوليصة
@endphp
<div class="wb-head">
    {{-- يمين: التاريخ --}}
    <div class="wb-head-side wb-head-right">
        <div class="wb-date-line">
            <span class="wb-lbl">التاريخ</span>
            <span class="wb-date-val" id="{{ $hijriId ?? 'hijri_view' }}">{{ $hijri ?? '' }}</span>
            <span class="wb-unit">هـ</span>
        </div>
        <div class="wb-date-line">
            <span class="wb-lbl">الموافق</span>
            @if (!empty($dateInputHtml))
                <span class="wb-date-val wb-date-input">{!! $dateInputHtml !!}</span>
            @else
                <span class="wb-date-val" id="{{ $gregId ?? 'greg_view' }}">{{ $greg ?? '' }}</span>
            @endif
            <span class="wb-unit">م</span>
        </div>
        @if($wbMobile)
            <div class="wb-mob">جوال : <span dir="ltr">{{ $wbMobile }}</span></div>
        @endif
    </div>

    {{-- الوسط: اللوجو واسم المؤسسة --}}
    <div class="wb-head-center">
        @if(!empty($sys->logo))
            <img src="{{ asset('assets/img/brand/' . $sys->logo) }}" class="wb-logo" alt="logo">
        @endif
        <div class="wb-name-ar">{{ $sys->name_ar ?? '' }}</div>
        <div class="wb-name-en" dir="ltr">{{ $sys->name_en ?? '' }}</div>
        @if(!empty($sys->SR))
            <div class="wb-cr"><span class="wb-rule"></span> س.ت {{ $sys->SR }} <span class="wb-rule"></span></div>
        @endif
    </div>

    {{-- يسار: رقم البوليصة --}}
    <div class="wb-head-side wb-head-left">
        <div class="wb-no-box" dir="ltr">NO. <span class="wb-no">{!! $noHtml ?? '' !!}</span></div>
        @if($wbMobile)
            <div class="wb-mob" dir="ltr">Mob.: {{ $wbMobile }}</div>
        @endif
    </div>
</div>
<div class="wb-title-wrap"><span class="wb-title">بوليصة شحن</span></div>
