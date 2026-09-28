<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\Covenant_liquidation;
use App\Models\financial_accounts;
use App\Models\waybill;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * الشاشة الرئيسية - كل الأرقام بتتحسب هنا مرة واحدة بدل الاستعلامات الكتير اللي كانت جوه الـ view
 */
class DashboardController extends Controller
{
    /** التصفيات المعتمدة (نفس فلتر الشاشة القديمة) */
    private function liq($branchId = null)
    {
        $q = Covenant_liquidation::whereNotIn('type', [3, 7, 8, 9, 10])->where('save', 1);
        if ($branchId !== null) $q->where('branchs_id', $branchId);
        return $q;
    }

    private function trx($branchId = null)
    {
        $q = Transactions::query();
        if ($branchId !== null) $q->where('branchs_id', $branchId);
        return $q;
    }

    /** أرقام اليوم / الشهر لنطاق معين (كل الفروع أو فرع واحد) */
    private function stats($branchId = null)
    {
        $today      = date('Y-m-d');
        $yesterday  = date('Y-m-d', strtotime('-1 day'));
        $monthStart = date('Y-m-01');

        $s = [];
        $s['trx_today_count']   = $this->trx($branchId)->whereDate('created_at', $today)->count();
        $s['trx_today_amount']  = (float) $this->trx($branchId)->whereDate('created_at', $today)->sum('price');
        $s['trx_yest_count']    = $this->trx($branchId)->whereDate('created_at', $yesterday)->count();
        $s['trx_month_count']   = $this->trx($branchId)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->count();
        $s['trx_month_amount']  = (float) $this->trx($branchId)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->sum('price');

        $s['liq_today_count']   = $this->liq($branchId)->whereDate('created_at', $today)->count();
        $s['liq_today_amount']  = (float) $this->liq($branchId)->whereDate('created_at', $today)->sum('price_filtering');
        $s['liq_yest_count']    = $this->liq($branchId)->whereDate('created_at', $yesterday)->count();
        $s['liq_month_count']   = $this->liq($branchId)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->count();
        $s['liq_month_amount']  = (float) $this->liq($branchId)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->sum('price_filtering');

        $s['liq_today_confirmed']   = $this->liq($branchId)->where('status', 2)->whereDate('created_at', $today)->count();
        $s['liq_today_unconfirmed'] = $this->liq($branchId)->where('status', 1)->whereDate('created_at', $today)->count();
        $s['liq_month_confirmed']   = $this->liq($branchId)->where('status', 2)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->count();
        $s['liq_month_unconfirmed'] = $this->liq($branchId)->where('status', 1)->whereDate('created_at', '>=', $monthStart)->whereDate('created_at', '<=', $today)->count();

        return $s;
    }

    /** بيانات آخر 7 أيام للرسوم (استعلام واحد لكل نوع) */
    private function last7($branchId = null)
    {
        $from = date('Y-m-d', strtotime('-6 days'));
        $days = [];
        $labelsAr = ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'];
        $labelsEn = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $ar = app()->getLocale() == 'ar';
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $days[$d] = ($ar ? $labelsAr : $labelsEn)[date('w', strtotime($d))];
        }

        $group = function ($q, $sumCol = null) use ($from) {
            $sel = 'DATE(created_at) as d, COUNT(*) as c' . ($sumCol ? ", SUM($sumCol) as s" : '');
            return $q->whereDate('created_at', '>=', $from)->select(DB::raw($sel))
                ->groupBy(DB::raw('DATE(created_at)'))->get()->keyBy('d');
        };

        $trx  = $group($this->trx($branchId), 'price');
        $liq  = $group($this->liq($branchId));
        $conf = $group($this->liq($branchId)->where('status', 2));
        $unc  = $group($this->liq($branchId)->where('status', 1));

