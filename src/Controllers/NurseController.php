<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Nurse;

class NurseController extends Controller
{
    public function getAll($req, $res, $args)
    {        
        $link = 'http://'.$req->getServerParam('SERVER_NAME').$req->getServerParam('REDIRECT_URL');
        $page = (int)$req->getQueryParam('page');

        $model = Nurse::whereNotIn('depart_id', [20,21,22,66])
                    ->with('hosppay18:hospcode,name')
                    ->with('person:person_firstname,person_lastname,person_birth')
                    ->with('person.prefix','person.position','academic', 'depart');

        $data = paginate($model, 'depart_id', 20, $page, $link);
        
        return $res->withJson($data);
    }
    
    public function getGenList($req, $res, $args)
    {
        $nurses = Nurse::with('hosppay18:hospcode,name')
                    ->with('person:person_firstname,person_lastname,person_birth')
                    ->with('person.prefix','person.position','academic')
                    ->get();

        return $res->withJson([
            'nurses' => $nurses
        ]);
    }
    
    public function updateDB($req, $res, $args)
    {
        $nurses = Nurse::all();

        foreach($nurses as $n) {
            $checkinDate = $this->modThYear($n->checkin_date);
            $startingDate = $this->modThYear($n->starting_date);
            // $chkinDate = $this->modDbYear($n->checkin_date);
            // $startDate = $this->modDbYear($n->starting_date);

            $n->checkin_date = $checkinDate;
            $n->starting_date = $startingDate;
            // $n->chkin_date = $chkinDate;
            // $n->start_date = $startDate;
            $n->save();
        }
        
        // return $res->withJson([
        //     'nurses' => $nurses
        // ]);
    }

    protected function modDbYear($date)
    {
        $arr = explode('-', $date);
        $thYear = ((int)substr($arr[0], 0, 2) != 25) ? '25'.substr($arr[0], -2) : $arr[0];
        $enYear = (int)$thYear - 543;

        return $enYear.'-'.$arr[1].'-'.$arr[2];
    }
    
    protected function modThYear($date)
    {
        $arr = explode('-', $date);
        $thYear = ((int)substr($arr[0], 0, 2) != 25) ? '25'.substr($arr[0], -2) : $arr[0];

        return $arr[2].'/'.$arr[1].'/'.$thYear;
    }
}
