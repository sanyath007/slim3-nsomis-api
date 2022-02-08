<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Promote;

class PromoteController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $page = (int)$req->getQueryParam('page');

        $model = Promote::where('person_id', $args['personId'])->with('person');

        $data = paginate($model, 10, $page, $req);
        
        return $res->withJson($data);
    }
}
