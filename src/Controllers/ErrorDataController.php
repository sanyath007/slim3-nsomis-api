<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class ErrorDataController extends Controller
{
    public function chartSend($req, $res, $args)
    {
        $sdate = $args['sdate'];
        $edate = $args['edate'];
        
        $sql="SELECT ip.ward, w.name,
            COUNT(ip.an) as total,
            COUNT(case when (ip.chart_state=2) then ip.an end) AS send,
            COUNT(case when (ip.chart_state=1) then ip.an end) AS notsend,
            COUNT(case when (ip.chart_state=1 and DATEDIFF(now(), ip.dchdate) < 7) then ip.an end) AS notsendless7,
            COUNT(case when (ip.chart_state=1 and DATEDIFF(now(), ip.dchdate) between 7 and 14) then ip.an end) AS notsend7,
            COUNT(case when (ip.chart_state=1 and DATEDIFF(now(), ip.dchdate) between 15 and 21) then ip.an end) AS notsend14,
            COUNT(case when (ip.chart_state=1 and DATEDIFF(now(), ip.dchdate) between 22 and 30) then ip.an end) AS notsend21,
            COUNT(case when (ip.chart_state=1 and DATEDIFF(now(), ip.dchdate) > 31) then ip.an end) AS notsendgreat30
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)
            WHERE (ip.dchdate BETWEEN ? AND ?)
            AND (ip.ward NOT IN ('03','16','17'))
            GROUP BY ip.ward, w.name ";
                    
        // $q = "SELECT * FROM ipt_ward_stat
        //     WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'chartSend' => DB::select($sql, [$sdate, $edate]),
            // 'wardStat' => DB::select($q, [$sdate, $edate]),
        ]);
    }
}