<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(47)->create();
        $useri = User::all();
        $titule = ['Mr.', 'Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.'];
        foreach ($useri as $user) {
            $fullName = $user->name;
            $nameParts = explode(' ', $fullName);

            if (in_array($nameParts[0], $titule)) {
                array_shift($nameParts); // uklanja titulu iz first_name varijable
            }

            $firstName = $nameParts[0] ?? '';
            $lastName = implode(' ', array_slice($nameParts, 1));

            $user->employee()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'job_title' => 'Receptionist',
                'hire_date' => now(),
                'status' => true,
            ]);
        }
        $employees = Employee::all();
        foreach ($employees as $employee){
            $employee->user->assignrole('employee');
        }
    }
}
