<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Manager;
use App\Models\Employee;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        
        $users = User::all();

        // Random izaberi 3 usera koji će biti manageri
        $managers = User::inRandomOrder()->take(5)->get();

        

        // Kreiraj manager zapise samo sa user_id
        foreach ($managers as $managerUser) {
            $manager = Manager::factory()->create([
                'user_id'=> $managerUser->id,
            ]);
        }

        // 4. Ostali useri su obični radnici (employees)
        $employeeUsers = User::where('id', '!=', $managerUser->id)->inRandomOrder()->take(rand(3,5))->get();


        foreach ($employeeUsers as $user) {
            Employee::factory()->create([
                'user_id' => $user->id,
                
            ]);
        }
    }
}
