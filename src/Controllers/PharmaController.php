<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\UserDrugList;

class PharmaController extends Controller
{
    private function createDrugListsSQL($icodes)
    {
        $drugLists = implode("', '", $icodes);

        return $drugLists;
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

    public function opMonth($req, $res, $args)
    {
        $drugLists = UserDrugList::where(['user_id' => 'sanyath'])->first();
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
        $drugLists = UserDrugList::where(['user_id' => 'sanyath'])->first();
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
