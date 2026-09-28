<?php

namespace App\Http\Controllers;

use App\Models\waybill;
use App\Models\waybill_item;
use App\Models\waybill_driver;
use App\Models\waybill_truck;
use App\Models\waybill_customer;
use App\Models\system_setting;
use App\Models\settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * بوليصة الشحن
 * - إنشاء / تعديل / طباعة / حذف بوليصة
 * - إضافة سائق / شاحنة / عميل جديد (Ajax) مع تعبئة البيانات تلقائياً عند الاختيار
 * - البوليصات السابقة
 */
class WaybillController extends Controller
{
    /** رابط بالبادئة الخاصة باللغة (مهم للـ POST) */
    private function u($path)
    {
        return url(LaravelLocalization::getCurrentLocale() . '/' . ltrim($path, '/'));
    }

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    /** رقم البوليصة التالي */
    private function nextNumber()
    {
        $max = waybill::select(DB::raw('MAX(CAST(waybill_no AS UNSIGNED)) as m'))->value('m');
        return ((int) $max) + 1;
    }

    /** بيانات الهيدر من الإعدادات */
    private function company()
    {
        return [
            'sys' => system_setting::find(1),
            'set' => settings::find(1),
        ];
    }

    // ================= شاشة إنشاء بوليصة =================
    public function create()
    {
        $this->setLocale();
        $drivers   = waybill_driver::orderBy('name')->get();
        $trucks    = waybill_truck::when(\Illuminate\Support\Facades\Schema::hasTable('truck_trips'), function ($q) { $q->with('activeTrip'); })->orderBy('plate_number')->get();
        $customers = waybill_customer::orderBy('name')->get();
        $nextNo    = $this->nextNumber();
        $waybill   = null;
        $company   = $this->company();

        return view('waybills.create', compact('drivers', 'trucks', 'customers', 'nextNo', 'waybill', 'company'));
    }

    // ================= شاشة تعديل بوليصة =================
    public function edit($id)
    {
        $this->setLocale();
        $waybill   = waybill::with('items')->findOrFail($id);
        $drivers   = waybill_driver::orderBy('name')->get();
        $trucks    = waybill_truck::when(\Illuminate\Support\Facades\Schema::hasTable('truck_trips'), function ($q) { $q->with('activeTrip'); })->orderBy('plate_number')->get();
        $customers = waybill_customer::orderBy('name')->get();
        $nextNo    = $waybill->waybill_no;
        $company   = $this->company();

        return view('waybills.create', compact('drivers', 'trucks', 'customers', 'nextNo', 'waybill', 'company'));
    }

    // ================= حفظ (جديد أو تعديل) =================
    public function store(Request $request)
    {
        $this->setLocale();

        $id = $request->waybill_id;

        // رقم البوليصة تلقائي - مش بيتاخد من الفورم
        $request->validate([
            'date' => 'required|date',
        ], [
            'date.required' => 'اختار تاريخ البوليصة',
        ]);

        // الأصناف
        $items = [];
        $total = 0;
        $senders = (array) $request->input('sender_name', []);
        foreach ($senders as $i => $sender) {
            $row = [
                'sender_name'   => $sender,
                'fare'          => (float) ($request->input('fare')[$i] ?? 0),
                'receiver_name' => $request->input('receiver_name')[$i] ?? null,
                'goods_type'    => $request->input('goods_type')[$i] ?? null,
                'goods_weight'  => $request->input('goods_weight')[$i] ?? null,
            ];
            if (trim($row['sender_name'] ?? '') === '' && trim($row['receiver_name'] ?? '') === ''
                && trim($row['goods_type'] ?? '') === '' && trim($row['goods_weight'] ?? '') === '' && !$row['fare']) {
                continue; // سطر فاضي
            }
            $total += $row['fare'];
            $items[] = $row;
        }

        $data = [
            'date'                      => $request->date ?: date('Y-m-d'),
            'date_hijri'                => $request->date_hijri,
            'customer_id'               => $request->customer_id ?: null,
            'customer_name'             => $request->customer_name,
            'destination_city'          => $request->destination_city,
            'driver_id'                 => $request->driver_id ?: null,
            'driver_name'               => $request->driver_name,
            'driver_license_number'     => $request->driver_license_number,
            'driver_license_issue_date' => $request->driver_license_issue_date,
            'truck_id'                  => $request->truck_id ?: null,
            'owner_name'                => $request->owner_name,
            'plate_number'              => $request->plate_number,
            'plate_region'              => $request->plate_region,
            'operation_license_number'  => $request->operation_license_number,
            'operation_license_issuer'  => $request->operation_license_issuer,
            'truck_type'                => $request->truck_type,
            'total_load'                => $request->total_load,
            'departure_date'            => $request->departure_date ?: null,
            'fare_paid_by'              => $request->fare_paid_by,
            'delivery_within'           => $request->delivery_within,
            'notes'                     => $request->notes,
            'total_fare'                => $total,
        ];

        DB::beginTransaction();
        try {
            if ($id) {
                $waybill = waybill::findOrFail($id);
                $waybill->update($data);
                waybill_item::where('waybill_id', $waybill->id)->delete();
            } else {
                // الرقم التالي تلقائي (مع قفل الجدول عشان مايتكررش لو اتنين حفظوا في نفس اللحظة)
                $max = waybill::lockForUpdate()->select(DB::raw('MAX(CAST(waybill_no AS UNSIGNED)) as m'))->value('m');
                $data['waybill_no'] = ((int) $max) + 1;
                $data['user_id']    = Auth()->user()->id ?? null;
                $data['branchs_id'] = Auth()->user()->branchs_id ?? null;
                $waybill = waybill::create($data);
            }
            foreach ($items as $row) {
                $row['waybill_id'] = $waybill->id;
                waybill_item::create($row);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->action === 'print') {
            return redirect($this->u('waybills/print/' . $waybill->id) . '?auto=1');
        }

        session()->flash('waybill_saved', 'تم حفظ بوليصة الشحن رقم ' . $waybill->waybill_no . ' بنجاح');
        return redirect($this->u('waybills/create'));
    }

    // ================= البوليصات السابقة =================
    public function index(Request $request)
    {
        $this->setLocale();

        $q = waybill::withCount('items')->orderByDesc('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('waybill_no', 'like', "%$s%")
                  ->orWhere('customer_name', 'like', "%$s%")
                  ->orWhere('driver_name', 'like', "%$s%")
                  ->orWhere('plate_number', 'like', "%$s%")
                  ->orWhere('destination_city', 'like', "%$s%");
            });
        }
        if ($request->filled('driver_id'))   $q->where('driver_id', $request->driver_id);
        if ($request->filled('truck_id'))    $q->where('truck_id', $request->truck_id);
        if ($request->filled('customer_id')) $q->where('customer_id', $request->customer_id);
        if ($request->filled('start_at'))    $q->whereDate('date', '>=', $request->start_at);
        if ($request->filled('end_at'))      $q->whereDate('date', '<=', $request->end_at);

