<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Scheduling;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\MemberOf;

class SchedulingController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $depart     = $req->getQueryParam('depart');
        $division   = $req->getQueryParam('division');
        $month      = $req->getQueryParam('month');
        $sdate = $month. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        return $res->withJson([
            'scheduling'  => Scheduling::with('shifts')->first(),
            'memberOfDep' => Person::join('level', 'level.person_id', '=', 'personal.person_id')
                                ->where([
                                    'level.faction_id'    => '5',
                                    'level.depart_id'     => $depart,
                                ])
                                ->where('person_state', '1')
                                ->get()
        ]);
    }

    public function initForm($req, $res, $args)
    {
        return $res->withJson([
            'factions'      => Faction::all(),
            'departs'       => Depart::all(),
            'divisions'     => Division::all(),
        ]);
    }

    public function getMemberOfDivision($req, $res, $args)
    {
        $members = Person::join('level', 'level.person_id', '=', 'personal.person_id')
                    ->where([
                        'level.faction_id'  => '5',
                        'level.ward_id'     => $args['division'],
                    ])
                    ->where('person_state', '1')
                    ->get();

        return $res->withJson($members);
    }
}
