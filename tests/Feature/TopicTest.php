<?php

use App\Models\Thread;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function maakTopic(): App\Models\Topic
{
    $thread = Thread::create(['title' => 'Algemeen', 'description' => 'Alles over alles']);

    return $thread->topics()->create([
        'title' => 'Hallo',
        'body' => 'Eerste bericht',
        'user_id' => User::factory()->create()->id,
    ]);
}

test('gast ziet topic met auteur en replies', function () {
    $topic = maakTopic();
    $topic->replies()->create(['body' => 'Antwoord', 'user_id' => null]);

    $this->get("/topics/{$topic->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('topics/Show')
            ->where('topic.title', 'Hallo')
            ->has('topic.user.username')
            ->missing('topic.user.password')
            ->where('topic.thread.title', 'Algemeen')
            ->has('replies', 1));
});

test('gast kan geen reply plaatsen', function () {
    $topic = maakTopic();

    $this->post("/topics/{$topic->id}/replies", ['body' => 'Hoi'])
        ->assertRedirect(route('login'));

    expect($topic->replies()->count())->toBe(0);
});

test('user kan een reply plaatsen', function () {
    $topic = maakTopic();
    $user = User::factory()->create();
    $ander = User::factory()->create();

    $this->actingAs($user)
        ->from("/topics/{$topic->id}")
        ->post("/topics/{$topic->id}/replies", ['body' => 'Mijn antwoord', 'user_id' => $ander->id])
        ->assertRedirect("/topics/{$topic->id}");

    $reply = $topic->replies()->first();
    expect($reply->body)->toBe('Mijn antwoord');
    expect($reply->user_id)->toBe($user->id);
});

test('lege reply geeft een fout', function () {
    $topic = maakTopic();

    $this->actingAs(User::factory()->create())
        ->post("/topics/{$topic->id}/replies", ['body' => ''])
        ->assertSessionHasErrors('body');
});
test('thread verwijderen verwijdert ook topics en replies', function () {
    $topic = maakTopic();
    $topic->replies()->create(['body' => 'Antwoord', 'user_id' => null]);

    $topic->thread->delete();

    expect(App\Models\Topic::count())->toBe(0);
    expect(App\Models\Reply::count())->toBe(0);
});