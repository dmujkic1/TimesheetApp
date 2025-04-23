<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        $projects = Project::all();

        for ($i = 1; $i < count($teams); $i+=2) {
            $idProjekta=($i+1)/2;
            $idTima1=$i;
            $idTima2=$i+1;
            $project = $projects->find($idProjekta);
            $team1 = $teams->find($idTima1);
            $team2 = $teams->find($idTima2);

            $team1->project()->attach($project->id);
            $team2->project()->attach($project->id);
        }
        $tim=$teams->find(count($teams));
        $projekat=$projects->find(count($projects));
        $tim->project()->attach($projekat->id);
    }
}
