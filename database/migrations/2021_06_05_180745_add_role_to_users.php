<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 25)->change();
        });

        $user = new \App\User();
        $user->name = 'Seller Techland';
        $user->email = 'seller@mail.com';
        $user->role = \App\User::ROLE_SELLER;
        $user->password = bcrypt('123456');
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
