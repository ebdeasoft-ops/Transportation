<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\settings;
use App\Models\system_setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (system_setting::count() == 0) {
            system_setting::create([
                'name_ar' => 'شركة النقل الأساسية',
                'name_en' => 'Base Transport Company',
                'SR' => '1234567890',
                'Tax' => '123456789012345',
                'logo' => 'empty',
                'address_ar' => 'الرياض',
                'address_en' => 'Riyadh',
                'serviceCost' => 0.00,
                'deliveryCost' => 0.00,
                'descriptionarbic' => 'وصف الشركة',
                'descriptionenglish' => 'Company Description',
                'discount_on_invoice' => 100,
                'bank_acount_iban' => 'SA0000000000000000000000',
                'bank_acount_number' => '1234567890',
                'bankname' => 'البنك الاهلي',
            ]);
        }

        if (settings::count() == 0) {
            settings::create([
                'name' => 'Base Settings',
                'mobile' => '0500000000',
                'trn' => 1234567890,
                'crn' => 1234567890,
                'street_name' => 'شارع الملك فهد',
                'building_number' => 1234,
                'scander_number' => 1212,
                'plot_identification' => 1234,
                'region' => 'الرياض',
                'city' => 'الرياض',
                'postal_number' => 12345,
                'egs_serial_number' => '123456',
                'business_category' => 'Transport',
                'common_name' => 'Base Transport',
                'organization_unit_name' => 'Main',
                'organization_name' => 'Base Transport',
                'country_name' => 'SA',
                'registered_address' => 'Riyadh',
                'otp' => '123456',
                'email_address' => 'info@transport.com',
                'invoice_type' => '1000',
                'is_production' => 0,
                'company_id' => 1,
            ]);
        }
    }
}
