<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaybillAndTripsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. Create dummy drivers
        $driverId1 = DB::table('waybill_drivers')->insertGetId([
            'name' => 'أحمد محمود',
            'phone' => '0501234567',
            'id_number' => '1023456789',
            'license_number' => 'L-987654',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $driverId2 = DB::table('waybill_drivers')->insertGetId([
            'name' => 'سيد مصطفى',
            'phone' => '0507654321',
            'id_number' => '1098765432',
            'license_number' => 'L-123456',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Create dummy trucks
        $truckId1 = DB::table('waybill_trucks')->insertGetId([
            'plate_number' => 'أ ب ج 123',
            'truck_type' => 'تريلا',
            'total_load' => '30 طن',
            'default_driver_id' => $driverId1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $truckId2 = DB::table('waybill_trucks')->insertGetId([
            'plate_number' => 'س ي ص 999',
            'truck_type' => 'دينا',
            'total_load' => '5 طن',
            'default_driver_id' => $driverId2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Create dummy customers
        $customerId = DB::table('waybill_customers')->insertGetId([
            'name' => 'شركة الأفق للتجارة',
            'phone' => '0112233445',
            'city' => 'الرياض',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Create dummy truck trips
        DB::table('truck_trips')->insert([
            [
                'truck_id' => $truckId1,
                'driver_id' => $driverId1,
                'driver_name' => 'أحمد محمود',
                'from_region' => 'الرياض',
                'to_region' => 'جدة',
                'load_type' => 'مواد بناء',
                'customer_name' => 'شركة الأفق للتجارة',
                'status' => 1, // محمّلة
                'loading_at' => $now->copy()->subDays(1),
                'unloaded_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'truck_id' => $truckId2,
                'driver_id' => $driverId2,
                'driver_name' => 'سيد مصطفى',
                'from_region' => 'الدمام',
                'to_region' => 'الرياض',
                'load_type' => 'مواد غذائية',
                'customer_name' => 'مؤسسة السعادة',
                'status' => 2, // تم التفريغ
                'loading_at' => $now->copy()->subDays(3),
                'unloaded_at' => $now->copy()->subDays(1),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 5. Create a dummy waybill
        $waybillId = DB::table('waybills')->insertGetId([
            'waybill_no' => 'WB-10001',
            'date' => $now->toDateString(),
            'customer_id' => $customerId,
            'customer_name' => 'شركة الأفق للتجارة',
            'destination_city' => 'جدة',
            'driver_id' => $driverId1,
            'driver_name' => 'أحمد محمود',
            'truck_id' => $truckId1,
            'plate_number' => 'أ ب ج 123',
            'truck_type' => 'تريلا',
            'total_fare' => 1500.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('waybill_items')->insert([
            'waybill_id' => $waybillId,
            'sender_name' => 'مصنع الحديد',
            'fare' => 1500.00,
            'receiver_name' => 'مستودع جدة',
            'goods_type' => 'حديد تسليح',
            'goods_weight' => '30 طن',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
