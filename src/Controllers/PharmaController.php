<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\UserDrugList;
use App\Models\DrugItem;

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
        
        $icodes = "";
        $i = 0;
        for($i; $i < count($icodeLines) - 1; $i++) {
            if($i !== count($icodeLines) - 2) {
                $icodes .= "'" .$icodeLines[$i]. "', ";
            } else {
                $icodes .= "'" .$icodeLines[$i]. "'";
            }
        }

        $item = new UserDrugList;
        $item->user_id = 'sumran';
        $item->name = 'sumran-opd';
        $item->type = 'OPD';
        $item->icodes = $icodes;
        
        if($item->save()) {
            return $res->withJson([
                'drugList' => $item
            ]);
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

        $sql = "SELECT icode, name, strength, units, unitprice 
                FROM drugitems WHERE (icode IN (". $userDrugLists->icodes ."))";

        //TODO: create pagination object

        return $res->withJson([
            'drugItems' => DB::select($sql)
        ]);
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
