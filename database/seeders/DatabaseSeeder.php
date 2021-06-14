<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CustomersTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(OrderTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
    }
}
