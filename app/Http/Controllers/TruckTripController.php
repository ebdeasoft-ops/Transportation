<?php

namespace App\Http\Controllers;

use App\Models\truck_trip;
use App\Models\waybill_truck;
use App\Models\waybill_driver;
use App\Models\financial_accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;

/**
 * حركة الشاحنات
 * - لوحة الشاحنات: فاضية (ومكانها) / محمّلة (من فين لفين)
 * - تسجيل حمولة: ممنوع لو الشاحنة عليها حمل لحد ما تتسجل "تم التفريغ"
 * - تقرير الأحمال
 */
class TruckTripController extends Controller
{
    private function u($path)
    {
        return url(LaravelLocalization::getCurrentLocale() . '/' . ltrim($path, '/'));
    }

    /**
     * بيضيف الجداول/الأعمدة الناقصة لوحده (بدل ما تشغّل ملفات SQL بإيدك)
     * بيشتغل مرة واحدة لكل طلب وبيتشيك بسرعة لو كل حاجة موجودة
     */
    public function __construct()
    {
        $this->ensureSchema();
    }

    private function ensureSchema()
    {
        static $done = false;
        if ($done) return;
        $done = true;
        try {
            if (Schema::hasTable('waybill_trucks')) {
                $cols = ['current_region' => 100, 'current_city' => 150, 'ownership' => 20,
                         'insurance_company' => 150, 'insurance_policy_no' => 100, 'istimara_no' => 100];
                foreach ($cols as $c => $len) {
                    if (!Schema::hasColumn('waybill_trucks', $c)) {
                        Schema::table('waybill_trucks', function (Blueprint $t) use ($c, $len) { $t->string($c, $len)->nullable(); });
                    }
                }
                foreach (['insurance_start', 'insurance_expiry', 'istimara_expiry'] as $c) {
                    if (!Schema::hasColumn('waybill_trucks', $c)) {
                        Schema::table('waybill_trucks', function (Blueprint $t) use ($c) { $t->date($c)->nullable(); });
                    }
                }
                if (!Schema::hasColumn('waybill_trucks', 'insurance_value')) {
                    Schema::table('waybill_trucks', function (Blueprint $t) { $t->double('insurance_value')->nullable(); });
                }
            }
            if (!Schema::hasTable('truck_trips')) {
                Schema::create('truck_trips', function (Blueprint $table) {
                    $table->id();
                    $table->bigInteger('truck_id')->unsigned();
                    $table->bigInteger('driver_id')->unsigned()->nullable();
                    $table->string('driver_name')->nullable();
                    $table->string('from_region', 100);
                    $table->string('from_city', 150)->nullable();
                    $table->string('to_region', 100);
                    $table->string('to_city', 150)->nullable();
                    $table->string('load_type')->nullable();
                    $table->string('load_weight', 100)->nullable();
                    $table->string('customer_name')->nullable();
                    $table->string('waybill_no', 50)->nullable();
                    $table->dateTime('loading_at')->nullable();
                    $table->dateTime('expected_unloading_at')->nullable();
                    $table->dateTime('unloaded_at')->nullable();
                    $table->tinyInteger('status')->default(1);
                    $table->text('notes')->nullable();
                    $table->text('unload_notes')->nullable();
                    $table->bigInteger('user_id')->unsigned()->nullable();
                    $table->bigInteger('unloaded_by')->unsigned()->nullable();
                    $table->bigInteger('branchs_id')->unsigned()->nullable();
                    $table->timestamps();
                    $table->index(['truck_id', 'status']);
                });
            }
            $tripCols = [
                'ownership'           => function (Blueprint $t) { $t->string('ownership', 20)->nullable(); },
                'invoice_number'      => function (Blueprint $t) { $t->string('invoice_number', 100)->nullable(); },
                'reference_no'        => function (Blueprint $t) { $t->string('reference_no', 100)->nullable(); },
                'price'               => function (Blueprint $t) { $t->double('price')->default(0); },
                'attachment'          => function (Blueprint $t) { $t->string('attachment')->nullable(); },
                'unload_attachment'   => function (Blueprint $t) { $t->string('unload_attachment')->nullable(); },
                'customer_account_id' => function (Blueprint $t) { $t->bigInteger('customer_account_id')->unsigned()->nullable(); },
            ];
            foreach ($tripCols as $c => $fn) {
                if (!Schema::hasColumn('truck_trips', $c)) Schema::table('truck_trips', $fn);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** العملاء المسجلين في النظام (نفس قائمة العملاء: financial_accounts.orginal_type = 1) */
    private function customers()
    {
        return financial_accounts::where('orginal_type', 1)->orderBy('name')->get(['id', 'name', 'account_number']);
    }

    private function setLocale()
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());
    }

    // ================= لوحة الشاحنات =================
    public function board(Request $request)
    {
        $this->setLocale();
        $trucks  = waybill_truck::with(['activeTrip.driver', 'default_driver'])->orderBy('plate_number')->get();
        $drivers = waybill_driver::orderBy('name')->get();
        $regions = truck_trip::regions();
        $now     = truck_trip::nowLocal();
        $customers = $this->customers();

        $emptyByRegion = [];
        foreach ($regions as $r) $emptyByRegion[$r] = 0;
        $emptyByRegion['غير محدد'] = 0;
        $loaded = 0; $overdue = 0; $docsAlert = 0; $availableCount = 0;
        foreach ($trucks as $t) {
            if ($t->docs_alert) $docsAlert++;
            if ($t->activeTrip) {
                $loaded++;
                if ($t->activeTrip->is_overdue) $overdue++;
            } elseif ($t->ownership === 'own') {
                // المتاحة = الفاضية ملك المؤسسة بس (الإيجار الخارجي مش بيتحسب متاح)
                $availableCount++;
                $key = $t->current_region && in_array($t->current_region, $regions) ? $t->current_region : 'غير محدد';
                $emptyByRegion[$key]++;
            }
        }

        return view('trucks.board', compact('trucks', 'drivers', 'regions', 'now', 'emptyByRegion', 'loaded', 'overdue', 'customers', 'docsAlert', 'availableCount'));
    }

    // ================= تسجيل حمولة =================
    public function load(Request $request)
    {
        $request->validate([
            'truck_id'              => 'required|integer',
            'from_region'           => truck_trip::regionRule(),
            'to_region'             => truck_trip::regionRule(),
            'load_type'             => 'required|max:255',
            'loading_at'            => 'required|date',
            'expected_unloading_at' => 'required|date|after_or_equal:loading_at',
            'price'                 => 'nullable|numeric|min:0',
            'customer_account_id'   => 'nullable|integer',
            'attachment'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'price.numeric'         => 'السعر لازم يكون رقم',
            'attachment.mimes'      => 'المرفق لازم يكون PDF أو صورة',
            'attachment.max'        => 'حجم المرفق أكبر من 5 ميجا',
            'from_region.required'  => 'اختار منطقة التحميل (من)',
            'to_region.required'    => 'اختار منطقة التنزيل (إلى)',
            'load_type.required'    => 'اكتب نوع التحميل',
            'loading_at.required'   => 'حدد معاد التحميل',
            'expected_unloading_at.required' => 'حدد معاد التنزيل المتوقع',
            'expected_unloading_at.after_or_equal' => 'معاد التنزيل المتوقع لازم يكون بعد معاد التحميل',
        ]);

        // المرفق (فاتورة / صورة)
        $attachment = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachment = 'trip_' . time() . rand(100, 999) . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('assets/uploads/truck_trips'), $attachment);
        }

