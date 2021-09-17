<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Nurse;
use App\Models\Person;
use App\Models\Prefix;
use App\Models\TypePosition;
use App\Models\Position;
use App\Models\Academic;
use App\Models\Hospcode;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Duty;
use App\Models\Move;
use App\Models\Transfer;
use App\Models\MemberOf;

class NurseController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $page   = (int)$req->getQueryParam('page');
        $depart = $req->getQueryParam('depart');
        $division = $req->getQueryParam('division');
        $fname  = $req->getQueryParam('fname');

        $model = Person::whereIn('position_id', [22,27,53])
                    ->whereNotIn('person_state', [6,7,8,9,99])
                    ->join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', '5')
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('level.depart_id', $depart);
                    })
                    ->when(!empty($division), function($q) use ($division) {
                        $q->where('level.ward_id', $division);
                    })
                    ->when(!empty($fname), function($q) use ($fname) {
                        $q->where('person_firstname', 'like', $fname. '%');
                    })
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division')
                    ->orderBy('person_birth');
                    
        $data = paginate($model, 10, $page, $req);
        
        return $res->withJson($data);
    }

    public function getInitForm($req, $res, $args)
    {
        return $res->withJson([
            'prefixes'      => Prefix::all(),
            'typepositions' => TypePosition::all(),
            'positions'     => Position::whereIn('position_id', [22,27,53])->get(),
            'academics'     => Academic::where('typeac_id', '1')->get(),
            'hospPay18s'    => Hospcode::where('chwpart', '30')->get(),
            'factions'      => Faction::all(),
            'departs'       => Depart::all(),
            'divisions'     => Division::orderBy('ward_name')->get(),
            'duties'        => Duty::all(),
        ]);
    }

    public function getProfile($req, $res, $args)
    {
        $nurse = Person::where('person_id', '=', $args['id'])
                    ->with('prefix','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division', 'memberOf.duty')
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
            $old     = $post['nurse']['member_of'];
            $nurse  = Person::where('person_id', $args['id'])->first();

            /** ประวัติการย้ายภายใน */
            $move = new Move;
            $move->move_person      = $nurse->person_id;
            $move->move_date        = $post['move_date'];
            $move->in_out           = $post['in_out'];

            if ($post['move_doc_no'] != '') {
                $move->move_doc_no      = $post['move_doc_no'];
                $move->move_doc_date    = $post['move_doc_date'];
            }

            /** เก็บประวัติสังกัดก่อนโอนย้าย (เฉพาะกรณีย้ายออก) */
            if ($post['in_out'] == 'O') {
                $move->old_duty         = $old['duty_id'];
                $move->old_faction      = $old['faction_id'];
                $move->old_depart       = $old['depart_id'];
                $move->old_division     = $old['ward_id'];
            }

            $move->new_duty         = $post['move_duty'];
            $move->new_faction      = $post['move_faction'];
            $move->new_depart       = $post['move_depart'];
            $move->new_division     = $post['move_division'];
            $move->is_active        = 1;

            if($move->save()) {
                /** อัพเดตสังกัดหน่วยงานปัจจุบัน */
                $current  = MemberOf::where('level_id', $old['level_id'])->first();
                $current->duty_id       = $post['move_duty'];
                $current->faction_id    = $post['move_faction'];
                $current->depart_id     = $post['move_depart'];
                $current->ward_id       = $post['move_division'];
                $current->save();

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
            $old     = $post['nurse']['member_of'];

            /** อัพเดตข้อมูลพยาบาล */
            $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '8']);

            if($nurse > 0) {
                /** ประวัติการโอนย้าย */
                $transfer = new Transfer;
                $transfer->transfer_person      = $args['id'];
                $transfer->transfer_date        = $post['transfer_date'];
                $transfer->in_out               = $post['in_out'];
                $transfer->transfer_to          = $post['transfer_to'];

                if ($post['transfer_doc_no'] != '') {
                    $transfer->transfer_doc_no      = $post['transfer_doc_no'];
                    $transfer->transfer_doc_date    = $post['transfer_doc_date'];
                }

                /** เก็บประวัติสังกัดก่อนโอนย้าย (เฉพาะกรณีโอนย้ายออก) */
                if ($post['in_out'] == 'O') {
                    $transfer->old_duty             = $old['duty_id'];
                    $transfer->old_faction          = $old['faction_id'];
                    $transfer->old_depart           = $old['depart_id'];
                    $transfer->old_division         = $old['ward_id'];
                }

                $transfer->save();

                /** อัพเดตสังกัดหน่วยงานปัจจุบัน (เฉพาะกรณีโอนย้ายเข้า) */
                if ($post['in_out'] == 'I') {
                    $member  = new MemberOf;
                    $member->duty_id       = '5';
                    $member->faction_id    = '5';
                    $member->depart_id     = '65';
                    $member->ward_id       = '113';
                    $member->save();
                }

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

    public function unknown($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            /** อัพเดตข้อมูลพยาบาล */
            $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '99']);

            if($nurse > 0) {
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

    public function getCardStat($req, $res, $args)
    {
        $sqlNurse = "select p.position_id, ps.position_name, count(p.person_id) as num 
                    from personal p
                    left join position ps on (p.position_id=ps.position_id)
                    where (p.person_state not in (6,7,8,9,99))
                    and (p.person_id in (select person_id from level where (faction_id='5')))
                    group by p.position_id, ps.position_name
                    order by count(p.person_id) desc";

        $sqlType = "select p.typeposition_id, t.typeposition_name, count(p.person_id) as num 
                    from personal p
                    left join typeposition t on (p.typeposition_id=t.typeposition_id)
                    where (p.person_state not in (6,7,8,9,99))
                    and (p.person_id in (select person_id from level where (faction_id='5')))
                    group by p.typeposition_id, t.typeposition_name
                    order by count(p.person_id) desc";

        return $res->withJson([
            'nurse' => DB::connection('person')->select($sqlNurse),
            'types' => DB::connection('person')->select($sqlType),
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
