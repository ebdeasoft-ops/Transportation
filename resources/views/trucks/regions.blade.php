@extends('layouts.master')
@section('css')
@include('transport_reports._style')
<style>
.rg-add{ display:flex; gap:10px; flex-wrap:wrap; }
.rg-add input{ flex:1; min-width:220px; }
.rg-table input.nm{ font-weight:700; min-width:160px; }
.rg-table input.so{ width:80px; direction:ltr; text-align:center; }
.rg-table tr.off td{ opacity:.55; }
</style>
@endsection
@section('title')
المناطق
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">المناطق <small class="text-muted" style="font-size:13px">(اللي بتظهر في «من / إلى» ومكان الشاحنة)</small></h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ url('trucks/board') }}" class="btn btn-secondary btn-sm"><i class="bx bxs-truck"></i> لوحة الشاحنات</a>
    </div>
</div>
@endsection
@section('content')
@php $lp = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(); @endphp

@if (session('trip_ok'))<div class="alert alert-success">{{ session('trip_ok') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

<div class="card"><div class="card-body">
    <div style="font-weight:800;margin-bottom:8px"><i class="bx bx-plus-circle" style="color:#2F6FED"></i> إضافة منطقة جديدة</div>
    <form method="post" action="{{ url($lp . '/trucks/regions') }}" class="rg-add">
        @csrf
        <input type="text" name="name" class="form-control" maxlength="100" required placeholder="مثال: الأحساء / الخرج / الإمارات / الأردن...">
        <button class="btn btn-primary"><i class="bx bx-plus"></i> إضافة</button>
    </form>
    <div class="text-muted mt-2" style="font-size:12.5px">تقدر كمان تضيف منطقة من زرار <b>+</b> اللي جنب «من / إلى» في شاشة إضافة شحنة.</div>
</div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table tr-table rg-table">
            <thead><tr><th>الترتيب</th><th>اسم المنطقة</th><th>الشحنات</th><th>الشاحنات فيها الآن</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @foreach ($regions as $r)
                    <tr class="{{ $r->active ? '' : 'off' }}">
                        <form method="post" action="{{ url($lp . '/trucks/regions/' . $r->id) }}" id="rf{{ $r->id }}">@csrf</form>
                        <td><input form="rf{{ $r->id }}" type="number" name="sort" class="form-control form-control-sm so" value="{{ $r->sort }}"></td>
                        <td><input form="rf{{ $r->id }}" type="text" name="name" class="form-control form-control-sm nm" value="{{ $r->name }}" maxlength="100" required></td>
                        <td>{{ $r->trips }}</td>
                        <td>{{ $r->trucks }}</td>
                        <td>
                            <select form="rf{{ $r->id }}" name="active" class="form-control form-control-sm" style="width:auto">
                                <option value="1" {{ $r->active ? 'selected' : '' }}>ظاهرة</option>
                                <option value="0" {{ $r->active ? '' : 'selected' }}>مخفية</option>
                            </select>
                        </td>
                        <td style="white-space:nowrap">
                            <button form="rf{{ $r->id }}" class="btn btn-sm btn-primary"><i class="bx bx-save"></i> حفظ</button>
                            @if (!$r->trips && !$r->trucks)
                                <form method="post" action="{{ url($lp . '/trucks/regions/' . $r->id . '/delete') }}" style="display:inline" onsubmit="return confirm('حذف المنطقة {{ $r->name }}؟')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-muted" style="font-size:12.5px">
        تعديل الاسم بيتطبق على كل الشحنات والشاحنات القديمة. المنطقة المستخدمة مينفعش تتحذف، لكن تقدر تخليها «مخفية» فمتظهرش في الاختيارات الجديدة.
    </div>
</div></div>
@endsection
