<?php

use App\Models\Thread;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('gast ziet de lijst met threads op de startpagina', function () {
    Thread::create(['title' => 'Algemeen', 'description' => 'Alles over alles']);
    Thread::create(['title' => 'Huiswerk', 'description' => 'Vragen over huiswerk']);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('threads/Index')
            ->has('threads', 2)
            ->has('threads.0', fn (Assert $thread) => $thread
                ->hasAll(['id', 'title', 'description', 'topics_count'])));
});
test('gast ziet een thread met topics', function () {
    $user = User::factory()->create();
    $thread = Thread::create(['title' => 'Algemeen', 'description' => 'Alles over alles']);
    $thread->topics()->create(['title' => 'Hallo', 'body' => 'Tekst', 'user_id' => $user->id]);
    $thread->topics()->create(['title' => 'Zonder auteur', 'body' => 'Tekst', 'user_id' => null]);

    $this->get("/threads/{$thread->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('threads/Show')
            ->where('thread.title', 'Algemeen')
            ->has('topics', 2)
            ->has('topics.0.replies_count')
            ->missing('topics.0.user.password'));
});

test('onbekende thread geeft 404', function () {
    $this->get('/threads/9999')->assertNotFound();
});
test('admin kan een thread aanmaken', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/threads/create')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('threads/Create'));

    $this->actingAs($admin)
        ->post('/threads', ['title' => 'Nieuw', 'description' => 'Korte tekst'])
        ->assertRedirect('/');

    $thread = Thread::first();
    expect($thread->title)->toBe('Nieuw');
    expect($thread->user_id)->toBe($admin->id);
});

test('gewone user kan geen thread aanmaken', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/threads/create')->assertForbidden();
    $this->actingAs($user)
        ->post('/threads', ['title' => 'Nieuw', 'description' => 'Korte tekst'])
        ->assertForbidden();

    expect(Thread::count())->toBe(0);
});

test('gast wordt bij thread aanmaken naar login gestuurd', function () {
    $this->get('/threads/create')->assertRedirect(route('login'));
});

test('thread zonder titel geeft een fout', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/threads', ['title' => '', 'description' => 'Tekst'])
        ->assertSessionHasErrors('title');
});
