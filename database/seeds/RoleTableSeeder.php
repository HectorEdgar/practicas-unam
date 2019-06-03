<?php

use Illuminate\Database\Seeder;
use sistema\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->nombre = 'admin';
        $role->descripcion = 'Este usuario tiene acceso a todas las funcionalidades del sistema';
        $role->save();

        $role = new Role();
        $role->nombre = 'catalogador';
        $role->descripcion = 'Este usuario cataloga...';
        $role->save();

        $role = new Role();
        $role->nombre = 'revisor';
        $role->descripcion = 'Este usuario revisa...';
        $role->save();
    }
}
