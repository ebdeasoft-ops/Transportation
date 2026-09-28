<?php

namespace App\Http\Controllers;

use App\Models\truck_trip;
use App\Support\AppSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * المناطق (من / إلى / مكان الشاحنة)
 * - إضافة منطقة جديدة (من الشاشة أو من زرار + جنب قائمة المناطق)
 * - تعديل الاسم (بيتعدل في كل الشحنات والشاحنات القديمة)
 * - إخفاء / إظهار - ترتيب - حذف (لو مش مستخدمة)
 */
class TruckRegionController extends Controller
{
    public function __construct()
    {
        AppSchema::regions();
    }

    private function u($path)
    {
        return url(LaravelLocalization::getCurrentLocale() . '/' . ltrim($path, '/'));
    }

    private function usage($name)
    {
        return truck_trip::where('from_region', $name)->orWhere('to_region', $name)->count()
            + DB::table('waybill_trucks')->where('current_region', $name)->count();
    }

    private function cleanName($name)
    {
        return trim(preg_replace('/\s+/u', ' ', str_replace([',', '|'], ' ', (string) $name)));
    }

    public function index()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
        $regions = DB::table('truck_regions')->orderBy('sort')->orderBy('id')->get();
        $trips = truck_trip::select('from_region', 'to_region')->get();
        $trucks = DB::table('waybill_trucks')->select('current_region')->get();
        foreach ($regions as $r) {
            $r->trips  = $trips->filter(function ($t) use ($r) { return $t->from_region === $r->name || $t->to_region === $r->name; })->count();
            $r->trucks = $trucks->where('current_region', $r->name)->count();
        }
        return view('trucks.regions', compact('regions'));
    }

    public function store(Request $request)
    {
        $name = $this->cleanName($request->name);
        $ajax = $request->expectsJson();
        if ($name === '' || mb_strlen($name) > 100) {
            $msg = 'اكتب اسم المنطقة (بحد أقصى 100 حرف)';
            return $ajax ? response()->json(['ok' => false, 'message' => $msg], 422) : back()->withErrors(['name' => $msg]);
        }
        $row = DB::table('truck_regions')->where('name', $name)->first();
        if ($row) {
            // موجودة (ولو مخفية نظهرها)
            if (!$row->active) DB::table('truck_regions')->where('id', $row->id)->update(['active' => 1, 'updated_at' => now()]);
            $msg = 'المنطقة «' . $name . '» موجودة بالفعل';
        } else {
            DB::table('truck_regions')->insert([
                'name' => $name, 'sort' => (int) DB::table('truck_regions')->max('sort') + 1, 'active' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $msg = 'تم إضافة المنطقة «' . $name . '»';
        }
        if ($ajax) return response()->json(['ok' => true, 'name' => $name, 'message' => $msg]);
        session()->flash('trip_ok', $msg);
        return redirect($this->u('trucks/regions'));
    }

    public function update(Request $request, $id)
    {
        $row = DB::table('truck_regions')->where('id', $id)->first();
        if (!$row) abort(404);
        $name = $this->cleanName($request->name ?? $row->name);
        if ($name === '') return back()->withErrors(['name' => 'اسم المنطقة مطلوب']);
        if ($name !== $row->name && DB::table('truck_regions')->where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'فيه منطقة تانية بنفس الاسم']);
        }
        DB::transaction(function () use ($row, $name, $request) {
            DB::table('truck_regions')->where('id', $row->id)->update([
                'name'       => $name,
                'sort'       => (int) ($request->sort ?? $row->sort),
                'active'     => $request->has('active') ? (int) (bool) $request->active : $row->active,
                'updated_at' => now(),
            ]);
            // تغيير الاسم بيتطبق على البيانات القديمة
            if ($name !== $row->name) {
                truck_trip::where('from_region', $row->name)->update(['from_region' => $name]);
                truck_trip::where('to_region', $row->name)->update(['to_region' => $name]);
                DB::table('waybill_trucks')->where('current_region', $row->name)->update(['current_region' => $name]);
            }
        });
        session()->flash('trip_ok', 'تم حفظ المنطقة «' . $name . '»');
        return redirect($this->u('trucks/regions'));
    }

    public function destroy($id)
    {
        $row = DB::table('truck_regions')->where('id', $id)->first();
        if (!$row) abort(404);
        if ($this->usage($row->name)) {
            return back()->withErrors(['name' => 'المنطقة «' . $row->name . '» مستخدمة في شحنات أو شاحنات. تقدر تخفيها بدل الحذف.']);
        }
        if (DB::table('truck_regions')->count() <= 1) {
            return back()->withErrors(['name' => 'لازم يفضل منطقة واحدة على الأقل']);
        }
        DB::table('truck_regions')->where('id', $id)->delete();
        session()->flash('trip_ok', 'تم حذف المنطقة «' . $row->name . '»');
        return redirect($this->u('trucks/regions'));
    }
}
