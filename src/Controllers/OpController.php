<?php

namespace App\Controllers;

use App\Contoller;
use Illuminate\Database\Capsule\Manager as DB;

class OpController extends Controller
{
    public function opvisit($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM vn_stat
            WHERE (vstdate BETWEEN '2019-10-01' AND '2020-09-30')
            GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'opvisit' => DB::select($sql),
        ]);
    }
    
    public function opVisitType($req, $res, $args)
    {
        $sql="SELECT 
            CASE 
                WHEN (o.ovstist IN ('01', '03', '05', '06')) THEN 'Walkin'
                WHEN (o.ovstist='02') THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
                ELSE 'Unknown'
            END AS type,
            COUNT(DISTINCT vn) as num_pt
            FROM ovst o
            LEFT JOIN ovstist t ON (o.ovstist=t.ovstist)
            WHERE (vstdate BETWEEN '2019-10-01' AND '2020-09-30')
            GROUP BY CASE 
                WHEN (o.ovstist IN ('01', '03', '05', '06')) THEN 'Walkin'
                WHEN (o.ovstist='02') THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
            END ";

        return $res->withJson([
            'opVisitType' => DB::select($sql),
        ]);
    }
    
    public function referIn($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM vn_stat
            WHERE (vn IN (SELECT vn FROM referin WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')))
            GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'referin' => DB::select($sql),
        ]);
    }
    
    public function referOut($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM referout
            WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')
            GROUP BY CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) ";

        return $res->withJson([
            'referout' => DB::select($sql),
        ]);
    }
}
