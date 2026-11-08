<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Concerns\ProfileValidationRules;

class UserController extends Controller
{
    use ProfileValidationRules;


    public function admin()
    {
        $users = User::select('id', 'username', 'email')->get();
    return Inertia::render('users/Index', [
        'users' => $users,]);
    }
    //Show Controller
    public function show(User $user)
    {
        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }
    // Update controller
    public function update(Request $request, User $user)   
    {
        $data = $request->validate($this->profileRules($user->id));  
        $user->update($data);           
    return redirect("/users/{$user->id}");   
    }
    // ProductController
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403);
        }

        $user->delete();

        return redirect('/users');
    }
}

