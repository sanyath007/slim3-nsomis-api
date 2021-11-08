<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class CovidController extends Controller
{
    public function getNumTambon($req, $res, $args)
    {
        $sql="SELECT t.addressid, t.name, 
                count(case when (i.dchdate is null) then i.an end) as num_pt, 
                count(case when (i.dchdate=?) then i.an end) as dc_num
                FROM ipt i 
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an in (select an from iptdiag where icd10='B342'))
                AND (p.chwpart='30' AND p.amppart='01')
                GROUP BY t.addressid, t.name
                ORDER BY count(i.an) DESC";

        return $res->withJson(DB::select($sql, [$args['date']]));
    }

    public function getPatientsTambon($req, $res, $args)
    {
        $sql="SELECT i.an, i.hn, i.regdate, i.regtime, concat(i.ward, '-', w.name) AS ward,
                concat(p.pname, p.fname, ' ', p.lname) AS ptname,
                t.full_name AS address, p.addrpart, p.moopart, a.pdx, i.prediag
                FROM ipt i 
                LEFT JOIN an_stat a ON (i.an=a.an)
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an IN (select an from iptdiag where icd10='B342'))
                AND (i.dchdate is null)
                AND (t.addressid=?)";

        return $res->withJson([
            'tambon'    => collect(DB::select("SELECT * FROM thaiaddress WHERE addressid=?", [$args['tambon']]))->first(),
            'patients'  => DB::select($sql, [$args['tambon']])
        ]);
    }

    public function getDischargesTambon($req, $res, $args)
    {
        $sql="SELECT i.an, i.hn, i.regdate, i.regtime, i.dchdate, i.dchtime,
                concat(i.ward, '-', w.name) AS ward,
                concat(p.pname, p.fname, ' ', p.lname) AS ptname,
                t.full_name AS address, p.addrpart, p.moopart, a.pdx, i.prediag, a.admdate
                FROM ipt i 
                LEFT JOIN an_stat a ON (i.an=a.an)
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an IN (select an from iptdiag where icd10='B342'))
                AND (i.dchdate=DATE(NOW()))
                AND (t.addressid=?)";

        return $res->withJson([
            'tambon'    => collect(DB::select("SELECT * FROM thaiaddress WHERE addressid=?", [$args['tambon']]))->first(),
            'patients'  => DB::select($sql, [$args['tambon']])
        ]);
    }

    public function getNumBed($req, $res, $args)
    {
        $sql="SELECT i.ward, w.name, 
                COUNT(CASE WHEN (i.an IN (select an from iptdiag where icd10='B342')) THEN i.an END) AS num_pt
                FROM ipt i 
                LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.dchdate is null)
                GROUP BY i.ward, w.name
                ORDER BY COUNT(i.an) DESC";

        return $res->withJson(DB::select($sql));
    }

    public function getPatientsward($req, $res, $args)
    {
        $sql="SELECT i.an, i.hn, i.regdate, i.regtime, concat(i.ward, '-', w.name) AS ward,
                concat(p.pname, p.fname, ' ', p.lname) AS ptname,
                t.full_name AS address, p.addrpart, p.moopart, a.pdx, i.prediag
                FROM ipt i 
                LEFT JOIN an_stat a ON (i.an=a.an)
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an IN (select an from iptdiag where icd10='B342'))
                AND (i.dchdate is null)
                AND (i.ward=?)";

        return $res->withJson([
            'ward'    => collect(DB::select("SELECT * FROM ward WHERE ward=?", [$args['ward']]))->first(),
            'patients'  => DB::select($sql, [$args['ward']])
        ]);
    }
    
    public function getPatientsAll($req, $res, $args)
    {
        $sql="SELECT i.an, i.hn, i.regdate, i.regtime, i.dchdate, i.dchtime,
                concat(i.ward, '-', w.name) AS ward,
                concat(p.pname, p.fname, ' ', p.lname) AS ptname,
                t.full_name AS address, p.addrpart, p.moopart, a.pdx, i.prediag, a.admdate
                FROM ipt i 
                LEFT JOIN an_stat a ON (i.an=a.an)
                LEFT JOIN patient p ON (p.hn=i.hn)
                LEFT JOIN ward w ON (i.ward=w.ward)
                LEFT JOIN thaiaddress t ON (t.addressid=concat(p.chwpart, p.amppart, p.tmbpart))
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an IN (select an from iptdiag where icd10='B342')) ";

        $type = "";
        if ($args['type'] == '1') {
            $sql .= "AND (i.regdate < DATE(NOW()) AND i.dchdate is null) ";
            $type = "ยกมา";
        } else if ($args['type'] == '2') {
            $sql .= "AND (i.regdate = DATE(NOW())) ";
            $type = "ใหม่";
        } else if ($args['type'] == '3') {
            $sql .= "AND (i.dchdate = DATE(NOW())) ";
            $type = "จำหน่าย";
        } else if ($args['type'] == '0') {
            $sql .= "AND (i.dchdate is null) ";
            $type = "คงพยาบาล";
        }
        
        $sql .= "ORDER BY i.regdate, i.an ";

        return $res->withJson([
            'type'    => $type,
            'patients'  => DB::select($sql)
        ]);
    }

    public function getCardStat($req, $res, $args)
    {
        $sql="SELECT DATE(NOW()) AS today, 
                COUNT(CASE WHEN (i.regdate = DATE(NOW())) THEN i.an END) AS new_case,
                COUNT(CASE WHEN (i.regdate < DATE(NOW()) AND i.dchdate is null) THEN i.an END) AS top_case,
                COUNT(CASE WHEN (i.dchdate = DATE(NOW())) THEN i.an END) AS discharge,
                COUNT(CASE WHEN (i.dchdate is null) THEN i.an END) AS still
                FROM ipt i LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.ward IN ('00', '06', '10', '11', '12', '18', '21'))
                AND (i.an in (select an from iptdiag where icd10='B342')) ";

        return $res->withJson(collect(DB::select($sql))->first());
    }

    public function getRegMonth($req, $res, $args)
    {
        $sdate = ($args['month']). '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql="SELECT CAST(DAY(regdate) AS SIGNED) AS d, COUNT(i.an) AS 'all' 
                FROM ipt i LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.regdate BETWEEN ? AND ?)
                AND (
                    (i.ward IN ('11', '12', '18', '10', '00', '21'))
                    OR (i.ward='06' AND i.an in (select an from iptdiag where icd10='B342'))
                )
                GROUP BY CAST(DAY(regdate) AS SIGNED) 
                ORDER BY CAST(DAY(regdate) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }

    public function getRegWardMonth($req, $res, $args)
    {
        $sdate = ($args['month']). '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql="SELECT CAST(DAY(regdate) AS SIGNED) AS d, 
                COUNT(CASE WHEN (i.ward='06') THEN i.an END) AS fl1,
                COUNT(CASE WHEN (i.ward='11') THEN i.an END) AS fl2,
                COUNT(CASE WHEN (i.ward='12') THEN i.an END) AS fl3,
                COUNT(CASE WHEN (i.ward='18') THEN i.an END) AS fl6,
                COUNT(CASE WHEN (i.ward='10') THEN i.an END) AS fl9,
                COUNT(CASE WHEN (i.ward='00') THEN i.an END) AS fl10,
                COUNT(CASE WHEN (i.ward='21') THEN i.an END) AS w11
                FROM ipt i LEFT JOIN ward w ON (i.ward=w.ward)
                WHERE (i.regdate BETWEEN ? AND ?)
                AND (
                    (i.ward IN ('11', '12', '18', '10', '00', '21'))
                    OR (i.ward='06' AND i.an in (select an from iptdiag where icd10='B342'))
                )
                GROUP BY CAST(DAY(regdate) AS SIGNED) 
                ORDER BY CAST(DAY(regdate) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
}
