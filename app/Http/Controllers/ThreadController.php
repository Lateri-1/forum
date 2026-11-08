<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::select('id', 'title', 'description')
            ->withCount('topics')
            ->latest()
            ->get();

        return Inertia::render('threads/Index', [
            'threads' => $threads,
        ]);
    }

    public function show(Thread $thread)
    {
        $topics = $thread->topics()
            ->with('user:id,username')
            ->withCount('replies')
            ->latest()
            ->get(['id', 'title', 'user_id', 'created_at']);

        return Inertia::render('threads/Show', [
            'thread' => $thread->only('id', 'title', 'description'),
            'topics' => $topics,
        ]);
    }

    public function create()
    {
        return Inertia::render('threads/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
        ]);

        $request->user()->threads()->create($data);

        return redirect('/');
    }
        public function destroy(Thread $thread)
    {
        $thread->delete();

        return redirect('/');
    }
}