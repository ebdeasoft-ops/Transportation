<?php

namespace App\Http\Controllers;

use App\Models\Avt;
use App\Models\credittransactions;
use App\Models\customers;
use App\Models\financial_accounts;
use App\Models\system_setting;
use App\Models\transport_invoice;
use App\Models\transport_invoice_item;
use App\Models\truck_trip;
use App\Support\AppSchema;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * فاتورة نقل ضريبية
 * - بتتعمل من الأحمال (الشحنات) اللي اتفرغت لعميل معين + بنود يدوية
 * - ضريبة القيمة المضافة من شاشة الضريبة (avts id=1)
 * - QR هيئة الزكاة
 * - ترحيل تلقائي: العميل مدين بالإجمالي / إيرادات النقل دائن بالصافي / ضريبة القيمة المضافة دائن بالضريبة
 * - الإلغاء بيعكس القيد ويرجّع الأحمال "غير مفوترة"
 */
class TransportInvoiceController extends Controller
{
    public function __construct()
    {
        AppSchema::transport();
    }

    private function u($path)
    {
        return url(LaravelLocalization::getCurrentLocale() . '/' . ltrim($path, '/'));
    }

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    private function customers()
    {
        return financial_accounts::where('orginal_type', 1)->orderBy('name')->get(['id', 'name', 'account_number', 'orginal_id']);
    }

    /** نسبة الضريبة من شاشة الضريبة (0.15) */
    public static function vatRate()
    {
        $r = optional(Avt::find(1))->AVT;
        if ($r === null) return 0.15;
        $r = (float) $r;
        return $r > 1 ? $r / 100 : $r; // لو متسجلة 15 بدل 0.15
    }

    /** بيانات العميل من جدول العملاء (الرقم الضريبي / العنوان / الجوال) */
    private function customerInfo($account)
    {
        $c = $account && $account->orginal_id ? customers::find($account->orginal_id) : null;
        $vat = $c ? preg_replace('/\D/', '', (string) $c->tax_no) : '';
        $phone = $c ? (string) $c->phone : '';
        return [
            'vat'     => ($vat && $vat !== '0') ? $vat : null,
            'address' => $c && $c->address && $c->address !== 'Client Address' ? $c->address : null,
            'phone'   => $phone && !str_contains($phone, '*') ? $phone : null,
        ];
    }

    // ================= الفواتير السابقة =================
    public function index(Request $request)
    {
        $this->setLocale();
        $from = $request->input('start_at', date('Y-m-01'));
        $to   = $request->input('end_at', date('Y-m-d'));

        $q = transport_invoice::with('user')->whereDate('issue_date', '>=', $from)->whereDate('issue_date', '<=', $to);
        if ($request->filled('customer_account_id')) $q->where('customer_account_id', $request->customer_account_id);
        if ($request->filled('status'))              $q->where('status', $request->status);
        if ($request->filled('q')) {
            $s = trim($request->q);
            $q->where(function ($w) use ($s) {
                $w->where('invoice_no', $s)->orWhere('customer_name', 'like', "%$s%")->orWhere('po_number', 'like', "%$s%");
            });
        }
        $invoices  = $q->orderByDesc('invoice_no')->get();
        $active    = $invoices->where('status', transport_invoice::ACTIVE);
        $summary   = [
            'count'     => $active->count(),
            'cancelled' => $invoices->where('status', transport_invoice::CANCELLED)->count(),
            'taxable'   => round($active->sum('taxable'), 2),
            'vat'       => round($active->sum('vat_amount'), 2),
            'total'     => round($active->sum('total'), 2),
        ];
        $customers = $this->customers();
        return view('transport_invoices.index', compact('invoices', 'summary', 'customers', 'from', 'to'));
    }

