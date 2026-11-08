<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $topic->replies()->create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return back();
    }
}