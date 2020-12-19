<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivedAtToPackingList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable();
        });

        $packingLists = \App\PackingList::query()->where('status', \App\PackingList::STATUS_SENT)->get();

        foreach ($packingLists as $packingList) {
            $packingList->received_at = $packingList->updated_at;
            $packingList->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropColumn('received_at');
        });
    }
}
