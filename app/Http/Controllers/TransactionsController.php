<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\shipments_details;
use App\Models\credittransactions;
use App\Models\Covenant_liquidation;
use App\Models\financial_accounts;
use App\Models\purchase_liquidation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Exports\liquidation;
use Maatwebsite\Excel\Facades\Excel;

class TransactionsController extends Controller
{
    /* =====================================================================
     * [مراجعة 2026-09-28] دوال مساعدة مشتركة لتصفية الشحنات / المشتريات / التحويلات
     * ===================================================================== */

    /** رسالة خطأ واضحة للواجهة (بتتعرض في SweetAlert بدل "عذراً حدث خطأ") */
    private function fail($message, $code = 422)
    {
        return response()->json(['message' => $message], $code);
    }

    /** حساب العهدة (أب 82) الخاص بفرع معين */
    private function custodyAccount($branchId)
    {
        return financial_accounts::where('parent_account_number', 82)
            ->where('candidate_branch', $branchId)
            ->first();
    }

    /**
     * عكس القيود اللي اترحلت فعلاً لتصفية معينة (شحنات أو مشتريات) بدل ما نعكس
     * مبلغ التصفية كله. كده لو التصفية كانت مرحّلة جزئياً (حالة 3) الرصيد يرجع صح.
     * صيغة الملاحظة وقت الترحيل: "...|  تصفية رقم     |  : {id}" (البحث بنهاية النص عشان
     * تصفية 13 ماتمسكش قيود تصفية 130).
     */
    private function reverseLiquidationPostings($liquidationId)
    {
        $entries = credittransactions::where('type', 11)
            ->where('customer_id', '!=', 0)
            ->where('note', 'LIKE', '%|  تصفية رقم     |  : ' . (int) $liquidationId)
            ->get();

        $reversed = 0;
        foreach ($entries as $entry) {
            $account = financial_accounts::lockForUpdate()->find($entry->customer_id);
            if ($account) {
                $account->update([
                    'current_balance'  => $account->current_balance + $entry->creditor - $entry->debtor,
                    'creditor_current' => $account->creditor_current - $entry->creditor,
                    'debtor_current'   => $account->debtor_current - $entry->debtor,
                ]);
            }
            $reversed += $entry->creditor - $entry->debtor;
            $entry->update(['customer_id' => 0]); // نفس طريقة النظام في إلغاء القيد
        }

        return $reversed;
    }

    /**
     * تحديث حالة التصفية بناءً على حالة بنودها:
     * كل البنود مرحّلة (2) => 2 | فيه بنود مرحّلة وبنود لسه => 3 | غير كده => 1
     */
    private function refreshLiquidationStatus($liquidationId)
    {
        $liq = Covenant_liquidation::find($liquidationId);
        if (!$liq) {
            return;
        }
        $items = $liq->type == 2
            ? purchase_liquidation::where('Transactions_id', $liquidationId)
            : shipments_details::where('Transactions_id', $liquidationId);

        $all     = (clone $items)->count();
        $posted  = (clone $items)->where('status', 2)->count();
        $pending = (clone $items)->where('status', 0)->count();

        $status = 1;
        if ($all > 0 && $posted == $all) {
            $status = 2;
        } elseif ($posted > 0) {
            $status = 3;
        }

        $liq->update([
            'status'        => $status,
            'manager_check' => $pending == 0 && $all > 0 ? 1 : 0,
        ]);
    }

    /** هل البند ده له قيد مرحّل فعلاً؟ (احتياط للبيانات القديمة اللي اترحلت وحالتها فضلت 1) */
    private function itemIsPosted($itemId, $isPurchase)
    {
        $prefix = ($isPurchase ? '|  مشتريات رقم     | ' : '|  شحنة رقم     | ') . ' : ' . (int) $itemId . '|';
        return credittransactions::where('type', 11)
            ->where('customer_id', '!=', 0)
            ->where('note', 'LIKE', $prefix . '%')
            ->exists();
    }

    /** ترحيل بند واحد (شحنة أو مشترى) على حساب العهدة وإنشاء قيد الحركة */
    private function postLiquidationItem($item, $liquidationId, $isPurchase)
    {
        $account = $this->custodyAccount($item->branchs_id);
        if (!$account) {
            throw new \RuntimeException('لا يوجد حساب عهدة مربوط بفرع هذا البند - راجع شجرة الحسابات (حساب تحت 82)');
        }

        $total = $isPurchase
            ? (float) $item->price_filtering
            : (float) $item->price_shipment + (float) $item->Ext + (float) $item->Daily;

        $account->update([
            'current_balance'  => $account->current_balance - $total,
            'creditor_current' => $account->creditor_current + $total,
        ]);
        $account->refresh();

        Covenant_liquidation::find($liquidationId)->update([
            'currentblance' => $account->debtor_current - $account->creditor_current,
        ]);

        credittransactions::create([
            'attachments'     => '',
            'orginal_type'    => 10,
            'user_id'         => Auth()->user()->id,
            'customer_id'     => $account->id,
            'recive_amount'   => $total,
            'branchs_id'      => Auth()->user()->branchs_id,
            'pay_method'      => __('home.Bank_transfer'),
            'created_at'      => \Carbon\Carbon::now()->addHours(3),
            'date_export'     => date('Y/m/d'),
            'note'            => ($isPurchase ? '|  مشتريات رقم     | ' : '|  شحنة رقم     | ') . ' : ' . (string) $item->id
                                 . '|  تصفية رقم     | ' . ' : ' . (string) $liquidationId,
            'Pay_Method_Name' => __('home.Bank_transfer'),
            'updated_at'      => \Carbon\Carbon::now()->addHours(3),
            'orginal_id'      => $account->orginal_id ?? 0,
            'debtor'          => 0,
            'creditor'        => $total,
            'Transactions'    => 0,
            'type'            => 11,
        ]);

        $item->update(['status' => 2]);
    }

