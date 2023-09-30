<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\subCategory;
use Illuminate\Support\Facades\File;

class subCategoiresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('public/Json/SubCategoires.json');
        $subCategories = json_decode($json);
        foreach ($subCategories as $subCategory) {
            subCategory::create([
                'name' => $subCategory->name,
                'slug' => $subCategory->slug,
                'status' => $subCategory->status,
                'category_id' => $subCategory->category_id,
            ]);
        }
    }
}
