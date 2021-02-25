<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class IpController extends Controller
{
    public function getAdmdateMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));
        
        $sql="SELECT 
            ip.ward, w.name, 
            SUM(ip.rw) AS rw, 
            COUNT(ip.an) AS dc_num, 
            SUM(a.admdate) as admdate
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)				
            WHERE (ip.dchdate BETWEEN ? AND ?)
            AND (ip.ward<>'05')
            AND (ip.an NOT IN (SELECT an FROM ipt_newborn))
            GROUP BY ip.ward, w.name ";
                    
        $q = "SELECT * FROM ipt_ward_stat
            WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'admdate' => DB::select($sql, [$sdate, $edate]),
            'wardStat' => DB::select($q, [$sdate, $edate]),
        ]);
    }
    
    public function getBedoccYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';

        $sql="SELECT 
            ip.ward, w.name, 
            SUM(ip.rw) AS rw, 
            COUNT(ip.an) AS dc_num, 
            SUM(a.admdate) as admdate
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)				
            WHERE (ip.dchdate BETWEEN ? AND ?)
            AND (ip.ward<>'05')
            AND (ip.an NOT IN (SELECT an FROM ipt_newborn))
            GROUP BY ip.ward, w.name ";
                    
        $q = "SELECT * FROM ipt_ward_stat
            WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'admdate' => DB::select($sql, [$sdate, $edate]),
            'wardStat' => DB::select($q, [$sdate, $edate]),
        ]);
    }
    
    public function getBedEmptyDay($req, $res, $args)
    {
        $sql="SELECT ic.ward, w.name as wardname,
            COUNT(CASE WHEN (
                (
                    (ic.regdate = '" .$args['date']. "' AND ic.regtime <= '23:59:59')
                    AND (
                        (ic.dchdate is null)
                        OR (ic.dchdate = '" .$args['date']. "' AND ic.dchtime > '16:00:00')
                        OR (ic.dchdate > '" .$args['date']. "')
                    )
                )
                OR (
                    (ic.regdate < '" .$args['date']. "')
                    AND (
                        (ic.dchdate is null)
                        OR (ic.dchdate = '" .$args['date']. "' AND ic.dchtime > '16:00:00')
                        OR (ic.dchdate > '" .$args['date']. "')
                    )
                )
            ) THEN ic.an END) AS num_pt 
            FROM (
                select an,hn,regdate,regtime,dchdate,dchtime,ward 
                from ipt where ((dchdate >= ?) or (dchdate is null))
                and (an not in (select an from ipt_newborn))
                ORDER BY regdate
            ) as ic 
            LEFT JOIN ward w ON (ic.ward=w.ward) 
            GROUP BY ic.ward, w.name ORDER BY ic.ward, w.name ";

        return $res->withJson(DB::select($sql, [$args['date']]));
    }

    public function ipclass($req, $res, $args)
    {        
        $sql="SELECT ip.ward, w.name, 
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='1'))) THEN ip.an END) AS type1,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='2'))) THEN ip.an END) AS type2,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='3'))) THEN ip.an END) AS type3,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='4'))) THEN ip.an END) AS type4,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='5'))) THEN ip.an END) AS type5,
            COUNT(CASE WHEN (ip.an not IN (select an from ipt_icnp)) THEN ip.an END) AS 'unknown',
            COUNT(ip.an) AS 'all'
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE (ip.dchdate BETWEEN ? AND ?) 
            GROUP BY ip.ward, w.name ";

        return $res->withJson([
            'classes' => DB::select($sql, [$args['sdate'], $args['edate']]),
        ]);
    }

    public function ptDchByWard($req, $res, $args)
    {
        $sql="SELECT a.an, a.hn, pat.cid,
            CONCAT(a.pttype,' - ',ptt.name) AS pttype, 
            a.regdate, a.regtime, a.dchdate, a.dchtime,
            CONCAT(
                CONVERT(pat.pname, char(5)), 
                CONVERT(pat.fname, char(20)), space(2), 
                CONVERT(pat.lname, char(20))
            ) AS patname, dx.icd10 AS pdx, icd.name AS des, w.name AS wardname, i.admdate
            FROM ipt a 
            LEFT JOIN an_stat i ON (i.an=a.an) 
            LEFT JOIN patient pat ON (a.hn=pat.hn) 
            LEFT JOIN iptdiag dx ON (a.an=dx.an AND dx.diagtype='1') 
            LEFT JOIN icd101 icd ON (dx.icd10=icd.code) 
            LEFT JOIN pttype ptt ON (a.pttype=ptt.pttype)
            LEFT JOIN ward w ON (a.ward=w.ward)
            WHERE (a.dchdate BETWEEN ? AND ?) AND (a.ward=?) 
            ORDER BY a.dchdate";

        return $res->withJson([
            'data' => DB::select($sql, [$args['sdate'], $args['edate'], $args['ward']]),
            'ward' => DB::table('ward')->where('ward', $args['ward'])->first()
        ]);
    }

    
    public function ptLosByCare($req, $res, $args)
    {
        $sql="SELECT a.an,a.hn,pat.cid,
            CONCAT(a.pttype,' - ',ptt.name) AS pttype, 
            a.regdate, a.regtime, a.dchdate, a.dchtime, 
            CONCAT(
                convert(pat.pname,char(5)), 
                convert(pat.fname,char(20)), 
                space(2), 
                convert(pat.lname,char(20))) AS patname, 
            dx.icd10 AS pdx, icd.name AS des, w.name AS wardname, ws.admdate, ws.admit_hour
            FROM ipt_ward_stat ws
            LEFT JOIN ipt a  ON (a.an=ws.an) 
            LEFT JOIN patient pat ON (a.hn=pat.hn) 
            LEFT JOIN iptdiag dx ON (a.an=dx.an and dx.diagtype='1') 
            LEFT JOIN icd101 icd ON (dx.icd10=icd.code) 
            LEFT JOIN pttype ptt ON (a.pttype=ptt.pttype)
            LEFT JOIN ward w ON (a.ward=w.ward)
            WHERE (ws.an IN (SELECT an FROM ipt WHERE (dchdate BETWEEN  ? AND ?)))
            AND (ws.ward=?) 
            ORDER BY regdate";

        return $res->withJson([
            'data' => DB::select($sql, [$args['sdate'], $args['edate'], $args['ward']]),
            'ward' => DB::table('ward')->where('ward', $args['ward'])->first()
        ]);
    }
}
