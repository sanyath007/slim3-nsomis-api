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

    public function getChartSentList($req, $res, $args)
    {
        $sql = "SELECT a.an, a.hn, pat.cid,
                CONCAT(a.pttype,' - ',ptt.name) AS pttype, 
                a.regdate, a.regtime, a.dchdate, a.dchtime,
                CONCAT(
                    CONVERT(pat.pname, char(5)), 
                    CONVERT(pat.fname, char(20)), space(2), 
                    CONVERT(pat.lname, char(20))
                ) AS patname, dx.icd10 AS pdx, icd.name AS des, w.name AS wardname, i.admdate,
                DATEDIFF(now(), a.dchdate) AS senddatediff
                FROM ipt a 
                LEFT JOIN an_stat i ON (i.an=a.an) 
                LEFT JOIN patient pat ON (a.hn=pat.hn) 
                LEFT JOIN iptdiag dx ON (a.an=dx.an AND dx.diagtype='1') 
                LEFT JOIN icd101 icd ON (dx.icd10=icd.code) 
                LEFT JOIN pttype ptt ON (a.pttype=ptt.pttype)
                LEFT JOIN ward w ON (a.ward=w.ward)
                WHERE (a.dchdate BETWEEN ? AND ?) 
                AND (a.ward=?) ";

        if ($args['status'] == '0') {
            $status = 'ยังไม่ส่งทั้งหมด';
            $sql .= "AND (a.chart_state=1) ";
        } else if ($args['status'] == '1') {
            $status = 'ส่งแล้วทั้งหมด';
            $sql .= "AND (a.chart_state=2) ";
        } else if ($args['status'] == '2') {
            $status = 'ยังไม่ส่งน้อยกว่า 7 วัน';
            $sql .= "AND (a.chart_state=1 and DATEDIFF(now(), a.dchdate) < 7) ";
        } else if ($args['status'] == '3') {
            $status = 'ยังไม่ส่ง 7-14 วัน';
            $sql .= "AND (a.chart_state=1 and DATEDIFF(now(), a.dchdate) between 7 and 14) ";
        } else if ($args['status'] == '4') {
            $status = 'ยังไม่ส่ง 15-21 วัน';
            $sql .= "AND (a.chart_state=1 and DATEDIFF(now(), a.dchdate) between 15 and 21) ";
        } else if ($args['status'] == '5') {
            $status = 'ยังไม่ส่ง 22-30 วัน';
            $sql .= "AND (a.chart_state=1 and DATEDIFF(now(), a.dchdate) between 22 and 30) ";
        } else if ($args['status'] == '6') {
            $status = 'ยังไม่ส่งมากกว่า 31 วัน';
            $sql .= "AND (a.chart_state=1 and DATEDIFF(now(), a.dchdate) > 31) ";
        }

        $sql .= "ORDER BY a.dchdate";

        return $res->withJson([
            'data'      => DB::select($sql, [$args['sdate'], $args['edate'], $args['ward']]),
            'ward'      => DB::table('ward')->where('ward', $args['ward'])->first(),
            'status'    => $status
        ]);
    }
}