        try {
            DB::transaction(function () use ($request, $attachment) {
                // قفل الشاحنة عشان محدش يحمّلها مرتين في نفس اللحظة
                $truck = waybill_truck::lockForUpdate()->findOrFail($request->truck_id);

                $busy = truck_trip::where('truck_id', $truck->id)->where('status', truck_trip::LOADED)->exists();
                if ($busy) {
                    throw new \RuntimeException('الشاحنة ' . $truck->plate_number . ' عليها حمل بالفعل. سجّل "تم التفريغ" الأول.');
                }

                $driver = $request->driver_id ? waybill_driver::find($request->driver_id) : null;
                $customer = $request->customer_account_id
                    ? financial_accounts::where('orginal_type', 1)->find($request->customer_account_id) : null;

                // الملكية بتتحدد وقت إضافة الشاحنة
                if (!in_array($truck->ownership, ['own', 'external'])) {
                    throw new \RuntimeException('حدد ملكية الشاحنة ' . $truck->plate_number . ' (خاص بالمؤسسة / إيجار خارجي) من «بيانات الشاحنة» الأول.');
                }

                truck_trip::create([
                    'truck_id'              => $truck->id,
                    'driver_id'             => $driver->id ?? null,
                    'driver_name'           => $driver->name ?? null,
                    'from_region'           => $request->from_region,
                    'from_city'             => $request->from_city,
                    'to_region'             => $request->to_region,
                    'to_city'               => $request->to_city,
                    'load_type'             => $request->load_type,
                    'load_weight'           => $request->load_weight,
                    'customer_account_id'   => $customer->id ?? null,
                    'customer_name'         => $customer->name ?? null,
                    'waybill_no'            => $request->waybill_no,
                    'loading_at'            => $request->loading_at,
                    'expected_unloading_at' => $request->expected_unloading_at,
                    'status'                => truck_trip::LOADED,
                    'notes'                 => $request->notes,
                    'ownership'             => $truck->ownership,
                    'invoice_number'        => $request->invoice_number,
                    'reference_no'          => $request->reference_no,
                    'price'                 => (float) $request->price,
                    'attachment'            => $attachment,
                    'user_id'               => Auth()->user()->id ?? null,
                    'branchs_id'            => Auth()->user()->branchs_id ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['truck' => $e->getMessage()]);
        }

        session()->flash('trip_ok', 'تم تسجيل الحمولة بنجاح');
        // لو اتسجلت من الرئيسية / التقرير نرجع لنفس الشاشة
        if ($request->filled('_quick') && url()->previous()) {
            return redirect(url()->previous());
        }
        return redirect($this->u('trucks/board'));
    }

    // ================= تعديل بيانات الشحنة =================
    public function edit($id)
    {
        $this->setLocale();
        $trip      = truck_trip::with(['truck', 'driver'])->findOrFail($id);
        $drivers   = waybill_driver::orderBy('name')->get();
        $customers = $this->customers();
        $regions   = array_values(array_unique(array_filter(array_merge(truck_trip::regions(), [$trip->from_region, $trip->to_region]))));
        $back      = url()->previous() && !str_contains(url()->previous(), '/edit') ? url()->previous() : $this->u('trucks/board');
        return view('trucks.trip_edit', compact('trip', 'drivers', 'customers', 'regions', 'back'));
    }

    public function update(Request $request, $id)
    {
        $trip = truck_trip::findOrFail($id);
        $rules = [
            'from_region'           => truck_trip::regionRule(),
            'to_region'             => truck_trip::regionRule(),
            'load_type'             => 'required|max:255',
            'loading_at'            => 'required|date',
            'expected_unloading_at' => 'required|date|after_or_equal:loading_at',
            'price'                 => 'nullable|numeric|min:0',
            'customer_account_id'   => 'nullable|integer',
            'attachment'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ];
        if ($trip->status == truck_trip::UNLOADED) {
            $rules['unloaded_at'] = 'required|date';
        }
        $request->validate($rules, [
            'load_type.required'    => 'اكتب نوع التحميل',
            'loading_at.required'   => 'حدد معاد التحميل',
            'expected_unloading_at.required' => 'حدد معاد التنزيل المتوقع',
            'expected_unloading_at.after_or_equal' => 'معاد التنزيل المتوقع لازم يكون بعد معاد التحميل',
            'unloaded_at.required'  => 'حدد معاد التفريغ الفعلي',
            'price.numeric'         => 'السعر لازم يكون رقم',
            'attachment.mimes'      => 'المرفق لازم يكون PDF أو صورة',
            'attachment.max'        => 'حجم المرفق أكبر من 5 ميجا',
        ]);

        $driver   = $request->driver_id ? waybill_driver::find($request->driver_id) : null;
        $customer = $request->customer_account_id ? financial_accounts::where('orginal_type', 1)->find($request->customer_account_id) : null;

        $data = [
            'from_region'           => $request->from_region,
            'from_city'             => $request->from_city,
            'to_region'             => $request->to_region,
            'to_city'               => $request->to_city,
            'load_type'             => $request->load_type,
            'load_weight'           => $request->load_weight,
            'loading_at'            => $request->loading_at,
            'expected_unloading_at' => $request->expected_unloading_at,
            'driver_id'             => $driver->id ?? null,
            'driver_name'           => $driver->name ?? null,
            'customer_account_id'   => $customer->id ?? null,
            'customer_name'         => $customer->name ?? ($request->customer_account_id ? $trip->customer_name : null),
            'invoice_number'        => $request->invoice_number,
            'reference_no'          => $request->reference_no,
            'price'                 => (float) $request->price,
            'waybill_no'            => $request->waybill_no,
            'notes'                 => $request->notes,
        ];
        // العميل القديم (اتكتب نص قبل الربط بالعملاء) يفضل زي ما هو لو ما اتغيرش
        if (!$request->customer_account_id && $request->input('keep_old_customer')) {
            $data['customer_name'] = $trip->customer_name;
        }
        if ($trip->status == truck_trip::UNLOADED) {
            $data['unloaded_at']  = $request->unloaded_at;
            $data['unload_notes'] = $request->unload_notes;
        }

        // مرفق جديد (بيستبدل القديم) أو حذف المرفق
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $name = 'trip_' . time() . rand(100, 999) . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('assets/uploads/truck_trips'), $name);
            $data['attachment'] = $name;
        } elseif ($request->input('remove_attachment')) {
            $data['attachment'] = null;
        }

        $trip->update($data);

        // لو الشحنة اتفرغت وغيرنا منطقة التنزيل: مكان الشاحنة يتحدث (لو دي آخر رحلة ليها)
        if ($trip->status == truck_trip::UNLOADED) {
            $last = truck_trip::where('truck_id', $trip->truck_id)->orderByDesc('id')->first();
            if ($last && $last->id == $trip->id) {
                waybill_truck::where('id', $trip->truck_id)->update(['current_region' => $trip->to_region, 'current_city' => $trip->to_city]);
            }
        }

        session()->flash('trip_ok', 'تم تعديل بيانات الشحنة');
        $back = $request->input('back');
        return redirect($back && str_starts_with($back, url('/')) ? $back : $this->u('trucks/board'));
    }

