<?php

use App\Models\Reply;
use App\Models\Thread;
use App\Models\Topic;
use App\Models\User;

function topicVan(User $auteur): Topic
{
    $thread = Thread::create(['title' => 'Algemeen', 'description' => 'Alles']);

    return $thread->topics()->create(['title' => 'Oud', 'body' => 'Oude tekst', 'user_id' => $auteur->id]);
}

test('auteur kan eigen topic bewerken', function () {
    $auteur = User::factory()->create();
    $topic = topicVan($auteur);

    $this->actingAs($auteur)->get("/topics/{$topic->id}/edit")->assertOk();

    $this->actingAs($auteur)
        ->patch("/topics/{$topic->id}", ['title' => 'Nieuw', 'body' => 'Nieuwe tekst'])
        ->assertRedirect("/topics/{$topic->id}");

    expect($topic->fresh()->title)->toBe('Nieuw');
});

test('andere user en admin kunnen topic niet bewerken', function () {
    $topic = topicVan(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->patch("/topics/{$topic->id}", ['title' => 'Hack', 'body' => 'Hack'])
        ->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())
        ->get("/topics/{$topic->id}/edit")
        ->assertForbidden();

    expect($topic->fresh()->title)->toBe('Oud');
});

test('auteur kan eigen reply bewerken, ander niet', function () {
    $auteur = User::factory()->create();
    $topic = topicVan($auteur);
    $reply = $topic->replies()->create(['body' => 'Oud', 'user_id' => $auteur->id]);

    $this->actingAs(User::factory()->create())
        ->patch("/replies/{$reply->id}", ['body' => 'Hack'])
        ->assertForbidden();

    $this->actingAs($auteur)
        ->from("/topics/{$topic->id}")
        ->patch("/replies/{$reply->id}", ['body' => 'Nieuw'])
        ->assertRedirect("/topics/{$topic->id}");

    expect($reply->fresh()->body)->toBe('Nieuw');
});

test('gewone user kan niets verwijderen', function () {
    $user = User::factory()->create();
    $topic = topicVan($user);
    $reply = $topic->replies()->create(['body' => 'Tekst', 'user_id' => $user->id]);

    $this->actingAs($user)->delete("/replies/{$reply->id}")->assertForbidden();
    $this->actingAs($user)->delete("/topics/{$topic->id}")->assertForbidden();
    $this->actingAs($user)->delete("/threads/{$topic->thread_id}")->assertForbidden();

    expect(Reply::count())->toBe(1);
    expect(Topic::count())->toBe(1);
    expect(Thread::count())->toBe(1);
});

test('admin kan reply, topic en thread verwijderen', function () {
    $admin = User::factory()->admin()->create();
    $topic = topicVan(User::factory()->create());
    $reply = $topic->replies()->create(['body' => 'Tekst', 'user_id' => null]);

    $this->actingAs($admin)->from("/topics/{$topic->id}")
        ->delete("/replies/{$reply->id}")
        ->assertRedirect("/topics/{$topic->id}");
    expect(Reply::count())->toBe(0);

    $this->actingAs($admin)->delete("/topics/{$topic->id}")
        ->assertRedirect("/threads/{$topic->thread_id}");
    expect(Topic::count())->toBe(0);

    $this->actingAs($admin)->delete("/threads/{$topic->thread_id}")
        ->assertRedirect('/');
    expect(Thread::count())->toBe(0);
});