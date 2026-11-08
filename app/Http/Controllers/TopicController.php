<?php

namespace App\Http\Controllers;

use App\Models\Topic;
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
}