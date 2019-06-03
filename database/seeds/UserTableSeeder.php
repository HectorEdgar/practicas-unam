<?php

use Illuminate\Database\Seeder;
use sistema\Role;
use sistema\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rol_catalogador = Role::where('nombre', 'catalogador')->first();
        $rol_revisor = Role::where('nombre', 'revisor')->first();
        $rol_admin = Role::where('nombre', 'admin')->first();


        $user = new User();
        $user->name = 'catalogador';
        $user->email = 'catalogador@example.com';
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($rol_catalogador);

        $user = new User();
        $user->name = 'revisor';
        $user->email = 'revisor@example.com';
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($rol_revisor);


        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@example.com';
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($rol_admin);
    }
}
