<?php

use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'local') {
            $box = new \App\Box();
            $box->description = 'Telefonos alcatel';
            $box->size = '20x14.5x5';
            $box->weight = 20;
            $box->save();

            $box = new \App\Box();
            $box->description = 'Telefonos Samsung';
            $box->size = '16x6x7';
            $box->weight = 17.5;
            $box->save();
        }
    }
}
