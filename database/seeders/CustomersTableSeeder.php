<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = collect(json_decode(file_get_contents(database_path('data/customers.json')), true));

        foreach ($customers as $customer) {
            DB::table('customers')->insert($customer);
        }
    }
}
