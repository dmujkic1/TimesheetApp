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
        $managerUserIds = [15, 13, 16, 6, 14];
        $clientUserIds = [49, 50, 51];

        // menadzeri (ujedno i zaposleni)
        foreach ($managerUserIds as $id) {
            $user = User::find($id);
            if ($user) {
                if (!$user->employee) {
                    $user->employee()->create([
                        'first_name' => explode(' ', $user->name)[0],
                        'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                        'email' => $user->email,
                        'job_title' => 'Manager',
                        'hire_date' => now(),
                        'status' => true,
                        'user_id' => $user->id,
                    ]);
                }
                $user->manager()->create([
                    'user_id' => $user->id,
                ]);
            }
        }

        $otherUsers = User::whereNotIn('id', array_merge($managerUserIds, $clientUserIds))->get();
        foreach ($otherUsers as $user) {
            if (!$user->employee) {
                $user->employee()->create([
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                    'email' => $user->email,
                    'job_title' => 'Receptionist',
                    'hire_date' => now(),
                    'status' => true,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
