<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('gast wordt naar login gestuurd', function () {
    $this->get('/users')->assertRedirect(route('login'));
});

test('gewone user krijgt 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/users')->assertForbidden();
    $this->actingAs($user)->get("/users/{$user->id}")->assertForbidden();
    $this->actingAs($user)->patch("/users/{$user->id}", [])->assertForbidden();
    $this->actingAs($user)->delete("/users/{$user->id}")->assertForbidden();
});

test('admin ziet de lijst zonder wachtwoord', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create();

    $this->actingAs($admin)->get('/users')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/Index')
            ->has('users', 2)
            ->missing('users.0.password'));
});

test('admin ziet details, onbekende id geeft 404', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->get("/users/{$user->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/Show')
            ->where('user.username', $user->username)
            ->missing('user.password'));

    $this->actingAs($admin)->get('/users/9999')->assertNotFound();
});

test('admin kan user bewerken, maar niet de rol', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->patch("/users/{$user->id}", [
        'name' => 'Nieuwe naam',
        'username' => $user->username,
        'email' => $user->email,
        'role' => 'admin',
    ])->assertRedirect("/users/{$user->id}");

    $user->refresh();
    expect($user->name)->toBe('Nieuwe naam');
    expect($user->role)->toBe('user');
});

test('email van een andere user geeft een fout', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->patch("/users/{$user->id}", [
        'name' => $user->name,
        'username' => $user->username,
        'email' => $admin->email,
    ])->assertSessionHasErrors('email');
});

test('admin kan een andere user verwijderen', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->delete("/users/{$user->id}")->assertRedirect('/users');

    $this->assertModelMissing($user);
});

test('admin kan zichzelf niet verwijderen', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete("/users/{$admin->id}")->assertForbidden();

    $this->assertModelExists($admin);
});

test('berichten blijven bestaan zonder auteur', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $threadId = DB::table('threads')->insertGetId([
        'title' => 'Test', 'description' => 'Test', 'user_id' => $admin->id,
    ]);
    $topicId = DB::table('topics')->insertGetId([
        'thread_id' => $threadId, 'title' => 'Test', 'body' => 'Test', 'user_id' => $user->id,
    ]);

    $this->actingAs($admin)->delete("/users/{$user->id}");

    expect(DB::table('topics')->find($topicId))->not->toBeNull();
    expect(DB::table('topics')->find($topicId)->user_id)->toBeNull();
});