<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managerUserIds = [15, 13, 16, 6, 14];
        $teams = Team::all();
        $employees = Employee::whereNotIn('user_id', $managerUserIds)->get();
        foreach ($employees as $employee) {
            $team = $teams->random();
            if ($team->employee()->where('employee_id', $employee->id)->exists()) {
                continue; // Ako je zaposleni već u timu, preskoči ga
            }
            $employee->team()->attach($team->id);
        }
    }
}