        $totalFare = (clone $q)->sum('total_fare');
        $waybills  = $q->paginate(25)->appends($request->query());

        $drivers   = waybill_driver::orderBy('name')->get();
        $trucks    = waybill_truck::orderBy('plate_number')->get();
        $customers = waybill_customer::orderBy('name')->get();

        return view('waybills.previous', compact('waybills', 'drivers', 'trucks', 'customers', 'totalFare'));
    }

    // ================= طباعة =================
    public function print($id)
    {
        $this->setLocale();
        $waybill = waybill::with('items')->findOrFail($id);
        $company = $this->company();
        return view('waybills.print', compact('waybill', 'company'));
    }

    // ================= حذف =================
    public function destroy($id)
    {
        $waybill = waybill::findOrFail($id);
        waybill_item::where('waybill_id', $waybill->id)->delete();
        $no = $waybill->waybill_no;
        $waybill->delete();
        session()->flash('waybill_deleted', 'تم حذف البوليصة رقم ' . $no);
        return redirect($this->u('waybills'));
    }

    // ================= Ajax: إضافة سائق / شاحنة / عميل =================
    public function storeDriver(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'phone' => 'required'], ['phone.required' => 'رقم جوال السائق مطلوب']);
        $phone = waybill_driver::normalizePhone($request->phone);
        if (!$phone) {
            return response()->json(['message' => 'رقم الجوال غلط', 'errors' => ['phone' => ['رقم الجوال غلط. لازم يكون 10 أرقام ويبدأ بـ 05 (مثال: 0551234567)']]], 422);
        }
        $driver = waybill_driver::create($request->only([
            'name', 'id_number', 'license_number', 'license_issue_date', 'nationality', 'notes',
        ]) + ['phone' => $phone, 'user_id' => Auth()->user()->id ?? null]);
        return response()->json(['success' => true, 'data' => $driver]);
    }

    public function storeTruck(Request $request)
    {
        $request->validate(['plate_number' => 'required|max:100']);
        $truck = waybill_truck::create($request->only([
            'plate_number', 'plate_region', 'owner_name', 'operation_license_number',
            'operation_license_issuer', 'truck_type', 'total_load', 'default_driver_id', 'notes',
        ]) + ['user_id' => Auth()->user()->id ?? null]);
        return response()->json(['success' => true, 'data' => $truck]);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate(['name' => 'required|max:255']);
        $customer = waybill_customer::create($request->only([
            'name', 'phone', 'city', 'address', 'tax_no', 'notes',
        ]) + ['user_id' => Auth()->user()->id ?? null]);
        return response()->json(['success' => true, 'data' => $customer]);
    }
}
