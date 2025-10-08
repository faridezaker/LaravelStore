<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->count(10)->create();

        Category::factory()->count(5)->create([
            'parent_id' => Category::inRandomOrder()->first()->id,
        ]);
    }
}