    // ================= فاتورة جديدة =================
    public function create(Request $request)
    {
        $this->setLocale();
        $customers = $this->customers();
        $account   = $request->filled('customer_account_id')
            ? financial_accounts::where('orginal_type', 1)->find($request->customer_account_id) : null;

        $trips = collect();
        $info  = ['vat' => null, 'address' => null, 'phone' => null];
        if ($account) {
            $info = $this->customerInfo($account);
            $tq = truck_trip::with(['truck', 'driver'])
                ->where('customer_account_id', $account->id)
                ->whereNull('transport_invoice_id');
            if (!$request->boolean('include_loaded')) $tq->where('status', truck_trip::UNLOADED);
            if ($request->filled('start_at')) $tq->whereDate('loading_at', '>=', $request->start_at);
            if ($request->filled('end_at'))   $tq->whereDate('loading_at', '<=', $request->end_at);
            $trips = $tq->orderBy('loading_at')->get();
        }
        $vatRate = self::vatRate();
        $zeroReasons = transport_invoice::ZERO_REASONS;
        $nextNo  = (int) transport_invoice::max('invoice_no') + 1;
        return view('transport_invoices.create', compact('customers', 'account', 'trips', 'info', 'vatRate', 'nextNo', 'zeroReasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_account_id' => 'required|integer',
            'issue_date'          => 'required|date',
            'discount'            => 'nullable|numeric|min:0',
            'trip_ids'            => 'nullable|array',
            'trip_price.*'        => 'nullable|numeric|min:0',
            'line_desc.*'         => 'nullable|max:255',
            'line_qty.*'          => 'nullable|numeric|min:0',
            'line_price.*'        => 'nullable|numeric|min:0',
            'customer_vat'        => 'nullable|regex:/^\d{15}$/',
            'vat_category'        => 'nullable|in:S,Z',
            'vat_exempt_code'     => 'required_if:vat_category,Z|nullable|in:' . implode(',', array_keys(transport_invoice::ZERO_REASONS)),
        ], [
            'vat_exempt_code.required_if'  => 'اختار سبب النسبة الصفرية',
            'customer_account_id.required' => 'اختار العميل',
            'issue_date.required'          => 'حدد تاريخ الفاتورة',
            'customer_vat.regex'           => 'الرقم الضريبي للعميل لازم يكون 15 رقم',
        ]);

        $account = financial_accounts::where('orginal_type', 1)->findOrFail($request->customer_account_id);
        $incVat  = $request->boolean('prices_include_vat');
        // الضريبة: 15% (من شاشة الضريبة) أو صفرية
        $zero    = $request->input('vat_category') === 'Z';
        $rate    = $zero ? 0 : self::vatRate();

        // ---- البنود ----
        $lines = [];
        $tripIds = array_map('intval', (array) $request->input('trip_ids', []));
        $trips = $tripIds ? truck_trip::with('truck')->whereIn('id', $tripIds)->get()->keyBy('id') : collect();
        foreach ($tripIds as $tid) {
            $t = $trips->get($tid);
            if (!$t) continue;
            $price = (float) ($request->input("trip_price.$tid") ?? $t->price);
            if ($incVat) $price = $price / (1 + $rate);
            $desc = 'نقل ' . ($t->load_type ?: 'بضائع') . ' من ' . $this->place($t->from_region, $t->from_city) . ' إلى ' . $this->place($t->to_region, $t->to_city)
                  . ($t->load_weight ? ' - الوزن: ' . $t->load_weight : '');
            $lines[] = [
                'truck_trip_id' => $t->id,
                'description'   => mb_substr($desc, 0, 255),
                'trip_date'     => $t->loading_at,
                'plate_number'  => optional($t->truck)->plate_number,
                'route_from'    => $this->place($t->from_region, $t->from_city),
                'route_to'      => $this->place($t->to_region, $t->to_city),
                'waybill_no'    => $t->waybill_no ?: $t->reference_no,
                'qty'           => 1,
                'unit_price'    => round($price, 2),
                'amount'        => round($price, 2),
            ];
        }
        foreach ((array) $request->input('line_desc', []) as $i => $d) {
            $d = trim((string) $d);
            $qty = (float) ($request->input("line_qty.$i") ?: 1);
            $price = (float) $request->input("line_price.$i");
            if ($d === '' || $price <= 0) continue;
            if ($incVat) $price = $price / (1 + $rate);
            $lines[] = [
                'truck_trip_id' => null, 'description' => mb_substr($d, 0, 255), 'trip_date' => null, 'plate_number' => null,
                'route_from' => null, 'route_to' => null, 'waybill_no' => null,
                'qty' => $qty, 'unit_price' => round($price, 2), 'amount' => round($qty * $price, 2),
            ];
        }
        if (!$lines) {
            return back()->withInput()->withErrors(['lines' => 'اختار شحنة واحدة على الأقل أو اكتب بند يدوي بسعر']);
        }

        $subtotal = round(array_sum(array_column($lines, 'amount')), 2);
        $discount = min(round((float) $request->discount, 2), $subtotal);
        $taxable  = round($subtotal - $discount, 2);
        $vat      = round($taxable * $rate, 2);
        $total    = round($taxable + $vat, 2);
        $info     = $this->customerInfo($account);

        try {
            $invoice = DB::transaction(function () use ($request, $account, $lines, $subtotal, $discount, $taxable, $vat, $total, $rate, $incVat, $info, $tripIds, $zero) {
                // الشحنات لازم تكون لسه مش مفوترة (قفل عشان محدش يفوترها مرتين)
                if ($tripIds) {
                    $taken = truck_trip::whereIn('id', $tripIds)->lockForUpdate()->whereNotNull('transport_invoice_id')->count();
                    if ($taken) throw new \RuntimeException('فيه شحنات اتعملها فاتورة قبل كده. حدّث الصفحة وحاول تاني.');
                }
                $no = (int) transport_invoice::lockForUpdate()->max('invoice_no') + 1;

                $inv = transport_invoice::create([
                    'invoice_no'          => $no,
                    'customer_account_id' => $account->id,
                    'customer_name'       => $account->name,
                    'customer_vat'        => $request->customer_vat ?: $info['vat'],
                    'customer_cr'         => $request->customer_cr,
                    'customer_address'    => $request->customer_address ?: $info['address'],
                    'customer_phone'      => $request->customer_phone ?: $info['phone'],
                    'issue_date'          => Carbon::parse($request->issue_date),
                    'supply_from'         => $request->supply_from ?: null,
                    'supply_to'           => $request->supply_to ?: null,
                    'subtotal'            => $subtotal,
                    'discount'            => $discount,
                    'taxable'             => $taxable,
                    'vat_rate'            => $rate,
                    'vat_category'        => $zero ? 'Z' : 'S',
                    'vat_exempt_code'     => $zero ? $request->vat_exempt_code : null,
                    'vat_exempt_reason'   => $zero ? (transport_invoice::ZERO_REASONS[$request->vat_exempt_code] ?? null) : null,
                    'vat_amount'          => $vat,
                    'total'               => $total,
                    'prices_include_vat'  => $incVat ? 1 : 0,
                    'po_number'           => $request->po_number,
                    'notes'               => $request->notes,
                    'status'              => transport_invoice::ACTIVE,
                    'user_id'             => Auth()->user()->id ?? null,
                    'branchs_id'          => Auth()->user()->branchs_id ?? null,
                ]);
                foreach ($lines as $l) {
                    transport_invoice_item::create($l + ['transport_invoice_id' => $inv->id]);
                }
                // ربط الشحنات بالفاتورة (ورقم الفاتورة يتكتب في الشحنة لو فاضي)
                foreach ($tripIds as $tid) {
                    $t = truck_trip::find($tid);
                    if (!$t) continue;
                    $t->transport_invoice_id = $inv->id;
                    if (!$t->invoice_number) $t->invoice_number = (string) $no;
                    if (!(float) $t->price) {
                        $item = collect($lines)->firstWhere('truck_trip_id', $tid);
                        $t->price = $item['amount'] ?? 0;
                    }
                    $t->save();
                }

                $this->postEntries($inv, false);
                return $inv;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['trips' => $e->getMessage()]);
        }

        session()->flash('inv_ok', 'تم حفظ فاتورة النقل رقم ' . $invoice->invoice_no . ' وترحيل القيد');
        return redirect($this->u('transport-invoices/' . $invoice->id));
    }

    // ================= عرض / طباعة =================
    public function show($id)
    {
        $this->setLocale();
        $inv = transport_invoice::with(['items', 'user'])->findOrFail($id);
        $sys = system_setting::find(1);
        $qr  = $inv->zatcaQr($sys->name_ar ?? '', $sys->Tax ?? '');
        $words = $this->amountInWords($inv->total);
        return view('transport_invoices.print', compact('inv', 'sys', 'qr', 'words'));
    }

    // ================= إلغاء (قيد عكسي) =================
    public function cancel(Request $request, $id)
    {
        $request->validate(['cancel_reason' => 'required|max:255'], ['cancel_reason.required' => 'اكتب سبب الإلغاء']);
        DB::transaction(function () use ($request, $id) {
            $inv = transport_invoice::lockForUpdate()->findOrFail($id);
            if ($inv->status == transport_invoice::CANCELLED) return;
            if ($inv->posted) $this->postEntries($inv, true);
            // الشحنات ترجع "غير مفوترة"
            foreach (truck_trip::where('transport_invoice_id', $inv->id)->get() as $t) {
                $t->transport_invoice_id = null;
                if ($t->invoice_number == (string) $inv->invoice_no) $t->invoice_number = null;
                $t->save();
            }
            $inv->update([
                'status'        => transport_invoice::CANCELLED,
                'posted'        => 0,
                'cancelled_at'  => Carbon::now('Asia/Riyadh'),
                'cancel_reason' => $request->cancel_reason,
                'cancelled_by'  => Auth()->user()->id ?? null,
            ]);
        });
        session()->flash('inv_ok', 'تم إلغاء الفاتورة وعكس القيد والشحنات رجعت غير مفوترة');
        return redirect($this->u('transport-invoices/' . $id));
    }

    // ================= القيود =================
    /** حساب إيرادات النقل (بيتعمل لوحده تحت "المبيعات" لو مش موجود) */
    private function revenueAccount()
    {
        $acc = financial_accounts::where('name', 'إيرادات النقل')->where('is_parent', 0)->first();
        if ($acc) return $acc;
        $parent = financial_accounts::where('is_parent', 1)->where('name', 'المبيعات')->first() ?: financial_accounts::find(111);
        if ($parent) {
            $last = financial_accounts::where('parent_account_number', $parent->id)->max('account_number');
            return financial_accounts::create([
                'name'                  => 'إيرادات النقل',
                'account_type'          => financial_accounts::where('parent_account_number', $parent->id)->value('account_type') ?: $parent->account_type,
                'is_parent'             => 0,
                'parent_account_number' => $parent->id,
                'account_number'        => $last ? $last + 1 : $parent->account_number * 10 + 1,
                'start_balance_status'  => 3,
                'start_balance'         => 0,
                'current_balance'       => 0,
                'notes'                 => 'حساب إيرادات فواتير النقل (اتعمل تلقائياً)',
                'active'                => 1,
                'added_by'              => Auth()->user()->id ?? 1,
                'date'                  => Carbon::now('Asia/Riyadh')->toDateString(),
                'com_code'              => 0,
            ]);
        }
        return financial_accounts::find(112);
    }

    private function vatAccount()
    {
        $a = financial_accounts::find(102);
        if ($a && str_contains($a->name, 'ضريبة')) return $a;
        return financial_accounts::where('is_parent', 0)->where('name', 'like', '%ضريبة القيمة المضافة%')->first() ?: $a;
    }

    /**
     * ترحيل (أو عكس) قيد الفاتورة
     * العميل: مدين بالإجمالي | إيرادات النقل: دائن بالصافي | الضريبة: دائن بالضريبة
     */
    private function postEntries(transport_invoice $inv, $reverse)
    {
        $note = ($reverse ? 'إلغاء ' : '') . 'فاتورة نقل ضريبية رقم : ' . $inv->invoice_no;
        $cust = financial_accounts::find($inv->customer_account_id);
        $rev  = $this->revenueAccount();
        $vatA = $this->vatAccount();

        $entries = [
            [$cust, 'debit',  (float) $inv->total],
            [$rev,  'credit', (float) $inv->taxable],
            [$vatA, 'credit', (float) $inv->vat_amount],
        ];
        foreach ($entries as [$acc, $side, $amount]) {
            if (!$acc || $amount <= 0) continue;
            // العكس: الجانب بيتقلب
            $realSide = $reverse ? ($side === 'debit' ? 'credit' : 'debit') : $side;
            $this->entry($acc, $realSide, $amount, $note, $inv, $acc->id == optional($vatA)->id);
        }
        // رصيد العميل في جدول العملاء
        if ($cust && $cust->orginal_id) {
            $c = customers::find($cust->orginal_id);
            if ($c) {
                $c->Balance = (float) $c->Balance + ($reverse ? -1 : 1) * (float) $inv->total;
                $c->save();
            }
        }
        if (!$reverse) $inv->update(['posted' => 1]);
    }

    /** سطر في دفتر الحركات + تحديث رصيد الحساب (نفس طريقة النظام) */
    private function entry(financial_accounts $acc, $side, $amount, $note, transport_invoice $inv, $isVat)
    {
        $acc = financial_accounts::lockForUpdate()->find($acc->id);
        $debit  = $side === 'debit' ? $amount : 0;
        $credit = $side === 'credit' ? $amount : 0;
        // حسابات العملاء طبيعتها مدينة، والإيرادات والضريبة طبيعتها دائنة
        $natureDebit = $acc->id == $inv->customer_account_id;
        $newBalance  = (float) $acc->current_balance + ($natureDebit ? ($debit - $credit) : ($credit - $debit));

        $acc->current_balance  = round($newBalance, 2);
        $acc->debtor_current   = round((float) $acc->debtor_current + $debit, 2);
        $acc->creditor_current = round((float) $acc->creditor_current + $credit, 2);
        $acc->save();

        credittransactions::create([
            'user_id'         => Auth()->user()->id ?? 1,
            'customer_id'     => $acc->id,
            'recive_amount'   => $amount,
            'branchs_id'      => Auth()->user()->branchs_id ?? ($inv->branchs_id ?: 1),
            'pay_method'      => 'Credit',
            'Pay_Method_Name' => 'آجل',
            'note'            => $note,
            'currentblance'   => round($newBalance, 2),
            'created_at'      => Carbon::now()->addHours(3),
            'updated_at'      => Carbon::now()->addHours(3),
            'orginal_id'      => 0,
            'debtor'          => $debit,
            'creditor'        => $credit,
            'vat'             => $isVat ? 1 : 0,
            'name'            => $inv->customer_name,
            'tax'             => $inv->customer_vat,
            'decument_id'     => $inv->id,
        ]);
    }

    /** "الرياض - الدمام" (من غير تكرار لو المدينة نفس المنطقة) */
    private function place($region, $city)
    {
        $city = trim((string) $city);
        return ($city && $city !== $region) ? $region . ' - ' . $city : (string) $region;
    }

    private function amountInWords($amount)
    {
        $whole = (int) floor($amount);
        $halala = (int) round(($amount - $whole) * 100);
        try {
            $w = \Hassanhelfi\NumberToArabic\NumToArabic::number2Word($whole) . ' ريال';
            if ($halala) $w .= ' و ' . \Hassanhelfi\NumberToArabic\NumToArabic::number2Word($halala) . ' هللة';
            return $w . ' فقط لا غير';
        } catch (\Throwable $e) {
            return '';
        }
    }
}
