<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index($request, $response, $args)
    {
        $users = User::all();
        
        return $response->withJson([
            'page' => 'User page',
            'users' => $users
        ]);
    }
}
