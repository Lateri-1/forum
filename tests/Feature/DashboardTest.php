<?php

use App\Models\User;

test('dashboard stuurt door naar het forum', function () {
    $this->get('/dashboard')->assertRedirect('/');

    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertRedirect('/');
});