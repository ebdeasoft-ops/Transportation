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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function delete_transction($id)
    {
        //
        $Transactions = Transactions::find($id);

        // [تم الإصلاح] لو العملية مش موجودة أصلاً، الكود القديم كان بيكمل ويحاول يقرأ
        // $Transactions->mantob على قيمة null فيعمل Fatal Error. بنوقف بأمان بدل كده.
        if (!$Transactions) {
            $data = Transactions::orderBy('id', 'desc')->paginate(20);
            return view('transfers.ajax_previousTransfers', compact('data'));
        }

        // [تم الإصلاح] استخدام DB::transaction عشان كل التعديلات دي (حذف السند + تعديل رصيد
        // حسابين ماليين) تتم كوحدة واحدة. لو حصل أي خطأ في نص العملية، كل حاجة بترجع زي ما كانت
        // (rollback) بدل ما تسيب البيانات في حالة نص متعدلة (مثلاً رصيد اتخصم وحساب تاني لأ).
        DB::transaction(function () use ($id, $Transactions) {
            $Covenant_liquidation = Covenant_liquidation::where('id_trasction', $id)->first();
            Covenant_liquidation::where('id_trasction', $id)->update(['save' => 0]);
            $credittransactions = credittransactions::where('Transactions', $id)->update(['customer_id' => 0]);
            $financial_accounts = financial_accounts::find($Transactions->mantob);
            financial_accounts::find($Transactions->mantob)->update(
                [
                    'current_balance' => $financial_accounts->current_balance - $Transactions->price,
                    'debtor_current' => $financial_accounts->debtor_current - $Transactions->price,
                ]
            );

            $financial_accounts = financial_accounts::find($Transactions->partner);
            financial_accounts::find($Transactions->partner)->update(
                [
                    'current_balance' => $financial_accounts->current_balance - $Transactions->price,
                    'creditor_current' => $financial_accounts->creditor_current - $Transactions->price,
                ]
            );

            Transactions::where('id', $id)->delete();
        });

        $data = Transactions::orderBy('id', 'desc')->paginate(20);

        return view('transfers.ajax_previousTransfers', compact('data'));
    }

    public function confirm_liquidation_from_owner($id)
    {

        $Covenant_liquidation_data = Covenant_liquidation::find($id);
        Covenant_liquidation::find($id)->update(['owner_confirm' => 1]);

        // [تم الإصلاح] كانت هنا if(1){ ... } وهو شرط دايمًا صح ومالوش أي معنى (كود متروك من التجربة) - تم حذفه
        // [تم الإصلاح] لفّيت كل حلقة foreach جوه DB::transaction لوحدها: كل صف (شحنة/مشترى) بيتحدث
        // هو وحسابه المالي وقيد الحركة المرتبط بيه سوا، فلو وقع خطأ في نص صف معين الصف ده بس اللي يرجع
        // زي ما كان، من غير ما يأثر على الصفوف اللي قبله اللي خلصت بنجاح.
        if ($Covenant_liquidation_data->type == 1) {
            $Covenant_liquidation_id = $id;

            foreach (shipments_details::where('Transactions_id', $Covenant_liquidation_id)->where('status', 1)->get() as $shipments_details) {
                DB::transaction(function () use ($shipments_details, $Covenant_liquidation_id) {
                    shipments_details::find($shipments_details->id)->update(['status' => 2]);

                    $shipments_details_check = shipments_details::where('Transactions_id', $Covenant_liquidation_id)->where('status', 0)->get();
                    if (count($shipments_details_check) == 0) {
                        Covenant_liquidation::find($Covenant_liquidation_id)->update([
                            'status' => 2
                        ]);
                    } else {
                        Covenant_liquidation::find($Covenant_liquidation_id)->update([
                            'status' => 3
                        ]);
                    }

                    $financial_accounts = financial_accounts::where('parent_account_number', 82)->where('candidate_branch', $shipments_details->branchs_id)->first();

                    // [تم الإصلاح] لو مفيش حساب مالي مطابق للفرع ده، الكود القديم كان هيعمل Fatal Error
                    // عند استخدام $financial_accounts->id تحت. ضفت تأكد إن الحساب موجود قبل الاستخدام.
                    if (!$financial_accounts) {
                        return;
                    }

                    $total = $shipments_details->price_shipment + $shipments_details->Ext + $shipments_details->Daily;
                    financial_accounts::find($financial_accounts->id)->update(
                        [
                            // [تم الإصلاح] كانت current_balance بتتحسب من debtor_current بالغلط بدل current_balance نفسه
                            'current_balance' => $financial_accounts->current_balance - $total,
                            'creditor_current' => $financial_accounts->creditor_current + $total,
                        ]
                    );

                    $financial_accounts_after_update = financial_accounts::find($financial_accounts->id);

                    Covenant_liquidation::find($Covenant_liquidation_id)->update(['currentblance' => $financial_accounts_after_update->debtor_current - $financial_accounts_after_update->creditor_current]);
                    credittransactions::create(
                        [
                            'attachments' => '',
                            'orginal_type' => 10,
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $total,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => __('home.Bank_transfer'),
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'date_export' => date("Y/m/d"),
                            'note' => '|  شحنة رقم     | ' . ' : ' . (string) $shipments_details->id . '|  تصفية رقم     | ' . ' : ' . (string) $Covenant_liquidation_id,
                            'Pay_Method_Name' => __('home.Bank_transfer'),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => $financial_accounts->orginal_id ?? 0,
                            'debtor' => 0,
                            'creditor' => $total,
                            'Transactions' => 0,
                            'type' => 11,
                        ]
                    );
                });
            }
        } else {

            foreach (purchase_liquidation::where('Transactions_id', $id)->where('save', 1)->where('status', 1)->get() as $shipments_details) {

                $Covenant_liquidation_id = $id;

                DB::transaction(function () use ($shipments_details, $id, $Covenant_liquidation_id) {
                    purchase_liquidation::find($shipments_details->id)->update(['status' => 2]);

                    $shipments_details_check = purchase_liquidation::where('Transactions_id', $id)->where('save', 1)->where('status', 0)->get();

                    if (count($shipments_details_check) == 0) {
                        Covenant_liquidation::find($Covenant_liquidation_id)->update([
                            'status' => 2
                        ]);
                    } else {
                        Covenant_liquidation::find($Covenant_liquidation_id)->update([
                            'status' => 3
                        ]);
                    }

                    $financial_accounts = financial_accounts::where('parent_account_number', 82)->where('candidate_branch', $shipments_details->branchs_id)->first();

                    // [تم الإصلاح] نفس حماية عدم وجود الحساب المالي
                    if (!$financial_accounts) {
                        return;
                    }

                    $total = $shipments_details->price_filtering;
                    financial_accounts::find($financial_accounts->id)->update(
                        [
                            // [تم الإصلاح] نفس تصحيح current_balance
                            'current_balance' => $financial_accounts->current_balance - $total,
                            'creditor_current' => $financial_accounts->creditor_current + $total,
                        ]
                    );

                    $financial_accounts_after_update = financial_accounts::find($financial_accounts->id);

                    Covenant_liquidation::find($Covenant_liquidation_id)->update(['currentblance' => $financial_accounts_after_update->debtor_current - $financial_accounts_after_update->creditor_current]);

                    credittransactions::create(
                        [
                            'attachments' => '',
                            'orginal_type' => 10,
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $total,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => __('home.Bank_transfer'),
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'date_export' => date("Y/m/d"),
                            'note' => '|  مشتريات رقم     | ' . ' : ' . (string) $shipments_details->id . '|  تصفية رقم     | ' . ' : ' . (string) $Covenant_liquidation_id,
                            'Pay_Method_Name' => __('home.Bank_transfer'),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => $financial_accounts->orginal_id ?? 0,
                            'debtor' => 0,
                            'creditor' => $total,
                            'Transactions' => 0,
                            'type' => 11,
                        ]
                    );
                });
            }
        }
        return 1;
    }

    public function get_liquitation_bystauts($branch, $statuts)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $page = request()->get('page', 1);
        $perPage = 20;

        if ($branch == '-') {
            $query = Covenant_liquidation::where('status', $statuts)
                ->where('save', 1)
                ->where('type', 1);
        } else {
            $query = Covenant_liquidation::where('status', $statuts)
                ->where('save', 1)
                ->where('branchs_id', '>=', $branch);
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
            $data = Covenant_liquidation::where('status', $statuts)->where('save', 1)->where('branchs_id', '>=', $branch)->orderBy('id', 'desc')->paginate(20);
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
        if ($branch == '-') {
            $query->where('save', 1);
        } else {
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

        $the_file_path = '';
        if ($request->has('attachments_update')) {

            $folder = 'assets//attachments';
            $image = $request->attachments_update;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] السطر ده كان $image->getClientOriginalName = $the_file_path;
            // ده بيحاول يـ"يكتب" في getClientOriginalName() كإنها property، لكنها method في UploadedFile،
            // فالسطر ده كان بيعمل حاجة واحدة بس: يضيف property جديدة اسمها getClientOriginalName
            // للـ object من غير أي تأثير حقيقي (وفي PHP 8.2+ بيدي تحذير deprecated). تم حذفه لأنه مالوش فايدة.
            $image->move($folder, $the_file_path);
        }

        // [تم الإصلاح] لفّ تعديل رصيد التصفية + تعديل بيانات المشترى نفسه جوه معاملة واحدة (transaction)
        // عشان الاتنين يتحدثوا مع بعض أو محدش منهم يتحدث خالص لو حصل خطأ في النص.
        $data = DB::transaction(function () use ($request, $the_file_path) {
            $purchase_liquidation_data = purchase_liquidation::find($request->id_update);
            $Covenant_liquidation_id = $purchase_liquidation_data->Transactions_id;
            $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
            Covenant_liquidation::find($Covenant_liquidation_id)->update([
                'price_filtering' => $Covenant_liquidation_data->price_filtering + $request->amount_update - $purchase_liquidation_data->price_filtering,
            ]);

            purchase_liquidation::find($request->id_update)->update([
                'price_filtering' => $request->amount_update,
                'note_detaials' => $request->notes_update,
                'invoice_number' => $request->Invoice_no_update,
                'attachments' => $the_file_path == '' ? $purchase_liquidation_data->attachments : $the_file_path,
            ]);

            $purshase_list = purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'purshase_list' => $purshase_list,
                'id' => $Covenant_liquidation_id
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

        Covenant_liquidation::find($request->Transactions_price_id_print)->update([
            'save' => 1,
            'status' => 1,
            'type' => 2
        ]);
        purchase_liquidation::where('Transactions_id', $request->Transactions_price_id_print)->update([
            'save' => 1
        ]);

        return redirect()->route('recent_liquidation');
    }

    public function save_liquidation_PURCHASES_data(Request $request)
    {

        Covenant_liquidation::find($request->Transactions_price_id_print)->update([
            'save' => 1,
            'status' => 1,
            'type' => 2
        ]);
        purchase_liquidation::where('Transactions_id', $request->Transactions_price_id_print)->update([
            'save' => 1
        ]);

        return redirect()->route('recent_liquidation');
    }

    public function purchase_liquidation(Request $request)
    {

        $the_file_path = '';
        if ($request->has('attachments')) {

            $folder = 'assets//attachments';
            $image = $request->attachments;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] نفس ملاحظة getClientOriginalName اللي فوق - سطر بدون أي تأثير حقيقي، تم حذفه.
            $image->move($folder, $the_file_path);
        }

        // [تم الإصلاح] لفّ إنشاء/تعديل التصفية + إنشاء سجل المشترى جوه معاملة واحدة عشان ميحصلش
        // إنشاء سجل مشترى من غير ما يترصد في التصفية الأب (أو العكس).
        $data = DB::transaction(function () use ($request, $the_file_path) {
            if ($request->transactions_id == 0) {
                $Covenant_liquidation = Covenant_liquidation::create([
                    'branchs_id' => Auth()->user()->branchs_id,
                    'user_id' => Auth()->user()->id,
                    'price_filtering' => $request->amount,
                    'note_detaials' => '-',
                    'type' => 2
                ]);
                $Covenant_liquidation_id = $Covenant_liquidation->id;
            } else {
                $Covenant_liquidation_id = $request->transactions_id;
                $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'price_filtering' => $Covenant_liquidation_data->price_filtering + $request->amount,
                ]);
            }

            purchase_liquidation::create([
                'branchs_id' => Auth()->user()->branchs_id,
                'user_id' => Auth()->user()->id,
                'price_filtering' => $request->amount,
                'note_detaials' => $request->notes,
                'invoice_number' => $request->Invoice_no,
                'attachments' => $the_file_path,
                'Transactions_id' => $Covenant_liquidation_id,
            ]);

            $purshase_list = purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'purshase_list' => $purshase_list,
                'id' => $Covenant_liquidation_id
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
        //
        $the_file_path = '';
        if ($transactions->has('attachments')) {

            $folder = 'assets//attachments';
            $image = $transactions->attachments;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] حذف سطر getClientOriginalName الغير فعّال (نفس الملاحظة أعلاه)
            $image->move($folder, $the_file_path);
        }
        $Transactions = Transactions::find($transactions->id);
        Transactions::find($transactions->id)->update([
            'loading' => $transactions->loading,
            'unloading' => $transactions->Unloading,
            'truck_data' => $transactions->truck_no,
            'invoice_number' => $transactions->invoice_no,
            'Daily' => $transactions->delay,
            'Ext' => $transactions->ext,
            'note_detaials' => $transactions->note,
            'attachments_2' => $the_file_path ?? $Transactions->attachments_2,
            'status' => 1
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
        Covenant_liquidation::find($request->Transactions_price_id_print)->update([
            'save' => 1,
            'status' => 1
        ]);
        shipments_details::where('Transactions_id', $request->Transactions_price_id_print)->update([
            'save' => 1
        ]);

        return 1;
    }

    public function save_transaction_detailsform(Request $request)
    {
        Covenant_liquidation::find($request->Transactions_price_id_print)->update([
            'save' => 1,
            'status' => 1
        ]);
        shipments_details::where('Transactions_id', $request->Transactions_price_id_print)->update([
            'save' => 1
        ]);

        return view(view: 'transfers.previous_liquidations');
    }

    public function update_transfer_to_mantop(Request $request)
    {
        //

        // [تم الإصلاح] الدالة دي بتعدل في 4 موديلات مختلفة (Transactions, Covenant_liquidation,
        // financial_accounts مرتين, credittransactions مرتين) في عملية واحدة منطقيًا (تغيير طرف
        // التحويل). لفّيتها كلها في DB::transaction عشان لو وقع خطأ في أي خطوة، كل حاجة ترجع
        // زي ما كانت بدل ما يفضل الرصيد متعدل في حساب واحد بس.
        $data = DB::transaction(function () use ($request) {
            Transactions::find($request->transactionId)->update([
                'mantob' => $request->mantop,
                'price' => $request->cashreceivedupdate,
                'partner' => $request->owner,
            ]);
            Covenant_liquidation::where('id_trasction', $request->transactionId)->update([
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

            Covenant_liquidation::where('id_trasction', $request->transactionId)->update([
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
        //
        $the_file_path = '';
        if ($request->has('attachments')) {
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
        //
        $shipments_details_old = shipments_details::find($transactions->id_update);

        $the_file_path = $shipments_details_old->attachments_2;
        if ($transactions->has('attachments_update')) {

            $folder = 'assets//attachments';
            $image = $transactions->attachments_update;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] حذف سطر getClientOriginalName الغير فعّال (نفس الملاحظة في باقي الدوال)
            $image->move($folder, $the_file_path);
        }

        // [تم الإصلاح] لفّ تعديل التصفية + تعديل تفاصيل الشحنة جوه معاملة واحدة عشان الاتنين
        // يتحدثوا مع بعض أو محدش منهم يتحدث.
        $data = DB::transaction(function () use ($transactions, $the_file_path) {
            $shipments_details = shipments_details::find($transactions->id_update);
            $Covenant_liquidation_id = $shipments_details->Transactions_id;
            $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
            Covenant_liquidation::find($Covenant_liquidation_id)->update([
                'price_filtering' => $Covenant_liquidation_data->price_filtering - ($shipments_details->Ext + $shipments_details->Daily + $shipments_details->price_shipment) +
                    ($transactions->PRICE_SHIPMENT_update + $transactions->ext_update + $transactions->delay_update),
            ]);
            shipments_details::find($transactions->id_update)->update([
                'price_shipment' => $transactions->PRICE_SHIPMENT_update,
                'loading' => $transactions->loading_update,
                'unloading' => $transactions->Unloading_update,
                'truck_data' => $transactions->truck_no_update,
                'polica_number' => $transactions->polica_number_update,
                'invoice_number' => $transactions->invoice_no_update,
                'Daily' => $transactions->delay_update,
                'Ext' => $transactions->ext_update,
                'note_detaials' => $transactions->note_update,
                'attachments_2' => $the_file_path,
            ]);
            $shipments_details = shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'shipments_details' => $shipments_details,
                'id' => $Covenant_liquidation_id
            ];
        });

        return $data;
    }

    public function add_describtion(Request $transactions)
    {
        //
        $the_file_path = '';
        if ($transactions->has('attachments')) {

            $folder = 'assets//attachments';
            $image = $transactions->attachments;
            $extension = $image->extension();
            $the_file_path = time() . rand(100, 999) . '.' . $extension;
            // [تم الإصلاح] حذف سطر getClientOriginalName الغير فعّال (نفس الملاحظة في باقي الدوال)
            $image->move($folder, $the_file_path);
        }

        // [تم الإصلاح] لفّ إنشاء/تعديل التصفية + إنشاء تفاصيل الشحنة جوه معاملة واحدة.
        $data = DB::transaction(function () use ($transactions, $the_file_path) {
            $Covenant_liquidation_id = 0;
            if ($transactions->transactions_id == 0) {
                $Covenant_liquidation = Covenant_liquidation::create([
                    'branchs_id' => Auth()->user()->branchs_id,
                    'user_id' => Auth()->user()->id,
                    'price_filtering' => $transactions->ext + $transactions->delay + $transactions->PRICE_SHIPMENT,
                    'note_detaials' => '-',
                ]);
                $Covenant_liquidation_id = $Covenant_liquidation->id;
            } else {
                $Covenant_liquidation_id = $transactions->transactions_id;
                $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);
                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'price_filtering' => $Covenant_liquidation_data->price_filtering + $transactions->ext + $transactions->delay + $transactions->PRICE_SHIPMENT,
                ]);
            }
            shipments_details::create([
                'price_shipment' => $transactions->PRICE_SHIPMENT,
                'Transactions_id' => $Covenant_liquidation_id,
                'loading' => $transactions->loading,
                'unloading' => $transactions->Unloading,
                'truck_data' => $transactions->truck_no,
                'date' => $transactions->date,
                'invoice_number' => $transactions->invoice_no,
                'Daily' => $transactions->delay,
                'Ext' => $transactions->ext,
                'note_detaials' => $transactions->note,
                'polica_number' => $transactions->polica_number,
                'attachments_2' => $the_file_path,
                'user_id' => Auth()->user()->id,
                'branchs_id' => Auth()->user()->branchs_id
            ]);
            $shipments_details = shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'shipments_details' => $shipments_details,
                'id' => $Covenant_liquidation_id
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

        // [تم الإصلاح] لفّ كل عمليات الحذف/التعديل (حساب مالي + قيود + حذف التصفية والتفاصيل)
        // جوه DB::transaction واحدة عشان لا يحصل حذف جزئي يسيب البيانات غير متزنة.
        DB::transaction(function () use ($id, $Covenant_liquidation) {
            if ($Covenant_liquidation->type != 7) {
                // تحديث معاملات العملاء
                credittransactions::where('note', 'LIKE', '%' . ' تصفية رقم     |  : ' . (string) $id . '%')->update(['customer_id' => 0]);
                // تعديل الحساب المالي
                $financial_accounts = financial_accounts::where('parent_account_number', 82)
                    ->where('candidate_branch', $Covenant_liquidation->branchs_id)
                    ->first();

                if ($financial_accounts) {
                    $financial_accounts->update([
                        // [تم الإصلاح] كانت current_balance بتتحسب من debtor_current بالغلط بدل current_balance نفسه
                        'current_balance' => $financial_accounts->current_balance + $Covenant_liquidation->price_filtering,
                        'creditor_current' => $financial_accounts->creditor_current - $Covenant_liquidation->price_filtering,
                    ]);
                }

                // حذف التصفيّة والتفاصيل
                $Covenant_liquidation->delete();
                shipments_details::where('Transactions_id', $id)->delete();
            } else {

                $credittransactions_main_data = credittransactions::find($Covenant_liquidation->id_trasction);
                // تحديث معاملات العملاء
                // تعديل الحساب المالي

                foreach (credittransactions::where('note', $credittransactions_main_data->note)->get() as $item) {

                    $financial_accounts = financial_accounts::find($item->customer_id);

                    if ($financial_accounts) {
                        $financial_accounts->update([
                            // [تم الإصلاح] price_filtering مش عمود موجود في جدول credittransactions أصلاً
                            // (موجود بس في Covenant_liquidation / purchase_liquidation) فكانت قيمته دايمًا null
                            // ومكانش بيعمل أي تعديل حقيقي على current_balance. استبدلتها بعكس أثر القيد
                            // (الدائن يرجع يزود الرصيد، والمدين يرجع ينقصه) بنفس منطق باقي الدوال في نفس الملف.
                            // تأكد من صحة الإشارة (+/-) دي بعد التجربة لأنها بناءً على استنتاج من باقي الكود.
                            'current_balance' => $financial_accounts->current_balance + $item->creditor - $item->debtor,
                            'debtor_current' => $financial_accounts->debtor_current - $item->debtor,
                            'creditor_current' => $financial_accounts->creditor_current - $item->creditor,
                        ]);
                    }
                }

                credittransactions::where('note', $credittransactions_main_data->note)->update(['customer_id' => 0]);

                // حذف التصفيّة والتفاصيل
                $Covenant_liquidation->delete();
                shipments_details::where('Transactions_id', $id)->delete();
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

        // [تم الإصلاح] لفّ تحديث حالة التصفية + تحديث الشحنات/المشتريات + تعديل الحساب المالي
        // جوه DB::transaction واحدة.
        DB::transaction(function () use ($id, $Covenant_liquidation) {
            // تحديث حالة التصفيّة
            $Covenant_liquidation->update([
                'status' => 1,
                'owner_confirm' => 0,
            ]);

            if ($Covenant_liquidation->type == 1) {
                // تحديث تفاصيل الشحن
                shipments_details::where('Transactions_id', $id)->update([
                    'status' => 0
                ]);

                // تحديث معاملات العملاء والحساب المالي
                credittransactions::where('note', 'LIKE', '%' . ' تصفية رقم     |  : ' . (string) $id . '%')
                    ->update(['customer_id' => 0]);

                $financial_accounts = financial_accounts::where('parent_account_number', 82)
                    ->where('candidate_branch', $Covenant_liquidation->branchs_id)
                    ->first();

                if ($financial_accounts) {
                    $financial_accounts->update([
                        // [تم الإصلاح] نفس تصحيح current_balance
                        'current_balance' => $financial_accounts->current_balance + $Covenant_liquidation->price_filtering,
                        'creditor_current' => $financial_accounts->creditor_current - $Covenant_liquidation->price_filtering,
                    ]);
                }
            } else {
                // تحديث تفاصيل المشتريات
                purchase_liquidation::where('Transactions_id', $id)->update([
                    'status' => 0
                ]);

                // تحديث معاملات العملاء والحساب المالي
                credittransactions::where('note', 'LIKE', '%' . ' تصفية رقم     |  :' . ' ' . (string) $id . ' ' . '%')
                    ->update(['customer_id' => 0]);

                $financial_accounts = financial_accounts::where('parent_account_number', 82)
                    ->where('candidate_branch', $Covenant_liquidation->branchs_id)
                    ->first();

                if ($financial_accounts) {
                    $financial_accounts->update([
                        // [تم الإصلاح] نفس تصحيح current_balance
                        'current_balance' => $financial_accounts->current_balance + $Covenant_liquidation->price_filtering,
                        'creditor_current' => $financial_accounts->creditor_current - $Covenant_liquidation->price_filtering,
                    ]);
                }
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

    public function confirm_cancel_purtchase($id)
    {
        $Covenant_liquidation = Covenant_liquidation::find($id);
        if ($Covenant_liquidation == NULL) {
            return 0;
        }

        // [تم الإصلاح] لفّ كل التعديلات جوه DB::transaction واحدة.
        DB::transaction(function () use ($id, $Covenant_liquidation) {
            Covenant_liquidation::find($id)->update([
                'status' => 1,
                'owner_confirm' => 0,
            ]);

            purchase_liquidation::where('Transactions_id', $id)->update([
                'status' => 0
            ]);

            credittransactions::where('note', 'LIKE', '%' . ' تصفية رقم     |  : ' . (string) $id . '%')->update(['customer_id' => 0]);
            $financial_accounts = financial_accounts::where('parent_account_number', 82)->where('candidate_branch', $Covenant_liquidation->branchs_id)->first();

            // [تم الإصلاح] الكود القديم كان بيستخدم $financial_accounts->id على طول من غير ما يتأكد
            // إن first() رجع نتيجة أصلاً - لو مفيش حساب مطابق كان هيعمل Fatal Error.
            if ($financial_accounts) {
                financial_accounts::find($financial_accounts->id)->update(
                    [
                        // [تم الإصلاح] نفس تصحيح current_balance
                        'current_balance' => $financial_accounts->current_balance + $Covenant_liquidation->price_filtering,
                        'creditor_current' => $financial_accounts->creditor_current - $Covenant_liquidation->price_filtering,
                    ]
                );
            }
        });

        return 1;
    }

    public function confirm_data_shipment($id)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        // [تم الإصلاح] لفّ تحديث حالة الشحنة + تعديل الحساب المالي + إنشاء قيد الحركة جوه
        // DB::transaction واحدة عشان الحركة المالية والتحديث يتسجلوا مع بعض أو محدش منهم.
        $data = DB::transaction(function () use ($id) {
            $shipments_details = shipments_details::find($id);
            $Covenant_liquidation_id = $shipments_details->Transactions_id;
            $Covenant_liquidation_data = Covenant_liquidation::find($Covenant_liquidation_id);

            shipments_details::find($id)->update(['status' => 1]);

            $shipments_check = shipments_details::where('Transactions_id', $Covenant_liquidation_id)->where('status', 0)->get();

            if (count($shipments_check) == 0) {
                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'manager_check' => 1,
                    'manager' => Auth()->user()->id
                ]);
            }

            $Covenant_liquidation_status = Covenant_liquidation::find($Covenant_liquidation_id);

            if ($Covenant_liquidation_status->owner_confirm == 1) {

                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'status' => 2
                ]);

                $financial_accounts = financial_accounts::where('parent_account_number', 82)->where('candidate_branch', $shipments_details->branchs_id)->first();

                // [تم الإصلاح] حماية من عدم وجود الحساب المالي (كان بيستخدم $financial_accounts->id مباشرة من غير تأكد)
                if ($financial_accounts) {
                    $total = $shipments_details->price_shipment + $shipments_details->Ext + $shipments_details->Daily;
                    financial_accounts::find($financial_accounts->id)->update(
                        [
                            // [تم الإصلاح] نفس تصحيح current_balance
                            'current_balance' => $financial_accounts->current_balance - $total,
                            'creditor_current' => $financial_accounts->creditor_current + $total,
                        ]
                    );

                    $financial_accounts_after_update = financial_accounts::find($financial_accounts->id);

                    Covenant_liquidation::find($Covenant_liquidation_id)->update(['currentblance' => $financial_accounts_after_update->debtor_current - $financial_accounts_after_update->creditor_current]);

                    credittransactions::create(
                        [
                            'attachments' => '',
                            'orginal_type' => 10,
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $total,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => __('home.Bank_transfer'),
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'date_export' => date("Y/m/d"),
                            'note' => '|  شحنة رقم     | ' . ' : ' . (string) $id . '|  تصفية رقم     | ' . ' : ' . (string) $Covenant_liquidation_id,
                            'Pay_Method_Name' => __('home.Bank_transfer'),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => $financial_accounts->orginal_id ?? 0,
                            'debtor' => 0,
                            'creditor' => $total,
                            'Transactions' => 0,
                            'type' => 11,
                        ]
                    );
                }
            }

            $shipments_details = shipments_details::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'shipments_details' => $shipments_details,
                'id' => $Covenant_liquidation_id
            ];
        });

        return $data;
    }

    public function confirm_data_purchases($id)
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        // [تم الإصلاح] نفس مبدأ confirm_data_shipment: لفّ كل التعديلات المالية والحركات جوه
        // DB::transaction واحدة.
        $data = DB::transaction(function () use ($id) {
            $shipments_details = purchase_liquidation::find($id);
            $Covenant_liquidation_id = $shipments_details->Transactions_id;

            purchase_liquidation::find($id)->update(['status' => 1]);
            $shipments_check = purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->where('status', 0)->get();
            if (count($shipments_check) == 0) {
                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'manager_check' => 1,
                    'manager' => Auth()->user()->id
                ]);
            }

            $Covenant_liquidation = Covenant_liquidation::find($Covenant_liquidation_id);

            if ($Covenant_liquidation->owner_confirm == 1) {

                Covenant_liquidation::find($Covenant_liquidation_id)->update([
                    'status' => 2
                ]);

                purchase_liquidation::find($id)->update(['status' => 2]);

                $financial_accounts = financial_accounts::where('parent_account_number', 82)->where('candidate_branch', $shipments_details->branchs_id)->first();

                // [تم الإصلاح] حماية من عدم وجود الحساب المالي
                if ($financial_accounts) {
                    $total = $shipments_details->price_filtering;
                    financial_accounts::find($financial_accounts->id)->update(
                        [
                            // [تم الإصلاح] نفس تصحيح current_balance
                            'current_balance' => $financial_accounts->current_balance - $total,
                            'creditor_current' => $financial_accounts->creditor_current + $total,
                        ]
                    );

                    $financial_accounts_after_update = financial_accounts::find($financial_accounts->id);

                    Covenant_liquidation::find($Covenant_liquidation_id)->update(['currentblance' => $financial_accounts_after_update->debtor_current - $financial_accounts_after_update->creditor_current]);

                    credittransactions::create(
                        [
                            'attachments' => '',
                            'orginal_type' => 10,
                            'user_id' => Auth()->user()->id,
                            'customer_id' => $financial_accounts->id,
                            'recive_amount' => $total,
                            'branchs_id' => Auth()->user()->branchs_id,
                            'pay_method' => __('home.Bank_transfer'),
                            'created_at' => \Carbon\Carbon::now()->addHours(3),
                            'date_export' => date("Y/m/d"),
                            'note' => '|  مشتريات رقم     | ' . ' : ' . (string) $id . '|  تصفية رقم     | ' . ' : ' . (string) $Covenant_liquidation_id,
                            'Pay_Method_Name' => __('home.Bank_transfer'),
                            'updated_at' => \Carbon\Carbon::now()->addHours(3),
                            'orginal_id' => $financial_accounts->orginal_id ?? 0,
                            'debtor' => 0,
                            'creditor' => $total,
                            'Transactions' => 0,
                            'type' => 11,
                        ]
                    );
                }
            }
            $shipments_details = purchase_liquidation::where('Transactions_id', $Covenant_liquidation_id)->get();

            return [
                'shipments_details' => $shipments_details,
                'id' => $Covenant_liquidation_id
            ];
        });

        return $data;
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