<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Team::factory(5)->create();  */
        $teams = [
            ['team_name' => 'Instagram Frontend', 'manager_id' => 1],
            ['team_name' => 'Instagram Backend', 'manager_id' => 2],
            ['team_name' => 'Facebook Frontend', 'manager_id' => 3],
            ['team_name' => 'Facebook Backend', 'manager_id' => 4],
            ['team_name' => 'X Frontend', 'manager_id' => 5],
            ['team_name' => 'X Backend', 'manager_id' => 1],
            ['team_name' => 'SpaceX Frontend', 'manager_id' => 2],
            ['team_name' => 'SpaceX Backend', 'manager_id' => 3],
            ['team_name' => 'Grawe Client Guide Frontend', 'manager_id' => 4],
            ['team_name' => 'Grawe Client Guide Backend', 'manager_id' => 5],
            ['team_name' => 'Grawe Edu Site Team', 'manager_id' => 1],
        ];
        foreach ($teams as $team) {
            Team::create($team);
        }
    }   
}
