@php
    $lpH = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    $curH = request()->path();
    $tabsH = [
        ['hr/reports/employees', 'bx-user-pin', 'الموظفين والرواتب'],
        ['hr/reports/payroll', 'bx-money-withdraw', 'مسير الرواتب الشهري'],
        ['hr/reports/attendance', 'bx-time', 'الحضور والانصراف'],
        ['hr/reports/leaves', 'bx-calendar-minus', 'الإجازات'],
        ['hr/documents/alerts', 'bx-bell', 'تنبيهات الوثائق'],
    ];
@endphp
<div class="tr-tabs no-print">
    @foreach ($tabsH as [$path, $icon, $label])
        <a href="{{ url($lpH . '/' . $path) }}" class="{{ str_contains($curH, $path) ? 'on' : '' }}"><i class="bx {{ $icon }}"></i>{{ $label }}</a>
    @endforeach
</div>
