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
    
    public function getPatientsTambon($req, $res, $args)
    {
        $sql="SELECT i.an, i.hn, i.regdate, i.regtime, concat(i.ward, '-', w.name) as ward,
                concat(p.pname, p.fname, ' ', p.lname) as ptname,
                t.full_name as address, p.addrpart, p.moopart
                FROM ipt i 
                LEFT JOIN patient p on (p.hn=i.hn)
                LEFT JOIN ward w on (i.ward=w.ward)
                LEFT JOIN thaiaddress t on (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward in ('00', '06', '10', '11', '12'))
                AND (i.dchdate is null)
                AND (t.addressid=?)";

        return $res->withJson([
            'tambon'    => collect(DB::select("SELECT * FROM thaiaddress WHERE addressid=?", [$args['tambon']]))->first(),
            'patients'  => DB::select($sql, [$args['tambon']])
        ]);
    }

    public function getNumBed($req, $res, $args)
    {
        $sql="SELECT i.ward, w.name, count(i.an) as num_pt
                FROM ipt i 
                LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.ward IN ('00', '06', '10', '11', '12'))
                AND (i.dchdate is null)
                GROUP BY i.ward, w.name
                ORDER BY count(i.an) DESC";

        return $res->withJson(DB::select($sql));
    }

    public function getCardStat($req, $res, $args)
    {
        $sql="SELECT DATE(NOW()) as today, 
                COUNT(CASE WHEN (i.regdate = DATE(NOW())) THEN i.an END) AS new_case,
                COUNT(CASE WHEN (i.regdate < DATE(NOW()) AND i.dchdate is null) THEN i.an end) AS top_case,
                COUNT(CASE WHEN (i.dchdate = DATE(NOW())) THEN i.an END) AS discharge,
                COUNT(CASE WHEN (i.dchdate is null) THEN i.an END) AS still
                FROM ipt i LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.ward IN ('00', '06', '10', '11', '12')) ";

        return $res->withJson(collect(DB::select($sql))->first());
    }
}
