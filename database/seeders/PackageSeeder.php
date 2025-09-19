<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::create([
            'name_ar' => 'الباقة المجانية',
            'name_en' => 'Free Package',
            'price_monthly' => 0,
            'price_annual' => 0,
            'status' => Package::FREE,
        ]);

        Package::create([
            'name_ar' => 'الباقة البرونزيه',
            'name_en' => 'Bronze Package',
            'price_monthly' => 10,
            'price_annual' => 100,
            'status' => Package::NEW,
        ]);

        Package::create([
            'name_ar' => 'الباقة الفضية',
            'name_en' => 'Silver Package',
            'price_monthly' => 20,
            'price_annual' => 200,
            'status' => Package::NEW,
        ]);

        Package::create([
            'name_ar' => 'الباقة الذهبيه',
            'name_en' => 'Gold Package',
            'price_monthly' => 30,
            'price_annual' => 300,
            'status' => Package::SOON,
        ]);
    }
}
