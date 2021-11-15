<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\EpidWeek;

class IpController extends Controller
{
    public function getAdmDcDay($req, $res, $args)
    {
        $currentDate = date('Y-m-d');
        $date = $args['date'];

        if($date == $currentDate) {
            $dchtime = date("H:i:s");
        } else {
            $dchtime = '23:59:59';
        }

        $sql = "SELECT ip.ward, w.name, 
                SUM(CASE WHEN (ip.dchdate=?) THEN ip.rw END) AS rw, 
                SUM(CASE WHEN (ip.dchdate=?) THEN a.admdate END) AS admdate,
                COUNT(CASE WHEN (ip.regdate=?) THEN ip.an END) AS adm_num,
                COUNT(CASE WHEN (ip.dchdate=?) THEN ip.an END) AS dc_num, ";

        if ($date == $currentDate) {
            $sql .= "COUNT(CASE WHEN (ip.dchdate IS NULL) THEN ip.an END) AS remain_num ";
        } else {
            $sql .= "COUNT(CASE WHEN (
                        (
                            (ip.regdate = '" .$date. "' AND ip.regtime <= '23:59:59')
                            OR (ip.regdate < '" .$date. "')
                        ) AND (
                            (ip.dchdate is null)
                            OR (ip.dchdate = '" .$date. "' AND ip.dchtime > '".$dchtime."')
                            OR (ip.dchdate > '" .$date. "')
                        )
                    ) THEN ip.an END) AS remain_num ";
        }

        $sql .= "FROM ipt ip
                LEFT JOIN ward w ON (ip.ward=w.ward)
                LEFT JOIN an_stat a ON (ip.an=a.an)	
                WHERE (ip.ward NOT IN ('03','16','17'))
                AND (ip.an not in (select an from ipt_newborn))
                GROUP BY ip.ward, w.name ";

        $q = "SELECT * FROM iptbedmove WHERE (movedate=?) ";

        return $res->withJson([
            'ipStat'    => DB::select($sql, [$date, $date, $date, $date]),
            'moveStat'  => DB::select($q, [$date]),
        ]);
    }

    public function getAdmDcMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql = "SELECT ip.ward, w.name, 
                SUM(CASE WHEN (ip.dchdate BETWEEN ? AND ?) THEN ip.rw END) AS rw, 
                SUM(CASE WHEN (ip.dchdate BETWEEN ? AND ?) THEN a.admdate END) AS admdate,
                COUNT(CASE WHEN (ip.regdate BETWEEN ? AND ?) THEN ip.an END) AS adm_num,
                COUNT(CASE WHEN (ip.dchdate BETWEEN ? AND ?) THEN ip.an END) AS dc_num
                FROM ipt ip
                LEFT JOIN ward w ON (ip.ward=w.ward)
                LEFT JOIN an_stat a ON (ip.an=a.an)	
                WHERE (ip.ward NOT IN ('03','16','17'))
                AND (ip.an not in (select an from ipt_newborn))
                GROUP BY ip.ward, w.name ";

        $q = "SELECT * FROM iptbedmove WHERE (movedate BETWEEN ? AND ?) ";

        return $res->withJson([
            'ipStat'    => DB::select($sql, [$sdate, $edate, $sdate, $edate, $sdate, $edate, $sdate, $edate]),
            'moveStat'  => DB::select($q, [$sdate, $edate]),
        ]);
    }

    public function getAdmdateMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));
        
        $sql="SELECT 
            ip.ward, w.name, 
            SUM(ip.rw) AS rw, 
            COUNT(ip.an) AS dc_num, 
            SUM(a.admdate) as admdate, '' as bed
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
            #AND (ip.ward<>'05')
            #AND (ip.an NOT IN (SELECT an FROM ipt_newborn))
            AND (ip.ward NOT IN ('03','16','17'))
            GROUP BY ip.ward, w.name ";
                    
        $q = "SELECT * FROM ipt_ward_stat WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'admdate' => DB::select($sql, [$sdate, $edate]),
            'wardStat' => DB::select($q, [$sdate, $edate]),
        ]);
    }

    public function getBedoccMonth($req, $res, $args)
    {
        $sdate = ($args['month']). '-01';
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
            #AND (ip.ward<>'05')
            #AND (ip.an NOT IN (SELECT an FROM ipt_newborn))
            AND (ip.ward NOT IN ('03','16','17'))
            GROUP BY ip.ward, w.name ";
                    
        $q = "SELECT * FROM ipt_ward_stat WHERE an IN (SELECT an FROM ipt WHERE dchdate BETWEEN ? AND ?) ";

        return $res->withJson([
            'admdate' => DB::select($sql, [$sdate, $edate]),
            'wardStat' => DB::select($q, [$sdate, $edate]),
        ]);
    }

    public function getBedoccWeek($req, $res, $args)
    {
        $week = EpidWeek::where('week_no', $args['week'])->first();

        $sql="SELECT 
            ip.ward, w.name, 
            SUM(ip.rw) AS rw, 
            COUNT(ip.an) AS dc_num, 
            SUM(a.admdate) as admdate 
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)				
            WHERE (ip.dchdate BETWEEN ? AND ?)
            #AND (ip.ward<>'05')
            #AND (ip.an NOT IN (SELECT an FROM ipt_newborn))
            AND (
                (ip.ward IN ('11', '12', '18', '10', '00', '21'))
                OR (ip.ward='06' AND ip.an in (select an from iptdiag where icd10='B342'))
            )
            GROUP BY ip.ward, w.name ";

        $q = "SELECT * FROM ipt_ward_stat 
                WHERE an IN (SELECT an FROM ipt WHERE (dchdate BETWEEN ? AND ?) 
                AND (
                    (ward IN ('11', '12', '18', '10', '00', '21'))
                    OR (ward='06' AND an in (select an from iptdiag where icd10='B342'))
                )) ";

        return $res->withJson([
            'admdate' => DB::select($sql, [$week->start_date, $week->end_date]),
            'wardStat' => DB::select($q, [$week->start_date, $week->end_date]),
        ]);
    }

    public function getBedEmptyDay($req, $res, $args)
    {
        if($args['date'] == date('Y-m-d')) {
            $dchtime = date("H:i:s");
        } else {
            $dchtime = '23:59:59';
        }

        $sql="SELECT ip.ward, w.name as wardname,
            COUNT(CASE WHEN (
                (
                    (ip.regdate = '" .$args['date']. "' AND ip.regtime <= '23:59:59')
                    OR (ip.regdate < '" .$args['date']. "')
                ) AND (
                    (ip.dchdate is null)
                    OR (ip.dchdate = '" .$args['date']. "' AND ip.dchtime > '".$dchtime."')
                    OR (ip.dchdate > '" .$args['date']. "')
                )
            ) THEN ip.an END) AS num_pt 
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE (ip.ward NOT IN ('03','16','17') AND ip.an not in (select an from ipt_newborn))
            OR (ip.ward IN ('13','15') AND ip.an in (select an from ipt_newborn))
            GROUP BY ip.ward, w.name ORDER BY ip.ward, w.name ";

        return $res->withJson(DB::select($sql));
    }

    public function getIpList($req, $res, $args)
    {
        if($args['date'] == date('Y-m-d')) {
            $dchtime = date("H:i:s");
        } else {
            $dchtime = '23:59:59';
        }

        $link = 'http://'.$req->getServerParam('SERVER_NAME').$req->getServerParam('REDIRECT_URL');
        $page = (int)$req->getQueryParam('page');
        
        $argsDate = $args['date'];
        $argsWard = $args['ward'];
        $model = DB::table('ipt')
                    ->leftJoin('patient', 'ipt.hn', '=', 'patient.hn')
                    ->leftJoin('ward', 'ipt.ward', '=', 'ward.ward')
                    ->leftJoin('ipt_icnp', 'ipt.an', '=', 'ipt_icnp.an')
                    ->where(function($query) use ($argsDate) {
                        $query->where(function($q) use ($argsDate) {
                            $q->where('ipt.regdate', $argsDate)
                                ->where('ipt.regtime', '<=', '23:59:59');
                        })
                        ->orWhere(function($q) use ($argsDate) {
                            $q->where('regdate', '<', $argsDate);
                        });
                    })
                    ->where(function($query) use ($argsDate, $dchtime) {
                        $query->whereNull('ipt.dchdate')
                            ->orWhere(function($q) use ($argsDate, $dchtime) {
                                $q->where('ipt.dchdate', $argsDate)->where('ipt.dchtime', '>', $dchtime);
                            })
                            ->orWhere(function($q) use ($argsDate, $dchtime) {
                                $q->where('ipt.dchdate', '>', $argsDate);
                            });
                    })
                    ->where(function($query) use ($argsWard) {
                        $query->where('ipt.ward', $argsWard);
                    })
                    ->whereRaw('ipt.an not in (select an from ipt_newborn)')
                    ->select('ipt.an','ipt.hn','ipt.regdate','ipt.regtime','ipt.dchdate','ipt.dchtime','ipt.ward',
                        DB::raw('concat(patient.pname,patient.fname, " ",patient.lname) as patient_name'),
                        'patient.birthday','ward.name as ward_name','ipt_icnp.icnp_classification_id');

        $data = paginate($model, 'ipt.regdate', 10, $page, $link);

        return $res->withJson($data);
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

    public function ipclassDay($req, $res, $args)
    {
        if($args['date'] == date('Y-m-d')) {
            $dchtime = date("H:i:s");
        } else {
            $dchtime = '23:59:59';
        }

        $sql="SELECT ip.ward, w.name, 
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='1'))) THEN ip.an END) AS type1,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='2'))) THEN ip.an END) AS type2,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='3'))) THEN ip.an END) AS type3,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='4'))) THEN ip.an END) AS type4,
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='5'))) THEN ip.an END) AS type5,
            COUNT(CASE WHEN (ip.an not IN (select an from ipt_icnp)) THEN ip.an END) AS 'unknown',
            COUNT(ip.an) AS 'all'
            FROM ipt ip LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE ((
                    (ip.regdate = '" .$args['date']. "' AND ip.regtime <= '23:59:59')
                    OR (ip.regdate < '" .$args['date']. "')
                ) AND (
                    (ip.dchdate is null)
                    OR (ip.dchdate = '" .$args['date']. "' AND ip.dchtime > '".$dchtime."')
                    OR (ip.dchdate > '" .$args['date']. "')
                )
            )
            GROUP BY ip.ward, w.name ";

        return $res->withJson([
            'classes' => DB::select($sql),
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
