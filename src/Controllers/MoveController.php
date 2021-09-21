<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Move;

class MoveController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $page = (int)$req->getQueryParam('page');

        $model = Move::where('move_person', $args['personId'])
                    ->with('newFaction', 'oldFaction')
                    ->with('newDepart', 'oldDepart');

        $data = paginate($model, 10, $page, $req);
        
        return $res->withJson($data);
    }
}
