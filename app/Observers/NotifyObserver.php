<?php

namespace App\Observers;

use App\Models\truck_trip;
use App\Models\Transactions;
use App\Models\Covenant_liquidation;
use App\Models\financial_accounts;
use App\Models\branchs;
use App\Services\AppNotifier as N;

/**
 * بيبعت الإشعارات تلقائي لما:
 *  - شاحنة تتحمّل / تنزّل                     => كل المستخدمين
 *  - تحويل جديد                              => مستخدمين الفرع المستلم
 *  - تصفية (شحنات/مشتريات) تتعتمد (status=2)   => مستخدمين فرع التصفية
 *  - تصفية شحنات/مشتريات جديدة تتحفظ           => المستخدم رقم 11
 * كل حاجة جوه try/catch عشان الإشعار عمره ما يوقف الحفظ الأصلي
 */
class NotifyObserver
{
    // ================= الشاحنات =================
    public static function tripCreated(truck_trip $trip)
    {
        try {
            $plate = optional($trip->truck)->plate_number;
            N::toAll([
                'type'  => 'truck_loaded',
                'title' => '🚚 شاحنة حمّلت: ' . $plate,
                'body'  => $trip->from_region . ' ← ' . $trip->to_region . ' · ' . $trip->load_type
                         . ($trip->expected_unloading_at ? ' · التنزيل المتوقع ' . $trip->expected_unloading_at->format('m/d h:i A') : ''),
                'url'   => N::link('trucks/board'),
                'icon'  => 'bx-upload',
                'color' => '#F59E0B',
            ], 'trip_load_' . $trip->id);
        } catch (\Throwable $e) { report($e); }
    }

    public static function tripUpdated(truck_trip $trip)
    {
        try {
            if ($trip->wasChanged('status') && $trip->status == truck_trip::UNLOADED) {
                $plate = optional($trip->truck)->plate_number;
                N::toAll([
                    'type'  => 'truck_unloaded',
                    'title' => '✅ شاحنة نزّلت: ' . $plate,
                    'body'  => 'في ' . $trip->to_region . ($trip->to_city ? ' - ' . $trip->to_city : '') . ' · بقت فاضية',
                    'url'   => N::link('trucks/board'),
                    'icon'  => 'bx-check-double',
                    'color' => '#10B981',
                ], 'trip_unload_' . $trip->id);
            }
        } catch (\Throwable $e) { report($e); }
    }

    // ================= التحويلات =================
    public static function transferCreated(Transactions $t)
    {
        try {
            $to = $t->mantob ? financial_accounts::find($t->mantob) : null;
            $n = [
                'type'  => 'transfer',
                'title' => '💸 تحويل جديد لفرعك #' . $t->id,
                'body'  => 'مبلغ ' . N::money($t->price) . ($to ? ' · للمستلم: ' . $to->name : ''),
                'url'   => N::link('previousTransfers'),
                'icon'  => 'bx-transfer-alt',
                'color' => '#2F6FED',
            ];
            $branches = collect([$t->branchs_id, $to->candidate_branch ?? null])->filter()->unique();
            foreach ($branches as $b) {
                N::toBranch($b, $n, 'transfer_' . $t->id);
            }
        } catch (\Throwable $e) { report($e); }
    }

    // ================= التصفيات =================
    public static function liquidationSaved(Covenant_liquidation $l)
    {
        try {
            if (!in_array((int) $l->type, [1, 2], true)) return;   // 1 = شحنات ، 2 = مشتريات
            $kind = $l->type == 2 ? 'تصفية مشتريات' : 'تصفية شحنات';
            $url  = N::link(($l->type == 2 ? 'print_full_purchase/' : 'print_transfers_after_full/') . $l->id);

            // تصفية جديدة اتحفظت => المستخدم 11
            $justSaved = $l->save == 1 && ($l->wasRecentlyCreated || $l->wasChanged('save'));
            if ($justSaved) {
                $branch = $l->branchs_id ? optional(branchs::find($l->branchs_id))->name : null;
                $by     = optional($l->user)->name;
                N::toUser(N::ADMIN_USER_ID, [
                    'type'  => 'liquidation_new',
                    'title' => '📄 ' . $kind . ' جديدة #' . $l->id,
                    'body'  => trim(($branch ? 'فرع ' . $branch . ' · ' : '') . ($by ? 'بواسطة ' . $by . ' · ' : '') . N::money($l->price_filtering), ' ·'),
                    'url'   => $url,
                    'icon'  => 'bx-file',
                    'color' => '#8B5CF6',
                ], 'liq_new_' . $l->id);
            }

            // التصفية اتعتمدت => مستخدمين الفرع
            if ($l->wasChanged('status') && $l->status == 2) {
                N::toBranch($l->branchs_id, [
                    'type'  => 'liquidation_ok',
                    'title' => '✔️ تم اعتماد ' . $kind . ' #' . $l->id,
                    'body'  => 'المبلغ ' . N::money($l->price_filtering),
                    'url'   => $url,
                    'icon'  => 'bx-check-shield',
                    'color' => '#10B981',
                ], 'liq_ok_' . $l->id);
            }
        } catch (\Throwable $e) { report($e); }
    }

    /** تسجيل الأحداث (بيتنادى من AppServiceProvider) */
    public static function register()
    {
        truck_trip::created(function ($m) { self::tripCreated($m); });
        truck_trip::updated(function ($m) { self::tripUpdated($m); });
        Transactions::created(function ($m) { self::transferCreated($m); });
        Covenant_liquidation::saved(function ($m) { self::liquidationSaved($m); });
    }
}
