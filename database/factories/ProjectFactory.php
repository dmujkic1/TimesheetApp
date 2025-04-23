<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_name' => fake()->sentence(1),
            'description' => fake()->paragraph(),
            //'team_id' => Team::inRandomOrder()->first()?->id ?? Team::factory(),
            'start_date' => fake()->dateTimeBetween('now', '+6 months'),    
            'end_date' => fake()->dateTimeBetween('+6 months', '+ 10 months'),
            'status' => fake()->randomElement(['Completed', 'Active', 'Archived']),
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory(),
            
        ];
    }
}
