<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class CovidController extends Controller
{
    public function getNumTambon($req, $res, $args)
    {
        $sql="SELECT t.addressid, t.name, count(i.an) as num_pt
                FROM ipt i 
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12'))
                AND (i.dchdate is null)
                AND (p.chwpart='30' AND p.amppart='01')
                GROUP BY t.addressid, t.name
                ORDER BY count(i.an) DESC";

        return $res->withJson(DB::select($sql)); //, [$args['date']]
    }
}
