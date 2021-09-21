<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Person;

class PersonPositionController extends Controller
{
    public function getAll($req, $res, $args)
    {        
        $link = 'http://'.$req->getServerParam('SERVER_NAME').$req->getServerParam('REDIRECT_URL');
        $page = (int)$req->getQueryParam('page');

        $model = Person::whereIn('position_id', [22])
                    ->with('prefix','position','academic');

        $data = paginate($model, 'person_singin', 10, $page, $link);
        
        return $res->withJson($data);
    }
}
