<?php

namespace App\Http\Controllers;

use App\Models\financial_accounts;
use App\Models\truck_expense;
use App\Models\truck_trip;
use App\Models\waybill_truck;
use App\Support\AppSchema;
use App\Support\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * مصروفات الشاحنات
 * - تسجيل مصروف على شاحنة (وقود / صيانة / إطارات ...) ومعاه مرفق
 * - ترحيل اختياري: «مصروفات الشاحنات» مدين / الخزينة أو البنك دائن
 * - الحذف بيعكس القيد
 */
class TruckExpenseController extends Controller
{
    public function __construct()
    {
        AppSchema::truckExpenses();
    }

    private function u($path)
    {
        return url(LaravelLocalization::getCurrentLocale() . '/' . ltrim($path, '/'));
    }

    /** حسابات الدفع: الخزائن (تحت 5) والبنوك (تحت 78) */
    public static function payAccounts()
    {
        return financial_accounts::where('is_parent', 0)->whereIn('parent_account_number', [5, 78])
            ->orderBy('parent_account_number')->orderBy('name')->get(['id', 'name', 'account_number', 'parent_account_number']);
    }

    public function index(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $from = $request->input('start_at', date('Y-m-01'));
        $to   = $request->input('end_at', date('Y-m-d'));

        $q = truck_expense::with(['truck', 'user', 'payAccount'])
            ->whereDate('expense_date', '>=', $from)->whereDate('expense_date', '<=', $to);
        if ($request->filled('truck_id')) $q->where('truck_id', $request->truck_id);
        if ($request->filled('type'))     $q->where('type', $request->type);
        $expenses = $q->orderByDesc('expense_date')->orderByDesc('id')->get();

        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function () use ($expenses) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['التاريخ', 'الشاحنة', 'النوع', 'المبلغ', 'البيان', 'المورد', 'مدفوع من', 'سجلها']);
                foreach ($expenses as $e) {
                    fputcsv($out, [$e->expense_date->format('Y-m-d'), optional($e->truck)->plate_number, truck_expense::typeLabel($e->type),
                        $e->amount, $e->description, $e->vendor, optional($e->payAccount)->name ?: 'بدون ترحيل', optional($e->user)->name]);
                }
                fclose($out);
            }, "truck_expenses_{$from}_{$to}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $byTruck = $expenses->groupBy('truck_id')->map(function ($g) {
            return ['plate' => optional($g->first()->truck)->plate_number, 'count' => $g->count(), 'total' => round($g->sum('amount'), 2)];
        })->sortByDesc('total')->values();
        $byType = $expenses->groupBy('type')->map(function ($g, $k) {
            return ['type' => truck_expense::typeLabel($k), 'total' => round($g->sum('amount'), 2)];
        })->sortByDesc('total')->values();

        $trucks      = waybill_truck::orderBy('plate_number')->get(['id', 'plate_number', 'ownership']);
        $payAccounts = self::payAccounts();
        $types       = truck_expense::TYPES;
        $total       = round($expenses->sum('amount'), 2);
        return view('trucks.expenses', compact('expenses', 'byTruck', 'byType', 'trucks', 'payAccounts', 'types', 'total', 'from', 'to'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'truck_id'     => 'required|integer',
            'expense_date' => 'required|date',
            'type'         => 'required|in:' . implode(',', array_keys(truck_expense::TYPES)),
            'amount'       => 'required|numeric|min:0.01',
            'attachment'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'truck_id.required' => 'اختار الشاحنة',
            'amount.required'   => 'اكتب المبلغ',
            'amount.min'        => 'المبلغ لازم يكون أكبر من صفر',
            'attachment.mimes'  => 'المرفق لازم يكون PDF أو صورة',
        ]);
        $truck = waybill_truck::findOrFail($request->truck_id);

        $file = null;
        if ($request->hasFile('attachment')) {
            $f = $request->file('attachment');
            $file = 'exp_' . time() . rand(100, 999) . '.' . strtolower($f->getClientOriginalExtension());
            $f->move(public_path('assets/uploads/truck_expenses'), $file);
        }

        DB::transaction(function () use ($request, $truck, $file) {
            $pay = $request->filled('pay_account_id')
                ? financial_accounts::where('is_parent', 0)->whereIn('parent_account_number', [5, 78])->find($request->pay_account_id) : null;
            $exp = truck_expense::create([
                'truck_id'      => $truck->id,
                'truck_trip_id' => $request->truck_trip_id ?: null,
                'expense_date'  => $request->expense_date,
                'type'          => $request->type,
                'amount'        => round((float) $request->amount, 2),
                'description'   => $request->description,
                'vendor'        => $request->vendor,
                'attachment'    => $file,
                'pay_account_id'=> $pay->id ?? null,
                'user_id'       => Auth()->user()->id ?? null,
                'branchs_id'    => Auth()->user()->branchs_id ?? null,
            ]);
            if ($pay) $this->post($exp, false);
        });

        session()->flash('trip_ok', 'تم تسجيل المصروف على الشاحنة ' . $truck->plate_number);
        return redirect($this->u('trucks/expenses') . '?' . http_build_query(array_filter($request->only(['start_at', 'end_at']))));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $exp = truck_expense::lockForUpdate()->findOrFail($id);
            if ($exp->posted) $this->post($exp, true);
            $exp->delete();
        });
        session()->flash('trip_ok', 'تم حذف المصروف' . ' وعكس القيد لو كان مترحّل');
        return back();
    }

    /** مصروفات الشاحنات مدين / الخزينة أو البنك دائن (والعكس عند الحذف) */
    private function post(truck_expense $exp, $reverse)
    {
        $expAcc = $exp->expense_account_id ? financial_accounts::find($exp->expense_account_id)
            : Ledger::childAccount('مصروفات الشاحنات', 149, 176);
        if (!$expAcc || !$exp->pay_account_id) return;
        $plate = optional($exp->truck)->plate_number;
        $note = ($reverse ? 'إلغاء ' : '') . 'مصروف شاحنة ' . $plate . ' : ' . truck_expense::typeLabel($exp->type) . ($exp->description ? ' - ' . $exp->description : '');
        Ledger::entry($expAcc->id, $reverse ? 'credit' : 'debit', (float) $exp->amount, $note, true);
        Ledger::entry($exp->pay_account_id, $reverse ? 'debit' : 'credit', (float) $exp->amount, $note, true);
        if (!$reverse) $exp->update(['posted' => 1, 'expense_account_id' => $expAcc->id]);
    }
}
