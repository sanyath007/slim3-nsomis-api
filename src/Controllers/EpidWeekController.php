<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\EpidWeek;

class EpidWeekController extends Controller
{
    public function getWeeks($req, $res, $args)
    {
        $year = $req->getParam('year');

        $weeks = EpidWeek::when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })->get();

        return $res->withJson($weeks);
    }
}
