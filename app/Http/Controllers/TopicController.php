<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopicController extends Controller
{
    public function show(Topic $topic)
    {
        $topic->load('user:id,username', 'thread:id,title');

        $replies = $topic->replies()
            ->with('user:id,username')
            ->oldest()
            ->get(['id', 'body', 'user_id', 'created_at']);

        return Inertia::render('topics/Show', [
            'topic' => $topic->only('id', 'title', 'body', 'created_at', 'user', 'thread'),
            'replies' => $replies,
        ]);
    }

    public function create(Thread $thread)
    {
        return Inertia::render('topics/Create', [
            'thread' => $thread->only('id', 'title'),
        ]);
    }

    public function store(Request $request, Thread $thread)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $topic = $thread->topics()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return redirect("/topics/{$topic->id}");
    }
        public function edit(Topic $topic)
    {
        return Inertia::render('topics/Edit', [
            'topic' => $topic->only('id', 'title', 'body'),
        ]);
    }

    public function update(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $topic->update($data);

        return redirect("/topics/{$topic->id}");
    }

    public function destroy(Topic $topic)
    {
        $threadId = $topic->thread_id;

        $topic->delete();

        return redirect("/threads/{$threadId}");
    }
}