<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Feature::create([
            'name_en' => 'Immediate Transport',
            'name_ar' => 'النقل اللحظي',
            'description_en' => 'Access to immediate transport services.',
            'description_ar' => 'الوصول إلى خدمات النقل اللحظي.',
            'service_id' => 1
        ]);

        Feature::create(attributes: [
            'name_en' => 'Daily Transport',
            'name_ar' => 'النقل اليومي',
            'description_en' => 'Access to Daily transport services.',
            'description_ar' => 'الوصول إلى خدمات النقل اليومي.',
            'service_id' => 3
        ]);

        Feature::create([
            'name_en' => 'Weekly Transport',
            'name_ar' => 'النقل الاسبوعي',
            'description_en' => 'Access to Weekly transport services.',
            'description_ar' => 'الوصول إلى خدمات النقل الاسبوعي.',
            'service_id' => 8
        ]);
    }
}
