<?php

namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller
{
    public function index($request, $response, $args)
    {
        return $response->withJson([
            'page' => 'Dashboard page',
        ]);
    }
}
