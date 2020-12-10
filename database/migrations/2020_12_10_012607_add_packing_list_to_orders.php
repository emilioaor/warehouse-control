<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingListToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('packing_list_id')->nullable()->constrained('packing_lists');
            $table->dropColumn('sign');
            $table->dropColumn('photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_packing_list_id_foreign');
            $table->dropColumn('packing_list_id');
            $table->string('photo')->nullable();
            $table->string('sign')->nullable();
        });
    }
}
