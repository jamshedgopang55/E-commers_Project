<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class productFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tittle  = fake()->name();
        $slug  = fake()->name($tittle);
        $sub = [9,10];
        $subRand = array_rand($sub);

        $brand = [1,2,3];
        $brandRand = array_rand($sub);

        return [
            'tittle'=>$tittle,
            'slug' =>$slug,
            'category_id' => 5,
            'sub_category_id' => $sub[$subRand],
            // 'brand_id' => $brand[$brandRand],
            'price' => rand(10,5000),
            'sku' => rand(100,1000),
            'track_qty' => 'Yes',
            'is_featured' => 'yes',
            'status' => 1
        ];
    }
}
