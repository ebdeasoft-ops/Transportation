{{-- أزرار الاتصال بالسائق: $drv (waybill_driver) أو $name + $phone --}}
@php
    $cName  = isset($drv) && $drv ? $drv->name : ($name ?? null);
    $cPhone = isset($drv) && $drv ? $drv->phone : ($phone ?? null);
    $cTel   = $cPhone ? 'tel:' . preg_replace('/[^\d+]/', '', $cPhone) : null;
    $cWa    = $cPhone ? 'https://wa.me/' . \App\Models\waybill_driver::intlPhone($cPhone) : null;
@endphp
<div class="drv-contact">
    <div class="drv-contact__who">
        <i class="bx bx-user"></i>
        <span class="drv-contact__name">{{ $cName ?: 'بدون سائق' }}</span>
        @if ($cPhone)<a class="drv-contact__num" href="{{ $cTel }}" dir="ltr">{{ $cPhone }}</a>@endif
    </div>
    @if ($cPhone)
        <div class="drv-contact__btns">
            <a class="drv-btn call" href="{{ $cTel }}" title="اتصال"><i class="bx bxs-phone"></i></a>
            <a class="drv-btn wa" href="{{ $cWa }}" target="_blank" rel="noopener" title="واتساب"><i class="bx bxl-whatsapp"></i></a>
        </div>
    @endif
</div>
