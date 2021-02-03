<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\UserDrugList;
use App\Models\DrugItem;

class PharmaController extends Controller
{
    private function drugListsToSQL($icodesArray=[])
    {
        $i = 0;
        $icodes = "";
        for($i; $i < count($icodesArray) - 1; $i++) {
            if($i !== count($icodesArray) - 2) {
                $icodes .= "'" .$icodesArray[$i]. "', ";
            } else {
                $icodes .= "'" .$icodesArray[$i]. "'";
            }
        }

        return $icodes;
    }

    private function drugListsToArray($icodesStr)
    {
        $cb = function($str) {
            return (int)str_replace('\'', '', $str);
        };

        return array_map($cb, explode(',', $icodesStr));
    }

    public function storeUserDrugList($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();

        $item = new UserDrugList;
        $item->user_id = $post['user_id'];
        $item->name = $post['name'];
        $item->type = $post['type'];
        $item->icodes = $post['icodes'];
        
        if($item->save()) {
            return $res->withJson([
                'drugList' => $item
            ]);
        }
    }
    
    public function storeUserDrugListFile($req, $res, $args)
    {
        /** File handling: Read text file and get line of data to array */
        $icodeLines = [];
        $fp = fopen('./uploads/delivery_drug.txt', 'r') or die('fopen failed!!');
        
        while(!feof($fp)) {
            array_push($icodeLines, trim(fgets($fp)));
        }

        fclose($fp);
        /** File handling */

        $item = new UserDrugList;
        $item->user_id = 'sumran';
        $item->name = 'sumran-opd';
        $item->type = 'OPD';
        $item->icodes = $this->drugListsToSQL($icodeLines);
        
        if($item->save()) {
            return $res->withJson([
                'drugList' => $item
            ]);
        }
    }

    public function removeUserDrugList($req, $res, $args)
    {
        if (!$args['id']) {
            return $res->withStatus(500)->withJson([
                'status' => 0,
                'message' => 'พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้'
            ]);
        } else {
            $udl = UserDrugList::find($args['id']);

            if ($udl->delete()) {
                return $res->withStatus(200)->withJson([
                    'status' => 1,
                    'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
                ]);
            } else {
                return $res->withStatus(500)->withJson([
                    'status' => 0,
                    'message' => 'พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้'
                ]);
            }
        }
    }

    public function getUserDrugList($req, $res, $args)
    {
        $userDrugLists = UserDrugList::where(['user_id' => $args['user']])->get();

        return $res->withJson([
            'userDrugLists' => $userDrugLists
        ]);
    }

    public function getUserDrugListDetail($req, $res, $args)
    {
        $userDrugLists = UserDrugList::find($args['id']);
        
        $link = 'http://'.$req->getServerParam('SERVER_NAME').$req->getServerParam('REDIRECT_URL');
        $page = (int)$req->getQueryParam('page');

        $data = null;
        $icodes = $this->drugListsToArray($userDrugLists->icodes);
        $model = DB::table('drugitems')
                    ->select('icode', 'name', 'strength', 'units', 'unitprice')
                    ->whereIn('icode', $icodes);

        if ($page) {
            $data = paginate($model, 'icode', 10, $page, $link);
        } else {
            $data = DB::select($sql);
        }

        return $res->withJson($data);
    }

    public function opMonth($req, $res, $args)
    {
        $drugLists = UserDrugList::where(['id' => $args['listId']])->first();
        $icodes = $drugLists->icodes;
        
        $sql="SELECT o.icode, (SELECT dd.name FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugName,
            (SELECT dd.strength FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugStrength,
            SUM(qty) AS sum_qty,
            (SELECT dd.units FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugUnits,
            SUM(o.sum_price) AS sum_price
            FROM opitemrece o 
            WHERE (o.rxdate BETWEEN ? AND ?)
            AND (LEFT(o.icode, 1)=1)
            AND (TRIM(o.an) <> '')
            AND (o.icode IN (". $icodes ."))
            GROUP BY o.icode 
            ORDER BY sum_price DESC ";

        return $res->withJson(DB::select($sql, [$args['sdate'], $args['edate']]));
    }
    
    public function ipMonth($req, $res, $args)
    {
        $drugLists = UserDrugList::where(['id' => $args['listId']])->first();
        $icodes = $drugLists->icodes;

        $sql="SELECT (SELECT dd.name FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugName,
            (SELECT dd.strength FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugStrength,
            SUM(qty) AS sum_qty,
            (SELECT dd.units FROM drugitems dd WHERE (o.icode = dd.icode)) AS drugUnits,
            SUM(o.sum_price) AS sum_price
            FROM opitemrece o 
            WHERE (o.rxdate BETWEEN ? AND ?)
            AND (LEFT(o.icode, 1)=1)
            AND (TRIM(o.an) <> '')
            AND (o.icode IN (". $icodes ."))
            GROUP BY o.icode 
            ORDER BY sum_price DESC ";

        return $res->withJson(DB::select($sql, [$args['sdate'], $args['edate']]));
    }
}
