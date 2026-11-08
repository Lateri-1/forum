<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@forum.test',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'user@forum.test',
        ]);

        $others = User::factory(3)->create();

        $algemeen = $admin->threads()->create([
            'title' => 'Algemeen',
            'description' => 'Alles wat nergens anders past. Stel je voor, deel nieuws of praat gewoon wat.',
        ]);

        $huiswerk = $admin->threads()->create([
            'title' => 'Huiswerk',
            'description' => 'Vragen en tips over opdrachten, toetsen en projecten.',
        ]);

        $welkom = $algemeen->topics()->create([
            'title' => 'Welkom op het forum',
            'body' => "Hoi allemaal!\n\nDit is het eerste topic. Lees de regels en veel plezier.",
            'user_id' => $admin->id,
        ]);

        $welkom->replies()->create(['body' => 'Bedankt, leuk forum!', 'user_id' => $user->id]);
        $welkom->replies()->create(['body' => 'Hallo iedereen 👋', 'user_id' => $others[0]->id]);

        $vraag = $huiswerk->topics()->create([
            'title' => 'Hoe werkt een foreign key?',
            'body' => 'Ik snap niet goed wat constrained() doet in een migratie. Kan iemand het uitleggen?',
            'user_id' => $user->id,
        ]);

        $vraag->replies()->create([
            'body' => 'Het zorgt ervoor dat user_id altijd naar een bestaande user wijst.',
            'user_id' => $others[1]->id,
        ]);
    }
}