        $out = ['labels' => array_values($days), 'trx' => [], 'trx_amount' => [], 'liq' => [], 'confirmed' => [], 'unconfirmed' => []];
        foreach (array_keys($days) as $d) {
            $out['trx'][]         = (int) ($trx[$d]->c ?? 0);
            $out['trx_amount'][]  = round((float) ($trx[$d]->s ?? 0), 2);
            $out['liq'][]         = (int) ($liq[$d]->c ?? 0);
            $out['confirmed'][]   = (int) ($conf[$d]->c ?? 0);
            $out['unconfirmed'][] = (int) ($unc[$d]->c ?? 0);
        }
        return $out;
    }

    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $user = Auth()->user();

        // المستخدمين 11 (Admin) و 29 (الرئيسي) بس هما اللي يشوفوا بيانات كل الفروع
        // باقي المستخدمين (بما فيهم فرع الإدارة 9) بيشوفوا بيانات فرعهم بس
        $isAdminBranch = in_array((int) $user->id, [11, 29]); // المستخدمين اللي يشوفوا كل الفروع
        $scope = $isAdminBranch ? null : $user->branchs_id;

        // كل الأرقام والرسوم: الإدارة = كل الفروع ، باقي الفروع = فرعهم بس
        $all   = $this->stats($scope);
        $chart = $this->last7($scope);

        // الحسابات المالية (نفس شروط الشاشة القديمة)
        if ($isAdminBranch) {
            $accounts = financial_accounts::where('id', '!=', 1258)->where('parent_account_number', 4)->where('branchs_id', null)
                ->orWhere('branchs_id', 9)->where('parent_account_number', 4)->where('id', '!=', 1258)->get()
                ->merge(financial_accounts::where('parent_account_number', 82)->where('id', '!=', 1258)->get())
                ->values();
        } else {
            // باقي الفروع: حسابات فرعهم بس
            $accounts = financial_accounts::whereIn('parent_account_number', [4, 82])->where('id', '!=', 1258)
                ->where('branchs_id', $user->branchs_id)->get()->values();
        }
        $custody = $accounts->filter(function ($a) {
            return mb_strpos($a->name, 'عهدة') !== false;
        })->values();

        // بوليصات الشحن (لو الجداول موجودة)
        $wb = ['enabled' => false, 'today' => 0, 'month' => 0, 'month_fare' => 0, 'recent' => collect()];
        if (Schema::hasTable('waybills')) {
            $wb['enabled']    = true;
            $wbq = function () use ($scope) {
                return waybill::when($scope !== null, function ($q) use ($scope) { $q->where('branchs_id', $scope); });
            };
            $wb['today']      = $wbq()->whereDate('date', date('Y-m-d'))->count();
            $wb['month']      = $wbq()->whereDate('date', '>=', date('Y-m-01'))->count();
            $wb['month_fare'] = (float) $wbq()->whereDate('date', '>=', date('Y-m-01'))->sum('total_fare');
            $wb['recent']     = waybill::when($scope !== null, function ($q) use ($scope) { $q->where('branchs_id', $scope); })->orderByDesc('id')->limit(6)->get();
        }

        $recentTrx = $this->trx($scope)->orderByDesc('id')->limit(6)->get();
        // أسماء الحسابات: mantob = المحوَّل له (المستلم) ، partner = الحساب المحوَّل منه
        $accIds   = $recentTrx->pluck('mantob')->merge($recentTrx->pluck('partner'))->filter()->unique()->values();
        $accNames = $accIds->count() ? financial_accounts::whereIn('id', $accIds)->pluck('name', 'id') : collect();
        $userNames = \App\Models\User::whereIn('id', $recentTrx->pluck('user_id')->filter()->unique())->pluck('name', 'id');
        foreach ($recentTrx as $tr) {
            $tr->to_name   = $accNames[$tr->mantob] ?? null;
            $tr->from_name = $accNames[$tr->partner] ?? null;
            $tr->by_name   = $userNames[$tr->user_id] ?? null;
        }

        // أحدث التصفيات (للإدارة)
        $recentLiq = $this->liq($scope)->with(['user', 'branch'])->orderByDesc('id')->limit(6)->get();

        // حالة الأسطول (تظهر لكل المستخدمين): الفاضية في كل منطقة + المحمّلة من فين لفين
        $fleet = null;
        if (Schema::hasTable('truck_trips') && Schema::hasColumn('waybill_trucks', 'current_region')) {
            $regions = \App\Models\truck_trip::REGIONS;
            $trucksAll = \App\Models\waybill_truck::with('activeTrip.driver')->get();
            $byRegion = array_fill_keys($regions, 0);
            $byRegion['غير محدد'] = 0;
            $loadedList = collect();
            $available = 0;
            foreach ($trucksAll as $t) {
                if ($t->activeTrip) {
                    $trip = $t->activeTrip; $trip->setRelation('truck', $t);
                    $loadedList->push($trip);
                } elseif ($t->ownership === 'own') {
                    // المتاحة = الفاضية ملك المؤسسة بس
                    $key = in_array($t->current_region, $regions) ? $t->current_region : 'غير محدد';
                    $byRegion[$key]++;
                    $available++;
                }
            }
            $fleet = [
                'total'    => $trucksAll->count(),
                'empty'    => $available,
                'loaded'   => $loadedList->count(),
                'overdue'  => $loadedList->filter(function ($x) { return $x->is_overdue; })->count(),
                'byRegion' => $byRegion,
                'loadedList' => $loadedList->sortBy('expected_unloading_at')->values(),
            ];
        }

        // قسم الفرع (نفس شرط الشاشة القديمة)
        $branch = null;
        if ($isAdminBranch && $user->id != 11) {
            $branch = $this->stats($user->branchs_id);
        }

        return view('index', compact('all', 'chart', 'accounts', 'custody', 'wb', 'recentTrx', 'recentLiq', 'branch', 'isAdminBranch', 'fleet'));
    }
}
