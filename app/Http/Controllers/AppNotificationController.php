<?php

namespace App\Http\Controllers;

use App\Models\truck_trip;
use App\Services\AppNotifier as N;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

class AppNotificationController extends Controller
{
    private function q()
    {
        return DB::table('app_notifications')->where('user_id', auth()->id());
    }

    /** الجرس في الهيدر بيسأل هنا كل 30 ثانية */
    public function feed()
    {
        if (!N::ensureTable()) return response()->json(['unread' => 0, 'items' => []]);
        $this->generateTruckAlerts();

        $items = $this->q()->orderByDesc('id')->limit(15)->get()->map(function ($n) {
            return [
                'id'    => $n->id,
                'title' => $n->title,
                'body'  => $n->body,
                'icon'  => $n->icon,
                'color' => $n->color,
                'read'  => (bool) $n->read_at,
                'ago'   => \Carbon\Carbon::parse($n->created_at)->locale('ar')->diffForHumans(),
                'open'  => url('notifications/' . $n->id . '/open'),
            ];
        });
        return response()->json([
            'unread' => $this->q()->whereNull('read_at')->count(),
            'items'  => $items,
        ]);
    }

    /** فتح إشعار = يتعلّم مقروء ويروح للصفحة بتاعته */
    public function open($id)
    {
        $n = $this->q()->where('id', $id)->first();
        if (!$n) return redirect(url('notifications'));
        $this->q()->where('id', $id)->update(['read_at' => now()]);
        return redirect($n->url ?: url('notifications'));
    }

    public function readAll()
    {
        if (N::ensureTable()) $this->q()->whereNull('read_at')->update(['read_at' => now()]);
        return request()->expectsJson() ? response()->json(['ok' => true]) : back();
    }

    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        N::ensureTable();
        $items = $this->q()->orderByDesc('id')->paginate(30);
        return view('notifications.index', compact('items'));
    }

    // ================= Web Push =================
    /** المتصفح بيبعت هنا بيانات الاشتراك بعد ما المستخدم يوافق */
    public function pushSubscribe(Request $request)
    {
        if (!N::ensureTable() || !N::pushEnabled()) {
            return response()->json(['ok' => false, 'message' => 'Web Push مش متفعل على السيرفر'], 400);
        }
        $request->validate([
            'endpoint'    => 'required|url|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
        ]);
        DB::table('push_subscriptions')->updateOrInsert(
            ['endpoint' => $request->endpoint],
            [
                'user_id'    => auth()->id(),
                'p256dh'     => $request->input('keys.p256dh'),
                'auth'       => $request->input('keys.auth'),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 250),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        return response()->json(['ok' => true]);
    }

    public function pushUnsubscribe(Request $request)
    {
        if (N::ensureTable() && $request->filled('endpoint')) {
            DB::table('push_subscriptions')->where('endpoint', $request->endpoint)->where('user_id', auth()->id())->delete();
        }
        return response()->json(['ok' => true]);
    }

    /** إشعار تجريبي لنفس المستخدم (عشان يتأكد إن الـ Push واصل) */
    public function pushTest()
    {
        N::send([auth()->id()], [
            'type'  => 'test',
            'title' => '🔔 إشعار تجريبي',
            'body'  => 'لو شايف الرسالة دي، إشعارات المتصفح شغالة على الجهاز ده',
            'url'   => url('notifications'),
            'icon'  => 'bx-bell',
            'color' => '#2F6FED',
        ], null, false);
        return response()->json(['ok' => true]);
    }

    /**
     * تنبيهات الشاحنات اللي بتعتمد على الوقت (مفيش cron):
     *  - قربت تنزّل (فاضل ساعة أو أقل على معاد التنزيل المتوقع)
     *  - متأخرة (عدّى معاد التنزيل المتوقع)
     * بتتحسب مرة كل دقيقة بالكتير، والـ ref_key بيمنع التكرار
     */
    private function generateTruckAlerts()
    {
        try {
            if (!Schema::hasTable('truck_trips')) return;
            if (!Cache::add('ahl_truck_alerts_check', 1, 60)) return;

            $now  = truck_trip::nowLocal();
            $soon = $now->copy()->addHour();
            $trips = truck_trip::with('truck')->where('status', truck_trip::LOADED)
                ->whereNotNull('expected_unloading_at')
                ->where('expected_unloading_at', '<=', $soon->format('Y-m-d H:i:s'))
                ->where('expected_unloading_at', '>=', $now->copy()->subDays(3)->format('Y-m-d H:i:s'))
                ->get();

            foreach ($trips as $t) {
                $plate = optional($t->truck)->plate_number;
                $late  = $t->expected_unloading_at->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s');
                if ($late) {
                    N::toAll([
                        'type'  => 'truck_overdue',
                        'title' => '⚠️ شاحنة متأخرة عن التنزيل: ' . $plate,
                        'body'  => $t->from_region . ' ← ' . $t->to_region . ' · كان المفروض تنزّل ' . $t->expected_unloading_at->format('m/d h:i A'),
                        'url'   => N::link('trucks/board'),
                        'icon'  => 'bx-error',
                        'color' => '#EF4444',
                    ], 'trip_late_' . $t->id, false);
                } else {
                    N::toAll([
                        'type'  => 'truck_soon',
                        'title' => '⏰ شاحنة قربت تنزّل: ' . $plate,
                        'body'  => 'متوقع التنزيل ' . $t->expected_unloading_at->format('h:i A') . ' في ' . $t->to_region . ($t->to_city ? ' - ' . $t->to_city : ''),
                        'url'   => N::link('trucks/board'),
                        'icon'  => 'bx-time-five',
                        'color' => '#F59E0B',
                    ], 'trip_soon_' . $t->id, false);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
