<?php

namespace App\Http\Controllers;

use App\Models\financial_accounts;
use App\Models\transport_invoice;
use App\Support\AppSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * التقارير المحاسبية (من دفتر الحركات credittransactions + شجرة الحسابات)
 * - ميزان المراجعة
 * - قائمة الدخل (الأرباح والخسائر)
 * - الميزانية العمومية (المركز المالي)
 * - إقرار ضريبة القيمة المضافة
 *
 * أنواع الحسابات (acounts_types): 1 أصول - 2 خصوم - 3 إيرادات - 4 مصروفات - 5 حقوق ملكية
 * الحركات المعتمدة بس (save = 1) زي كشف الحساب
 */
class AccountingReportController extends Controller
{
    const TYPES = [1 => 'الأصول', 2 => 'الخصوم', 3 => 'الإيرادات', 4 => 'المصروفات', 5 => 'حقوق الملكية'];

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    private function period(Request $request)
    {
        return [$request->input('start_at', date('Y-01-01')), $request->input('end_at', date('Y-m-d'))];
    }

    /** طبيعة الحساب مدينة؟ (أصول / مصروفات) */
    private static function debitNature($type)
    {
        return in_array((int) $type, [1, 4]);
    }

    /** مجموع المدين والدائن لكل حساب (بين تاريخين أو قبل تاريخ) */
    private function sums($from = null, $to = null, $before = null, $branch = null)
    {
        $q = DB::table('credittransactions')->where('save', 1)
            ->select('customer_id', DB::raw('COALESCE(SUM(debtor),0) d'), DB::raw('COALESCE(SUM(creditor),0) c'))
            ->groupBy('customer_id');
        if ($before) $q->whereDate('created_at', '<', $before);
        if ($from)   $q->whereDate('created_at', '>=', $from);
        if ($to)     $q->whereDate('created_at', '<=', $to);
        if ($branch) $q->where('branchs_id', $branch);
        return $q->get()->keyBy('customer_id');
    }

    /**
     * شجرة الحسابات مع تجميع الأرصدة من الأبناء للآباء
     * $sets = ['open' => sums, 'move' => sums] → كل حساب فيه open_d, open_c, move_d, move_c (ذاتي + الأبناء)
     */
    private function tree(array $sets)
    {
        $accounts = financial_accounts::orderBy('account_number')->get(['id', 'name', 'account_number', 'parent_account_number', 'is_parent', 'account_type']);
        $byId = [];
        foreach ($accounts as $a) {
            $n = ['id' => $a->id, 'name' => $a->name, 'number' => $a->account_number, 'parent' => $a->parent_account_number,
                'type' => (int) $a->account_type, 'children' => []];
            foreach ($sets as $k => $s) {
                $row = $s->get($a->id);
                $n[$k . '_d'] = $row ? (float) $row->d : 0.0;
                $n[$k . '_c'] = $row ? (float) $row->c : 0.0;
            }
            $byId[$a->id] = $n;
        }
        // الحركات على حسابات مش موجودة في الشجرة (اتحذفت) بتروح لبند «غير مصنف»
        $orphan = ['id' => 0, 'name' => 'حسابات غير موجودة في الشجرة', 'number' => '—', 'parent' => null, 'type' => 0, 'children' => []];
        $hasOrphan = false;
        foreach ($sets as $k => $s) {
            $orphan[$k . '_d'] = 0.0; $orphan[$k . '_c'] = 0.0;
            foreach ($s as $id => $row) {
                if (!isset($byId[$id])) { $orphan[$k . '_d'] += (float) $row->d; $orphan[$k . '_c'] += (float) $row->c; $hasOrphan = true; }
            }
        }
        $roots = [];
        foreach ($byId as $id => $n) {
            $p = $n['parent'];
            if ($p && isset($byId[$p]) && $p != $id) $byId[$p]['children'][] = $id;
            else $roots[] = $id;
        }
        $keys = [];
        foreach ($sets as $k => $s) { $keys[] = $k . '_d'; $keys[] = $k . '_c'; }
        // تجميع (مع حماية من الحلقات)
        $done = [];
        $agg = function ($id, $depth = 0) use (&$agg, &$byId, &$done, $keys) {
            if (isset($done[$id]) || $depth > 20) return;
            foreach ($byId[$id]['children'] as $cid) {
                $agg($cid, $depth + 1);
                foreach ($keys as $k) $byId[$id][$k] += $byId[$cid][$k];
            }
            $done[$id] = true;
        };
        foreach ($roots as $r) $agg($r);
        // نوع الحساب: لو الفرع مالوش نوع ياخد نوع الجذر
        $setType = function ($id, $type, $depth = 0) use (&$setType, &$byId) {
            if ($depth > 20) return;
            if (!$byId[$id]['type']) $byId[$id]['type'] = $type;
            foreach ($byId[$id]['children'] as $cid) $setType($cid, $byId[$id]['type'], $depth + 1);
        };
        foreach ($roots as $r) $setType($r, $byId[$r]['type']);
        if ($hasOrphan) { $byId[0] = $orphan; $roots[] = 0; }
        return [$byId, $roots];
    }

