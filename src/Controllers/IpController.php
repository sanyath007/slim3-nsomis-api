<?php

namespace App\Controllers;

use App\Contoller;
use Illuminate\Database\Capsule\Manager as DB;

class IpController extends Controller
{
    public function admdate($req, $res, $args)
    {
        $sql="SELECT 
            ip.ward, w.name, SUM(ip.rw) AS rw, COUNT(ip.an) AS dc_num, SUM(a.admdate) as admdate		
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)				
            WHERE (ip.dchdate BETWEEN '2020-11-01' AND '2020-11-30')				
            GROUP BY ip.ward, w.name ";
                    
        $q = "SELECT * FROM ipt_ward_stat
            WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN '2020-11-01' AND '2020-11-30')";

        return $res->withJson([
            'admdate' => DB::select($sql),
            'wardStat' => DB::select($q),
        ]);
    }
    
    public function ipvisit($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(dchdate),'-', MONTH(dchdate)) AS yearmonth,
            COUNT(DISTINCT an) as num_pt
            FROM an_stat
            WHERE (dchdate BETWEEN '2019-10-01' AND '2020-09-30')
            GROUP BY CONCAT(YEAR(dchdate),'-', MONTH(dchdate)) ";

        return $res->withJson([
            'ipvisit' => DB::select($sql),
        ]);
    }
}
