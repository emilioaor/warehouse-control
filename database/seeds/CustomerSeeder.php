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
            $customer->default_courier_id = \App\Courier::query()->first()->id;
            $customer->address = 'City, store strep #2333';
            $customer->save();

            $customerEmail = new \App\CustomerEmail();
            $customerEmail->email = 'store@technologystore.com';
            $customerEmail->customer_id = $customer->id;
            $customerEmail->save();

            $customer = new \App\Customer();
            $customer->description = 'Store Plus';
            $customer->phone = '04243020333';
            $customer->default_courier_id = \App\Courier::query()->orderBy('id', 'DESC')->first()->id;
            $customer->address = 'City, plus strep #3334';
            $customer->save();

            $customerEmail = new \App\CustomerEmail();
            $customerEmail->email = 'contact@storeplus.com';
            $customerEmail->customer_id = $customer->id;
            $customerEmail->save();
        }
    }
}
