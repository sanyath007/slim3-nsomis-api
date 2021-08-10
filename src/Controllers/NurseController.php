<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Nurse;
use App\Models\Person;
use App\Models\Prefix;
use App\Models\Position;
use App\Models\Academic;
use App\Models\Hospcode;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Duty;

class NurseController extends Controller
{
    public function getAll($req, $res, $args)
    {        
        $link   = 'http://'.$req->getServerParam('SERVER_NAME').$req->getServerParam('REDIRECT_URL');
        $page   = (int)$req->getQueryParam('page');
        $depart = $req->getQueryParam('depart');
        $fname  = $req->getQueryParam('fname');

        $model = Person::where('profession_id', '4')
                    ->whereNotIn('person_state', [6,7,8])
                    ->join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', '5')
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('level.depart_id', $depart);
                    })
                    ->when(!empty($fname), function($q) use ($fname) {
                        $q->where('person_firstname', 'like', $fname. '%');
                    })
                    ->with('prefix','position','academic','office','memberOf','memberOf.depart');

        $data = paginate($model, 'person_birth', 20, $page, $link);
        
        return $res->withJson($data);
    }

    public function getInitForm($req, $res, $args)
    {
        return $res->withJson([
            'prefixes'      => Prefix::all(),
            'positions'     => Position::where('position_id', '22')->get(),
            'academics'     => Academic::where('typeac_id', '1')->get(),
            'hospPay18s'    => Hospcode::where('chwpart', '30')->get(),
            'departs'       => Depart::where('faction_id', '5')->get(),
            'divisions'     => Division::all(),
            'duties'        => Duty::all(),
        ]);
    }

    public function getProfile($req, $res, $args)
    {
        $nurse = Nurse::where('cid', '=', $args['id'])
                    ->with('hosppay18:hospcode,name')
                    ->with('person:person_firstname,person_lastname,person_birth')
                    ->with('person.prefix','person.position','academic', 'depart')
                    ->first();
        
        return $res->withJson($nurse);
    }
    
    public function getGenList($req, $res, $args)
    {
        $nurses = Nurse::whereNotIn('depart_id', [20,21,22,66])
                    ->whereNotIn('status', [2,3])
                    ->with('hosppay18:hospcode,name')
                    ->with('person:person_firstname,person_lastname,person_birth')
                    ->with('person.prefix','person.position','academic')
                    ->get();

        return $res->withJson([
            'nurses' => $nurses
        ]);
    }
    
    public function store($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $nurse = new Nurse;
            $nurse->cid         = $post['cid'];
            $nurse->position_id = $post['position'];
            $nurse->ac_id       = $post['academic'];
            $nurse->hospcode    = '23839';
            $nurse->hosp_pay18  = $post['hosp_pay18'];
            $nurse->depart_id   = $post['depart'];
            $nurse->chkin_date  = $post['checkin_date'];
            $nurse->start_date  = $post['start_date'];
            $nurse->cert_no     = $post['cert_no'];
            $nurse->position_no = $post['position_no'];
            $nurse->status      = 1;
            
            if($nurse->save()) {
                return $res->withJson([
                    'nurse' => $nurse
                ]);
            } else {
                //throw error handler
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function update($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $nurse  = Nurse::find($args['id']);
            $nurse->cid         = $post['cid'];
            $nurse->position_id = $post['position'];
            $nurse->ac_id       = $post['academic'];
            $nurse->hospcode    = '23839';
            $nurse->hosp_pay18  = $post['hosp_pay18'];
            $nurse->depart_id   = $post['depart'];
            $nurse->chkin_date  = $post['checkin_date'];
            $nurse->start_date  = $post['start_date'];
            $nurse->cert_no     = $post['cert_no'];
            $nurse->position_no = $post['position_no'];
            $nurse->status      = 1;
            
            if($nurse->save()) {
                return $res->withJson([
                    'nurse' => $nurse
                ]);
            } else {
                //throw error handler
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function move($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $nurse  = Nurse::find($args['id']);
            $nurse->cid         = $post['cid'];
            $nurse->position_id = $post['position'];
            $nurse->ac_id       = $post['academic'];
            $nurse->hospcode    = '23839';
            $nurse->hosp_pay18  = $post['hosp_pay18'];
            
            if($nurse->save()) {
                return $res->withJson([
                    'nurse' => $nurse
                ]);
            } else {
                //throw error handler
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function transfer($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $nurse  = Nurse::find($args['id']);
            $nurse->hospcode    = '23839';
            $nurse->hosp_pay18  = $post['hosp_pay18'];
            $nurse->status      = 8;
            
            if($nurse->save()) {
                return $res->withJson([
                    'nurse' => $nurse
                ]);
            } else {
                //throw error handler
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
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
