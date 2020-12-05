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
            $courier->phone = '04244043234';
            $courier->address = 'City, DHL strep #2020';
            $courier->save();

            $courier = new \App\Courier();
            $courier->name = 'Zoom';
            $courier->phone = '04244043234';
            $courier->address = 'City, Zoom strep #3030';
            $courier->save();

            $courier = new \App\Courier();
            $courier->name = 'MRW';
            $courier->phone = '04244043234';
            $courier->address = 'City, MRW strep #4040';
            $courier->save();
        }
    }
}
