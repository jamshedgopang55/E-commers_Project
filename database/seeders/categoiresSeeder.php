<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\category;
use Illuminate\Support\Facades\File;

class categoiresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('public/Json/categoires.json');
        $categories = json_decode($json);
        foreach ($categories as $category) {
            category::create([
                'name' => $category->name,
                'slug' => $category->slug,
                'status' => $category->status,
            ]);
        }
    }
}
