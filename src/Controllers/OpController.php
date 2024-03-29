<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class OpController extends Controller
{
    public function opvisit($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM vn_stat
            WHERE (vstdate BETWEEN ? AND ?)
            GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'opvisit' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function opVisitType($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT 
            CASE 
                WHEN (o.ovstist IN ('01', '03', '05', '06')) THEN 'Walkin'
                WHEN (o.ovstist IN ('02','07')) THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
                ELSE 'Unknown'
            END AS type,
            COUNT(DISTINCT vn) as num_pt
            FROM ovst o
            LEFT JOIN ovstist t ON (o.ovstist=t.ovstist)
            WHERE (vstdate BETWEEN ? AND ?)
            GROUP BY CASE 
                WHEN (o.ovstist IN ('01', '03', '05', '06')) THEN 'Walkin'
                WHEN (o.ovstist IN ('02','07')) THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
            END ";

        return $res->withJson([
            'opVisitType' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
}
