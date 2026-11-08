<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
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
    }

