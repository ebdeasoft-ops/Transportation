<div class="d-flex no-print" style="gap:8px">
    <a class="btn btn-success btn-sm" href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['export' => 'csv'])) }}"><i class="bx bx-spreadsheet"></i> تصدير Excel</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> طباعة</button>
</div>
