<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {

       $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$email || !$password) {
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'role' => 1,
            ]
        );
        // User::updateOrCreate(
        //     //new 
        //     // ['email' => env('ADMIN_EMAIL')],
        //     // [
        //     //     'name' => 'Admin',
        //     //     'password' => Hash::make(env('ADMIN_PASSWORD')),
        //     //     'role' => 1,
        //     // ]

            
        //     // ['email' => 'admin@molfazo.com'],
        //     // [
        //     //     'name' => 'Admin',
        //     //     'password' => Hash::make('admininBozor@123'),
        //     //     // 'password' => Hash::make('admin@123'),
        //     //     'role' => 1,
        //     // ]
        // );
    }
}
