<?php

namespace App\Http\Controllers;

use App\Models\Reply;
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
        public function update(Request $request, Reply $reply)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $reply->update($data);

        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->delete();

        return back();
    }
}