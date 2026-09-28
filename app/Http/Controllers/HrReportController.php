<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Custody;
use App\Models\departments;
use App\Models\employee;
use App\Models\Leave;
use App\Models\Loans;
use App\Support\AppSchema;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * تقارير الموارد البشرية
 * - الموظفين والرواتب (حسب القسم)
 * - الحضور والانصراف (ملخص لكل موظف)
 * - الإجازات (حسب النوع + الرصيد)
 * - الرواتب الشهرية (مستحقات وخصومات وسلف وصافي)
 */
class HrReportController extends Controller
{
    public function __construct()
    {
        AppSchema::hr();
    }

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    private function period(Request $request)
    {
        return [$request->input('start_at', date('Y-m-01')), $request->input('end_at', date('Y-m-d'))];
    }

    private function deptNames()
    {
        return departments::pluck('name_ar', 'id');
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

    private function allowances($e)
    {
        return (float) $e->housing_allowance + (float) $e->transportation_allowance + (float) $e->other_allowances;
    }

    /** جدول الزيادات والخصومات (اسمه فيه حرف «ـ») */
    private function incTable()
    {
        try {
            return (new \App\Models\Increaseـor_deduction_employee)->getTable();
        } catch (\Throwable $e) {
            return 'increaseـor_deduction_employees';
        }
    }

    // ================= الموظفين والرواتب =================
    public function employees(Request $request)
    {
        $this->setLocale();
        $depts = $this->deptNames();
        $q = employee::query()->orderBy('name_ar');
        if ($request->filled('department')) $q->where('department', $request->department);
        $emps = $q->get();

        $today = Carbon::today();
        $contracts = Contract::whereIn('employee_id', $emps->pluck('id'))->orderByDesc('end_date')->get()->groupBy('employee_id');
        $custody = Custody::where('status', 'delivered')->get()->groupBy('employee_id');

        $rows = $emps->map(function ($e) use ($depts, $contracts, $custody, $today) {
            $c = optional($contracts->get($e->id))->first();
            $alerts = [];
            if ($c) {
                foreach (['end_date' => 'العقد', 'iqama_expiry_date' => 'الإقامة', 'work_permit_expiry_date' => 'رخصة العمل'] as $f => $l) {
                    if ($c->$f && Carbon::parse($c->$f)->lte($today->copy()->addDays(30))) {
                        $alerts[] = $l . (Carbon::parse($c->$f)->lt($today) ? ' منتهية' : ' قربت تنتهي');
                    }
                }
            }
            $allow = $this->allowances($e);
            return [
                'emp' => $e, 'dept' => $depts[$e->department] ?? $e->department, 'basic' => (float) $e->salary,
                'allow' => $allow, 'total' => (float) $e->salary + $allow, 'contract' => $c, 'alerts' => $alerts,
                'custody' => optional($custody->get($e->id))->count() ?? 0,
            ];
        });

        if ($request->input('export') === 'csv') {
            return $this->csv('employees.csv', ['الاسم', 'القسم', 'الجنسية', 'رقم الهوية', 'الجوال', 'الأساسي', 'البدلات', 'الإجمالي', 'نهاية العقد', 'تنبيهات', 'عهد لم تُرجع'],
                $rows->map(function ($r) {
                    return [$r['emp']->name_ar, $r['dept'], $r['emp']->nationality, $r['emp']->personal_identification, $r['emp']->phone,
                        $r['basic'], $r['allow'], $r['total'], optional($r['contract'])->end_date, implode(' - ', $r['alerts']), $r['custody']];
                }));
        }

        $byDept = $rows->groupBy('dept')->map(function ($g, $k) {
            return ['name' => $k ?: '—', 'count' => $g->count(), 'total' => round($g->sum('total'), 2)];
        })->sortByDesc('total')->values();
        $summary = [
            'count' => $rows->count(), 'basic' => round($rows->sum('basic'), 2), 'allow' => round($rows->sum('allow'), 2),
            'total' => round($rows->sum('total'), 2), 'alerts' => $rows->filter(function ($r) { return $r['alerts']; })->count(),
            'custody' => $rows->sum('custody'),
        ];
        return view('hr_reports.employees', compact('rows', 'byDept', 'summary', 'depts'));
    }

    // ================= الحضور =================
    public function attendance(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $emps = employee::orderBy('name_ar')->get();
        $att = Attendance::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get()->groupBy('employee_id');

        $rows = $emps->map(function ($e) use ($att) {
            $a = $att->get($e->id, collect());
            $ot = 0;
            foreach ($a->where('type', 'overtime') as $r) {
                if ($r->check_in && $r->check_out) $ot += Carbon::parse($r->check_in)->floatDiffInHours(Carbon::parse($r->check_out));
            }
            $present = $a->where('status', 'present')->count();
            $late = $a->where('status', 'late')->count();
            $absent = $a->whereIn('status', ['absent', 'unauthorized_absent'])->count();
            $days = $present + $late + $absent;
            return [
                'emp' => $e, 'records' => $a->count(), 'present' => $present, 'late' => $late, 'absent' => $absent,
                'overtime' => round($ot, 1), 'deduction' => round($a->sum('discount_amount'), 2),
                'rate' => $days ? round(($present + $late) / $days * 100) : null,
            ];
        })->filter(function ($r) use ($request) { return !$request->boolean('active_only') || $r['records']; })->values();

        if ($request->input('export') === 'csv') {
            return $this->csv("attendance_{$from}_{$to}.csv", ['الموظف', 'حاضر', 'متأخر', 'غائب', 'نسبة الحضور %', 'ساعات إضافي', 'خصومات الغياب'],
                $rows->map(function ($r) { return [$r['emp']->name_ar, $r['present'], $r['late'], $r['absent'], $r['rate'], $r['overtime'], $r['deduction']]; }));
        }
        $summary = ['present' => $rows->sum('present'), 'late' => $rows->sum('late'), 'absent' => $rows->sum('absent'),
            'overtime' => round($rows->sum('overtime'), 1), 'deduction' => round($rows->sum('deduction'), 2)];
        return view('hr_reports.attendance', compact('rows', 'summary', 'from', 'to'));
    }

    // ================= الإجازات =================
    public function leaves(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $year = substr($to, 0, 4);
        $emps = employee::orderBy('name_ar')->get();
        $leaves = Leave::where(function ($q) use ($from, $to) {
            $q->whereDate('start_date', '<=', $to)->whereDate('end_date', '>=', $from);
        })->get()->groupBy('employee_id');
        $annualYear = Leave::where('status', 'approved')->where('leave_type', 'annual')->whereYear('start_date', $year)
            ->get()->groupBy('employee_id');
        $types = ['annual' => 'سنوية', 'casual' => 'عارضة', 'sick' => 'مرضية', 'unpaid' => 'بدون راتب', 'unauthorized' => 'غياب بدون إذن'];

        $rows = $emps->map(function ($e) use ($leaves, $annualYear, $types) {
            $l = $leaves->get($e->id, collect());
            $ap = $l->where('status', 'approved');
            $r = ['emp' => $e, 'pending' => $l->where('status', 'pending')->count(), 'rejected' => $l->where('status', 'rejected')->count(),
                'deduction' => round($ap->sum('deduction_amount'), 2), 'total' => (int) $ap->sum('days_count')];
            foreach ($types as $k => $v) $r[$k] = (int) $ap->where('leave_type', $k)->sum('days_count');
            $used = (int) optional($annualYear->get($e->id))->sum('days_count');
            $r['balance'] = (float) ($e->total_leave_days ?? 21) - $used;
            $r['used_year'] = $used;
            return $r;
        })->filter(function ($r) use ($request) { return !$request->boolean('active_only') || $r['total'] || $r['pending']; })->values();

        if ($request->input('export') === 'csv') {
            return $this->csv("leaves_{$from}_{$to}.csv", array_merge(['الموظف'], array_values($types), ['إجمالي الأيام', 'طلبات معلقة', 'الخصم', "المستخدم من السنوية $year", 'الرصيد المتبقي']),
                $rows->map(function ($r) use ($types) {
                    $x = [$r['emp']->name_ar]; foreach ($types as $k => $v) $x[] = $r[$k];
                    return array_merge($x, [$r['total'], $r['pending'], $r['deduction'], $r['used_year'], $r['balance']]);
                }));
        }
        $summary = ['total' => $rows->sum('total'), 'pending' => $rows->sum('pending'), 'deduction' => round($rows->sum('deduction'), 2)];
        foreach ($types as $k => $v) $summary[$k] = $rows->sum($k);
        return view('hr_reports.leaves', compact('rows', 'summary', 'types', 'from', 'to', 'year'));
    }

    // ================= الرواتب الشهرية =================
    public function payroll(Request $request)
    {
        $this->setLocale();
        $month = $request->input('month', date('Y-m'));
        $start = $month . '-01';
        $end   = date('Y-m-t', strtotime($start));
        $depts = $this->deptNames();
        $emps  = employee::orderBy('name_ar')->get();

        $inc = collect();
        try {
            $inc = DB::table($this->incTable())->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get()->groupBy('employee_id');
        } catch (\Throwable $e) { report($e); }
        $loans = Loans::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get()->groupBy('employee_id');
        $att   = Attendance::whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get()->groupBy('employee_id');
        $lv    = Leave::where('status', 'approved')->whereDate('start_date', '>=', $start)->whereDate('start_date', '<=', $end)->get()->groupBy('employee_id');

        $rows = $emps->map(function ($e) use ($inc, $loans, $att, $lv, $depts) {
            $i = $inc->get($e->id, collect());
            $allow = $this->allowances($e);
            $bonus = (float) $i->sum('increase');
            $ded   = (float) $i->sum('deduction');
            $absence = (float) $att->get($e->id, collect())->sum('discount_amount');
            $leaveDed = (float) $lv->get($e->id, collect())->sum('deduction_amount');
            $loan  = (float) $loans->get($e->id, collect())->sum(function ($x) { return (float) $x->Loans_amount; });
            $gross = (float) $e->salary + $allow + $bonus;
            $totalDed = $ded + $absence + $leaveDed;
            return [
                'emp' => $e, 'dept' => $depts[$e->department] ?? $e->department,
                'basic' => (float) $e->salary, 'allow' => $allow, 'bonus' => $bonus, 'gross' => round($gross, 2),
                'deduction' => round($ded, 2), 'absence' => round($absence, 2), 'leave' => round($leaveDed, 2), 'loans' => round($loan, 2),
                'net' => round($gross - $totalDed - $loan, 2),
            ];
        });

        if ($request->input('export') === 'csv') {
            return $this->csv("payroll_{$month}.csv", ['الموظف', 'القسم', 'الأساسي', 'البدلات', 'مكافآت', 'الإجمالي', 'خصومات', 'خصم غياب', 'خصم إجازات', 'سلف', 'الصافي'],
                $rows->map(function ($r) {
                    return [$r['emp']->name_ar, $r['dept'], $r['basic'], $r['allow'], $r['bonus'], $r['gross'], $r['deduction'], $r['absence'], $r['leave'], $r['loans'], $r['net']];
                }));
        }
        $totals = [];
        foreach (['basic', 'allow', 'bonus', 'gross', 'deduction', 'absence', 'leave', 'loans', 'net'] as $k) $totals[$k] = round($rows->sum($k), 2);
        return view('hr_reports.payroll', compact('rows', 'totals', 'month'));
    }
}
