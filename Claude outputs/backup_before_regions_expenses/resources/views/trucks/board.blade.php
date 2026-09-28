@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@include('trucks._call_style')
<style>
.tb{ --n:#0B1A33; --b:#2F6FED; --g:#10B981; --a:#F59E0B; --r:#EF4444; --ink:#0F172A; --ink2:#475569; --ink3:#94A3B8; --line:#E6EBF2; }
.tb *{ box-sizing:border-box; }
@keyframes tbUp{ from{ opacity:0; transform:translateY(12px); } to{ opacity:1; transform:none; } }
@keyframes tbPulse{ 0%{ box-shadow:0 0 0 0 rgba(239,68,68,.5); } 70%{ box-shadow:0 0 0 8px rgba(239,68,68,0); } 100%{ box-shadow:0 0 0 0 rgba(239,68,68,0); } }
.tb-anim{ animation:tbUp .45s cubic-bezier(.2,.7,.2,1) both; animation-delay:calc(var(--i,0) * 40ms); }

/* KPIs */
.tb-kpis{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:16px; }
.tb-kpi{ background:#fff; border:1px solid var(--line); border-radius:14px; padding:14px 16px; display:flex; align-items:center; gap:12px; box-shadow:0 8px 24px -16px rgba(15,23,42,.2); cursor:pointer; transition:.2s; }
.tb-kpi:hover{ transform:translateY(-2px); }
.tb-kpi.on{ outline:2px solid var(--c); }
.tb-kpi i{ width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; color:var(--c); background:color-mix(in srgb,var(--c) 12%,#fff); }
.tb-kpi b{ display:block; font-size:24px; font-weight:800; line-height:1.1; }
.tb-kpi span{ font-size:12.5px; color:var(--ink2); font-weight:700; }

/* regions */
.tb-regions{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }
.tb-reg{ border:1px solid var(--line); background:#fff; border-radius:999px; padding:6px 12px; font-size:12.5px; font-weight:700; color:var(--ink2); cursor:pointer; transition:.15s; display:inline-flex; align-items:center; gap:6px; }
.tb-reg b{ background:#ECFDF5; color:#047857; border-radius:999px; padding:1px 8px; font-size:12px; }
.tb-reg.zero b{ background:#F1F5F9; color:var(--ink3); }
.tb-reg:hover, .tb-reg.on{ border-color:var(--b); color:var(--b); background:#EAF1FF; }

/* toolbar */
.tb-bar{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px; }
.tb-bar input{ flex:1; min-width:200px; }

/* cards */
.tb-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px; }
.tb-card{ background:#fff; border:1px solid var(--line); border-radius:16px; padding:16px; position:relative; overflow:hidden; box-shadow:0 10px 26px -18px rgba(15,23,42,.25); transition:transform .2s, box-shadow .2s; }
.tb-card:hover{ transform:translateY(-3px); box-shadow:0 20px 40px -22px rgba(15,23,42,.35); }
.tb-card::before{ content:""; position:absolute; inset-inline-start:0; top:0; bottom:0; width:4px; background:var(--c); }
.tb-card.empty{ --c:var(--g); } .tb-card.loaded{ --c:var(--a); } .tb-card.late{ --c:var(--r); }
.tb-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
.tb-plate{ font-size:17px; font-weight:800; color:var(--ink); direction:ltr; text-align:start; }
.tb-type{ font-size:12px; color:var(--ink3); font-weight:600; }
.tb-status{ font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:999px; white-space:nowrap; display:inline-flex; align-items:center; gap:4px; }
.tb-card.empty .tb-status{ background:#D1FAE5; color:#047857; }
.tb-card.loaded .tb-status{ background:#FEF3C7; color:#B45309; }
.tb-card.late .tb-status{ background:#FEE2E2; color:#B91C1C; animation:tbPulse 2s infinite; }
.tb-loc{ margin-top:12px; display:flex; align-items:center; gap:8px; font-weight:700; color:var(--ink2); font-size:13.5px; }
.tb-loc i{ color:var(--g); font-size:18px; }
.tb-route{ margin-top:12px; display:flex; align-items:center; gap:8px; background:#F8FAFD; border:1px solid var(--line); border-radius:12px; padding:10px; }
.tb-route .pt{ flex:1; min-width:0; text-align:center; }
.tb-route .pt b{ display:block; font-size:13.5px; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tb-route .pt small{ font-size:11px; color:var(--ink3); }
.tb-route .ar{ flex:none; color:var(--a); font-size:20px; }
.tb-meta{ display:grid; grid-template-columns:1fr 1fr; gap:6px 12px; margin-top:10px; font-size:12px; color:var(--ink2); }
.tb-meta div{ display:flex; gap:5px; align-items:center; min-width:0; }
.tb-meta i{ color:var(--ink3); font-size:14px; flex:none; }
.tb-meta span{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tb-eta{ margin-top:8px; font-size:12px; font-weight:800; }
.tb-card.loaded .tb-eta{ color:#B45309; } .tb-card.late .tb-eta{ color:#B91C1C; }
.tb-actions{ display:flex; gap:8px; margin-top:14px; }
.tb-actions .btn{ flex:1; }
.tb-empty-state{ text-align:center; color:var(--ink3); padding:40px 0; grid-column:1/-1; }
.tb-hidden{ display:none !important; }
.tb-docs{ display:flex; flex-wrap:wrap; gap:5px; margin-top:6px; }
.tb-doc{ font-size:11px; font-weight:800; padding:2px 8px; border-radius:999px; display:inline-flex; align-items:center; gap:3px; }
.tb-doc.expired{ background:#FEE2E2; color:#B91C1C; } .tb-doc.soon{ background:#FEF3C7; color:#B45309; }
.tb-edit-link{ color:#94A3B8 !important; font-size:17px; margin-inline-start:6px; vertical-align:middle; }
.tb-edit-link:hover{ color:#2F6FED !important; }
.tb-alert{ display:flex; align-items:center; gap:10px; background:#FFF7ED; border:1px solid #FED7AA; color:#9A3412; border-radius:12px; padding:10px 14px; margin-bottom:14px; font-weight:700; cursor:pointer; }
.tb-alert i{ font-size:20px; }
.tb-alert.on{ outline:2px solid #F59E0B; }
.tb-own{ display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:800; padding:2px 8px; border-radius:999px; margin-top:4px; }
.tb-own.own{ background:#DBEAFE; color:#1D4ED8; } .tb-own.external{ background:#EDE9FE; color:#6D28D9; } .tb-own.none{ background:#F1F5F9; color:#94A3B8; }
.tb-own-pick{ display:flex; gap:10px; }
.tb-own-pick label{ flex:1; margin:0 !important; cursor:pointer; }
.tb-own-pick input{ display:none; }
.tb-own-pick span{ display:flex; align-items:center; justify-content:center; gap:6px; border:1.5px solid #E6EBF2; border-radius:10px; padding:10px; font-weight:800; color:#475569; transition:.15s; }
.tb-own-pick input:checked + span{ border-color:#2F6FED; background:#EAF1FF; color:#2F6FED; }

@media (max-width:991px){ .tb-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (max-width:575px){ .tb-kpis{ grid-template-columns:1fr 1fr; } .tb-meta{ grid-template-columns:1fr; } }
/* شريط «إضافة شحنة جديدة» */
.tb-newbar{ display:flex; align-items:center; gap:14px; background:linear-gradient(135deg,#059669,#10B981); color:#fff; border-radius:14px; padding:14px 18px; margin-bottom:16px; box-shadow:0 10px 25px -12px rgba(16,185,129,.8); }
.tb-newbar > i{ font-size:30px; }
.tb-newbar b{ display:block; font-size:16px; } .tb-newbar span{ font-size:13px; opacity:.95; }
.tb-newbar .btn{ margin-inline-start:auto; white-space:nowrap; }
.tb-card.empty .js-load{ font-weight:800; }
/* اختيار الشاحنة لشحنة جديدة */
.pk-search{ margin-bottom:10px; }
.pk-list{ max-height:55vh; overflow:auto; display:flex; flex-direction:column; gap:8px; }
.pk-item{ display:flex; align-items:center; gap:12px; width:100%; text-align:start; background:#fff; border:1.5px solid #E6EBF2; border-radius:12px; padding:10px 14px; cursor:pointer; transition:.15s; }
.pk-item:hover{ border-color:#10B981; background:#F0FDF9; }
.pk-item i.bxs-truck{ font-size:24px; color:#10B981; }
.pk-item b{ direction:ltr; font-size:15px; }
.pk-item small{ color:#64748B; display:block; }
.pk-item .pk-go{ margin-inline-start:auto; color:#10B981; font-weight:800; font-size:12.5px; white-space:nowrap; }
.pk-empty{ text-align:center; color:#94A3B8; padding:24px 0; }
</style>
@endsection
@section('title')
لوحة الشاحنات
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">لوحة الشاحنات (الأحمال)</h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ url('trucks/report') }}" class="btn btn-secondary btn-sm"><i class="bx bx-bar-chart-alt-2"></i> تقرير الأحمال</a>
        <a href="{{ url('trucks/drivers') }}" class="btn btn-secondary btn-sm"><i class="bx bx-id-card"></i> السائقين</a>
        <button class="btn btn-success btn-sm" data-driver-modal><i class="bx bx-user-plus"></i> إضافة سائق</button>
        <button class="btn btn-info btn-sm" data-customer-modal><i class="bx bx-user-plus"></i> إضافة عميل</button>
        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#pickTruckModal"><i class="bx bx-package"></i> شحنة جديدة</button>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTruckModal"><i class="bx bx-plus"></i> إضافة شاحنة</button>
    </div>
</div>
@endsection
@section('content')
@php
    $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $fmt = function ($d) { return $d ? $d->format('Y/m/d h:i A') : '—'; };
    $emptyCount = $availableCount ?? ($trucks->count() - $loaded);
@endphp
<div class="tb">
    @if (count($errors) > 0)
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if (session('trip_ok'))
        <div class="alert alert-success">{{ session('trip_ok') }}</div>
    @endif
    @if (request('new'))
        <div class="tb-newbar"><i class="bx bx-package"></i><div><b>إضافة شحنة جديدة</b><span>اختار الشاحنة اللي هتحمّلها ودوس «تحميل». الظاهر دلوقتي الشاحنات المتاحة (الفاضية ملك المؤسسة) بس.</span></div>
            <a href="{{ url('trucks/board') }}" class="btn btn-sm btn-light">عرض كل الشاحنات</a></div>
    @endif

    {{-- KPIs (بتفلتر لما تدوس عليها) --}}
    <div class="tb-kpis">
        <div class="tb-kpi tb-anim on" style="--c:#2F6FED;--i:0" data-filter="all"><i class="bx bxs-truck"></i><div><b>{{ $trucks->count() }}</b><span>كل الشاحنات</span></div></div>
        <div class="tb-kpi tb-anim" style="--c:#10B981;--i:1" data-filter="empty"><i class="bx bx-check-circle"></i><div><b>{{ $emptyCount }}</b><span>متاحة (ملك المؤسسة)</span></div></div>
        <div class="tb-kpi tb-anim" style="--c:#F59E0B;--i:2" data-filter="loaded"><i class="bx bx-package"></i><div><b>{{ $loaded }}</b><span>محمّلة</span></div></div>
        <div class="tb-kpi tb-anim" style="--c:#EF4444;--i:3" data-filter="late"><i class="bx bx-time-five"></i><div><b>{{ $overdue }}</b><span>متأخرة عن التنزيل</span></div></div>
    </div>

    @if (($docsAlert ?? 0) > 0)
        <div class="tb-alert" id="tbDocsAlert"><i class="bx bx-error"></i> فيه {{ $docsAlert }} شاحنة التأمين أو الاستمارة بتاعتها منتهية أو قربت تنتهي (خلال 30 يوم) - دوس هنا لعرضها</div>
    @endif

    {{-- الشاحنات الفاضية في كل منطقة --}}
    <div class="card"><div class="card-body" style="padding:14px 16px !important">
        <div style="font-weight:800;margin-bottom:10px">الشاحنات المتاحة حسب المنطقة <small style="color:#64748B;font-weight:700">(ملك المؤسسة)</small></div>
        <div class="tb-regions">
            <span class="tb-reg on" data-region="">كل المناطق</span>
            @foreach ($emptyByRegion as $reg => $cnt)
                <span class="tb-reg {{ $cnt ? '' : 'zero' }}" data-region="{{ $reg }}">{{ $reg }} <b>{{ $cnt }}</b></span>
            @endforeach
        </div>
    </div></div>

    <div class="tb-bar">
        <input type="text" id="tbSearch" class="form-control" placeholder="ابحث برقم اللوحة أو السائق أو المنطقة أو نوع الحمولة...">
    </div>

    {{-- الشاحنات --}}
    <div class="tb-grid" id="tbGrid">
        @forelse ($trucks as $k => $t)
            @php
                $trip = $t->activeTrip;
                $state = $trip ? ($trip->is_overdue ? 'late' : 'loaded') : 'empty';
                $region = $trip ? '' : ($t->current_region ?: 'غير محدد');
                $search = strtolower($t->plate_number . ' ' . $t->truck_type . ' ' . \App\Models\truck_trip::ownershipLabel($t->ownership) . ' ' . ($trip ? $trip->driver_name . ' ' . optional($trip->driver)->phone . ' ' . $trip->from_region . ' ' . $trip->to_region . ' ' . $trip->load_type . ' ' . $trip->customer_name : $region . ' ' . optional($t->default_driver)->name . ' ' . optional($t->default_driver)->phone));
            @endphp
            <div class="tb-card tb-anim {{ $state }}" style="--i:{{ min($k, 20) }}" data-state="{{ $state }}" data-own="{{ $t->ownership === 'own' ? 1 : 0 }}" data-region="{{ $region }}" data-search="{{ $search }}" data-docs="{{ $t->docs_alert ? 1 : 0 }}">
                <div class="tb-head">
                    <div>
                        <div class="tb-plate">{{ $t->plate_number }}<a class="tb-edit-link" href="{{ url($lp . '/trucks/' . $t->id . '/edit') }}" title="تعديل بيانات الشاحنة (اللوحة / التأمين / الاستمارة)"><i class="bx bx-cog"></i></a></div>
                        <div class="tb-type">{{ $t->truck_type ?: 'شاحنة' }}{{ $t->total_load ? ' · ' . $t->total_load : '' }}</div>
                        @if ($t->insurance_status == 'expired' || $t->insurance_status == 'soon' || $t->istimara_status == 'expired' || $t->istimara_status == 'soon')
                            <div class="tb-docs">
                                @if ($t->insurance_status == 'expired')<span class="tb-doc expired"><i class="bx bx-shield-x"></i> التأمين منتهي</span>
                                @elseif ($t->insurance_status == 'soon')<span class="tb-doc soon"><i class="bx bx-shield"></i> التأمين ينتهي {{ $t->insurance_expiry->format('Y/m/d') }}</span>@endif
                                @if ($t->istimara_status == 'expired')<span class="tb-doc expired"><i class="bx bx-id-card"></i> الاستمارة منتهية</span>
                                @elseif ($t->istimara_status == 'soon')<span class="tb-doc soon"><i class="bx bx-id-card"></i> الاستمارة تنتهي {{ $t->istimara_expiry->format('Y/m/d') }}</span>@endif
                            </div>
                        @endif
                        <span class="tb-own {{ $t->ownership ?: 'none' }}"><i class="bx {{ $t->ownership == 'external' ? 'bx-transfer' : 'bx-buildings' }}"></i> {{ \App\Models\truck_trip::ownershipLabel($t->ownership) }}</span>
                    </div>
                    <span class="tb-status">
                        @if ($state == 'empty') <i class="bx bx-check"></i> فاضية
                        @elseif ($state == 'late') <i class="bx bx-error"></i> متأخرة
                        @else <i class="bx bx-package"></i> محمّلة @endif
                    </span>
                </div>

                @if (!$trip)
                    <div class="tb-loc"><i class="bx bx-map"></i> {{ $region }}{{ $t->current_city ? ' - ' . $t->current_city : '' }}</div>
                    @include('trucks._call', ['drv' => $t->default_driver, 'name' => null, 'phone' => null])
                    <div class="tb-actions">
                        <button class="btn btn-success btn-sm js-load" data-truck="{{ $t->id }}" data-plate="{{ $t->plate_number }}" data-region="{{ $t->current_region }}" data-city="{{ $t->current_city }}" data-driver="{{ $t->default_driver_id }}" data-own="{{ $t->ownership }}"><i class="bx bx-upload"></i> تحميل</button>
                        <a class="btn btn-secondary btn-sm" href="{{ url($lp . '/trucks/' . $t->id . '/edit') }}"><i class="bx bx-edit"></i> بيانات الشاحنة</a>
                    </div>
                @else
                    <div class="tb-route">
                        <div class="pt"><small>من</small><b>{{ $trip->from_region }}</b><small>{{ $trip->from_city }}</small></div>
                        <i class="bx bx-left-arrow-alt ar"></i>
                        <div class="pt"><small>إلى</small><b>{{ $trip->to_region }}</b><small>{{ $trip->to_city }}</small></div>
                    </div>
                    <div class="tb-meta">
                        <div><i class="bx bx-package"></i><span>{{ $trip->load_type }}{{ $trip->load_weight ? ' · ' . $trip->load_weight : '' }}</span></div>
                        <div><i class="bx bx-upload"></i><span>{{ $fmt($trip->loading_at) }}</span></div>
                        <div><i class="bx bx-download"></i><span>{{ $fmt($trip->expected_unloading_at) }}</span></div>
                        @if ($trip->customer_name)<div><i class="bx bx-briefcase"></i><span>{{ $trip->customer_name }}</span></div>@endif
                        @if ($trip->waybill_no)<div><i class="bx bx-receipt"></i><span>بوليصة {{ $trip->waybill_no }}</span></div>@endif
                        @if ($trip->invoice_number)<div><i class="bx bx-file"></i><span>فاتورة {{ $trip->invoice_number }}</span></div>@endif
                        @if ($trip->price > 0)<div><i class="bx bx-money"></i><span>{{ number_format($trip->price, 2) }} ر.س</span></div>@endif
                        @if ($trip->attachment)<div><i class="bx bx-paperclip"></i><span><a href="{{ asset('assets/uploads/truck_trips/' . $trip->attachment) }}" target="_blank">المرفق</a></span></div>@endif
                    </div>
                    @include('trucks._call', ['drv' => $trip->driver, 'name' => $trip->driver_name, 'phone' => null])
                    <div class="tb-eta js-eta" data-eta="{{ optional($trip->expected_unloading_at)->format('Y-m-d\TH:i:s') }}"></div>
                    <div class="tb-actions">
                        <button class="btn btn-primary btn-sm js-qunload" data-trip="{{ $trip->id }}" data-plate="{{ $t->plate_number }}" data-to="{{ $trip->to_region }}" data-city="{{ $trip->to_city }}"><i class="bx bx-check-double"></i> تم التفريغ</button>
                        <a class="btn btn-secondary btn-sm" style="flex:none" href="{{ url($lp . '/trucks/trips/' . $trip->id . '/edit') }}" title="تعديل بيانات الشحنة"><i class="bx bx-edit"></i></a>
                        <form method="post" action="{{ url($lp . '/trucks/trips/' . $trip->id . '/cancel') }}" style="flex:none" onsubmit="return confirm('إلغاء الحمولة دي؟ (لو اتسجلت بالغلط)');">
                            {{ csrf_field() }}
                            <button class="btn btn-secondary btn-sm" title="إلغاء الحمولة"><i class="bx bx-x"></i></button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="tb-empty-state"><i class="bx bxs-truck" style="font-size:40px"></i><br>مفيش شاحنات متسجلة لسه. دوس «إضافة شاحنة».</div>
        @endforelse
        <div class="tb-empty-state tb-hidden" id="tbNoRes">مفيش نتائج</div>
    </div>
</div>

{{-- ============ مودال تحميل ============ --}}
<div class="modal fade" id="loadModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">تسجيل حمولة - <span id="lmPlate"></span></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" action="{{ url($lp . '/trucks/trips') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="truck_id" id="lmTruck" value="{{ old('truck_id') }}">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3"><span class="tb-own" id="lmOwn"></span></div>
                    <div class="col-md-6 mb-2"><label>من (منطقة التحميل) *</label>
                        <select name="from_region" id="lmFrom" class="form-control" required><option value="">اختار المنطقة</option>@foreach ($regions as $r)<option value="{{ $r }}" {{ old('from_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label>إلى (منطقة التنزيل) *</label>
                        <select name="to_region" class="form-control" required><option value="">اختار المنطقة</option>@foreach ($regions as $r)<option value="{{ $r }}" {{ old('to_region') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label>مدينة التحميل</label><input name="from_city" id="lmFromCity" class="form-control" value="{{ old('from_city') }}"></div>
                    <div class="col-md-6 mb-2"><label>مدينة التنزيل</label><input name="to_city" class="form-control" value="{{ old('to_city') }}"></div>
                    <div class="col-md-6 mb-2"><label>نوع التحميل *</label><input name="load_type" class="form-control" list="loadTypes" required value="{{ old('load_type') }}" placeholder="مثال: مواد بناء، مواد غذائية، حديد...">
                        <datalist id="loadTypes"><option value="مواد بناء"><option value="مواد غذائية"><option value="حديد"><option value="أسمنت"><option value="أثاث"><option value="معدات"><option value="مبردات"><option value="مواد بترولية"><option value="بضائع عامة"></datalist></div>
                    <div class="col-md-6 mb-2"><label>الوزن / الكمية</label><input name="load_weight" class="form-control" value="{{ old('load_weight') }}" placeholder="مثال: 25 طن"></div>
                    <div class="col-md-6 mb-2"><label>معاد التحميل *</label><input type="datetime-local" name="loading_at" id="lmLoadAt" class="form-control" required value="{{ old('loading_at') }}"></div>
                    <div class="col-md-6 mb-2"><label>معاد التنزيل المتوقع *</label><input type="datetime-local" name="expected_unloading_at" id="lmEta" class="form-control" required value="{{ old('expected_unloading_at') }}"></div>
                    <div class="col-md-6 mb-2"><label class="d-flex justify-content-between">السائق <a href="#" data-driver-modal style="font-size:12px">+ سائق جديد</a></label>
                        <select name="driver_id" id="lmDriver" class="form-control" data-drivers><option value="">—</option>@foreach ($drivers as $d)<option value="{{ $d->id }}">{{ $d->name }}{{ $d->phone ? ' - ' . $d->phone : '' }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-2"><label class="d-flex justify-content-between">اسم الشركة (العميل) <a href="#" data-customer-modal style="font-size:12px">+ عميل جديد</a></label>
                        <select name="customer_account_id" id="lmCustomer" class="form-control" data-customers>
                            <option value="">اختار العميل</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_account_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->account_number ? ' (' . $c->account_number . ')' : '' }}</option>
                            @endforeach
                        </select></div>
                    <div class="col-md-6 mb-2"><label>رقم الفاتورة</label><input name="invoice_number" class="form-control" value="{{ old('invoice_number') }}"></div>
                    <div class="col-md-6 mb-2"><label>مرجع</label><input name="reference_no" class="form-control" value="{{ old('reference_no') }}"></div>
                    <div class="col-md-6 mb-2"><label>السعر (ر.س)</label><input type="number" step="any" min="0" name="price" class="form-control" value="{{ old('price') }}"></div>
                    <div class="col-md-6 mb-2"><label>المرفق (فاتورة / صورة - PDF أو صورة)</label><input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp"></div>
                    <div class="col-md-6 mb-2"><label>رقم البوليصة</label><input name="waybill_no" class="form-control" value="{{ old('waybill_no') }}"></div>
                    <div class="col-md-6 mb-2"><label>ملاحظات</label><input name="notes" class="form-control" value="{{ old('notes') }}"></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-success"><i class="bx bx-upload"></i> تسجيل الحمولة</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
        </form>
    </div></div>
</div>

{{-- مودال تم التفريغ (بالخطوات) --}}
@include('trucks._unload_quick', ['toast' => false])

{{-- ============ مودال مكان الشاحنة ============ --}}
<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">بيانات الشاحنة - <span id="rmPlate"></span></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" id="rmForm" action="">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="mb-2"><label>المنطقة *</label><select name="current_region" id="rmRegion" class="form-control" required>@foreach ($regions as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach</select></div>
                <div class="mb-2"><label>المدينة</label><input name="current_city" id="rmCity" class="form-control"></div>
                <div class="mb-2"><label>الملكية *</label>
                    <div class="tb-own-pick">
                        <label><input type="radio" name="ownership" value="own" id="rmOwn" required><span><i class="bx bx-buildings"></i> خاص بالمؤسسة</span></label>
                        <label><input type="radio" name="ownership" value="external" id="rmExt"><span><i class="bx bx-transfer"></i> إيجار خارجي</span></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">حفظ</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
        </form>
    </div></div>
</div>

{{-- ============ مودال إضافة شاحنة ============ --}}
<div class="modal fade" id="addTruckModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">إضافة شاحنة</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form method="post" action="{{ url($lp . '/trucks/add') }}">
            {{ csrf_field() }}
            <div class="modal-body"><div class="row">
                <div class="col-md-12 mb-3"><label>الشاحنة *</label>
                    <div class="tb-own-pick">
                        <label><input type="radio" name="ownership" value="own" required><span><i class="bx bx-buildings"></i> ملك المؤسسة</span></label>
                        <label><input type="radio" name="ownership" value="external"><span><i class="bx bx-transfer"></i> إيجار خارجي (من الخارج)</span></label>
                    </div>
                </div>
                <div class="col-md-6 mb-2"><label>رقم اللوحة *</label><input name="plate_number" class="form-control" required></div>
                <div class="col-md-6 mb-2"><label>نوع الشاحنة</label><input name="truck_type" class="form-control" placeholder="تريلا، سطحة، قلاب، براد..."></div>
                <div class="col-md-6 mb-2"><label>مكانها الحالي (المنطقة)</label><select name="current_region" class="form-control"><option value="">غير محدد</option>@foreach ($regions as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach</select></div>
                <div class="col-md-6 mb-2"><label>المدينة</label><input name="current_city" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>الحمولة الإجمالية</label><input name="total_load" class="form-control"></div>
                <div class="col-md-6 mb-2"><label class="d-flex justify-content-between">السائق الافتراضي <a href="#" data-driver-modal style="font-size:12px">+ سائق جديد</a></label><select name="default_driver_id" class="form-control" data-drivers><option value="">—</option>@foreach ($drivers as $d)<option value="{{ $d->id }}">{{ $d->name }}{{ $d->phone ? ' - ' . $d->phone : '' }}</option>@endforeach</select></div>
                <div class="col-md-6 mb-2"><label>اسم المالك</label><input name="owner_name" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>جهة اللوحة</label><input name="plate_region" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>شركة التأمين</label><input name="insurance_company" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>تاريخ انتهاء التأمين</label><input type="date" name="insurance_expiry" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>رقم الاستمارة</label><input name="istimara_no" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>تاريخ انتهاء الاستمارة</label><input type="date" name="istimara_expiry" class="form-control"></div>
            </div></div>
            <div class="modal-footer"><button class="btn btn-primary">حفظ الشاحنة</button><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button></div>
        </form>
    </div></div>
</div>

{{-- شحنة جديدة: اختيار شاحنة فاضية --}}
@php $pkTrucks = $trucks->filter(function ($x) { return !$x->activeTrip && $x->ownership === 'own'; }); @endphp
<div class="modal fade" id="pickTruckModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bx bx-package"></i> شحنة جديدة - اختار الشاحنة</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <input type="search" class="form-control pk-search" id="pkSearch" placeholder="ابحث برقم اللوحة أو المنطقة أو السائق...">
            <div class="pk-list" id="pkList">
                @foreach ($pkTrucks as $t)
                    <button type="button" class="pk-item" data-truck="{{ $t->id }}" data-search="{{ mb_strtolower($t->plate_number . ' ' . $t->current_region . ' ' . $t->current_city . ' ' . optional($t->default_driver)->name) }}">
                        <i class="bx bxs-truck"></i>
                        <span><b>{{ $t->plate_number }}</b>
                            <small>{{ $t->current_region ?: 'مكان غير محدد' }}{{ $t->current_city ? ' - ' . $t->current_city : '' }}{{ optional($t->default_driver)->name ? ' · ' . $t->default_driver->name : '' }} · {{ \App\Models\truck_trip::ownershipLabel($t->ownership) }}</small></span>
                        <span class="pk-go">تحميل <i class="bx bx-left-arrow-alt"></i></span>
                    </button>
                @endforeach
                <div class="pk-empty" id="pkNone" style="{{ $pkTrucks->count() ? 'display:none' : '' }}"><i class="bx bx-info-circle"></i> مفيش شاحنات متاحة (ملك المؤسسة) دلوقتي</div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-primary btn-sm" id="pkAddTruck"><i class="bx bx-plus"></i> شاحنة مش موجودة؟ أضفها</button><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button></div>
    </div></div>
</div>
@include('trucks._driver_modal')
@include('trucks._customer_modal')
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
(function () {
    var base = "{{ url($lp . '/trucks') }}";
    // بحث في العملاء جوه نافذة التحميل
    if ($.fn.select2) {
        $('#lmCustomer').select2({ width: '100%', dir: 'rtl', dropdownParent: $('#loadModal'), placeholder: 'اختار العميل', allowClear: true });
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function nowRiyadh(addH) {
        // الوقت بتوقيت الرياض بصيغة datetime-local
        var d = new Date(Date.now() + (addH || 0) * 3600e3);
        var p = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Riyadh', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false }).formatToParts(d);
        var o = {}; p.forEach(function (x) { o[x.type] = x.value; });
        return o.year + '-' + o.month + '-' + o.day + 'T' + (o.hour === '24' ? '00' : o.hour) + ':' + o.minute;
    }

    // ---------- تحميل ----------
    $(document).on('click', '.js-load', function () {
        var b = $(this);
        if (!b.data('own')) {
            // شاحنة قديمة من غير ملكية: لازم تتحدد الأول
            alert('حدد ملكية الشاحنة (خاص بالمؤسسة / إيجار خارجي) من «بيانات الشاحنة» الأول');
            window.location = base + '/' + b.data('truck') + '/edit';
            return;
        }
        $('#lmPlate').text(b.data('plate'));
        $('#lmTruck').val(b.data('truck'));
        $('#lmFrom').val(b.data('region') || '');
        $('#lmFromCity').val(b.data('city') || '');
        $('#lmDriver').val(b.data('driver') || '');
        // الملكية بتتحدد من بيانات الشاحنة (وقت إضافتها) - هنا للعرض بس
        var own = b.data('own') || '';
        $('#lmOwn').attr('class', 'tb-own ' + (own || 'none')).html(own === 'own' ? '<i class="bx bx-buildings"></i> خاص بالمؤسسة' : own === 'external' ? '<i class="bx bx-transfer"></i> إيجار خارجي' : '<i class="bx bx-error"></i> ملكية الشاحنة مش متحددة - حددها من «بيانات الشاحنة» الأول');
        if (!$('#lmLoadAt').val()) $('#lmLoadAt').val(nowRiyadh(0));
        if (!$('#lmEta').val()) $('#lmEta').val(nowRiyadh(24));
        $('#loadModal').modal('show');
    });
    // ---------- المكان ----------
    $(document).on('click', '.js-region', function () {
        var b = $(this);
        $('#rmPlate').text(b.data('plate'));
        $('#rmForm').attr('action', base + '/' + b.data('truck') + '/region');
        $('#rmRegion').val(b.data('region') || $('#rmRegion option:first').val());
        $('#rmCity').val(b.data('city') || '');
        $('#rmOwn').prop('checked', b.data('own') === 'own'); $('#rmExt').prop('checked', b.data('own') === 'external');
        $('#regionModal').modal('show');
    });
    // ---------- شحنة جديدة (اختيار شاحنة) ----------
    $('#pkSearch').on('input', function () {
        var v = this.value.trim().toLowerCase(), n = 0;
        $('#pkList .pk-item').each(function () { var ok = !v || this.dataset.search.indexOf(v) > -1; $(this).toggle(ok); if (ok) n++; });
        $('#pkNone').toggle(n === 0);
    });
    $('#pkAddTruck').on('click', function () { $('#pickTruckModal').one('hidden.bs.modal', function () { $('#addTruckModal').modal('show'); }).modal('hide'); });
    $('#pickTruckModal').on('shown.bs.modal', function () { $('#pkSearch').trigger('focus'); });
    $(document).on('click', '.pk-item', function () {
        var id = $(this).data('truck');
        $('#pickTruckModal').one('hidden.bs.modal', function () {
            $('.js-load[data-truck="' + id + '"]').first().trigger('click');
        }).modal('hide');
    });
    @if (request('new') && !$errors->any())
        // جاي من «إضافة شحنة جديدة»: نعرض الشاحنات الفاضية بس
        setTimeout(function () { $('.tb-kpi[data-filter=empty]').trigger('click'); }, 0);
    @endif
    @if ($errors->any() && old('truck_id'))
        $('#lmPlate').text('');
        $('#loadModal').modal('show');
    @endif

    // ---------- فلترة ----------
    var state = 'all', region = '', q = '', docsOnly = false;
    $('#tbDocsAlert').on('click', function () { docsOnly = !docsOnly; $(this).toggleClass('on', docsOnly); apply(); });
    function apply() {
        var any = false;
        document.querySelectorAll('#tbGrid .tb-card').forEach(function (c) {
            var okS = state === 'all' || c.dataset.state === state || (state === 'loaded' && c.dataset.state === 'late');
            if (state === 'empty' && c.dataset.own !== '1') okS = false;   // المتاحة = ملك المؤسسة بس
            var okR = !region || c.dataset.region === region;
            var okQ = !q || c.dataset.search.indexOf(q) > -1;
            if (docsOnly && c.dataset.docs !== '1') okQ = false;
            var show = okS && okR && okQ;
            c.classList.toggle('tb-hidden', !show);
            if (show) any = true;
        });
        var nr = document.getElementById('tbNoRes'); if (nr) nr.classList.toggle('tb-hidden', any || !document.querySelector('#tbGrid .tb-card'));
    }
    $('.tb-kpi').on('click', function () { $('.tb-kpi').removeClass('on'); $(this).addClass('on'); state = this.dataset.filter; if (state !== 'empty' && state !== 'all') { region = ''; $('.tb-reg').removeClass('on').first().addClass('on'); } apply(); });
    $('.tb-reg').on('click', function () {
        $('.tb-reg').removeClass('on'); $(this).addClass('on'); region = this.dataset.region;
        if (region) { state = 'empty'; $('.tb-kpi').removeClass('on'); $('.tb-kpi[data-filter=empty]').addClass('on'); }
        apply();
    });
    $('#tbSearch').on('input', function () { q = this.value.trim().toLowerCase(); apply(); });

    // ---------- العد التنازلي لمعاد التنزيل ----------
    function eta() {
        var now = new Date(nowRiyadh(0) + ':00');
        document.querySelectorAll('.js-eta').forEach(function (el) {
            if (!el.dataset.eta) return;
            var diff = (new Date(el.dataset.eta) - now) / 60000, a = Math.abs(diff), h = Math.floor(a / 60), m = Math.round(a % 60);
            var txt = (h ? h + ' ساعة ' : '') + m + ' دقيقة';
            el.innerHTML = diff >= 0 ? '<i class="bx bx-time"></i> متبقي على التنزيل: ' + txt : '<i class="bx bx-error"></i> متأخرة ' + txt;
        });
    }
    eta(); setInterval(eta, 60000);
})();
</script>
@endsection
