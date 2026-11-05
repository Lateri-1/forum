<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class UserController extends Controller
{
    public function admin()
    {
        $users = users :: select('id', 'username', 'email')
    return Inertia::render('users/admin', [
        'users' => $users,])
    }
}

