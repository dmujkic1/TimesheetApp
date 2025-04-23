<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Project::factory(8)->create();   */    
        Project::create([
            'project_name' => 'Instagram',
            'description' => 'Photo sharing social media app.',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => 'Active',
            'client_id' => 1, // Mark Zuckerberg - Meta
        ]);

        Project::create([
            'project_name' => 'Facebook',
            'description' => 'The original social networking site.',
            'start_date' => now()->subMonths(3),
            'end_date' => now()->addMonths(3),
            'status' => 'Completed',
            'client_id' => 1, // Mark Zuckerberg - Meta
        ]);

        Project::create([
            'project_name' => 'X',
            'description' => 'The everything app for social media.',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(2),
            'status' => 'Active',
            'client_id' => 2, // Elon Musk - X Corp
        ]);

        Project::create([
            'project_name' => 'Space X',
            'description' => 'Website and management tools for aerospace projects.',
            'start_date' => now(),
            'end_date' => now()->addMonths(8),
            'status' => 'Archived',
            'client_id' => 2, // Elon Musk - X Corp
        ]);

        Project::create([
            'project_name' => 'Grawe Client GUID Web App',
            'description' => 'Client portal for accessing personal insurance information.',
            'start_date' => now()->subMonths(1),
            'end_date' => now()->addMonths(5),
            'status' => 'Active',
            'client_id' => 3, // Fikret Hodžić - Grawe
        ]);

        Project::create([
            'project_name' => 'Grawe Osiguranje Edu Site',
            'description' => 'Educational site for Grawe clients and employees.',
            'start_date' => now(),
            'end_date' => now()->addMonths(4),
            'status' => 'Completed',
            'client_id' => 3, // Fikret Hodžić - Grawe
        ]);  
    }
}
