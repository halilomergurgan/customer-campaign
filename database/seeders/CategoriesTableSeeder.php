<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = collect(json_decode(file_get_contents(database_path('data/categories.json')), true));

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