    /** عكس سند تحويل (الطرفين) وإخفاء تصفية التحويل المرتبطة بيه */
    private function voidTransfer(Transactions $Transactions)
    {
        $mantob = financial_accounts::find($Transactions->mantob);
        if ($mantob) {
            $mantob->update([
                'current_balance' => $mantob->current_balance - $Transactions->price,
                'debtor_current'  => $mantob->debtor_current - $Transactions->price,
            ]);
        }

        $partner = financial_accounts::find($Transactions->partner);
        if ($partner) {
            $partner->update([
                'current_balance'  => $partner->current_balance - $Transactions->price,
                'creditor_current' => $partner->creditor_current - $Transactions->price,
            ]);
        }

        // [إصلاح] لازم type=3: جدول التصفيات فيه أنواع تانية (7/8/9/10) بتستخدم id_trasction
        // لرقم قيد في credittransactions، فمن غير الشرط ده ممكن نلمس تصفية مالهاش علاقة.
        Covenant_liquidation::where('id_trasction', $Transactions->id)->where('type', 3)->update(['save' => 0]);
        credittransactions::where('Transactions', $Transactions->id)->update(['customer_id' => 0]);
        Transactions::where('id', $Transactions->id)->delete();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function delete_transction($id)
    {
        $Transactions = Transactions::find($id);

        if ($Transactions) {
            // [مراجعة] الحذف كله (عكس الطرفين + إخفاء تصفية التحويل) كوحدة واحدة
            DB::transaction(function () use ($Transactions) {
                $this->voidTransfer($Transactions);
            });
        }

        $data = Transactions::orderBy('id', 'desc')->paginate(20);

        return view('transfers.ajax_previousTransfers', compact('data'));
    }

    public function confirm_liquidation_from_owner($id)
    {
        $Covenant_liquidation_data = Covenant_liquidation::find($id);
        if (!$Covenant_liquidation_data) {
            return $this->fail('التصفية غير موجودة', 404);
        }

        // [مراجعة] منع التأكيد مرتين (دبل كليك / صفحتين مفتوحين) - كان بيرحّل نفس البنود مرتين
        if ($Covenant_liquidation_data->owner_confirm == 1) {
            return 1;
        }
        if (!in_array($Covenant_liquidation_data->type, [1, 2])) {
            return $this->fail('هذا النوع من التصفيات لا يحتاج اعتماد');
        }

        try {
            // [مراجعة] العملية كلها معاملة واحدة: لو بند واحد فشل (مثلاً مفيش حساب عهدة للفرع)
            // مفيش ولا بند يترحّل ولا التصفية تتعلّم إنها اتأكدت. قبل كده كان بيعدّي البند بصمت
            // ويعلّم التصفية "تمت" من غير قيد.
            DB::transaction(function () use ($id, $Covenant_liquidation_data) {
                $isPurchase = $Covenant_liquidation_data->type == 2;
                $items = $isPurchase
                    ? purchase_liquidation::where('Transactions_id', $id)->where('save', 1)->where('status', 1)->get()
                    : shipments_details::where('Transactions_id', $id)->where('status', 1)->get();

                foreach ($items as $item) {
                    $this->postLiquidationItem($item, $id, $isPurchase);
                }

                Covenant_liquidation::find($id)->update(['owner_confirm' => 1]);
                $this->refreshLiquidationStatus($id);
            });
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return 1;
    }

    public function get_liquitation_bystauts($branch, $statuts)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 20;

        // [مراجعة] كان "branchs_id >= الفرع" فبيجيب فروع تانية، ولما مفيش فرع كان بيجيب الشحنات بس
        $query = Covenant_liquidation::where('status', $statuts)->where('save', 1);
        if ($branch != '-') {
            $query->where('branchs_id', $branch);
        }

        // ترتيب البيانات تصاعدياً حسب ID لتسهيل الحساب التراكمي
        $query->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // احصل على بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // حساب الرصيد لكل صف في الصفحة الحالية
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance; // إضافة رصيد الصف
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function getTransction_purchasebystauts($branch, $statuts)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branch == '-') {
            $data = Covenant_liquidation::where('status', $statuts)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
        } else {
            $data = Covenant_liquidation::where('status', $statuts)->where('save', 1)->where('branchs_id', $branch)->orderBy('id', 'desc')->paginate(20);
        }

        return view('transfers.ajax_liquidation_purchase', compact('data'));
    }

