<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::factory(15)->create();
        //User::create(['dmujkic1', 'dmujkic1@etf.unsa.ba', '2025-04-09 15:57:54', 'mojaSifra']);
        /* User::create([
            "name" => "Dino Mujkić",
            "email" => "dinom@gmail.com",
            "password" => Hash::make("mojaSifra")
        ]); */
        /* User::create([
            "name" => "Dani Mujkić",
            "email" => "danim@gmail.com",
            "password" => Hash::make("mojaSifraa")
        ]); */
    }
}
