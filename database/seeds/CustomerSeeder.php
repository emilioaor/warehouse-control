<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'local') {
            $customer = new \App\Customer();
            $customer->description = 'Technology Store';
            $customer->phone = '04123239452';
            $customer->email = 'store@technologystore.com';
            $customer->default_courier_id = \App\Courier::query()->first()->id;
            $customer->save();
        }
    }
}
