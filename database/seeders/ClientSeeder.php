<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klijent1 = User::create([
            'id' => 19,
            'name' => 'Mark Zuckerberg',
            'email' => 'zuck@fb.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ]);
        $klijent1->client()->create([
            'name' => 'Meta Platforms, Inc.',
            'address' => '1 Meta Way Menlo Park, CA 94025-1444',
            'email' => 'metausa@gmail.business.com',
            'country' => 'USA',
            'city' => 'Menlo Park, CA',
        ]);

        $klijent2 = User::create([
            'id' => 20,
            'name' => 'Elon Musk',
            'email' => 'elongate@x.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ]);
        $klijent2->client()->create([
            'name' => 'X Corporation',
            'address' => '1355 Market St #900, San Francisco',
            'email' => 'xusa@yahoo.com',
            'country' => 'USA',
            'city' => 'San Francisco, CA',
        ]);

        $klijent3 = User::create([
            'id' => 21,
            'name' => 'Fikret Hodžić',
            'email' => 'fikro_boss@outlook.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ]);
        $klijent3->client()->create([
            'name' => 'GRAWE osiguranje d.d. Sarajevo',
            'address' => 'Trg solidarnosti 2, Sarajevo 71000',
            'email' => 'office.sarajevo@grawe.ba',
            'country' => 'Bosnia and Herzegovina',
            'city' => 'Sarajevo',
        ]);
    }
}
