<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Person;
use App\Models\Prefix;
use App\Models\Position;
use App\Models\Academic;
use App\Models\Hospcode;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Duty;
use App\Models\Move;
use App\Models\Transfer;
use App\Models\Leave;
use App\Models\MemberOf;

class SupportController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $page   = (int)$req->getQueryParam('page');
        $depart = $req->getQueryParam('depart');
        $division = $req->getQueryParam('division');
        $fname  = $req->getQueryParam('fname');

        $model = Person::whereNotIn('position_id', [22,27,53])
                    ->whereNotIn('person_state', [6,7,8,9,99])
                    // ->whereIn('person_state', [99]) //ดึงบุคลากรที่ไม่ทราบสถานะ
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
                    ->with('prefix','position','academic','office','memberOf','memberOf.depart','memberOf.division')
                    ->orderBy('person_birth');
                    
        $data = paginate($model, 10, $page, $req);
        
        return $res->withJson($data);
    }

    public function getInitForm($req, $res, $args)
    {
        return $res->withJson([
            'prefixes'      => Prefix::all(),
            'positions'     => Position::where('position_id', '22')->get(),
            'academics'     => Academic::where('typeac_id', '1')->get(),
            'hospPay18s'    => Hospcode::where('chwpart', '30')->get(),
            'departs'       => Depart::where('faction_id', '5')->orderBy('depart_name')->get(),
            'divisions'     => Division::orderBy('ward_name')->get(),
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
            $old     = $post['nurse']['member_of'];
            $nurse  = Person::where('person_id', $args['id'])->first();

            /** ประวัติการย้ายภายใน */
            $move = new Move;
            $move->move_person      = $nurse->person_id;

            if ($post['move_doc_no'] != '') {
                $move->move_date        = toDateDb($post['move_date']);
                $move->move_doc_no      = $post['move_doc_no'];
                $move->move_doc_date    = toDateDb($post['move_doc_date']);
            }

            $move->old_duty         = $old['duty_id'];
            $move->old_faction      = $old['faction_id'];
            $move->old_depart       = $old['depart_id'];
            $move->old_division     = $old['ward_id'];
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
                $transfer->transfer_date        = toDateDb($post['transfer_date']);

                if ($post['transfer_doc_no'] != '') {
                    $transfer->transfer_doc_no      = $post['transfer_doc_no'];
                    $transfer->transfer_doc_date    = toDateDb($post['transfer_doc_date']);
                }

                $transfer->transfer_to          = $post['transfer_to'];
                $transfer->old_duty             = $old['duty_id'];
                $transfer->old_faction          = $old['faction_id'];
                $transfer->old_depart           = $old['depart_id'];
                $transfer->old_division         = $old['ward_id'];
                $transfer->save();

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

    public function leave($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $old     = $post['nurse']['member_of'];

            /** อัพเดตข้อมูลพยาบาล */
            if ($post['leave_type'] == '1') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '7']);
            } else if ($post['leave_type'] == '2') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '6']);
            } else if ($post['leave_type'] == '3') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '9']);
            }

            if($nurse > 0) {
                /** ประวัติการโอนย้าย */
                $leave = new Leave;
                $leave->leave_person      = $args['id'];
                $leave->leave_date        = toDateDb($post['leave_date']);

                if ($post['leave_doc_no'] != '') {
                    $leave->leave_doc_no      = $post['leave_doc_no'];
                    $leave->leave_doc_date    = toDateDb($post['leave_doc_date']);
                }

                $leave->leave_type          = $post['leave_type'];
                $leave->leave_reason        = $post['leave_reason'];
                $leave->old_duty            = $old['duty_id'];
                $leave->old_faction         = $old['faction_id'];
                $leave->old_depart          = $old['depart_id'];
                $leave->old_division        = $old['ward_id'];
                
                if ($leave->save()) {
                    return $res->withJson([
                        'nurse' => $nurse
                    ]);
                } else {
                    var_dump($leave);
                }
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
            $person  = Person::where('person_id', $args['id'])->update(['person_state' => '99']);

            if($person > 0) {
                return $res->withJson([
                    'person' => $person
                ]);
            } else {
                //throw error handler
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
