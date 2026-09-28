@php
    $lpA = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $curA = request()->path();
    $tabsA = [
        ['accounting-reports/trial-balance', 'bx-spreadsheet', 'ميزان المراجعة'],
        ['accounting-reports/income-statement', 'bx-trending-up', 'قائمة الدخل (أرباح وخسائر)'],
        ['accounting-reports/balance-sheet', 'bx-building', 'الميزانية العمومية'],
        ['accounting-reports/vat', 'bx-receipt', 'إقرار ضريبة القيمة المضافة'],
        ['account_statement', 'bx-user', 'كشف حساب'],
        ['Daily_record_report', 'bx-notepad', 'دفتر اليومية'],
    ];
@endphp
<div class="tr-tabs no-print">
    @foreach ($tabsA as [$path, $icon, $label])
        <a href="{{ url($lpA . '/' . $path) }}" class="{{ str_ends_with($curA, $path) ? 'on' : '' }}"><i class="bx {{ $icon }}"></i>{{ $label }}</a>
    @endforeach
</div>
<style>
.ac-row td{ font-variant-numeric:tabular-nums; }
.ac-l1 td{ font-weight:800; background:#F1F5F9; }
.ac-l2 td{ font-weight:700; }
.ac-name{ white-space:nowrap; }
.ac-num{ direction:ltr; text-align:right; white-space:nowrap; }
.ac-sec{ background:#1E3A8A !important; color:#fff; font-weight:800; }
.ac-sec td{ background:#1E3A8A !important; color:#fff !important; }
.ac-tot td{ font-weight:800; background:#EFF6FF; border-top:2px solid #1E3A8A !important; }
.ac-grand td{ font-weight:900; font-size:15px; background:#1E3A8A; color:#fff; }
</style>
