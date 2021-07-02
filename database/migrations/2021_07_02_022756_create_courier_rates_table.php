<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers');
            $table->foreignId('sector_id')->constrained('sectors');
            $table->enum('way', ['airway', 'seaway']);
            $table->float('rate');
            $table->timestamps();
        });

        $sectors = \App\Sector::all();
        $couriers = \App\Courier::all();

        foreach ($couriers as $courier) {
            foreach (['airway', 'seaway'] as $way) {
                foreach ($sectors as $sector) {

                    $courierRate = new \App\CourierRate();
                    $courierRate->courier_id = $courier->id;
                    $courierRate->sector_id = $sector->id;
                    $courierRate->way = $way;
                    $courierRate->rate = 0;
                    $courierRate->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_rates');
    }
}
