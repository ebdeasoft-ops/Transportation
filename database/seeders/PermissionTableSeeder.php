<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // مسح الصلاحيات القديمة تماماً لضمان عدم وجود أي صلاحيات غير مستخدمة (Sales, Purchases, Products... الخ)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Home',

            'Reports',
            'budget sheet',
            'Bank Statement',
            'Transfer cash to a bank Rep',
            'Bank transfers',
            'Credit collection',
            'List of suppliers',
            'List of customers',
            'Supplier credit payment',
            'Shift details',
            'Expenses',
            'VAT',

            'Accounts',
            'Receipt document',
            'Voucher',
            'Cash expenses',
            'Expenses for the owner',
            'Add cach from bank',
            'Transfer to main branch',
            'Confirm transfer of master branch',
            'Transfer cash to a bank',
            'Transfer cash to the next day',

            'User and branches',
            'Create a new branch',
            'add branch',
            'List of users',
            'Users permissions',

            'Human Resource',
            'Employee',
            'Add new employee',
            'create a department',
            'Increase or deduction',
            'Salary document',
            'Attendances',
            'Leaves',
            'Contracts',
            'Custodies',
            'End of services',
            'HR settings',

            'Transportation',
            'Waybills',
            'Truck trips',
            'Transport invoices',
            'Trucks',
            'Drivers',

            'Setting',
            'AVT',
            'System setting',
            'Branches',
            
            'Technical support',
            'Notification',
        ];

        $permissions_ar = [
            'الرئيسية',

            'التقارير',
            'الميزانية العمومية',
            'كشف الحساب البنكي',
            'ايداع من البنك',
            'تحويل نقدي الصندوق الي البنك',
            'تحصيل الأجل',
            'قائمة الموردين',
            'قائمة العملاء',
            'الدفع الأجل للمورد',
            'تفاصيل الوردية',
            'المصروفات',
            'ضريبة القيمة المضافة',

            'الحسابات',
            'سند صرف',
            'سند قبض',
            'مصروفات نقدية',
            'مصروفات المالك',
            'اضافة نقدي من البنك',
            'التحويل إلى الفرع الرئيسي',
            'تاكيد التحويل لفرع الرئيسي',
            'تحويل نقدي الصندوق لبنك',
            'ترحيل النقدية ليوم التالي',

            'المستخدمين و الفروع',
            'إنشاء فرع جديد',
            'اضافة فرع',
            'قائمة المستخدمين',
            'صلاحيات المستخدمين',

            'الموارد البشرية',
            'قائمة الموظفين',
            'اضافة موظف جديد',
            'انشاء قسم جديد',
            'زيادة او خصم للموظف',
            'مستند المرتبات',
            'الحضور والانصراف',
            'الإجازات',
            'العقود',
            'العهد',
            'نهاية الخدمة',
            'إعدادات الموارد البشرية',

            'النقل والشحن',
            'بوليصات الشحن',
            'رحلات الشاحنات',
            'فواتير النقل',
            'الشاحنات',
            'السائقين',

            'الاعدادت',
            'الضريبة',
            'اعدادات النظام',
            'الفروع',
            
            'التواصل مع الدعم الفني',
            'الاشعارات',
        ];
      
        $i = 0;
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'name_ar' => $permissions_ar[$i]]);
            $i++;
        }
        
        // إعادة ربط الصلاحيات الجديدة للمدير
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $role->syncPermissions(Permission::all());
    }
}
