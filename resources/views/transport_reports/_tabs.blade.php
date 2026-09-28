@php
    $lpT = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $cur = request()->path();
    $tabs = [
        ['transport-reports/overview', 'bx-line-chart', 'تقرير النقل الشامل'],
        ['transport-reports/top', 'bx-trophy', 'الأكثر نقلاً'],
        ['transport-reports/invoices', 'bx-receipt', 'الفواتير والضريبة'],
        ['transport-reports/customers', 'bx-group', 'العملاء'],
        ['transport-reports/trucks', 'bxs-truck', 'الشاحنات والربح'],
        ['trucks/expenses', 'bx-wallet', 'مصروفات الشاحنات'],
        ['transport-reports/drivers', 'bx-id-card', 'السائقين'],
        ['transport-reports/unbilled', 'bx-error-circle', 'الأحمال غير المفوترة'],
        ['trucks/report', 'bx-bar-chart-alt-2', 'تقرير الأحمال'],
    ];
    $keep = array_filter(request()->only(['start_at', 'end_at']));
@endphp
<div class="tr-tabs no-print">
    @foreach ($tabs as [$path, $icon, $label])
        <a href="{{ url($lpT . '/' . $path) }}{{ $keep ? '?' . http_build_query($keep) : '' }}" class="{{ str_contains($cur, $path) ? 'on' : '' }}"><i class="bx {{ $icon }}"></i>{{ $label }}</a>
    @endforeach
</div>
