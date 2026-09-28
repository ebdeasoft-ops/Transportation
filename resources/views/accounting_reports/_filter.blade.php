<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}"><div class="row">
        @if (empty($asOf))
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ $from }}"></div>
        @endif
        <div class="col-lg-2 col-md-4 mb-2"><label>{{ empty($asOf) ? 'إلى تاريخ' : 'حتى تاريخ' }}</label><input type="date" name="end_at" class="form-control" value="{{ $to }}"></div>
        @isset($level)
        <div class="col-lg-2 col-md-4 mb-2"><label>مستوى الحسابات</label>
            <select name="level" class="form-control">
                @foreach ([1 => 'الرئيسية بس', 2 => 'مستويين', 3 => '3 مستويات', 9 => 'كل الحسابات'] as $k => $l)<option value="{{ $k }}" {{ $level == $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach
            </select></div>
        @endisset
        @if (!empty($branches) && count($branches))
        <div class="col-lg-2 col-md-4 mb-2"><label>الفرع</label>
            <select name="branch" class="form-control"><option value="">كل الفروع</option>@foreach ($branches as $b)<option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach</select></div>
        @endif
        {!! $extra ?? '' !!}
        <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
    </div></form>
</div></div>