    // ================= تم التفريغ =================
    public function unload(Request $request, $id)
    {
        $request->validate([
            'unloaded_at'       => 'required|date',
            'unload_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'unloaded_at.required'    => 'حدد معاد التفريغ',
            'unload_attachment.mimes' => 'مرفق التفريغ لازم يكون PDF أو صورة',
            'unload_attachment.max'   => 'حجم مرفق التفريغ أكبر من 5 ميجا',
        ]);

        // مرفق التفريغ (اختياري): سند استلام / صورة
        $unloadFile = null;
        if ($request->hasFile('unload_attachment')) {
            $file = $request->file('unload_attachment');
            $unloadFile = 'unload_' . $id . '_' . time() . rand(100, 999) . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('assets/uploads/truck_trips'), $unloadFile);
        }

        DB::transaction(function () use ($request, $id, $unloadFile) {
            $trip = truck_trip::lockForUpdate()->findOrFail($id);
            if ($trip->status != truck_trip::LOADED) return;

            $region = in_array($request->unload_region, truck_trip::regions(true)) ? $request->unload_region : $trip->to_region;
            $trip->update([
                'status'       => truck_trip::UNLOADED,
                'unloaded_at'  => $request->unloaded_at,
                'unload_notes' => $request->unload_notes,
                'unload_attachment' => $unloadFile ?: $trip->unload_attachment,
                'unloaded_by'  => Auth()->user()->id ?? null,
                'to_region'    => $region,
                'to_city'      => $request->unload_city ?: $trip->to_city,
            ]);
            // الشاحنة بقت فاضية في منطقة التفريغ
            waybill_truck::where('id', $trip->truck_id)->update([
                'current_region' => $region,
                'current_city'   => $request->unload_city ?: $trip->to_city,
            ]);
        });

