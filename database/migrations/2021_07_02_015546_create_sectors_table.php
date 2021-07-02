<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        $sector = new \App\Sector();
        $sector->code = 'sector1';
        $sector->name = 'Zona 1';
        $sector->save();

        $sector = new \App\Sector();
        $sector->code = 'sector2';
        $sector->name = 'Zona 2';
        $sector->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sectors');
    }
}
