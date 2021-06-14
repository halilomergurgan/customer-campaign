<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = collect(json_decode(file_get_contents(database_path('data/orders.json')), true));

        foreach ($orders as $order) {
            DB::table('orders')->insert($order);
        }
    }
}
