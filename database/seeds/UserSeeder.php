<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User();
        $user->name = 'Emilio Ochoa';
        $user->email = 'emilioaor@gmail.com';
        $user->password = bcrypt('123456');
        $user->role = \App\User::ROLE_ADMIN;
        $user->save();

        if (config('app.env') === 'local') {
            $user = new \App\User();
            $user->name = 'User Test';
            $user->email = 'test@mail.com';
            $user->password = bcrypt('123456');
            $user->role = \App\User::ROLE_WAREHOUSE;
            $user->save();

            $user = new \App\User();
            $user->name = 'User Test';
            $user->email = 'test2@mail.com';
            $user->password = bcrypt('123456');
            $user->role = \App\User::ROLE_WAREHOUSE;
            $user->save();

            $user = new \App\User();
            $user->name = 'User Test';
            $user->email = 'test3@mail.com';
            $user->password = bcrypt('123456');
            $user->role = \App\User::ROLE_WAREHOUSE;
            $user->save();

            $user = new \App\User();
            $user->name = 'User Test';
            $user->email = 'test4@mail.com';
            $user->password = bcrypt('123456');
            $user->role = \App\User::ROLE_WAREHOUSE;
            $user->save();

            $user = new \App\User();
            $user->name = 'User Test';
            $user->email = 'test5@mail.com';
            $user->password = bcrypt('123456');
            $user->role = \App\User::ROLE_WAREHOUSE;
            $user->save();
        }
    }
}
