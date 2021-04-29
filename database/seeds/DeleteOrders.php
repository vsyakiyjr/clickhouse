<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeleteOrders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deliverance_days')->truncate();
        DB::table('orders')->truncate();
        DB::table('shoppingcart')->truncate();
        DB::table('users')->truncate();
        $this->call(AdminSeeder::class);
    }
}
