<?php

namespace App\Controllers;

use App\Controller;

class HomeController extends Controller
{
    public function home($request, $response, $args)
    {
        return $response->withJson([
            'page' => 'Home page',
            'body' => 'This is Home page.'
        ]);
    }
}
