<?php

namespace App\Http\Controllers;

use App\Models\credittransactions;
use App\Models\financial_accounts;
use App\Models\transport_invoice;
use App\Models\truck_trip;
use App\Models\waybill_driver;
use App\Models\waybill_truck;
use App\Support\AppSchema;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * تقارير النقل
 * - الفواتير وضريبة القيمة المضافة
 * - العملاء + كشف حساب عميل
 * - الشاحنات / السائقين
 * - الأحمال غير المفوترة
 */
class TransportReportController extends Controller
{
    public function __construct()
    {
        AppSchema::transport();
    }

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    private function period(Request $request)
    {
        return [$request->input('start_at', date('Y-m-01')), $request->input('end_at', date('Y-m-d'))];
    }

    private function customerAccounts()
    {
        return financial_accounts::where('orginal_type', 1)->orderBy('name')->get(['id', 'name', 'account_number', 'current_balance']);
    }

    private function csv($name, array $head, $rows)
    {
        return response()->streamDownload(function () use ($head, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $head);
            foreach ($rows as $r) fputcsv($out, $r);
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ================= الفواتير + الضريبة =================
    public function invoices(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $q = transport_invoice::whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to);
        if ($request->filled('customer_account_id')) $q->where('customer_account_id', $request->customer_account_id);
        $all = $q->orderBy('invoice_no')->get();
        $active = $all->where('status', transport_invoice::ACTIVE);

        if ($request->input('export') === 'csv') {
            return $this->csv("transport_invoices_{$from}_{$to}.csv",
                ['رقم الفاتورة', 'التاريخ', 'العميل', 'الرقم الضريبي', 'النوع', 'نوع الضريبة', 'الإجمالي قبل الخصم', 'الخصم', 'الخاضع للضريبة', 'الضريبة', 'الإجمالي', 'الحالة'],
                $all->map(function ($i) {
                    return [$i->invoice_no, $i->issue_date->format('Y-m-d'), $i->customer_name, $i->customer_vat, $i->is_standard ? 'ضريبية' : 'مبسطة', $i->is_zero_rated ? 'صفرية' : 'أساسية',
                        $i->subtotal, $i->discount, $i->taxable, $i->vat_amount, $i->total, $i->is_cancelled ? 'ملغاة' : 'سارية'];
                }));
        }

        $summary = [
            'count'     => $active->count(),
            'standard'  => $active->filter->is_standard->count(),
            'simple'    => $active->reject->is_standard->count(),
            'subtotal'  => round($active->sum('subtotal'), 2),
            'discount'  => round($active->sum('discount'), 2),
            'taxable'   => round($active->sum('taxable'), 2),
            'vat'       => round($active->sum('vat_amount'), 2),
            'total'     => round($active->sum('total'), 2),
            'std_taxable'  => round($active->where('vat_category', '!=', 'Z')->sum('taxable'), 2),
            'zero_taxable' => round($active->where('vat_category', 'Z')->sum('taxable'), 2),
            'zero_count'   => $active->where('vat_category', 'Z')->count(),
            'cancelled' => $all->where('status', transport_invoice::CANCELLED)->count(),
            'cancelled_total' => round($all->where('status', transport_invoice::CANCELLED)->sum('total'), 2),
        ];
        // إقرار الضريبة شهرياً
        $months = $active->groupBy(function ($i) { return $i->issue_date->format('Y-m'); })
            ->map(function ($g, $k) {
                return ['month' => $k, 'count' => $g->count(), 'taxable' => round($g->sum('taxable'), 2),
                    'std' => round($g->where('vat_category', '!=', 'Z')->sum('taxable'), 2), 'zero' => round($g->where('vat_category', 'Z')->sum('taxable'), 2), 'vat' => round($g->sum('vat_amount'), 2), 'total' => round($g->sum('total'), 2)];
            })->sortKeys()->values();
        $byCustomer = $active->groupBy('customer_name')
            ->map(function ($g, $k) { return ['name' => $k, 'count' => $g->count(), 'total' => round($g->sum('total'), 2)]; })
            ->sortByDesc('total')->take(8)->values();
        $customers = $this->customerAccounts();
        $invoices = $all;
        return view('transport_reports.invoices', compact('invoices', 'summary', 'months', 'byCustomer', 'customers', 'from', 'to'));
    }

    // ================= العملاء =================
    public function customers(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $accounts = $this->customerAccounts()->keyBy('id');

        $trips = truck_trip::whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to)->get();
        $invoices = transport_invoice::where('status', transport_invoice::ACTIVE)
            ->whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to)->get();

