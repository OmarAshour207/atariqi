<?php

namespace Database\Seeders;

use App\Models\PackageFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PackageFeature::create([
        //     'package_id' => 1,
        //     'feature_id' => 1,
        // ]);

        PackageFeature::create([
            'package_id' => 2,
            'feature_id' => 1,
        ]);

        PackageFeature::create([
            'package_id' => 2,
            'feature_id' => 2,
        ]);

        PackageFeature::create([
            'package_id' => 3,
            'feature_id' => 2,
        ]);

        PackageFeature::create([
            'package_id' => 3,
            'feature_id' => 3,
        ]);

        PackageFeature::create([
            'package_id' => 4,
            'feature_id' => 1,
        ]);

        PackageFeature::create([
            'package_id' => 4,
            'feature_id' => 2,
        ]);

        PackageFeature::create([
            'package_id' => 4,
            'feature_id' => 3,
        ]);
    }
}
