<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\EpidWeek;

class EpidWeekController extends Controller
{
    public function getWeeks($req, $res, $args)
    {        
        return $res->withJson(EpidWeek::all());
    }
}
