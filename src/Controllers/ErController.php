<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class ErController extends Controller
{
    public function sumPeriod($req, $res, $args)
    {
        $sql="SELECT 
            pr.name as period, 
            COUNT(vn) as pt_num,
            COUNT(CASE WHEN(er.er_pt_type='1') THEN (er.vn) END) AS type1,
            COUNT(CASE WHEN(er.er_pt_type='2') THEN (er.vn) END) AS type2,
            COUNT(CASE WHEN(er.er_pt_type='3') THEN (er.vn) END) AS type3,
            COUNT(CASE WHEN(er.er_pt_type='4') THEN (er.vn) END) AS type4,
            COUNT(CASE WHEN(er.er_pt_type='5') THEN (er.vn) END) AS type5,
            COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS emer1,
            COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS emer2,
            COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS emer3,
            COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS emer4,
            COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS emer5
            FROM er_regist er 
            LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
            where (vstdate BETWEEN ? AND ?)
            GROUP BY pr.name
            ORDER BY pr.name ";

        return $res->withJson(DB::select($sql, [$args['sdate'], $args['edate']]));
    }

    public function getVisitMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));
        
        $sql="SELECT
        CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS 'emergency',
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS 'ugency',
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS 'semi',
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS 'non',
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS 'resuscitation'
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN ? AND ?)
        GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'visit' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function getEmergencyMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));
        
        $sql="SELECT
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS 'Emergency',
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS 'Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS 'Semi-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS 'Non-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS 'Resuscitation'
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'emergency' => DB::select($sql, [$sdate, $edate]),
        ]);
    }

    public function getVisitYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT
        CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS 'emergency',
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS 'ugency',
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS 'semi',
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS 'non',
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS 'resuscitation'
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN ? AND ?)
        GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'visit' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function getEmergencyYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS 'Emergency',
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS 'Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS 'Semi-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS 'Non-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS 'Resuscitation'
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'emergency' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
}