        $ids = $trips->pluck('customer_account_id')->merge($invoices->pluck('customer_account_id'))->filter()->unique();
        $rows = $ids->map(function ($id) use ($trips, $invoices, $accounts) {
            $t = $trips->where('customer_account_id', $id);
            $unb = $t->whereNull('transport_invoice_id');
            $inv = $invoices->where('customer_account_id', $id);
            $acc = $accounts->get($id);
            return [
                'id'            => $id,
                'name'          => $acc->name ?? ($t->first()->customer_name ?? $inv->first()->customer_name ?? '—'),
                'trips'         => $t->count(),
                'trips_value'   => round($t->sum('price'), 2),
                'unbilled'      => $unb->count(),
                'unbilled_value'=> round($unb->sum('price'), 2),
                'invoices'      => $inv->count(),
                'invoiced'      => round($inv->sum('total'), 2),
                'vat'           => round($inv->sum('vat_amount'), 2),
                'balance'       => $acc ? round((float) $acc->current_balance, 2) : null,
            ];
        })->sortByDesc('trips_value')->values();

        // أحمال مالهاش عميل مربوط
        $noCustomer = $trips->whereNull('customer_account_id');

        if ($request->input('export') === 'csv') {
            return $this->csv("customers_{$from}_{$to}.csv",
                ['العميل', 'عدد الأحمال', 'قيمة الأحمال', 'غير مفوترة', 'قيمة غير المفوترة', 'عدد الفواتير', 'المفوتر شامل الضريبة', 'الضريبة', 'رصيد الحساب'],
                $rows->map(function ($r) { return [$r['name'], $r['trips'], $r['trips_value'], $r['unbilled'], $r['unbilled_value'], $r['invoices'], $r['invoiced'], $r['vat'], $r['balance']]; }));
        }
        return view('transport_reports.customers', compact('rows', 'noCustomer', 'from', 'to'));
    }

    // ================= كشف حساب عميل =================
    public function customerStatement(Request $request, $id)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $account = financial_accounts::findOrFail($id);

        $before = credittransactions::where('customer_id', $id)->whereDate('created_at', '<', $from)
            ->selectRaw('COALESCE(SUM(debtor),0) d, COALESCE(SUM(creditor),0) c')->first();
        $opening = round((float) $before->d - (float) $before->c, 2);

        $moves = credittransactions::where('customer_id', $id)
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->orderBy('created_at')->orderBy('id')->get();
        $bal = $opening;
        $lines = $moves->map(function ($m) use (&$bal) {
            $bal += (float) $m->debtor - (float) $m->creditor;
            return ['date' => $m->created_at, 'note' => $m->note, 'debit' => (float) $m->debtor, 'credit' => (float) $m->creditor, 'balance' => round($bal, 2)];
        });

        $invoices = transport_invoice::where('customer_account_id', $id)
            ->whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to)->orderBy('invoice_no')->get();
        $unbilled = truck_trip::with('truck')->where('customer_account_id', $id)->whereNull('transport_invoice_id')->orderBy('loading_at')->get();

        $totals = ['debit' => round($lines->sum('debit'), 2), 'credit' => round($lines->sum('credit'), 2), 'closing' => round($bal, 2)];
        $customers = $this->customerAccounts();
        return view('transport_reports.customer_statement', compact('account', 'opening', 'lines', 'totals', 'invoices', 'unbilled', 'customers', 'from', 'to'));
    }

    // ================= الشاحنات =================
    public function trucks(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $trips = truck_trip::whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to)->get()->groupBy('truck_id');
        $q = waybill_truck::with('activeTrip')->orderBy('plate_number');
        if ($request->filled('ownership')) $q->where('ownership', $request->ownership);
        $exp = $this->expensesBetween($from, $to)->groupBy('truck_id');
        $rows = $q->get()->map(function ($tr) use ($trips, $exp) {
            $t = $trips->get($tr->id, collect());
            $st = $this->stats($t);
            $e = round($exp->get($tr->id, collect())->sum('amount'), 2);
            return ['truck' => $tr] + $st + ['expenses' => $e, 'profit' => round($st['revenue'] - $e, 2)];
        });
        if ($request->boolean('active_only')) $rows = $rows->where('trips', '>', 0)->values();
        $rows = $rows->sortByDesc('revenue')->values();

        if ($request->input('export') === 'csv') {
            return $this->csv("trucks_{$from}_{$to}.csv",
                ['رقم اللوحة', 'الملكية', 'عدد الرحلات', 'تم التفريغ', 'متأخرة', 'الإيراد', 'المصروفات', 'صافي الربح', 'المفوتر', 'متوسط مدة الرحلة (ساعة)', 'الحالة الآن'],
                $rows->map(function ($r) {
                    return [$r['truck']->plate_number, truck_trip::ownershipLabel($r['truck']->ownership), $r['trips'], $r['done'], $r['late'], $r['revenue'], $r['expenses'], $r['profit'], $r['invoiced'], $r['avg_hours'], $r['truck']->activeTrip ? 'محمّلة' : 'فاضية'];
                }));
        }
        $total = $this->stats($trips->flatten(1));
        $total['expenses'] = round($rows->sum('expenses'), 2);
        $total['profit']   = round($total['revenue'] - $total['expenses'], 2);
        return view('transport_reports.trucks', compact('rows', 'total', 'from', 'to'));
    }

    // ================= السائقين =================
    public function drivers(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $all = truck_trip::with('truck')->whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to)->get();
        $byDriver = $all->groupBy(function ($t) { return $t->driver_id ?: ('n:' . ($t->driver_name ?: '—')); });
        $drivers = waybill_driver::get()->keyBy('id');
        $rows = $byDriver->map(function ($t, $key) use ($drivers) {
            $d = is_numeric($key) ? $drivers->get($key) : null;
            return [
                'name'   => $d->name ?? ($t->first()->driver_name ?: 'بدون سائق'),
                'phone'  => $d->phone ?? null,
                'trucks' => $t->map(function ($x) { return optional($x->truck)->plate_number; })->filter()->unique()->implode('، '),
            ] + $this->stats($t);
        })->sortByDesc('trips')->values();

        if ($request->input('export') === 'csv') {
            return $this->csv("drivers_{$from}_{$to}.csv",
                ['السائق', 'الجوال', 'الشاحنات', 'عدد الرحلات', 'تم التفريغ', 'متأخرة', 'الإيراد', 'متوسط مدة الرحلة (ساعة)'],
                $rows->map(function ($r) { return [$r['name'], $r['phone'], $r['trucks'], $r['trips'], $r['done'], $r['late'], $r['revenue'], $r['avg_hours']]; }));
        }
        $total = $this->stats($all);
        return view('transport_reports.drivers', compact('rows', 'total', 'from', 'to'));
    }

    /** مصروفات الشاحنات في فترة (لو الجدول لسه ما اتعملش بترجع فاضية) */
    private function expensesBetween($from, $to)
    {
        \App\Support\AppSchema::truckExpenses();
        return \App\Models\truck_expense::whereDate('expense_date', '>=', $from)->whereDate('expense_date', '<=', $to)->get();
    }

    // ================= تقرير النقل الشامل =================
    public function overview(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $trips = truck_trip::whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to)->get();
        $inv = transport_invoice::where('status', transport_invoice::ACTIVE)->whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to)->get();
        $exp = $this->expensesBetween($from, $to);

        $st = $this->stats($trips);
        $summary = $st + [
            'invoices'      => $inv->count(),
            'invoiced'      => round($inv->sum('total'), 2),
            'vat'           => round($inv->sum('vat_amount'), 2),
            'unbilled'      => round($trips->whereNull('transport_invoice_id')->sum('price'), 2),
            'expenses'      => round($exp->sum('amount'), 2),
            'own_trips'     => $trips->where('ownership', 'own')->count(),
            'ext_trips'     => $trips->where('ownership', 'external')->count(),
            'own_revenue'   => round($trips->where('ownership', 'own')->sum('price'), 2),
            'ext_revenue'   => round($trips->where('ownership', 'external')->sum('price'), 2),
            'customers'     => $trips->pluck('customer_account_id')->filter()->unique()->count(),
            'trucks'        => $trips->pluck('truck_id')->unique()->count(),
        ];
        $summary['profit'] = round($summary['revenue'] - $summary['expenses'], 2);

        // شهرياً
        $months = collect();
        foreach ($trips->groupBy(function ($t) { return optional($t->loading_at)->format('Y-m'); }) as $k => $g) {
            $months[$k] = ['month' => $k, 'trips' => $g->count(), 'revenue' => round($g->sum('price'), 2), 'expenses' => 0, 'invoiced' => 0];
        }
        foreach ($exp->groupBy(function ($e) { return $e->expense_date->format('Y-m'); }) as $k => $g) {
            $row = $months[$k] ?? ['month' => $k, 'trips' => 0, 'revenue' => 0, 'expenses' => 0, 'invoiced' => 0];
            $row['expenses'] = round($g->sum('amount'), 2); $months[$k] = $row;
        }
        foreach ($inv->groupBy(function ($i) { return $i->issue_date->format('Y-m'); }) as $k => $g) {
            $row = $months[$k] ?? ['month' => $k, 'trips' => 0, 'revenue' => 0, 'expenses' => 0, 'invoiced' => 0];
            $row['invoiced'] = round($g->sum('total'), 2); $months[$k] = $row;
        }
        $months = $months->sortKeys()->values();

        $routes = $trips->groupBy(function ($t) { return $t->from_region . ' ← ' . $t->to_region; })
            ->map(function ($g, $k) { return ['name' => $k, 'count' => $g->count(), 'revenue' => round($g->sum('price'), 2)]; })
            ->sortByDesc('count')->take(8)->values();
        $types = $trips->groupBy(function ($t) { return trim($t->load_type) ?: '—'; })
            ->map(function ($g, $k) { return ['name' => $k, 'count' => $g->count(), 'revenue' => round($g->sum('price'), 2)]; })
            ->sortByDesc('count')->take(8)->values();
        $regionsFrom = $trips->groupBy('from_region')->map->count()->sortDesc()->take(8);
        $regionsTo   = $trips->groupBy('to_region')->map->count()->sortDesc()->take(8);

        return view('transport_reports.overview', compact('summary', 'months', 'routes', 'types', 'regionsFrom', 'regionsTo', 'from', 'to'));
    }

    // ================= الأكثر نقلاً (شاحنات / عملاء / سائقين) =================
    public function top(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $by = $request->input('by', 'trips') === 'revenue' ? 'revenue' : 'trips';
        $limit = 10;
        $trips = truck_trip::with('truck')->whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to)->get();
        $exp = $this->expensesBetween($from, $to)->groupBy('truck_id');

        $rank = function ($groups, $nameFn) use ($by, $limit) {
            return $groups->map(function ($g, $k) use ($nameFn) {
                return ['name' => $nameFn($g, $k), 'trips' => $g->count(), 'revenue' => round($g->sum('price'), 2),
                    'weight_hint' => $g->pluck('load_type')->filter()->countBy()->sortDesc()->keys()->first(), 'key' => $k];
            })->sortByDesc($by)->take($limit)->values();
        };
        $topTrucks = $rank($trips->groupBy('truck_id'), function ($g) { return optional($g->first()->truck)->plate_number ?: '—'; })
            ->map(function ($r) use ($exp) { $e = round($exp->get($r['key'], collect())->sum('amount'), 2); return $r + ['expenses' => $e, 'profit' => round($r['revenue'] - $e, 2)]; });
        $topCustomers = $rank($trips->filter(function ($t) { return $t->customer_account_id || $t->customer_name; })
            ->groupBy(function ($t) { return $t->customer_account_id ?: ('n:' . $t->customer_name); }), function ($g) { return $g->first()->customer_name ?: '—'; });
        $topDrivers = $rank($trips->filter(function ($t) { return $t->driver_id || $t->driver_name; })
            ->groupBy(function ($t) { return $t->driver_id ?: ('n:' . $t->driver_name); }), function ($g) { return $g->first()->driver_name ?: '—'; });
        $topRoutes = $rank($trips->groupBy(function ($t) { return $t->from_region . ' ← ' . $t->to_region; }), function ($g, $k) { return $k; });

        $totals = ['trips' => $trips->count(), 'revenue' => round($trips->sum('price'), 2)];
        return view('transport_reports.top', compact('topTrucks', 'topCustomers', 'topDrivers', 'topRoutes', 'totals', 'by', 'from', 'to'));
    }

    /** إحصائيات مجموعة رحلات */
    private function stats($t)
    {
        $done = $t->where('status', truck_trip::UNLOADED);
        $timed = $done->filter(function ($x) { return $x->loading_at && $x->unloaded_at; });
        return [
            'trips'     => $t->count(),
            'done'      => $done->count(),
            'loaded'    => $t->where('status', truck_trip::LOADED)->count(),
            'late'      => $done->filter(function ($x) { return $x->unloaded_at && $x->expected_unloading_at && $x->unloaded_at->gt($x->expected_unloading_at); })->count()
                         + $t->filter(function ($x) { return $x->is_overdue; })->count(),
            'revenue'   => round($t->sum('price'), 2),
            'invoiced'  => round($t->whereNotNull('transport_invoice_id')->sum('price'), 2),
            'avg_hours' => $timed->count() ? round($timed->avg(function ($x) { return $x->loading_at->diffInMinutes($x->unloaded_at) / 60; }), 1) : 0,
        ];
    }

    // ================= الأحمال غير المفوترة =================
    public function unbilled(Request $request)
    {
        $this->setLocale();
        $q = truck_trip::with(['truck', 'driver'])->whereNull('transport_invoice_id');
        $status = $request->input('status', '2');
        if ($status !== 'all') $q->where('status', (int) $status);
        if ($request->filled('start_at')) $q->whereDate('loading_at', '>=', $request->start_at);
        if ($request->filled('end_at'))   $q->whereDate('loading_at', '<=', $request->end_at);
        if ($request->filled('customer_account_id')) $q->where('customer_account_id', $request->customer_account_id);
        $trips = $q->orderBy('customer_name')->orderBy('loading_at')->get();

        if ($request->input('export') === 'csv') {
            return $this->csv('unbilled_loads.csv',
                ['التاريخ', 'العميل', 'رقم اللوحة', 'السائق', 'من', 'إلى', 'نوع التحميل', 'السعر', 'الحالة'],
                $trips->map(function ($t) {
                    return [optional($t->loading_at)->format('Y-m-d'), $t->customer_name, optional($t->truck)->plate_number, $t->driver_name,
                        trim($t->from_region . ' ' . $t->from_city), trim($t->to_region . ' ' . $t->to_city), $t->load_type, $t->price,
                        $t->status == truck_trip::LOADED ? 'محمّلة' : 'تم التفريغ'];
                }));
        }

        $groups = $trips->groupBy(function ($t) { return $t->customer_account_id ?: 0; })
            ->map(function ($g, $id) {
                return ['id' => (int) $id, 'name' => $id ? ($g->first()->customer_name ?: '—') : 'بدون عميل مربوط', 'count' => $g->count(),
                    'value' => round($g->sum('price'), 2), 'zero' => $g->filter(function ($t) { return !(float) $t->price; })->count(), 'trips' => $g,
                    'oldest' => $g->min('loading_at')];
            })->sortByDesc('value')->values();
        $summary = ['count' => $trips->count(), 'value' => round($trips->sum('price'), 2), 'customers' => $groups->where('id', '>', 0)->count(),
            'zero' => $trips->filter(function ($t) { return !(float) $t->price; })->count()];
        $customers = $this->customerAccounts();
        return view('transport_reports.unbilled', compact('groups', 'summary', 'customers', 'status'));
    }
}
