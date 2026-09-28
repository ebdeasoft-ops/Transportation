@php
    $sys = $company['sys'] ?? null;
    $set = $company['set'] ?? null;
@endphp
<div class="wb-footer">
    @if(!empty($sys->address_ar))<div>{{ $sys->address_ar }}</div>@endif
    @if(!empty($sys->address_en))<div dir="ltr">{{ $sys->address_en }}</div>@endif
    <div dir="ltr">info@ahdlogistc.com</div>
</div>
