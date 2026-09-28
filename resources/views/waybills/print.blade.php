<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوليصة شحن رقم {{ $waybill->waybill_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    @include('waybills._style')
    <style>
        body { background: #eef0f6; margin: 0; padding: 20px 10px; }
        .wb-paper { min-height: 1050px; display: flex; flex-direction: column; }
        .wb-paper .wb-grow { flex: 1; }
        .wb-field .wb-val { display: flex; align-items: flex-end; min-height: 22px; line-height: 1.5; padding: 0 6px 1px; }
        .no-print { text-align: center; margin: 0 auto 14px; }
        .no-print button, .no-print a {
            font-family: 'Cairo', sans-serif; font-weight: 700; border: none; border-radius: 8px;
            padding: 8px 22px; margin: 0 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px;
        }
        .btn-p { background: #2b2f8f; color: #fff; }
        .btn-b { background: #fff; color: #2b2f8f; border: 1px solid #2b2f8f !important; }
        @page { size: A4; margin: 8mm; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            html, body { width: 194mm; }
            .wb-paper { box-shadow: none; border: none; max-width: 100%; width: 100%; padding: 0; min-height: 0; border-radius: 0; }
            /* تثبيت شكل الورقة في الطباعة (بدون تكديس الموبايل) */
            .wb-head { flex-direction: row !important; }
            .wb-head-side { width: 27% !important; }
            .wb-head-left { text-align: left !important; }
            .wb-row { flex-direction: row !important; gap: 18px !important; margin-bottom: 4px; }
            .wb-note-strong { margin: 4px 0 6px; font-size: 14.5px; }
            .wb-signs { margin: 6px 0 8px; }
            .wb-mob { margin-top: 4px; }
            .wb-date-line { margin-bottom: 3px; }
            .wb-name-ar { font-size: 22px; }
            .wb-title { font-size: 24px; }
            .wb-title-wrap { margin: 4px 0 12px; }
            .wb-logo { max-height: 60px; }
            .wb-field > label, .wb-field .wb-val { font-size: 13.5px; }
            table.wb-table td { height: 25px; font-size: 12.5px; }
            table.wb-table th { font-size: 13.5px; }
            .wb-table-wrap { margin: 12px 0 10px; overflow: visible; }
            table.wb-table { min-width: 0; }
            .wb-footer { margin-top: 10px; }
            .wb-paper, .wb-table-wrap, table.wb-table, .wb-row { page-break-inside: avoid; break-inside: avoid; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
@php
    $fmt = function ($d) {
        if (!$d) return '';
        try { return \Carbon\Carbon::parse($d)->format('Y/m/d'); } catch (\Throwable $e) { return $d; }
    };
    $items = $waybill->items;
    $rows  = max(7, $items->count());
    $num   = function ($n) { return $n ? rtrim(rtrim(number_format($n, 2, '.', ','), '0'), '.') : ''; };
@endphp

<div class="no-print">
    <button class="btn-p" onclick="window.print()">طباعة</button>
    <a class="btn-b" href="{{ url('waybills/edit/' . $waybill->id) }}">تعديل</a>
    <a class="btn-b" href="{{ url('waybills') }}">البوليصات السابقة</a>
    <a class="btn-b" href="{{ url('waybills/create') }}">بوليصة جديدة</a>
</div>

<div class="wb-paper">
    @include('waybills._header', [
        'company' => $company,
        'noHtml'  => e($waybill->waybill_no),
        'hijri'   => $waybill->date_hijri,
        'greg'    => $fmt($waybill->date),
    ])

    <div class="wb-row">
        <div class="wb-field wide">
            <label>المكرم / السادة :</label>
            <span class="wb-val">{{ $waybill->customer_name }}</span>
            <span class="wb-suffix">المحترم</span>
        </div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>المدينة المتجهة إليها البضاعة :</label><span class="wb-val">{{ $waybill->destination_city }}</span></div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>اسم السائق :</label><span class="wb-val">{{ $waybill->driver_name }}</span></div>
        <div class="wb-field"><label>رقم رخصة القيادة :</label><span class="wb-val">{{ $waybill->driver_license_number }}</span></div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>اسم مالك السيارة :</label><span class="wb-val">{{ $waybill->owner_name }}</span></div>
        <div class="wb-field"><label>تاريخ صدورها :</label><span class="wb-val">{{ $waybill->driver_license_issue_date }}</span></div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>رقم السيارة :</label><span class="wb-val">{{ $waybill->plate_number }}</span></div>
        <div class="wb-field"><label>جهتها :</label><span class="wb-val">{{ $waybill->plate_region }}</span></div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>رقم رخصة التشغيل :</label><span class="wb-val">{{ $waybill->operation_license_number }}</span></div>
        <div class="wb-field"><label>نوع السيارة :</label><span class="wb-val">{{ $waybill->truck_type }}</span></div>
    </div>
    <div class="wb-row">
        <div class="wb-field"><label>جهة صدورها :</label><span class="wb-val">{{ $waybill->operation_license_issuer }}</span></div>
        <div class="wb-field"><label>الحمولة الإجمالية :</label><span class="wb-val">{{ $waybill->total_load }}</span></div>
    </div>

    <div class="wb-table-wrap">
        <table class="wb-table">
            <thead>
                <tr>
                    <th rowspan="2">م</th>
                    <th colspan="2">الراسل</th>
                    <th rowspan="2">اسم المرسل إليه</th>
                    <th rowspan="2">نوع البضاعة</th>
                    <th rowspan="2">وزن البضاعة</th>
                </tr>
                <tr><th>الاسم</th><th>الأجرة</th></tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $rows; $i++)
                    @php $it = $items[$i] ?? null; @endphp
                    <tr>
                        <td class="wb-idx">{{ $it ? $i + 1 : '' }}</td>
                        <td>{{ $it->sender_name ?? '' }}</td>
                        <td>{{ $it ? $num($it->fare) : '' }}</td>
                        <td>{{ $it->receiver_name ?? '' }}</td>
                        <td>{{ $it->goods_type ?? '' }}</td>
                        <td>{{ $it->goods_weight ?? '' }}</td>
                    </tr>
                @endfor
            </tbody>
            @if ($waybill->total_fare > 0)
                <tfoot>
                    <tr><td colspan="2">الإجمالي</td><td>{{ $num($waybill->total_fare) }}</td><td colspan="3"></td></tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="wb-signs">
        <div>تاريخ المغادرة : <span style="font-weight:600;color:#111">{{ $fmt($waybill->departure_date) }}</span></div>
        <div style="min-width:40%">توقيع المسؤول</div>
    </div>

    <div class="wb-note-strong">يجب تشريع البضاعة للمحافظة عليها</div>
    <div class="wb-row"><div class="wb-field"><label>تدفع الأجرة من قبل :</label><span class="wb-val">{{ $waybill->fare_paid_by }}</span></div></div>
    <div class="wb-row"><div class="wb-field"><label>يجب إيصال البضاعة خلال :</label><span class="wb-val">{{ $waybill->delivery_within }}</span></div></div>
    <div class="wb-note-strong">ملاحظة : أي نقصان أو تلف وأي تعطيل مسؤولية السائق</div>
    @if ($waybill->notes)
        <div class="wb-row"><div class="wb-field"><label>ملاحظات :</label><span class="wb-val">{{ $waybill->notes }}</span></div></div>
    @endif

    <div class="wb-grow"></div>
    <div class="wb-row" style="margin-top:18px">
        <div class="wb-field"><label>توقيع السائق :</label><span class="wb-val"></span></div>
        <div class="wb-field"><label>توقيع المستلم :</label><span class="wb-val"></span></div>
    </div>

    @include('waybills._footer', ['company' => $company])
</div>

@if (request('auto'))
<script>window.onload = function () { window.print(); };</script>
@endif
</body>
</html>
