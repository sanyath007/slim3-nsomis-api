<?php

namespace App\Controllers;

use App\Contoller;
use Illuminate\Database\Capsule\Manager as DB;

class ErController extends Controller
{
    public function visit($req, $res, $args)
    {
        $sql="SELECT
        CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
        #CONCAT(pr.name) as period, 
        #COUNT(vn) as pt_num,
        #COUNT(CASE WHEN(er.er_pt_type='1') THEN (er.vn) END) AS type1,
        #COUNT(CASE WHEN(er.er_pt_type='2') THEN (er.vn) END) AS type2,
        #COUNT(CASE WHEN(er.er_pt_type='3') THEN (er.vn) END) AS type3,
        #COUNT(CASE WHEN(er.er_pt_type='4') THEN (er.vn) END) AS type4,
        #COUNT(CASE WHEN(er.er_pt_type='5') THEN (er.vn) END) AS type5,
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS emergency,
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS ugency,
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS semiugency,
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS nonugency,
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS resuscitation
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN '2019-10-01' AND '2020-09-30')
        #GROUP BY er.er_period
        #ORDER BY er.er_period
        GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate))";

        return $res->withJson([
            'visit' => DB::select($sql),
        ]);
    }
    
    public function emergency($req, $res, $args)
    {
        $sql="SELECT
        COUNT(CASE WHEN(er.er_emergency_type='1') THEN (er.vn) END) AS 'Emergency',
        COUNT(CASE WHEN(er.er_emergency_type='2') THEN (er.vn) END) AS 'Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='3') THEN (er.vn) END) AS 'Semi-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='4') THEN (er.vn) END) AS 'Non-Ugency',
        COUNT(CASE WHEN(er.er_emergency_type='5') THEN (er.vn) END) AS 'Resuscitation'
        FROM er_regist er 
        LEFT JOIN er_period pr ON (er.er_period=pr.er_period)
        WHERE (vstdate BETWEEN '2019-10-01' AND '2020-09-30') ";

        return $res->withJson([
            'emergency' => DB::select($sql),
        ]);
    }
    
    // public function referIn($req, $res, $args)
    // {
    //     $sql="SELECT CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
    //         COUNT(DISTINCT vn) as num_pt
    //         FROM vn_stat
    //         WHERE (vn IN (SELECT vn FROM referin WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')))
    //         GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

    //     return $res->withJson([
    //         'referin' => DB::select($sql),
    //     ]);
    // }
    
    // public function referOut($req, $res, $args)
    // {
    //     $sql="SELECT CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) AS yearmonth,
    //         COUNT(DISTINCT vn) as num_pt
    //         FROM referout
    //         WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')
    //         GROUP BY CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) ";

    //     return $res->withJson([
    //         'referout' => DB::select($sql),
    //     ]);
    // }
}
