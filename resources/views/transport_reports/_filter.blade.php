{{-- فلتر الفترة (+ حقول إضافية بـ $extra) --}}
<div class="card tr-filter"><div class="card-body">
    <form method="get" action="{{ url()->current() }}">
        <div class="row">
            <div class="col-lg-2 col-md-4 mb-2"><label>من تاريخ</label><input type="date" name="start_at" class="form-control" value="{{ $from }}"></div>
            <div class="col-lg-2 col-md-4 mb-2"><label>إلى تاريخ</label><input type="date" name="end_at" class="form-control" value="{{ $to }}"></div>
            {!! $extra ?? '' !!}
            <div class="col-lg-1 col-md-4 mb-2 d-flex align-items-end"><button class="btn btn-primary btn-block">عرض</button></div>
        </div>
    </form>
</div></div>
