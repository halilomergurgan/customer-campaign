<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = collect(json_decode(file_get_contents(database_path('data/products.json')), true));

        foreach ($products as $product) {
            if (!array_key_exists('name', $product)) {
                $product['name'] = $product['description'];
            }

            DB::table('products')->insert($product);
        }
    }
}
