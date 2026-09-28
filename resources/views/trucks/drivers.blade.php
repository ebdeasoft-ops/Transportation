@extends('layouts.master')
@section('css')
@include('trucks._call_style')
<style>
.dv-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:14px; }
.dv-card{ background:#fff; border:1px solid #E6EBF2; border-radius:16px; padding:16px; box-shadow:0 10px 26px -18px rgba(15,23,42,.25); transition:transform .2s; animation:dvUp .4s cubic-bezier(.2,.7,.2,1) both; animation-delay:calc(var(--i,0) * 35ms); }
.dv-card:hover{ transform:translateY(-3px); }
@keyframes dvUp{ from{ opacity:0; transform:translateY(10px); } to{ opacity:1; transform:none; } }
.dv-top{ display:flex; align-items:center; gap:12px; }
.dv-av{ width:46px; height:46px; flex:none; border-radius:14px; background:linear-gradient(135deg,#2F6FED,#1D4ED8); color:#fff; font-weight:800; font-size:19px; display:flex; align-items:center; justify-content:center; }
.dv-name{ font-weight:800; font-size:15px; color:#0F172A; }
.dv-sub{ font-size:12px; color:#94A3B8; font-weight:600; }
.dv-state{ margin-inline-start:auto; font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:999px; white-space:nowrap; }
.dv-state.free{ background:#D1FAE5; color:#047857; } .dv-state.busy{ background:#FEF3C7; color:#B45309; }
.dv-phone{ display:flex; align-items:center; gap:8px; margin-top:12px; background:#F8FAFD; border:1px solid #E6EBF2; border-radius:12px; padding:8px 10px; }
.dv-phone a.num{ flex:1; font-size:17px; font-weight:800; color:#2F6FED !important; letter-spacing:.5px; }
.dv-phone .none{ flex:1; color:#EF4444; font-weight:700; font-size:12.5px; }
.dv-meta{ margin-top:10px; font-size:12px; color:#475569; display:grid; grid-template-columns:1fr 1fr; gap:4px 10px; }
.dv-meta b{ color:#0F172A; }
.dv-trip{ margin-top:10px; font-size:12px; font-weight:700; color:#B45309; background:#FFFBEB; border-radius:10px; padding:6px 10px; }
.dv-foot{ margin-top:12px; display:flex; justify-content:flex-end; }
</style>
@endsection
@section('title')
السائقين
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">السائقين</h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ url('trucks/board') }}" class="btn btn-secondary btn-sm"><i class="bx bxs-truck"></i> لوحة الشاحنات</a>
        <button class="btn btn-success btn-sm" data-driver-modal><i class="bx bx-user-plus"></i> إضافة سائق</button>
    </div>
</div>
@endsection
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if (session('trip_ok'))
        <div class="alert alert-success">{{ session('trip_ok') }}</div>
    @endif

    <div class="card"><div class="card-body" style="padding:14px 16px !important">
        <form method="get" action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/trucks/drivers') }}" class="d-flex" style="gap:8px">
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="ابحث بالاسم أو رقم الجوال أو الهوية...">
            <button class="btn btn-primary"><i class="bx bx-search"></i> بحث</button>
        </form>
    </div></div>

    <div class="dv-grid">
        @forelse ($drivers as $k => $d)
            @php
                $trip = $onTrip[$d->id] ?? null;
                $tel = $d->phone ? 'tel:' . preg_replace('/[^\d+]/', '', $d->phone) : null;
                $wa  = $d->phone ? 'https://wa.me/' . \App\Models\waybill_driver::intlPhone($d->phone) : null;
            @endphp
            <div class="dv-card" style="--i:{{ min($k, 20) }}">
                <div class="dv-top">
                    <span class="dv-av">{{ mb_substr(trim($d->name), 0, 1) }}</span>
                    <div style="min-width:0">
                        <div class="dv-name">{{ $d->name }}</div>
                        <div class="dv-sub">{{ $d->nationality ?: 'سائق' }}</div>
                    </div>
                    <span class="dv-state {{ $trip ? 'busy' : 'free' }}">{{ $trip ? 'في رحلة' : 'متاح' }}</span>
                </div>
                <div class="dv-phone">
                    @if ($d->phone)
                        <a class="num" href="{{ $tel }}" dir="ltr">{{ $d->phone }}</a>
                        <a class="drv-btn call" href="{{ $tel }}" title="اتصال"><i class="bx bxs-phone"></i></a>
                        <a class="drv-btn wa" href="{{ $wa }}" target="_blank" rel="noopener" title="واتساب"><i class="bx bxl-whatsapp"></i></a>
                    @else
                        <span class="none"><i class="bx bx-error-circle"></i> مفيش رقم جوال - دوس تعديل وضيفه</span>
                    @endif
                </div>
                <div class="dv-meta">
                    <span>الهوية: <b>{{ $d->id_number ?: '—' }}</b></span>
                    <span>الرخصة: <b>{{ $d->license_number ?: '—' }}</b></span>
                </div>
                @if ($trip)
                    <div class="dv-trip"><i class="bx bx-package"></i> {{ optional($trip->truck)->plate_number }} : {{ $trip->from_region }} ← {{ $trip->to_region }}</div>
                @endif
                <div class="dv-foot">
                    <button class="btn btn-secondary btn-sm" data-driver-modal data-driver="{{ json_encode($d->only(['id', 'name', 'phone', 'id_number', 'nationality', 'license_number', 'license_issue_date', 'notes']), JSON_UNESCAPED_UNICODE) }}"><i class="bx bx-edit"></i> تعديل</button>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column:1/-1"><div class="card-body" style="text-align:center;color:#94A3B8;padding:40px !important">
                <i class="bx bx-id-card" style="font-size:40px"></i><br>مفيش سائقين لسه. دوس «إضافة سائق».
            </div></div>
        @endforelse
    </div>

    @include('trucks._driver_modal')
@endsection
