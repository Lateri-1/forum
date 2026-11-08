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
    public function show(User $user)
    {
        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }
    public function update(Request $request, User $user)   
    {
        $data = $request->validate($this->profileRules($user->id));  
        $user->update($data);           
    return redirect("/users/{$user->id}");   
    }
}