    /** تسطيح الشجرة لصفوف (لحد مستوى معين) */
    private function flatten($byId, $roots, $maxLevel, $filter)
    {
        $rows = [];
        $walk = function ($id, $level) use (&$walk, &$rows, $byId, $maxLevel, $filter) {
            $n = $byId[$id];
            if (!$filter($n)) return;
            $n['level'] = $level;
            $n['has_children'] = count($n['children']) > 0;
            $rows[] = $n;
            if ($level < $maxLevel) foreach ($n['children'] as $c) $walk($c, $level + 1);
        };
        foreach ($roots as $r) $walk($r, 1);
        return $rows;
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

    private function branches()
    {
        try { return DB::table('branchs')->orderBy('id')->get(['id', 'name']); } catch (\Throwable $e) { return collect(); }
    }

    // ================= ميزان المراجعة =================
    public function trialBalance(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $level  = max(1, min(9, (int) $request->input('level', 9)));
        $branch = $request->input('branch') ?: null;
        [$byId, $roots] = $this->tree([
            'open' => $this->sums(null, null, $from, $branch),
            'move' => $this->sums($from, $to, null, $branch),
        ]);
        foreach ($byId as $id => $n) {
            $net = $n['open_d'] + $n['move_d'] - $n['open_c'] - $n['move_c'];
            $byId[$id]['close_d'] = $net > 0 ? $net : 0;
            $byId[$id]['close_c'] = $net < 0 ? -$net : 0;
            $on = $n['open_d'] - $n['open_c'];
            $byId[$id]['open_nd'] = $on > 0 ? $on : 0;
            $byId[$id]['open_nc'] = $on < 0 ? -$on : 0;
        }
        $hideZero = $request->input('hide_zero', '1') === '1';
        $rows = $this->flatten($byId, $roots, $level, function ($n) use ($hideZero) {
            return !$hideZero || abs($n['open_d']) + abs($n['open_c']) + abs($n['move_d']) + abs($n['move_c']) > 0.004;
        });
        $totals = ['open_nd' => 0, 'open_nc' => 0, 'move_d' => 0, 'move_c' => 0, 'close_d' => 0, 'close_c' => 0];
        foreach ($roots as $r) foreach ($totals as $k => $v) $totals[$k] += $byId[$r][$k];

        if ($request->input('export') === 'csv') {
            return $this->csv("trial_balance_{$from}_{$to}.csv",
                ['رقم الحساب', 'اسم الحساب', 'المستوى', 'رصيد أول المدة مدين', 'رصيد أول المدة دائن', 'حركة مدين', 'حركة دائن', 'الرصيد مدين', 'الرصيد دائن'],
                array_map(function ($r) { return [$r['number'], $r['name'], $r['level'], round($r['open_nd'], 2), round($r['open_nc'], 2), round($r['move_d'], 2), round($r['move_c'], 2), round($r['close_d'], 2), round($r['close_c'], 2)]; }, $rows));
        }
        $branches = $this->branches();
        return view('accounting_reports.trial_balance', compact('rows', 'totals', 'from', 'to', 'level', 'branches', 'hideZero'));
    }

    // ================= قائمة الدخل =================
    public function incomeStatement(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        $level  = max(1, min(9, (int) $request->input('level', 3)));
        $branch = $request->input('branch') ?: null;
        [$byId, $roots] = $this->tree(['move' => $this->sums($from, $to, null, $branch)]);
        $sections = [];
        foreach ([3 => 'الإيرادات', 4 => 'المصروفات'] as $type => $label) {
            $rootsT = array_values(array_filter($roots, function ($r) use ($byId, $type) { return $byId[$r]['type'] == $type; }));
            foreach ($byId as $id => $n) {
                $byId[$id]['amount'] = $type == 3 ? $n['move_c'] - $n['move_d'] : $n['move_d'] - $n['move_c'];
            }
            $rows = $this->flatten($byId, $rootsT, $level, function ($n) { return abs($n['amount'] ?? 0) > 0.004; });
            $total = 0; foreach ($rootsT as $r) $total += $type == 3 ? $byId[$r]['move_c'] - $byId[$r]['move_d'] : $byId[$r]['move_d'] - $byId[$r]['move_c'];
            // نحسب amount لكل صف حسب نوع القسم
            foreach ($rows as &$row) $row['amount'] = $type == 3 ? $row['move_c'] - $row['move_d'] : $row['move_d'] - $row['move_c'];
            unset($row);
            $sections[$type] = ['label' => $label, 'rows' => $rows, 'total' => round($total, 2)];
        }
        $net = round($sections[3]['total'] - $sections[4]['total'], 2);

        if ($request->input('export') === 'csv') {
            $out = [];
            foreach ($sections as $s) {
                $out[] = [$s['label'], '', ''];
                foreach ($s['rows'] as $r) $out[] = [$r['number'], str_repeat('  ', $r['level'] - 1) . $r['name'], round($r['amount'], 2)];
                $out[] = ['', 'إجمالي ' . $s['label'], $s['total']];
            }
            $out[] = ['', 'صافي الربح / الخسارة', $net];
            return $this->csv("income_statement_{$from}_{$to}.csv", ['رقم الحساب', 'البيان', 'المبلغ'], $out);
        }
        $branches = $this->branches();
        return view('accounting_reports.income_statement', compact('sections', 'net', 'from', 'to', 'level', 'branches'));
    }

    // ================= الميزانية العمومية =================
    public function balanceSheet(Request $request)
    {
        $this->setLocale();
        $to    = $request->input('end_at', date('Y-m-d'));
        $level = max(1, min(9, (int) $request->input('level', 3)));
        [$byId, $roots] = $this->tree(['all' => $this->sums(null, $to)]);
        foreach ($byId as $id => $n) {
            $byId[$id]['amount'] = self::debitNature($n['type']) ? $n['all_d'] - $n['all_c'] : $n['all_c'] - $n['all_d'];
        }
        $sections = [];
        foreach ([1 => 'الأصول', 2 => 'الخصوم (الالتزامات)', 5 => 'حقوق الملكية'] as $type => $label) {
            $rootsT = array_values(array_filter($roots, function ($r) use ($byId, $type) { return $byId[$r]['type'] == $type; }));
            $rows = $this->flatten($byId, $rootsT, $level, function ($n) { return abs($n['amount']) > 0.004; });
            $total = 0; foreach ($rootsT as $r) $total += $byId[$r]['amount'];
            $sections[$type] = ['label' => $label, 'rows' => $rows, 'total' => round($total, 2)];
        }
        // صافي ربح / خسارة الفترة (الإيرادات − المصروفات) لحد التاريخ
        $rev = 0; $exp = 0; $other = 0;
        foreach ($roots as $r) {
            if ($byId[$r]['type'] == 3) $rev += $byId[$r]['all_c'] - $byId[$r]['all_d'];
            elseif ($byId[$r]['type'] == 4) $exp += $byId[$r]['all_d'] - $byId[$r]['all_c'];
            elseif (!in_array($byId[$r]['type'], [1, 2, 5])) $other += $byId[$r]['all_d'] - $byId[$r]['all_c'];
        }
        $profit = round($rev - $exp, 2);
        $liabEq = round($sections[2]['total'] + $sections[5]['total'] + $profit, 2);
        $diff   = round($sections[1]['total'] - $liabEq, 2);
        return view('accounting_reports.balance_sheet', compact('sections', 'profit', 'liabEq', 'diff', 'to', 'level', 'rev', 'exp', 'other'));
    }

    // ================= إقرار ضريبة القيمة المضافة =================
    public function vat(Request $request)
    {
        $this->setLocale();
        [$from, $to] = $this->period($request);
        if (!$request->filled('start_at')) {
            // افتراضي: الربع الحالي
            $q = (int) ceil(date('n') / 3);
            $from = date('Y') . '-' . str_pad(($q - 1) * 3 + 1, 2, '0', STR_PAD_LEFT) . '-01';
            $to = date('Y-m-t', strtotime(date('Y') . '-' . str_pad($q * 3, 2, '0', STR_PAD_LEFT) . '-01'));
        }

        // حسابات الضريبة: الحساب 102 وأي حساب اسمه فيه «ضريبة القيمة المضافة» + أبناؤهم
        $vatIds = financial_accounts::where('id', 102)->orWhere('name', 'like', '%ضريبة القيمة المضافة%')->pluck('id')->all();
        $all = $vatIds;
        for ($i = 0; $i < 5; $i++) {
            $kids = financial_accounts::whereIn('parent_account_number', $all)->pluck('id')->all();
            $new = array_diff($kids, $all);
            if (!$new) break;
            $all = array_merge($all, $new);
        }
        $moves = DB::table('credittransactions')->where('save', 1)->whereIn('customer_id', $all ?: [0])
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->orderBy('created_at')->orderBy('id')->get(['id', 'created_at', 'note', 'debtor', 'creditor', 'name', 'tax']);
        $output = round($moves->sum('creditor'), 2);   // ضريبة المخرجات (مبيعات)
        $input  = round($moves->sum('debtor'), 2);     // ضريبة المدخلات (مشتريات / مصروفات) + أي عكس
        $net    = round($output - $input, 2);

        // فواتير النقل
        AppSchema::transport();
        $ti = transport_invoice::where('status', transport_invoice::ACTIVE)->whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to)->get();
        $transport = [
            'std_taxable'  => round($ti->where('vat_category', '!=', 'Z')->sum('taxable'), 2),
            'std_vat'      => round($ti->where('vat_category', '!=', 'Z')->sum('vat_amount'), 2),
            'zero_taxable' => round($ti->where('vat_category', 'Z')->sum('taxable'), 2),
            'count'        => $ti->count(),
        ];
        // فواتير المبيعات القديمة في النظام (لو موجودة)
        $sales = ['total' => 0, 'count' => 0];
        try {
            $inv = DB::table('invoices')->where('save', 1)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->selectRaw('COUNT(*) c, COALESCE(SUM(COALESCE(Bank_transfer,0)+COALESCE(creaditamount,0)+COALESCE(bankamount,0)+COALESCE(cashamount,0)),0) t')->first();
            $sales = ['total' => round((float) $inv->t, 2), 'count' => (int) $inv->c];
        } catch (\Throwable $e) { report($e); }

        if ($request->input('export') === 'csv') {
            return $this->csv("vat_{$from}_{$to}.csv", ['التاريخ', 'البيان', 'مدين (مدخلات)', 'دائن (مخرجات)'],
                $moves->map(function ($m) { return [substr($m->created_at, 0, 10), $m->note, $m->debtor, $m->creditor]; }));
        }
        return view('accounting_reports.vat', compact('moves', 'output', 'input', 'net', 'transport', 'sales', 'from', 'to'));
    }
}