        session()->flash('trip_ok', 'تم تسجيل التفريغ - الشاحنة بقت فاضية');
        // يرجع لنفس الشاشة اللي اتعمل منها التفريغ (اللوحة / الرئيسية / تقرير الأحمال)
        $prev = url()->previous();
        return redirect($prev && $prev !== url()->current() ? $prev : $this->u('trucks/board'));
    }

    // ================= إلغاء حمولة اتسجلت غلط =================
    public function cancel($id)
    {
        $trip = truck_trip::findOrFail($id);
        if ($trip->status == truck_trip::LOADED) {
            $trip->delete();
            session()->flash('trip_ok', 'تم إلغاء الحمولة');
        }
        return redirect($this->u('trucks/board'));
    }

    // ================= تحديد مكان شاحنة فاضية =================
    public function setRegion(Request $request, $id)
    {
        $request->validate(['current_region' => truck_trip::regionRule()]);
        $upd = [
            'current_region' => $request->current_region,
            'current_city'   => $request->current_city,
        ];
        if (in_array($request->ownership, ['own', 'external'])) $upd['ownership'] = $request->ownership;
        waybill_truck::where('id', $id)->update($upd);
        session()->flash('trip_ok', 'تم تحديث بيانات الشاحنة');
        return redirect($this->u('trucks/board'));
    }

    // ================= تعديل بيانات الشاحنة (اللوحة + التأمين + الاستمارة) =================
    public function truckEdit($id)
    {
        $this->setLocale();
        $truck   = waybill_truck::with('activeTrip')->findOrFail($id);
        $drivers = waybill_driver::orderBy('name')->get();
        $regions = array_values(array_unique(array_filter(array_merge(truck_trip::regions(), [$truck->current_region]))));
        $trips   = truck_trip::where('truck_id', $truck->id)->count();
        return view('trucks.truck_edit', compact('truck', 'drivers', 'regions', 'trips'));
    }

    public function truckUpdate(Request $request, $id)
    {
        $truck = waybill_truck::findOrFail($id);
        $request->validate([
            'plate_number'     => 'required|max:100',
            'ownership'        => 'required|in:own,external',
            'current_region'   => truck_trip::regionRule(false),
            'insurance_start'  => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'insurance_value'  => 'nullable|numeric|min:0',
            'istimara_expiry'  => 'nullable|date',
        ], [
            'plate_number.required' => 'رقم اللوحة مطلوب',
            'ownership.required'    => 'اختار الشاحنة ملك المؤسسة ولا إيجار خارجي',
            'insurance_value.numeric' => 'قيمة التأمين لازم تكون رقم',
        ]);

        $truck->update($request->only([
            'plate_number', 'truck_type', 'total_load', 'owner_name', 'plate_region',
            'operation_license_number', 'operation_license_issuer', 'default_driver_id', 'notes',
            'current_region', 'current_city', 'ownership',
            'insurance_company', 'insurance_policy_no', 'insurance_start', 'insurance_expiry', 'istimara_no', 'istimara_expiry',
        ]) + ['insurance_value' => $request->filled('insurance_value') ? (float) $request->insurance_value : null]);

        session()->flash('trip_ok', 'تم تعديل بيانات الشاحنة ' . $truck->plate_number);
        return redirect($this->u('trucks/board'));
    }

    // ================= إضافة شاحنة =================
    public function storeTruck(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|max:100',
            'ownership'    => 'required|in:own,external',
        ], [
            'plate_number.required' => 'رقم اللوحة مطلوب',
            'ownership.required'    => 'اختار الشاحنة ملك المؤسسة ولا إيجار خارجي',
        ]);
        waybill_truck::create($request->only([
            'plate_number', 'plate_region', 'owner_name', 'operation_license_number', 'operation_license_issuer',
            'truck_type', 'total_load', 'default_driver_id', 'notes', 'current_region', 'current_city', 'ownership',
            'insurance_company', 'insurance_policy_no', 'insurance_expiry', 'istimara_no', 'istimara_expiry',
        ]) + ['user_id' => Auth()->user()->id ?? null]);
        session()->flash('trip_ok', 'تم إضافة الشاحنة');
        return redirect($this->u('trucks/board'));
    }

    // ================= السائقين =================
    public function drivers(Request $request)
    {
        $this->setLocale();
        $q = waybill_driver::orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('id_number', 'like', "%$s%");
            });
        }
        $drivers = $q->get();
        // السائق حالياً على أنهي شاحنة محمّلة
        $onTrip = truck_trip::with('truck')->where('status', truck_trip::LOADED)->whereNotNull('driver_id')->get()->keyBy('driver_id');
        return view('trucks.drivers', compact('drivers', 'onTrip'));
    }

    // ================= إضافة عميل جديد (نفس شجرة الحسابات: عملاء orginal_type = 1) =================
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'   => 'required|max:255',
            'phone'  => 'nullable|max:30',
            'tax_no' => 'nullable|max:30',
        ], ['name.required' => 'اكتب اسم العميل']);

        $name = trim($request->name);
        $exists = financial_accounts::where('orginal_type', 1)->where('name', $name)->first();
        if ($exists) {
            return response()->json(['ok' => true, 'existed' => true, 'id' => $exists->id, 'name' => $exists->name, 'account_number' => $exists->account_number]);
        }

        $phone = $request->phone ? (waybill_driver::normalizePhone($request->phone) ?: $request->phone) : null;

        $acc = DB::transaction(function () use ($request, $name, $phone) {
            // الحساب الأب بتاع العملاء (نفس اللي العملاء الحاليين تحته، والافتراضي 2 زي شاشة الحسابات)
            $parentId = financial_accounts::where('orginal_type', 1)->whereNotNull('parent_account_number')
                ->select('parent_account_number', DB::raw('COUNT(*) c'))->groupBy('parent_account_number')->orderByDesc('c')
                ->value('parent_account_number') ?: 2;
            $parent = financial_accounts::find($parentId);

            $customer = \App\Models\customers::create([
                'name'         => $name,
                'comp_name'    => $name,
                'address'      => $request->address ?: 'Client Address',
                'tax_no'       => $request->tax_no ?: 0,
                'Balance'      => 0,
                'phone'        => $phone ?: '05*********',
                'email'        => 'Email@gmail.com',
                'notes'        => $request->notes ?: 'لا توجد ملاحظات ',
                'Limit_credit' => 10000,
            ]);

            // رقم الحساب = آخر رقم تحت نفس الأب + 1
            $last = financial_accounts::where('parent_account_number', $parentId)->max('account_number');
            $number = $last ? $last + 1 : (($parent->account_number ?? 0) * 10 + 1);

            return financial_accounts::create([
                'name'                  => $name,
                'account_type'          => $parent->account_type ?? null,
                'is_parent'             => 0,
                'parent_account_number' => $parentId,
                'account_number'        => $number,
                'orginal_type'          => 1,
                'orginal_id'            => $customer->id,
                'start_balance_status'  => 3,
                'start_balance'         => 0,
                'current_balance'       => 0,
                'notes'                 => $request->notes,
                'active'                => 1,
                'added_by'              => Auth()->user()->id ?? null,
                'date'                  => \Carbon\Carbon::now()->addHours(3),
                'com_code'              => 0,
                'branchs_id'            => Auth()->user()->branchs_id ?? null,
            ]);
        });

        return response()->json(['ok' => true, 'id' => $acc->id, 'name' => $acc->name, 'account_number' => $acc->account_number]);
    }

    public function storeDriver(Request $request)
    {
        $ajax = $request->expectsJson();
        $rules = ['name' => 'required|max:255', 'phone' => 'required|max:50'];
        $msgs  = [
            'name.required'  => 'اسم السائق مطلوب',
            'phone.required' => 'رقم جوال السائق مطلوب عشان المندوب يقدر يتصل عليه',
        ];
        $v = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $msgs);
        if ($v->fails()) {
            return $ajax ? response()->json(['ok' => false, 'errors' => $v->errors()], 422) : back()->withInput()->withErrors($v);
        }
        // رقم الجوال لازم يكون سعودي صحيح (05xxxxxxxx) عشان الاتصال والواتساب يشتغلوا
        $phone = waybill_driver::normalizePhone($request->phone);
        if (!$phone) {
            $err = 'رقم الجوال غلط. لازم يكون 10 أرقام ويبدأ بـ 05 (مثال: 0551234567)';
            return $ajax ? response()->json(['ok' => false, 'errors' => ['phone' => [$err]]], 422) : back()->withInput()->withErrors(['phone' => $err]);
        }
        $name = trim($request->name);
        $data = $request->only(['id_number', 'license_number', 'license_issue_date', 'nationality', 'notes']);
        $data['name']  = $name;
        $data['phone'] = $phone;
        $editId = $request->filled('driver_id') ? (int) $request->driver_id : null;

        // هل السائق موجود مسبقاً؟ (نفس رقم الجوال، أو نفس رقم الهوية)
        $dup = waybill_driver::where(function ($q) use ($phone, $request) {
                $q->where('phone', $phone);
                if ($request->filled('id_number')) $q->orWhere('id_number', trim($request->id_number));
            })
            ->when($editId, function ($q) use ($editId) { $q->where('id', '!=', $editId); })
            ->first();
        if ($dup) {
            $msg = 'السائق موجود مسبقاً: ' . $dup->name . ' - ' . $dup->phone;
            if ($ajax) {
                return response()->json(['ok' => true, 'existed' => true, 'message' => $msg, 'id' => $dup->id, 'name' => $dup->name, 'phone' => $dup->phone,
                    'driver' => $dup->only(['id', 'name', 'phone', 'id_number', 'nationality', 'license_number', 'license_issue_date', 'notes'])]);
            }
            return back()->withInput()->withErrors(['phone' => $msg]);
        }

        if ($editId) {
            waybill_driver::where('id', $editId)->update($data);
            $driver = waybill_driver::find($editId);
            $msg = 'تم تعديل بيانات السائق';
        } else {
            $driver = waybill_driver::create($data + ['user_id' => Auth()->user()->id ?? null]);
            $msg = 'تم إضافة السائق ' . $name;
        }
        if ($ajax) {
            return response()->json(['ok' => true, 'existed' => false, 'message' => $msg, 'id' => $driver->id, 'name' => $driver->name, 'phone' => $driver->phone,
                'driver' => $driver->only(['id', 'name', 'phone', 'id_number', 'nationality', 'license_number', 'license_issue_date', 'notes'])]);
        }
        session()->flash('trip_ok', $msg);
        return back();
    }

    // ================= تقرير الأحمال =================
    public function report(Request $request)
    {
        $this->setLocale();
        $from = $request->input('start_at', date('Y-m-01'));
        $to   = $request->input('end_at', date('Y-m-d'));

        $q = truck_trip::with(['truck', 'user', 'driver'])
            ->whereDate('loading_at', '>=', $from)->whereDate('loading_at', '<=', $to);
        if ($request->filled('truck_id'))    $q->where('truck_id', $request->truck_id);
        if ($request->filled('from_region')) $q->where('from_region', $request->from_region);
        if ($request->filled('to_region'))   $q->where('to_region', $request->to_region);
        if ($request->filled('status'))      $q->where('status', $request->status);
        if ($request->filled('load_type'))   $q->where('load_type', 'like', '%' . $request->load_type . '%');
        if ($request->filled('ownership'))   $q->where('ownership', $request->ownership);
        if ($request->filled('customer_account_id')) $q->where('customer_account_id', $request->customer_account_id);

        $trips = $q->orderByDesc('loading_at')->get();

        // تصدير Excel (CSV) بنفس أعمدة الشيت
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($trips, $from, $to);
        }

        // ملخص
        $now = truck_trip::nowLocal();
        $done = $trips->where('status', truck_trip::UNLOADED);
        $summary = [
            'total'     => $trips->count(),
            'loaded'    => $trips->where('status', truck_trip::LOADED)->count(),
            'done'      => $done->count(),
            'overdue'   => $trips->filter(function ($t) { return $t->is_overdue; })->count(),
            'late'      => $done->filter(function ($t) {
                return $t->unloaded_at && $t->expected_unloading_at && $t->unloaded_at->gt($t->expected_unloading_at);
            })->count(),
            'price'     => round($trips->sum('price'), 2),
            'price_own' => round($trips->where('ownership', 'own')->sum('price'), 2),
            'price_ext' => round($trips->where('ownership', 'external')->sum('price'), 2),
            'avg_hours' => round($done->filter(function ($t) { return $t->loading_at && $t->unloaded_at; })
                ->avg(function ($t) { return $t->loading_at->diffInMinutes($t->unloaded_at) / 60; }) ?? 0, 1),
        ];
        $routes = $trips->groupBy(function ($t) { return $t->from_region . '|' . $t->to_region; })
            ->map(function ($g, $k) { [$a, $b] = explode('|', $k); return ['from' => $a, 'to' => $b, 'count' => $g->count()]; })
            ->sortByDesc('count')->take(6)->values();
        $types = $trips->groupBy(function ($t) { return trim($t->load_type) ?: '—'; })
            ->map(function ($g, $k) { return ['type' => $k, 'count' => $g->count()]; })
            ->sortByDesc('count')->take(6)->values();

        $trucks  = waybill_truck::orderBy('plate_number')->get();
        $regions = truck_trip::regions();
        $customers = $this->customers();

        return view('trucks.report', compact('trips', 'summary', 'routes', 'types', 'trucks', 'regions', 'from', 'to', 'now', 'customers'));
    }

    private function exportCsv($trips, $from, $to)
    {
        $name = 'loads_' . $from . '_' . $to . '.csv';
        return response()->streamDownload(function () use ($trips) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // عشان Excel يقرا العربي صح
            fputcsv($out, ['التاريخ', 'اسم السائق', 'جوال السائق', 'رقم اللوحة', 'ايجار خارجي / خاص بالمؤسسة', 'من', 'الى',
                'رقم الفاتورة', 'مرجع', 'السعر', 'اسم الشركة', 'نوع التحميل', 'التنزيل المتوقع', 'التنزيل الفعلي', 'الحالة', 'المرفق']);
            foreach ($trips as $t) {
                fputcsv($out, [
                    optional($t->loading_at)->format('Y-m-d H:i'),
                    $t->driver_name,
                    optional($t->driver)->phone,
                    optional($t->truck)->plate_number,
                    truck_trip::ownershipLabel($t->ownership),
                    trim($t->from_region . ' ' . $t->from_city),
                    trim($t->to_region . ' ' . $t->to_city),
                    $t->invoice_number,
                    $t->reference_no,
                    $t->price,
                    $t->customer_name,
                    $t->load_type,
                    optional($t->expected_unloading_at)->format('Y-m-d H:i'),
                    optional($t->unloaded_at)->format('Y-m-d H:i'),
                    $t->status == truck_trip::LOADED ? 'محمّلة' : 'تم التفريغ',
                    $t->attachment ? asset('assets/uploads/truck_trips/' . $t->attachment) : '',
                ]);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
