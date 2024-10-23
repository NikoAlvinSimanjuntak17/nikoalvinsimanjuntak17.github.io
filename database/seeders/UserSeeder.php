<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
   


        User::create([
            'id'            => 4,
            'name'          => 'Niko',
            'username'      => 'niko',
            'password'      => bcrypt('niko123'),
            'description'   => 'IT Staff',
            'role'          => 'USER',
            'email'      => 'nikoalvinsimanjuntak82@gmail.com',
        ]);


    }
}
