<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Asegúrate de que el rol exista
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // 2) Crea 10 usuarios y les asigna el rol
        User::factory()                     // usa tu factory de User
            ->count(10)
            ->sequence(fn ($seq) => [       // para nombres/emails predecibles
                'name'   => 'Empleado '.$seq->index + 1,
                'surname'=> 'Demo',
                'email'  => 'empleado'.$seq->index + 1 .'@example.com',
                'dni'    => str_pad($seq->index + 1, 8, '0', STR_PAD_LEFT).'D',
                'password' => Hash::make('1234'),   // cámbiala en prod
                'email_verified_at' => now(),
                'is_active' => true,
            ])
            ->create()
            ->each(fn ($user) => $user->assignRole($employeeRole));
    }
}