    public function index()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('transfers.create_transfer');
    }

    public function update_data_purchase($id)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('transfers.update_data_purchase', compact('id'));
    }

    public function create_purchase()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('transfers.create_purchase');
    }

    public function previous_liquidations()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view(view: 'transfers.previous_liquidations');
    }

    public function ajax_liquidation(Request $request, $branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        // [تم الإصلاح] كانت هنا $branch = $request->branch ?? '-';
        // بس $request مكانتش متعرّفة أصلاً كـ Request في توقيع الدالة (parameter) - ده كان بيسبب
        // Undefined variable $request فور دخول الدالة (PHP 8 بيرميها Error فوري). دلوقتي ضفت
        // Request $request فعليًا في التوقيع، ولو حابب تفضل تستخدم $branch الجاي من الراوت
        // (route parameter) زي الأول احذف السطر ده تاني، هو مش لازم أصلاً.
        $branch = $request->branch ?? $branch ?? '-';

        // query أساسي
        $query = Covenant_liquidation::with(['user', 'branch'])
            ->orderBy('id', 'desc');

        // فلترة الفرع
        // [مراجعة] save=1 دايماً - لما كان فيه فرع متحدد كانت بتظهر التصفيات المسودة والمحذوفة
        $query->where('save', 1);
        if ($branch != '-') {
            $query->where('branchs_id', $branch);
        }

        // pagination
        $data = (clone $query)->paginate(20);

        // إجمالي الداخل
        $total_before = (clone $query)
            ->whereIn('type', [3, 7, 9])
            ->where('status', 2)
            ->sum('price_filtering');

        // إجمالي الخارج
        $total = (clone $query)
            ->whereNotIn('type', [3, 7, 9])
            ->where('status', 2)
            ->sum('price_filtering');

        $balance = $total_before - $total;

        return view(
            'transfers.previous_liquidations',
            compact('data', 'total_before', 'total', 'balance', 'branch')
        );
    }

    public function searchTransctionbyDate($id, $branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if ($branch == '-') {
            $data = Transactions::whereDate('created_at', '>=', $id)->whereDate('created_at', '<=', $id)->paginate(20);
            return view('transfers.ajax_previousTransfers', compact('data'));
        }

        $data = Transactions::whereDate('created_at', '>=', $id)->whereDate('created_at', '<=', $id)->where('branchs_id', $branch)->paginate(20);
        return view('transfers.ajax_previousTransfers', compact('data'));
    }

    public function search_liquidation_purchase_byDate($id, $branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 20;

        // إنشاء الاستعلام الأساسي حسب branch
        if ($branch == '-') {
            $query = Covenant_liquidation::whereDate('created_at', '>=', $id)
                ->whereDate('created_at', '<=', $id)
                ->where('save', 1);
            $view = 'transfers.ajax_liquidation_purchase';
        } else {
            $query = Covenant_liquidation::whereDate('created_at', '>=', $id)
                ->whereDate('created_at', '<=', $id)
                ->where('save', 1)
                ->where('branchs_id', $branch);
            $view = 'transfers.ajax_liquidation';
        }

        // ترتيب البيانات تصاعدياً حسب ID لتسهيل الحساب التراكمي
        $query->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // احصل على بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // حساب الرصيد لكل صف في الصفحة الحالية
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance; // إضافة الرصيد التراكمي للصف
            return $item;
        });

        return view($view, compact('data', 'previous_balance'));
    }

    public function getTransction_purchasebyBransh($branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = Covenant_liquidation::where('branchs_id', $branch)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
        return view('transfers.ajax_liquidation_purchase', compact('data'));
    }

    public function liquidation_purchase_ajax($branch)
    {
        //
        if ($branch == '-') {
            app()->setLocale(LaravelLocalization::getCurrentLocale());
            $data = Covenant_liquidation::orderBy('id', 'desc')->where('save', 1)->paginate(20);
            return view('transfers.ajax_liquidation_purchase', compact('data'));
        }
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = Covenant_liquidation::where('branchs_id', $branch)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
        return view('transfers.ajax_liquidation_purchase', compact('data'));
    }

    public function search_liquidation_purchase_byByIdfunction($id, $branch)
    {
        //
        if ($branch == '-') {

            app()->setLocale(LaravelLocalization::getCurrentLocale());
            $data = Covenant_liquidation::where('id', $id)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
            return view('transfers.ajax_liquidation_purchase', compact('data'));
        }
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = Covenant_liquidation::where('id', $id)->where('branchs_id', $branch)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
        return view('transfers.ajax_liquidation_purchase', compact('data'));
    }

    public function search_liquidation_byDate($id, $branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $page = request()->get('page', 1);
        $perPage = 20;

        // بناء الاستعلام حسب branch
        if ($branch == '-') {
            $query = Covenant_liquidation::whereDate('created_at', '>=', $id)
                ->whereDate('created_at', '<=', $id)
                ->where('save', 1);
        } else {
            $query = Covenant_liquidation::whereDate('created_at', '>=', $id)
                ->whereDate('created_at', '<=', $id)
                ->where('save', 1)
                ->where('branchs_id', $branch);
        }

        // ترتيب البيانات تصاعدياً حسب ID لتسهيل الحساب التراكمي
        $query->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // احصل على بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // إضافة الرصيد التراكمي لكل صف
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance;
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function getTransctionbyBransh($branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $data = Transactions::where('branchs_id', $branch)->where('save', 1)->orderBy('id', 'desc')->paginate(20);
        return view('transfers.ajax_previousTransfers', compact('data'));
    }

    public function searchaboutliquidationByIdfunction($id, $branch)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 20;

        // بناء الاستعلام حسب branch
        if ($branch == '-') {
            $query = Covenant_liquidation::where('id', $id)
                ->where('save', 1);
        } else {
            $query = Covenant_liquidation::where('branchs_id', $branch)
                ->where('id', $id)
                ->where('save', 1);
        }

        // ترتيب البيانات تصاعدياً حسب ID لتسهيل الحساب التراكمي
        $query->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // احصل على بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // إضافة الرصيد التراكمي لكل صف
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance;
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    // علاقة جدول المشتريات
    public function purchaseLiquidations()
    {
        return $this->hasMany(purchase_liquidation::class, 'Transactions_id', 'id');
    }

    // علاقة جدول الشحنات
    public function shipmentDetails()
    {
        return $this->hasMany(shipments_details::class, 'Transactions_id', 'id');
    }

    public function liqutation_content_search($id, $branch, $search_term)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 20;

        // 1. استلام نص البحث
        $searchTerm = $search_term;
        $liquidations = [];
        $liquidations[] = purchase_liquidation::where('note_detaials', 'like', "%{$searchTerm}%")->orwhere('invoice_number', 'like', "%{$searchTerm}%")->get(['Transactions_id']);
        $liquidations[] = shipments_details::where('note_detaials', 'like', "%{$searchTerm}%")->orwhere('polica_number', 'like', "%{$searchTerm}%")->orwhere('invoice_number', 'like', "%{$searchTerm}%")->get(['Transactions_id']);
        $uniqueIds = collect($liquidations)
            ->flatten(1)
            ->pluck('Transactions_id')
            ->unique() // حذف التكرار
            ->filter()
            ->values()
            ->all();

        // 2. بناء الاستعلام باستخدام whereIn للبحث عن مجموعة IDs
        $query = Covenant_liquidation::whereIn('id', $uniqueIds)
            ->where('save', 1);

        // إضافة شرط الفرع (Branch) إذا كان موجوداً
        if ($branch != '-') {
            $query->where('branchs_id', $branch);
        }

        // 3. الترتيب ضروري جداً للحساب التراكمي
        $query->orderBy('id', 'asc');

        // 4. حساب الرصيد التراكمي لما قبل الصفحة الحالية
        $previous_data = (clone $query)->take(($page - 1) * $perPage)->get();

        $previous_balance = $previous_data->reduce(function ($carry, $item) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            return $carry + ($inside - $outside);
        }, 0);

        // 5. جلب بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // 6. إضافة الرصيد التراكمي لكل صف في الصفحة الحالية
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;

            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance;

            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function get_liquidation_byBransh($branch)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 10;

        $query = Covenant_liquidation::where('save', 1);

        if ($branch != '-') {
            $query->where('branchs_id', $branch);
        }

        // ترتيب البيانات تصاعدياً حسب ID لتسهيل الحساب التراكمي
        $query->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // احصل على بيانات الصفحة الحالية
        $data = $query->paginate($perPage);

        // حساب الرصيد لكل صف في الصفحة الحالية
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance; // إضافة رصيد الصف
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function searchaboutTransctionByIdfunction($id, $branch)
    {
    }

    public function getpreviousTransfersajax()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        if (Auth()->User()->branchs_id == 9) {
            $data = Transactions::orderBy('id', 'desc')->paginate(20);
        } else {
            $data = Transactions::where('branchs_id', Auth()->User()->branchs_id)->orderBy('id', 'desc')->paginate(20);
        }
        return view('transfers.ajax_previousTransfers', compact('data'));
    }

    public function previousTransfers()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('transfers.previousTransfers');
    }

    public function complet_data()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $id = 0;
        return view('transfers.complet_data', compact(('id')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function update_data_shipment($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        return view('transfers.update_purchase_liquidation', compact('id'));
    }

    public function update_purchase_liquidation(Request $request)
    {
        $purchase_liquidation_data = purchase_liquidation::find($request->id_update);
        if (!$purchase_liquidation_data) {
            return $this->fail('البند غير موجود', 404);
        }
        // [مراجعة] بند مرحّل على الحساب مينفعش يتعدل (كان بيغيّر المبلغ من غير ما يعدّل القيد
        // فالرصيد يبوظ). لازم "إلغاء التأكيد" الأول.
        if ($purchase_liquidation_data->status == 2 || $this->itemIsPosted($purchase_liquidation_data->id, true)) {
            return $this->fail('لا يمكن تعديل بند تم ترحيله. قم بإلغاء تأكيد التصفية أولاً');
        }
        $amount = (float) str_replace(',', '', $request->amount_update);
        if ($amount <= 0) {
            return $this->fail('من فضلك ادخل مبلغ صحيح أكبر من صفر');
        }

        $the_file_path = '';
        if ($request->hasFile('attachments_update')) {
            $folder = 'assets//attachments';
            $image = $request->attachments_update;
            $the_file_path = time() . rand(100, 999) . '.' . $image->extension();
            $image->move($folder, $the_file_path);
        }

        $data = DB::transaction(function () use ($request, $the_file_path, $purchase_liquidation_data, $amount) {
            $Covenant_liquidation_id = $purchase_liquidation_data->Transactions_id;
            $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
            $Covenant_liquidation_data->update([
                'price_filtering' => $Covenant_liquidation_data->price_filtering + $amount - $purchase_liquidation_data->price_filtering,
            ]);

            $purchase_liquidation_data->update([
                'price_filtering' => $amount,
                'note_detaials'   => $request->notes_update,
                'invoice_number'  => $request->Invoice_no_update,
                'attachments'     => $the_file_path == '' ? $purchase_liquidation_data->attachments : $the_file_path,
                // [مراجعة] لو البند كان متراجع (1) وبعدين اتعدل، يرجع يتراجع تاني
                'status'          => 0,
            ]);
            $this->refreshLiquidationStatus($Covenant_liquidation_id);

            return [
                'purshase_list' => purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get(),
                'id'            => $Covenant_liquidation_id,
            ];
        });

        return $data;
    }

    public function ajax_liquidation_purchase()
    {
        // [تم الإصلاح] كانت النتيجة مش متخزنة في $data، فكانت compact('data') بترجع متغير غير معرّف
        // (Undefined variable $data) وتوقع الصفحة كلها.
        $data = Covenant_liquidation::where('type', 2)->orderBy('id', 'desc')->paginate(20);

        return view('transfers.ajax_liquidation_purchase', compact('data'));
    }

    public function privvious_purchase_liquidation()
    {

        $data = Covenant_liquidation::where('type', 2)->orderBy('id', 'desc')->paginate(20);

        return view('transfers.previous_liquidations', compact('data'));
    }

    public function save_liquidation_data(Request $request)
    {
        $liq = Covenant_liquidation::find($request->Transactions_price_id_print);
        // [مراجعة] لو المستخدم داس حفظ من غير ما يضيف ولا بند كان بيعمل Error 500
        if (!$liq) {
            return redirect()->back()->with('nodataprint', 1);
        }
        purchase_liquidation::where('Transactions_id', $liq->id)->update(['save' => 1]);
        $liq->update(['save' => 1, 'type' => 2]);
        $this->refreshLiquidationStatus($liq->id);

        return redirect()->route('recent_liquidation');
    }

    public function save_liquidation_PURCHASES_data(Request $request)
    {
        $liq = Covenant_liquidation::find($request->Transactions_price_id_print);
        // [مراجعة] لو المستخدم داس حفظ من غير ما يضيف ولا بند كان بيعمل Error 500
        if (!$liq) {
            return redirect()->back()->with('nodataprint', 1);
        }
        purchase_liquidation::where('Transactions_id', $liq->id)->update(['save' => 1]);
        $liq->update(['save' => 1, 'type' => 2]);
        $this->refreshLiquidationStatus($liq->id);

        return redirect()->route('recent_liquidation');
    }

    public function purchase_liquidation(Request $request)
    {
        $amount = (float) str_replace(',', '', $request->amount);
        if ($amount <= 0) {
            return $this->fail('من فضلك ادخل مبلغ صحيح أكبر من صفر');
        }
        // [مراجعة] empty() بدل == 0 : الحقل بييجي فاضي "" من الفورم، وفي PHP 8 "" == 0 = false
        $existingId = empty($request->transactions_id) ? 0 : (int) $request->transactions_id;
        if ($existingId) {
            $liq = Covenant_liquidation::find($existingId);
            if (!$liq) {
                return $this->fail('التصفية غير موجودة', 404);
            }
            if ($liq->status == 2) {
                return $this->fail('التصفية مرحّلة بالكامل، لا يمكن إضافة بنود عليها. قم بإلغاء التأكيد أولاً');
            }
        }

        $the_file_path = '';
        if ($request->hasFile('attachments')) {
            $folder = 'assets//attachments';
            $image = $request->attachments;
            $the_file_path = time() . rand(100, 999) . '.' . $image->extension();
            $image->move($folder, $the_file_path);
        }

        $data = DB::transaction(function () use ($request, $the_file_path, $existingId, $amount) {
            if (!$existingId) {
                $Covenant_liquidation = Covenant_liquidation::create([
                    'branchs_id'      => Auth()->user()->branchs_id,
                    'user_id'         => Auth()->user()->id,
                    'price_filtering' => $amount,
                    'note_detaials'   => '-',
                    'type'            => 2,
                ]);
                $Covenant_liquidation_id = $Covenant_liquidation->id;
            } else {
                $Covenant_liquidation_id = $existingId;
                $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
                $Covenant_liquidation_data->update([
                    'price_filtering' => $Covenant_liquidation_data->price_filtering + $amount,
                ]);
            }

            $liq = Covenant_liquidation::find($Covenant_liquidation_id);
            purchase_liquidation::create([
                'branchs_id'      => Auth()->user()->branchs_id,
                'user_id'         => Auth()->user()->id,
                'price_filtering' => $amount,
                'note_detaials'   => $request->notes,
                'invoice_number'  => $request->Invoice_no,
                'attachments'     => $the_file_path,
                'Transactions_id' => $Covenant_liquidation_id,
                // [مراجعة] لو بنضيف على تصفية محفوظة (من شاشة التعديل) البند يتحفظ على طول
                // بدل ما يفضل مخفي من الشاشات ومحسوب في المبلغ
                'save'            => $liq->save == 1 ? 1 : 0,
                'status'          => 0,
            ]);
            if ($liq->save == 1) {
                $this->refreshLiquidationStatus($Covenant_liquidation_id);
            }

            return [
                'purshase_list' => purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get(),
                'id'            => $Covenant_liquidation_id,
            ];
        });

        return $data;
    }

    public function show_transction_Recent($request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $Transactions = Transactions::where('id', $request)->get();
        $data = [];
        $transaction = [];
        $pay = '';
        foreach ($Transactions as $item) {
            $transaction[] = [
                "sent_abd_count" => $item->id,
                'name' => $item->financial_accounts_data->name,
                'created_at' => $item->created_at,
                'date' => $item->date,
                'date_export' => $item->created_at,
                'method_pay' => __('home.Bank_transfer'),
                'paid_amount' => $item->price,
                'note' => $item->note,
                'branch' => $item->branch->name,
            ];
        }

        $data = [
            "transaction" => $transaction,
        ];
        return view('transfers.print_transfers', compact('data'));
    }

    public function make_update_data_shipment(Request $transactions)
    {
        $Transactions = Transactions::find($transactions->id);
        if (!$Transactions) {
            return redirect()->back();
        }

        $the_file_path = '';
        if ($transactions->hasFile('attachments')) {
            $folder = 'assets//attachments';
            $image = $transactions->attachments;
            $the_file_path = time() . rand(100, 999) . '.' . $image->extension();
            $image->move($folder, $the_file_path);
        }
        $Transactions->update([
            'loading'        => $transactions->loading,
            'unloading'      => $transactions->Unloading,
            'truck_data'     => $transactions->truck_no,
            'invoice_number' => $transactions->invoice_no,
            'Daily'          => $transactions->delay,
            'Ext'            => $transactions->ext,
            'note_detaials'  => $transactions->note,
            // [مراجعة] كان "$the_file_path ?? قديم" و "" مش null فكان المرفق القديم بيتمسح دايماً
            'attachments_2'  => $the_file_path !== '' ? $the_file_path : $Transactions->attachments_2,
            'status'         => 1,
        ]);
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        return view('transfers.previousTransfers');
    }

    public function print_transfers(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $Transactions = Transactions::where('id', $request->id)->get();
        $data = [];
        $transaction = [];
        $pay = '';
        foreach ($Transactions as $item) {
            $transaction[] = [
                "sent_abd_count" => $item->id,
                'name' => $item->financial_accounts_data->name,
                'created_at' => $item->created_at,
                'date' => $item->date,
                'date_export' => $item->created_at,
                'method_pay' => __('home.Bank_transfer'),
                'paid_amount' => $item->price,
                'note' => $item->note,
                'branch' => $item->branch->name,
            ];
        }

        $data = [
            "transaction" => $transaction,
        ];
        return view('transfers.print_transfers', compact('data'));
    }

    public function print_transfers_after_full(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        $transaction = [];
        $pay = '';

        return view('transfers.print_transfers_after_full', compact('data'))->with('id', $request->id);
    }

    public function export_Liquidation($id)
    {

        return Excel::download(new liquidation($id), 'export_Liquidation_NO_:' . $id . '.xlsx');
    }

    public function print_full_purchase($request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $data = [];
        $transaction = [];
        $pay = '';

        return view('transfers.print_full_purchase', compact('data'))->with('id', $request);
    }

    public function save_transaction_details(Request $request)
    {
        $liq = Covenant_liquidation::find($request->Transactions_price_id_print);
        // [مراجعة] حفظ من غير ولا شحنة كان بيعمل Error 500
        if (!$liq) {
            return $this->fail('من فضلك أضف شحنة واحدة على الأقل قبل الحفظ');
        }
        DB::transaction(function () use ($liq) {
            shipments_details::where('Transactions_id', $liq->id)->update(['save' => 1]);
            $liq->update(['save' => 1, 'type' => 1]);
            $this->refreshLiquidationStatus($liq->id);
        });

        return 1;
    }

    public function save_transaction_detailsform(Request $request)
    {
        $liq = Covenant_liquidation::find($request->Transactions_price_id_print);
        if ($liq) {
            DB::transaction(function () use ($liq) {
                shipments_details::where('Transactions_id', $liq->id)->update(['save' => 1]);
                // [مراجعة] كانت بترجّع الحالة 1 دايماً حتى لو فيه بنود مرحّلة (3) - دلوقتي بتتحسب
                $liq->update(['save' => 1, 'type' => 1]);
                $this->refreshLiquidationStatus($liq->id);
            });
        }

        // [مراجعة] redirect بدل return view: كانت الصفحة بتفضل على رابط POST ولو عمل Refresh بيعيد الإرسال
        return redirect()->route('recent_liquidation');
    }

    public function update_transfer_to_mantop(Request $request)
    {
        //

        // [تم الإصلاح] الدالة دي بتعدل في 4 موديلات مختلفة (Transactions, Covenant_liquidation,
        // financial_accounts مرتين, credittransactions مرتين) في عملية واحدة منطقيًا (تغيير طرف
        // التحويل). لفّيتها كلها في DB::transaction عشان لو وقع خطأ في أي خطوة، كل حاجة ترجع
        // زي ما كانت بدل ما يفضل الرصيد متعدل في حساب واحد بس.
        // [مراجعة] تحقق من البيانات قبل تعديل الأرصدة
        $amount = (float) str_replace(',', '', $request->cashreceivedupdate);
        if (!Transactions::find($request->transactionId)) {
            return $this->fail('سند التحويل غير موجود', 404);
        }
        if ($amount <= 0) {
            return $this->fail('من فضلك ادخل مبلغ صحيح أكبر من صفر');
        }
        if (!financial_accounts::find($request->owner) || !financial_accounts::find($request->mantop)) {
            return $this->fail('من فضلك اختر الحسابين (المحوّل منه والمندوب)');
        }
        $request->merge(['cashreceivedupdate' => $amount]);

        $data = DB::transaction(function () use ($request) {
            Transactions::find($request->transactionId)->update([
                'mantob' => $request->mantop,
                'price' => $request->cashreceivedupdate,
                'partner' => $request->owner,
            ]);
            // [مراجعة] where type=3 : id_trasction بيستخدم في أنواع تانية لرقم قيد مش رقم تحويل
            Covenant_liquidation::where('id_trasction', $request->transactionId)->where('type', 3)->update([
                'price_filtering' => $request->cashreceivedupdate,
            ]);

            $owner_old = credittransactions::where('Transactions', $request->transactionId)->where('debtor', 0)->first();

            // [تم الإصلاح] لو مفيش قيد قديم مطابق (owner_old = null)، الكود كان هيعمل Fatal Error
            // عند $owner_old->customer_id. ضفت تأكد بسيط.
            if ($owner_old) {
                $financial_accounts = financial_accounts::find($owner_old->customer_id);
                if ($financial_accounts) {
                    financial_accounts::find($owner_old->customer_id)->update(
                        [
                            'current_balance' => $financial_accounts->current_balance - $owner_old->recive_amount,
                            'creditor_current' => $financial_accounts->creditor_current - $owner_old->recive_amount,
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        ]
                    );
                }
            }

            $financial_accounts = financial_accounts::find($request->owner);
            financial_accounts::find($request->owner)->update(
                [
                    'current_balance' => $financial_accounts->current_balance + $request->cashreceivedupdate,
                    'creditor_current' => $financial_accounts->creditor_current + $request->cashreceivedupdate,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );

            credittransactions::where('Transactions', $request->transactionId)->where('debtor', 0)->update(
                [
                    'user_id' => Auth()->user()->id,
                    'customer_id' => $request->owner,
                    'recive_amount' => $request->cashreceivedupdate,
                    'branchs_id' => Auth()->user()->branchs_id,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'orginal_id' => $financial_accounts->orginal_id ?? 0,
                    'debtor' => 0,
                    'creditor' => $request->cashreceivedupdate,
                    'Transactions' => $request->transactionId
                ]
            );

            $mantob_old = credittransactions::where('Transactions', $request->transactionId)->where('creditor', 0)->first();

            // [تم الإصلاح] نفس حماية عدم وجود القيد القديم
            if ($mantob_old) {
                $financial_accounts = financial_accounts::find($mantob_old->customer_id);
                if ($financial_accounts) {
                    financial_accounts::find($mantob_old->customer_id)->update(
                        [
                            'current_balance' => $financial_accounts->current_balance - $mantob_old->recive_amount,
                            'debtor_current' => $financial_accounts->debtor_current - $mantob_old->recive_amount,
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                        ]
                    );
                }
            }

            $financial_accounts = financial_accounts::find($request->mantop);
            Transactions::find($request->transactionId)->update(['branchs_id' => $financial_accounts->candidate_branch]);

            Covenant_liquidation::where('id_trasction', $request->transactionId)->where('type', 3)->update([
                'price_filtering' => $request->cashreceivedupdate,
                'branchs_id' => $financial_accounts->candidate_branch
            ]);

            financial_accounts::find($request->mantop)->update(
                [
                    'current_balance' => $financial_accounts->current_balance + $request->cashreceivedupdate,
                    'debtor_current' => $financial_accounts->debtor_current + $request->cashreceivedupdate,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                ]
            );

            credittransactions::where('Transactions', $request->transactionId)->where('creditor', 0)->update(
                [
                    'user_id' => Auth()->user()->id,
                    'customer_id' => $request->mantop,
                    'recive_amount' => $request->cashreceivedupdate,
                    'branchs_id' => Auth()->user()->branchs_id,
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'orginal_id' => $financial_accounts->orginal_id ?? 0,
                    'creditor' => 0,
                    'debtor' => $request->cashreceivedupdate,
                    'Transactions' => $request->transactionId
                ]
            );

            $mantop = financial_accounts::find($request->mantop);

            $owner = financial_accounts::find($request->owner);

            return [
                [
                    'sent_serf_count' => $request->transactionId,
                    'id' => $request->transactionId,
                    'name' => $owner->name,
                    'mantop' => $mantop->name,
                    'method_pay' => __('home.Bank_transfer'),
                    'paid_amount' => $request->cashreceivedupdate
                ]
            ];
        });

        return $data;
    }

    public function getAndUpdatetransfer($request)
    {

        $Transactions = Transactions::find($request);
        $mantop = financial_accounts::find($Transactions->mantob);
        $owner = financial_accounts::find($Transactions->partner);
        $data = [];
        $data[] = [
            'sent_serf_count' => $request,
            'id' => $request,
            'name' => $owner->name,
            'mantop' => $mantop->name,
            'owner_id' => $owner->id,
            'mantop_id' => $mantop->id,
            'method_pay' => __('home.Bank_transfer'),
            'paid_amount' => $Transactions->price
        ];

        return $data;
    }

    public function create_transfer(Request $request)
    {
        // [مراجعة] تحقق قبل ما نعمل أي قيود
        $amount = (float) str_replace(',', '', $request->cashreceived);
        if ($amount <= 0) {
            return $this->fail('من فضلك ادخل مبلغ صحيح أكبر من صفر');
        }
        if (!financial_accounts::find($request->paymentmethod) || !financial_accounts::find($request->clientnamesearch)) {
            return $this->fail('من فضلك اختر الحسابين (المحوّل منه والمندوب)');
        }
        $request->merge(['cashreceived' => $amount]);

        $the_file_path = '';
        if ($request->hasFile('attachments')) {
            $folder = 'assets//attachments';
            $image = $request->attachments;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] حذف سطر getClientOriginalName الغير فعّال (نفس الملاحظة في باقي الدوال)
            $image->move(
                $folder,
                $the_file_path
            );
        }

        // [تم الإصلاح] لفّيت كل خطوات إنشاء التحويل (سند + تصفية + قيدين + تعديل رصيد حسابين)
        // جوه DB::transaction واحدة، عشان التحويل يتسجل كامل أو مايتسجلش خالص، بدل ما يحصل مثلاً
        // إنشاء السند وتعديل رصيد حساب واحد بس لو وقع خطأ في نص العملية.
        $data = DB::transaction(function () use ($request, $the_file_path) {
            $financial_accounts = financial_accounts::find($request->paymentmethod);
            $Transactions = Transactions::create([
                'attachments' => $the_file_path,
                'note' => $request->notes,
                'branchs_id' => $financial_accounts->candidate_branch,
                'mantob' => $request->paymentmethod,
                'date' => $request->date,
                'price' => $request->cashreceived,
                'partner' => $request->clientnamesearch,
                'user_id' => Auth()->user()->id,
            ]);

            Covenant_liquidation::create([
                'branchs_id' => $financial_accounts->candidate_branch,
                'user_id' => Auth()->user()->id,
                'price_filtering' => $request->cashreceived,
                'note_detaials' => $request->notes . '|  سند تحويل بنكي  | ' . ' : ' . (string) $Transactions->id,
                'type' => 3,
                'status' => 2,
                'save' => 1,
                'id_trasction' => $Transactions->id
            ]);

            $owner = credittransactions::create(
                [
                    'attachments' => $the_file_path,
                    'orginal_type' => 10,
                    'user_id' => Auth()->user()->id,
                    'customer_id' => $request->clientnamesearch,
                    'recive_amount' => $request->cashreceived,
                    'branchs_id' => Auth()->user()->branchs_id,
                    'pay_method' => __('home.Bank_transfer'),
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'date_export' => $request->date,
                    'note' => $request->notes . '|  سند تحويل بنكي  | ' . ' : ' . (string) $Transactions->id,
                    'Pay_Method_Name' => __('home.Bank_transfer'),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'orginal_id' => $financial_accounts->orginal_id ?? 0,
                    'debtor' => 0,
                    'creditor' => $request->cashreceived,
                    'Transactions' => $Transactions->id,
                    'type' => 10,
                ]
            );
            $financial_accounts = financial_accounts::find($request->clientnamesearch);
            financial_accounts::find($request->clientnamesearch)->update(
                [
                    'current_balance' => $financial_accounts->current_balance + $request->cashreceived,
                    'creditor_current' => $financial_accounts->creditor_current + $request->cashreceived,
                ]
            );

            $mantop = credittransactions::create(
                [
                    'attachments' => $the_file_path,
                    'orginal_type' => 10,
                    'user_id' => Auth()->user()->id,
                    'customer_id' => $request->paymentmethod,
                    'recive_amount' => $request->cashreceived,
                    'branchs_id' => Auth()->user()->branchs_id,
                    'pay_method' => __('home.Bank_transfer'),
                    'created_at' => \Carbon\Carbon::now()->addHours(3),
                    'date_export' => $request->date,
                    'note' => $request->notes . '|  سند تحويل بنكي  | ' . ' : ' . (string) $Transactions->id,
                    'Pay_Method_Name' => __('home.Bank_transfer'),
                    'updated_at' => \Carbon\Carbon::now()->addHours(3),
                    'orginal_id' => $financial_accounts->orginal_id ?? 0,
                    'debtor' => $request->cashreceived,
                    'creditor' => 0,
                    'Transactions' => $Transactions->id,
                    'type' => 10,
                ]
            );

            $financial_accounts = financial_accounts::find($request->paymentmethod);
            financial_accounts::find($request->paymentmethod)->update(
                [
                    'current_balance' => $financial_accounts->current_balance + $request->cashreceived,
                    'debtor_current' => $financial_accounts->debtor_current + $request->cashreceived,
                ]
            );

            return [
                [
                    'sent_serf_count' => $Transactions->id,
                    'id' => $Transactions->id,
                    'name' => $owner->financial_accounts_data->name,
                    'mantop' => $mantop->financial_accounts_data->name,
                    'method_pay' => __('home.Bank_transfer'),
                    'paid_amount' => $request->cashreceived
                ]
            ];
        });

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function update_describtion(Request $transactions)
    {
        $shipments_details_old = shipments_details::find($transactions->id_update);
        if (!$shipments_details_old) {
            return $this->fail('الشحنة غير موجودة', 404);
        }
        // [مراجعة] شحنة مرحّلة مينفعش تتعدل (كان بيغيّر المبلغ والقيد فاضل بالمبلغ القديم)
        if ($shipments_details_old->status == 2 || $this->itemIsPosted($shipments_details_old->id, false)) {
            return $this->fail('لا يمكن تعديل شحنة تم ترحيلها. قم بإلغاء تأكيد التصفية أولاً');
        }
        $newTotal = (float) $transactions->PRICE_SHIPMENT_update + (float) $transactions->ext_update + (float) $transactions->delay_update;
        if ((float) $transactions->PRICE_SHIPMENT_update < 0 || (float) $transactions->ext_update < 0
            || (float) $transactions->delay_update < 0 || $newTotal <= 0) {
            return $this->fail('من فضلك ادخل مبالغ صحيحة (الإجمالي أكبر من صفر)');
        }

        $the_file_path = $shipments_details_old->attachments_2;
        if ($transactions->hasFile('attachments_update')) {
            $folder = 'assets//attachments';
            $image = $transactions->attachments_update;
            $the_file_path = time() . rand(100, 999) . '.' . $image->extension();
            $image->move($folder, $the_file_path);
        }

        $data = DB::transaction(function () use ($transactions, $the_file_path, $shipments_details_old, $newTotal) {
            $Covenant_liquidation_id = $shipments_details_old->Transactions_id;
            $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
            $oldTotal = (float) $shipments_details_old->Ext + (float) $shipments_details_old->Daily + (float) $shipments_details_old->price_shipment;
            $Covenant_liquidation_data->update([
                'price_filtering' => $Covenant_liquidation_data->price_filtering - $oldTotal + $newTotal,
            ]);
            $shipments_details_old->update([
                'price_shipment' => $transactions->PRICE_SHIPMENT_update,
                'loading'        => $transactions->loading_update,
                'unloading'      => $transactions->Unloading_update,
                'truck_data'     => $transactions->truck_no_update,
                'polica_number'  => $transactions->polica_number_update,
                'invoice_number' => $transactions->invoice_no_update,
                'Daily'          => $transactions->delay_update,
                'Ext'            => $transactions->ext_update,
                'note_detaials'  => $transactions->note_update,
                'attachments_2'  => $the_file_path,
                // [مراجعة] أي تعديل بيرجّع الشحنة لحالة "لم تتم مراجعتها" عشان المدير يراجع الجديد
                'status'         => 0,
            ]);
            if ($Covenant_liquidation_data->save == 1) {
                $this->refreshLiquidationStatus($Covenant_liquidation_id);
            }

            return [
                'shipments_details' => shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get(),
                'id'                => $Covenant_liquidation_id,
            ];
        });

        return $data;
    }

    public function add_describtion(Request $transactions)
    {
        $lineTotal = (float) $transactions->ext + (float) $transactions->delay + (float) $transactions->PRICE_SHIPMENT;
        if ((float) $transactions->PRICE_SHIPMENT < 0 || (float) $transactions->ext < 0
            || (float) $transactions->delay < 0 || $lineTotal <= 0) {
            return $this->fail('من فضلك ادخل مبالغ صحيحة (الإجمالي أكبر من صفر)');
        }
        // [مراجعة] empty() بدل == 0 : الحقل بييجي فاضي "" وفي PHP 8 "" == 0 = false
        $existingId = empty($transactions->transactions_id) ? 0 : (int) $transactions->transactions_id;
        if ($existingId) {
            $liq = Covenant_liquidation::find($existingId);
            if (!$liq) {
                return $this->fail('التصفية غير موجودة', 404);
            }
            if ($liq->status == 2) {
                return $this->fail('التصفية مرحّلة بالكامل، لا يمكن إضافة شحنات عليها. قم بإلغاء التأكيد أولاً');
            }
        }

        $the_file_path = '';
        if ($transactions->hasFile('attachments')) {
            $folder = 'assets//attachments';
            $image = $transactions->attachments;
            $the_file_path = time() . rand(100, 999) . '.' . $image->extension();
            $image->move($folder, $the_file_path);
        }

        $data = DB::transaction(function () use ($transactions, $the_file_path, $existingId, $lineTotal) {
            if (!$existingId) {
                $Covenant_liquidation = Covenant_liquidation::create([
                    'branchs_id'      => Auth()->user()->branchs_id,
                    'user_id'         => Auth()->user()->id,
                    'price_filtering' => $lineTotal,
                    'note_detaials'   => '-',
                    'type'            => 1, // [مراجعة] النوع كان مش متحدد وقت الإنشاء
                ]);
                $Covenant_liquidation_id = $Covenant_liquidation->id;
            } else {
                $Covenant_liquidation_id = $existingId;
                $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
                $Covenant_liquidation_data->update([
                    'price_filtering' => $Covenant_liquidation_data->price_filtering + $lineTotal,
                ]);
            }

            $liq = Covenant_liquidation::find($Covenant_liquidation_id);
            shipments_details::create([
                'price_shipment'  => $transactions->PRICE_SHIPMENT,
                'Transactions_id' => $Covenant_liquidation_id,
                'loading'         => $transactions->loading,
                'unloading'       => $transactions->Unloading,
                'truck_data'      => $transactions->truck_no,
                'date'            => $transactions->date,
                'invoice_number'  => $transactions->invoice_no,
                'Daily'           => $transactions->delay,
                'Ext'             => $transactions->ext,
                'note_detaials'   => $transactions->note,
                'polica_number'   => $transactions->polica_number,
                'attachments_2'   => $the_file_path,
                'user_id'         => Auth()->user()->id,
                'branchs_id'      => Auth()->user()->branchs_id,
                // [مراجعة] شحنة مضافة من شاشة التعديل على تصفية محفوظة تتحفظ على طول
                'save'            => $liq->save == 1 ? 1 : 0,
                'status'          => 0,
            ]);
            if ($liq->save == 1) {
                $this->refreshLiquidationStatus($Covenant_liquidation_id);
            }

            return [
                'shipments_details' => shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get(),
                'id'                => $Covenant_liquidation_id,
            ];
        });

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function delete_Liquidation($id)
    {

        $Covenant_liquidation = Covenant_liquidation::find($id);
        if ($Covenant_liquidation == NULL) {
            return 0;
        }
        // [مراجعة] القيد الافتتاحي بيتحذف من شاشته - الحذف من هنا كان بيعكس حساب العهدة بالغلط
        if (in_array($Covenant_liquidation->type, [9, 10])) {
            return $this->fail('القيد الافتتاحي يتم تعديله/حذفه من شاشة القيد الافتتاحي');
        }

        DB::transaction(function () use ($id, $Covenant_liquidation) {
            if (in_array($Covenant_liquidation->type, [1, 2])) {
                // [مراجعة] عكس القيود المرحّلة فعلاً (بدل عكس المبلغ كله حتى لو التصفية مرحّلة جزئياً)
                $this->reverseLiquidationPostings($id);
                shipments_details::where('Transactions_id', $id)->delete();
                // [مراجعة] بنود المشتريات كانت بتفضل موجودة يتيمة وتظهر في البحث
                purchase_liquidation::where('Transactions_id', $id)->delete();
                $Covenant_liquidation->delete();
            } elseif ($Covenant_liquidation->type == 3) {
                // [مراجعة] تصفية تحويل: كان بيعكس حساب العهدة بمبلغ التحويل ويسيب سند التحويل
                // وقيوده زي ما هي. دلوقتي بيلغي سند التحويل نفسه بالطرفين.
                $Transactions = Transactions::find($Covenant_liquidation->id_trasction);
                if ($Transactions) {
                    $this->voidTransfer($Transactions);
                }
                $Covenant_liquidation->delete();
            } else {
                // قيد يومي (7/8) - نفس المنطق القديم
                $credittransactions_main_data = credittransactions::find($Covenant_liquidation->id_trasction);
                if ($credittransactions_main_data) {
                    foreach (credittransactions::where('note', $credittransactions_main_data->note)->where('customer_id', '!=', 0)->get() as $item) {
                        $financial_accounts = financial_accounts::find($item->customer_id);
                        if ($financial_accounts) {
                            $financial_accounts->update([
                                'current_balance'  => $financial_accounts->current_balance + $item->creditor - $item->debtor,
                                'debtor_current'   => $financial_accounts->debtor_current - $item->debtor,
                                'creditor_current' => $financial_accounts->creditor_current - $item->creditor,
                            ]);
                        }
                    }
                    credittransactions::where('note', $credittransactions_main_data->note)->update(['customer_id' => 0]);
                }
                $Covenant_liquidation->delete();
            }
        });

        // جلب البيانات بعد التحديث مع الرصيد التراكمي لكل صف
        $page = request()->get('page', 1);
        $perPage = 20;

        $query = Covenant_liquidation::where('save', 1)->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // البيانات الحالية مع paginate
        $data = $query->paginate($perPage);

        // إضافة الرصيد التراكمي لكل صف
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance;
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function confirm_cancel_Liquidation($id)
    {

        $Covenant_liquidation = Covenant_liquidation::find($id);
        if ($Covenant_liquidation == NULL) {
            return 0;
        }
        // [مراجعة] إلغاء التأكيد للشحنات والمشتريات بس - على التحويل كان بيعكس حساب العهدة بالغلط
        if (!in_array($Covenant_liquidation->type, [1, 2])) {
            return $this->fail('إلغاء التأكيد متاح لتصفية الشحنات والمشتريات فقط');
        }

        DB::transaction(function () use ($id, $Covenant_liquidation) {
            // [مراجعة] (1) بنعكس القيود المرحّلة فعلاً بس. (2) نمط البحث في المشتريات كان فيه
            // مسافة زيادة في الآخر فمكانش بيلاقي القيد أبداً => القيد يفضل والرصيد يترد مرتين.
            $this->reverseLiquidationPostings($id);

            if ($Covenant_liquidation->type == 1) {
                shipments_details::where('Transactions_id', $id)->update(['status' => 0]);
            } else {
                purchase_liquidation::where('Transactions_id', $id)->update(['status' => 0]);
            }

            $Covenant_liquidation->update([
                'status'        => 1,
                'owner_confirm' => 0,
                'manager_check' => 0,
            ]);
        });

        // جلب البيانات بعد التحديث مع الرصيد التراكمي لكل صف
        $page = request()->get('page', 1);
        $perPage = 20;

        $query = Covenant_liquidation::where('save', 1)->orderBy('id', 'asc');

        // حساب الرصيد التراكمي قبل الصفحة الحالية
        $previous_balance = $query->skip(0)->take(($page - 1) * $perPage)->get()
            ->reduce(function ($carry, $item) {
                $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
                return $carry + ($inside - $outside);
            }, 0);

        // البيانات الحالية مع paginate
        $data = $query->paginate($perPage);

        // إضافة الرصيد التراكمي لكل صف
        $running_balance = $previous_balance;
        $data->getCollection()->transform(function ($item) use (&$running_balance) {
            $inside = in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $outside = !in_array($item->type, [3, 7, 9]) ? $item->price_filtering : 0;
            $running_balance += ($inside - $outside);
            $item->current_balance = $running_balance;
            return $item;
        });

        return view('transfers.ajax_liquidation', compact('data', 'previous_balance'));
    }

    public function confirm_cancel_purtchase($id)
    {
        $Covenant_liquidation = Covenant_liquidation::find($id);
        if ($Covenant_liquidation == NULL) {
            return 0;
        }

        DB::transaction(function () use ($id, $Covenant_liquidation) {
            // [مراجعة] بنعكس القيود اللي اترحلت فعلاً بس (مش مبلغ التصفية كله)
            $this->reverseLiquidationPostings($id);
            purchase_liquidation::where('Transactions_id', $id)->update(['status' => 0]);
            $Covenant_liquidation->update(['status' => 1, 'owner_confirm' => 0, 'manager_check' => 0]);
        });

        return 1;
    }

    public function confirm_data_shipment($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $shipment = shipments_details::find($id);
        if (!$shipment) {
            return $this->fail('الشحنة غير موجودة', 404);
        }
        $Covenant_liquidation_id = $shipment->Transactions_id;

        try {
            DB::transaction(function () use ($shipment, $Covenant_liquidation_id) {
                // [مراجعة] لو الشحنة اتراجعت قبل كده (دبل كليك) منعملش حاجة - كان بيعمل قيد تاني
                if ($shipment->status != 0) {
                    return;
                }
                $shipment->update(['status' => 1]);
                Covenant_liquidation::find($Covenant_liquidation_id)->update(['manager' => Auth()->user()->id]);

                $liq = Covenant_liquidation::find($Covenant_liquidation_id);
                if ($liq->owner_confirm == 1) {
                    // المالك اعتمد قبل كده => الشحنة تترحّل على طول وحالتها تبقى 2
                    // (قبل كده كانت بتفضل 1 والتصفية تتعلّم "تمت" حتى لو فيه شحنات لسه متراجعتش)
                    $this->postLiquidationItem($shipment->fresh(), $Covenant_liquidation_id, false);
                }
                $this->refreshLiquidationStatus($Covenant_liquidation_id);
            });
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return [
            'shipments_details' => shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get(),
            'id'                => $Covenant_liquidation_id,
        ];
    }

    public function confirm_data_purchases($id)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        $item = purchase_liquidation::find($id);
        if (!$item) {
            return $this->fail('البند غير موجود', 404);
        }
        $Covenant_liquidation_id = $item->Transactions_id;

        try {
            DB::transaction(function () use ($item, $Covenant_liquidation_id) {
                // [مراجعة] منع المراجعة/الترحيل مرتين
                if ($item->status != 0) {
                    return;
                }
                $item->update(['status' => 1]);
                Covenant_liquidation::find($Covenant_liquidation_id)->update(['manager' => Auth()->user()->id]);

                $liq = Covenant_liquidation::find($Covenant_liquidation_id);
                if ($liq->owner_confirm == 1) {
                    $this->postLiquidationItem($item->fresh(), $Covenant_liquidation_id, true);
                }
                $this->refreshLiquidationStatus($Covenant_liquidation_id);
            });
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        return [
            'shipments_details' => purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get(),
            'id'                => $Covenant_liquidation_id,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transactions $transactions)
    {
        //
    }
}