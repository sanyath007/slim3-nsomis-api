<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class ScopeController extends Controller
{
    public function getSumYear($req, $res, $args)
    {
        $sdate = ((int)$args['year']-1). '-10-01';
        $edate = $args['year']. '-09-31';
        
        $sql="SELECT 
            CONCAT(year(ov.vstdate), '-', month(ov.vstdate)) AS yymm,
            COUNT(CASE WHEN (pt.sex=1) then ov.vn END) AS men,
            COUNT(CASE WHEN (pt.sex=2) then ov.vn END) AS women,
            COUNT(CASE WHEN (ov.hn in (select hn from oapp where hn=ov.hn and nextdate=ov.vstdate)) then ov.vn END) AS app,
            COUNT(CASE WHEN (ov.hn not in (select hn from oapp where hn=ov.hn and nextdate=ov.vstdate)) then ov.vn END) AS notapp,
            COUNT(ov.vn) as total
            FROM ovst ov 
            LEFT JOIN vn_stat vs ON (ov.hn=vs.hn and ov.vn=vs.vn)
            LEFT JOIN patient pt ON (ov.hn=pt.hn)
            WHERE (ov.main_dep='097')
            AND (ov.vstdate between ? and ?)
            GROUP BY CONCAT(year(ov.vstdate), month(ov.vstdate)) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
}
