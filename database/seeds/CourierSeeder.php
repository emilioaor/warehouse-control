<?php

use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'local') {
            $courier = new \App\Courier();
            $courier->name = 'DHL';
            $courier->save();

            $courier = new \App\Courier();
            $courier->name = 'Zoom';
            $courier->save();

            $courier = new \App\Courier();
            $courier->name = 'MRW';
            $courier->save();
        }
    }
}
