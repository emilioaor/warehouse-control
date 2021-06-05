<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->constrained('users');
        });

        $seller = \App\User::query()->where('role', \App\User::ROLE_SELLER)->first();

        \App\Customer::query()->update(['seller_id' => $seller->id]);

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable(false)->change();
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
            $table->dropColumn('seller_id');
        });
    }
}
