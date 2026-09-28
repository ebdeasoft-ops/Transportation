<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * نظام الإشعارات الداخلي
 * - كل إشعار = صف لكل مستخدم في جدول app_notifications
 * - ref_key بيمنع تكرار نفس الإشعار لنفس المستخدم
 * - أي خطأ هنا مش بيوقف العملية الأصلية (حفظ تحويل / تصفية / حمولة)
 */
class AppNotifier
{
    /** المستخدم اللي بيشوف إشعار أي تصفية شحنات/مشتريات جديدة */
    const ADMIN_USER_ID = 11;

    private static $ready = null;

    public static function ensureTable()
    {
        if (self::$ready !== null) return self::$ready;
        try {
            if (!Schema::hasTable('app_notifications')) {
                Schema::create('app_notifications', function (Blueprint $t) {
                    $t->id();
                    $t->bigInteger('user_id')->unsigned();
                    $t->string('type', 40)->nullable();
                    $t->string('title');
                    $t->text('body')->nullable();
                    $t->string('url', 500)->nullable();
                    $t->string('icon', 50)->nullable();
                    $t->string('color', 20)->nullable();
                    $t->string('ref_key', 150)->nullable();
                    $t->timestamp('read_at')->nullable();
                    $t->timestamps();
                    $t->index(['user_id', 'read_at']);
                    $t->unique(['user_id', 'ref_key']);
                });
            }
            // اشتراكات Web Push (كل جهاز / متصفح فعّل الإشعارات)
            if (!Schema::hasTable('push_subscriptions')) {
                Schema::create('push_subscriptions', function (Blueprint $t) {
                    $t->id();
                    $t->bigInteger('user_id')->unsigned();
                    $t->string('endpoint', 500);
                    $t->string('p256dh');
                    $t->string('auth');
                    $t->string('user_agent')->nullable();
                    $t->timestamps();
                    $t->index('user_id');
                    $t->unique('endpoint');
                });
            }
            return self::$ready = true;
        } catch (\Throwable $e) {
            report($e);
            return self::$ready = false;
        }
    }

    /**
     * إرسال إشعار لمجموعة مستخدمين
     * $n = ['type','title','body','url','icon','color']
     */
    public static function send($userIds, array $n, $refKey = null, $exceptActor = true)
    {
        try {
            if (!self::ensureTable()) return;
            $ids = collect($userIds)->filter()->map(function ($x) { return (int) $x; })->unique();
            if ($exceptActor && auth()->check()) {
                $ids = $ids->reject(function ($id) { return $id === (int) auth()->id(); });
            }
            if ($ids->isEmpty()) return;

            // اللي وصله نفس الإشعار قبل كده (نفس ref_key) منبعتلوش تاني
            if ($refKey) {
                $already = DB::table('app_notifications')->where('ref_key', $refKey)->whereIn('user_id', $ids->all())->pluck('user_id')->map(function ($x) { return (int) $x; });
                $ids = $ids->diff($already)->values();
                if ($ids->isEmpty()) return;
            }

            $now = now();
            $rows = $ids->map(function ($uid) use ($n, $refKey, $now) {
                return [
                    'user_id'    => $uid,
                    'type'       => $n['type'] ?? null,
                    'title'      => mb_substr($n['title'] ?? '', 0, 250),
                    'body'       => $n['body'] ?? null,
                    'url'        => $n['url'] ?? null,
                    'icon'       => $n['icon'] ?? 'bx-bell',
                    'color'      => $n['color'] ?? '#2F6FED',
                    'ref_key'    => $refKey,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->values()->all();

            // insertOrIgnore: لو نفس الإشعار (ref_key) اتبعت قبل كده لنفس المستخدم مش هيتكرر
            DB::table('app_notifications')->insertOrIgnore($rows);

            // Web Push: بيتبعت بعد ما الصفحة ترد على المستخدم عشان الحفظ ما يبطأش
            $pushIds = $ids->all();
            app()->terminating(function () use ($pushIds, $n, $refKey) {
                self::push($pushIds, $n, $refKey);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Web Push مفعّل؟ (المكتبة متسطبة + المفاتيح موجودة في .env) */
    public static function pushEnabled()
    {
        return class_exists(\Minishlink\WebPush\WebPush::class)
            && config('services.webpush.public_key') && config('services.webpush.private_key');
    }

    /** إرسال Web Push لكل أجهزة المستخدمين دول */
    public static function push(array $userIds, array $n, $refKey = null)
    {
        try {
            if (!self::pushEnabled() || empty($userIds) || !Schema::hasTable('push_subscriptions')) return;
            $subs = DB::table('push_subscriptions')->whereIn('user_id', $userIds)->get();
            if ($subs->isEmpty()) return;

            $webPush = new \Minishlink\WebPush\WebPush([
                'VAPID' => [
                    'subject'    => config('services.webpush.subject'),
                    'publicKey'  => config('services.webpush.public_key'),
                    'privateKey' => config('services.webpush.private_key'),
                ],
            ], ['TTL' => 86400]);   // لو الجهاز مقفول، الرسالة تستنى لحد 24 ساعة

            $payload = json_encode([
                'title' => $n['title'] ?? '',
                'body'  => $n['body'] ?? '',
                'url'   => $n['url'] ?? url('notifications'),
                'tag'   => $refKey ?: ($n['type'] ?? null),
            ], JSON_UNESCAPED_UNICODE);

            foreach ($subs as $s) {
                $webPush->queueNotification(
                    \Minishlink\WebPush\Subscription::create([
                        'endpoint' => $s->endpoint,
                        'keys'     => ['p256dh' => $s->p256dh, 'auth' => $s->auth],
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                // الاشتراك انتهى (المستخدم لغى الإذن أو مسح بيانات المتصفح) => نمسحه
                if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                    DB::table('push_subscriptions')->where('endpoint', $report->getEndpoint())->delete();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function toAll(array $n, $refKey = null, $exceptActor = true)
    {
        self::send(User::where('active', 1)->pluck('id'), $n, $refKey, $exceptActor);
    }

    public static function toBranch($branchId, array $n, $refKey = null, $exceptActor = true)
    {
        if (!$branchId) return;
        self::send(User::where('branchs_id', $branchId)->where('active', 1)->pluck('id'), $n, $refKey, $exceptActor);
    }

    public static function toUser($userId, array $n, $refKey = null, $exceptActor = true)
    {
        self::send([$userId], $n, $refKey, $exceptActor);
    }

    /** رابط بالبادئة الخاصة باللغة */
    public static function link($path)
    {
        try {
            $lp = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
        } catch (\Throwable $e) {
            $lp = 'ar';
        }
        return url($lp . '/' . ltrim($path, '/'));
    }

    public static function money($v)
    {
        return number_format((float) $v, 2) . ' ر.س';
    }
}
