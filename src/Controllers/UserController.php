<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\User;

class UserController extends Controller
{
    public function index($request, $response, $args)
    {
        $users = DB::select("SELECT loginname, name, entryposition FROM opduser");
        
        $data = json_encode($users, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE);

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write($data);
    }
    
    public function getUser($request, $response, $args)
    {
        $user = User::where('loginname', '=', $args['loginname'])->first();
        // $user = DB::select($sql, [$sdate, $edate]),
        var_dump($user->toSql());
        // $data = json_encode($user, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        $data = json_encode($user, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE);

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write($data);
    }
}
