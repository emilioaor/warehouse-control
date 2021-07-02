<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectorToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('sector_id')->nullable()->constrained('sectors');
        });

        $sector = \App\Sector::query()->first();
        \App\Customer::query()->update(['sector_id' => $sector->id]);

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('sector_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_sector_id_foreign');
            $table->dropColumn('sector_id');
        });
    }
}
