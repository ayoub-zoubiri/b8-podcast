<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::factory(5)->create()->each(function ($user) {
            Podcast::factory(3)->create(['user_id' => $user->id])->each(function ($podcast) {
                Episode::factory(2)->create(['podcast_id' => $podcast->id]);
            });
        });

    }
}
