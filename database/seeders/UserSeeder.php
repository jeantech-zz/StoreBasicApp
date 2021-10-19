<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rol=Role::Where('name','Admin')->first();
        $idRol=$rol->id;
        $password=Hash::make("Admin");
        
        User::create([
            "name"=>"Admin",
            "email"=>"Admin@gmail.com",
            "password"=>$password,
            "rol_id"=>$idRol,
        ]);

      
    }
}
