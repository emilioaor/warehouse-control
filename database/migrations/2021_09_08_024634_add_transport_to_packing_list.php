<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportToPackingList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->foreignId('transport_id')->nullable()->constrained('transports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropForeign('packing_lists_transport_id_foreign');
            $table->dropColumn('transport_id');
        });
    }
}
