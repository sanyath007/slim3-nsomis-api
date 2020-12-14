<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class ReferController extends Controller
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
                WHEN (o.ovstist='02') THEN 'Appoint'
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
                WHEN (o.ovstist='02') THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
            END ";

        return $res->withJson([
            'opVisitType' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function referInMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-30';
        
        $sql="SELECT CAST(DAY(refer_date) AS SIGNED) AS d,
            COUNT(DISTINCT CASE WHEN (refer_point='ER') THEN vn END) as 'ER',
            COUNT(DISTINCT CASE WHEN (refer_point='OPD') THEN vn END) as 'OPD',
            COUNT(DISTINCT CASE WHEN (refer_point='IPD') THEN vn END) as 'IPD',
            COUNT(DISTINCT vn) as 'ALL'
            FROM referin
            WHERE (refer_date BETWEEN ? AND ?)
            GROUP BY CAST(DAY(refer_date) AS SIGNED) 
            ORDER BY CAST(DAY(refer_date) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
    
    public function referOutMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-30';
        
        $sql="SELECT CAST(DAY(refer_date) AS SIGNED) AS d,
            COUNT(DISTINCT CASE WHEN (refer_point='ER') THEN vn END) as 'ER',
            COUNT(DISTINCT CASE WHEN (refer_point='OPD') THEN vn END) as 'OPD',
            COUNT(DISTINCT CASE WHEN (refer_point='IPD') THEN vn END) as 'IPD',
            COUNT(DISTINCT vn) as 'ALL'
            FROM referout
            WHERE(refer_date BETWEEN ? AND ?)
            GROUP BY CAST(DAY(refer_date) AS SIGNED) 
            ORDER BY CAST(DAY(refer_date) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }

    public function referInYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) AS yearmonth,
            COUNT(DISTINCT CASE WHEN (refer_point='ER') THEN vn END) as 'ER',
            COUNT(DISTINCT CASE WHEN (refer_point='OPD') THEN vn END) as 'OPD',
            COUNT(DISTINCT CASE WHEN (refer_point='IPD') THEN vn END) as 'IPD',
            COUNT(DISTINCT vn) as 'ALL'
            FROM referin
            WHERE (refer_date BETWEEN ? AND ?)
            GROUP BY CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) ";

        return $res->withJson([
            'referin' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function referOutYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) AS yearmonth,
            COUNT(DISTINCT CASE WHEN (refer_point='ER') THEN vn END) as 'ER',
            COUNT(DISTINCT CASE WHEN (refer_point='OPD') THEN vn END) as 'OPD',
            COUNT(DISTINCT CASE WHEN (refer_point='IPD') THEN vn END) as 'IPD',
            COUNT(DISTINCT vn) as 'ALL'
            FROM referout
            WHERE(refer_date BETWEEN ? AND ?)
            GROUP BY CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) ";

        return $res->withJson([
            'referout' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
}
