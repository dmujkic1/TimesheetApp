<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Manager;
use App\Models\Employee;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      
        $this->call([
            AdminSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ManagerSeeder::class,
            TeamSeeder::class,
            ProjectSeeder::class,
            
        ]);

        //Employee::factory(10)->active()->create();
        //Manager::factory(5)->active()->create();
      /*   Manager::factory()->count(5)->create();
        Employee::factory()->count(10)->create(); */

       /*  User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
    }
}
