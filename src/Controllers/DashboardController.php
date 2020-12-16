<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class DashboardController extends Controller
{
    public function opVisitDay($req, $res, $args)
    {       
        $sql="SELECT CAST(HOUR(vsttime) AS SIGNED) AS hhmm,
            COUNT(DISTINCT vn) as num_pt
            FROM ovst
            WHERE (vstdate=?)
            GROUP BY CAST(HOUR(vsttime) AS SIGNED) 
            ORDER BY CAST(HOUR(vsttime) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }
    
    public function opVisitMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT CAST(DAY(vstdate) AS SIGNED) AS d,
            COUNT(DISTINCT vn) as num_pt
            FROM ovst
            WHERE (vstdate BETWEEN ? AND ?)
            GROUP BY CAST(DAY(vstdate) AS SIGNED) 
            ORDER BY CAST(DAY(vstdate) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }
    
    public function opVisitTypeDay($req, $res, $args)
    {        
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
            WHERE (vstdate=?)
            GROUP BY CASE 
                WHEN (o.ovstist IN ('01', '03', '05', '06')) THEN 'Walkin'
                WHEN (o.ovstist='02') THEN 'Appoint'
                WHEN (o.ovstist='04') THEN 'Refer'
                WHEN (o.ovstist IN ('08', '09', '10')) THEN 'EMS'
            END ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }
    
    public function opVisitTypeMonth($req, $res, $args)
    {        
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

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

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }

    public function ipVisitDay($req, $res, $args)
    {
        $sql="SELECT CAST(HOUR(dchtime) AS SIGNED) AS hhmm,
            COUNT(DISTINCT an) as num_pt
            FROM ipt
            WHERE (dchdate=?)
            GROUP BY CAST(HOUR(dchtime) AS SIGNED) 
            ORDER BY CAST(HOUR(dchtime) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }

    public function ipVisitMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT CAST(DAY(dchdate) AS SIGNED) AS d,
            COUNT(DISTINCT an) as num_pt
            FROM ipt
            WHERE (dchdate BETWEEN ? AND ?)
            GROUP BY CAST(DAY(dchdate) AS SIGNED) 
            ORDER BY CAST(DAY(dchdate) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }
    
    public function ipVisitYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT CONCAT(YEAR(dchdate),'-', MONTH(dchdate)) AS yearmonth,
            COUNT(DISTINCT an) as num_pt
            FROM an_stat
            WHERE (dchdate BETWEEN ? AND ?)
            GROUP BY CONCAT(YEAR(dchdate),'-', MONTH(dchdate)) ";

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }

    public function ipClassDay($req, $res, $args)
    {
        $sql="SELECT 
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='1'))) THEN ip.an END) AS 'ประเภท 1',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='2'))) THEN ip.an END) AS 'ประเภท 2',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='3'))) THEN ip.an END) AS 'ประเภท 3',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='4'))) THEN ip.an END) AS 'ประเภท 4',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='5'))) THEN ip.an END) AS 'ประเภท 5',
            COUNT(CASE WHEN (ip.an not IN (select an from ipt_icnp)) THEN ip.an END) AS 'ไม่ระบุ'
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE (ip.dchdate=?) ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }

    public function ipClassMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT 
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='1'))) THEN ip.an END) AS 'ประเภท 1',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='2'))) THEN ip.an END) AS 'ประเภท 2',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='3'))) THEN ip.an END) AS 'ประเภท 3',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='4'))) THEN ip.an END) AS 'ประเภท 4',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='5'))) THEN ip.an END) AS 'ประเภท 5',
            COUNT(CASE WHEN (ip.an not IN (select an from ipt_icnp)) THEN ip.an END) AS 'ไม่ระบุ'
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE (ip.dchdate BETWEEN ? AND ?) ";

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }

    public function ipClassYear($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT 
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='1'))) THEN ip.an END) AS 'ประเภท 1',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='2'))) THEN ip.an END) AS 'ประเภท 2',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='3'))) THEN ip.an END) AS 'ประเภท 3',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='4'))) THEN ip.an END) AS 'ประเภท 4',
            COUNT(CASE WHEN (ip.an IN (select an from ipt_icnp where (icnp_classification_id='5'))) THEN ip.an END) AS 'ประเภท 5',
            COUNT(CASE WHEN (ip.an not IN (select an from ipt_icnp)) THEN ip.an END) AS 'ไม่ระบุ'
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            WHERE (ip.dchdate BETWEEN ? AND ?) ";

        return $res->withJson(
            DB::select($sql, [$sdate, $edate])
        );
    }

    public function referInDay($req, $res, $args)
    {        
        $sql="SELECT CAST(HOUR(vsttime) AS SIGNED) AS hhmm,
            COUNT(DISTINCT vn) as num_pt
            FROM ovst
            WHERE (vn IN (SELECT vn FROM referin WHERE(refer_date=?)))
            GROUP BY CAST(HOUR(vsttime) AS SIGNED) 
            ORDER BY CAST(HOUR(vsttime) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }
    
    public function referOutDay($req, $res, $args)
    {
        $sql="SELECT CAST(HOUR(refer_time) AS SIGNED) AS hhmm,
            COUNT(DISTINCT vn) as num_pt
            FROM referout
            WHERE(refer_date=?)
            GROUP BY CAST(HOUR(refer_time) AS SIGNED) 
            ORDER BY CAST(HOUR(refer_time) AS SIGNED) ";

        return $res->withJson(
            DB::select($sql, [$args['date']])
        );
    }

    public function orVisitMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';
        
        $sql="SELECT CAST(DAY(o.begin_datetime) AS SIGNED) AS d,
            COUNT(DISTINCT CASE WHEN (o.operation_type_id=1) THEN o.operation_id END) as minor,
            COUNT(DISTINCT CASE WHEN (o.operation_type_id IN (2,3,5)) THEN o.operation_id END) as major,
            COUNT(DISTINCT CASE WHEN (operation_type_id NOT IN (1,2,3,5) OR o.operation_type_id IS NULL OR o.operation_type_id='') THEN o.operation_id END) as other
            FROM operation_detail o
            WHERE (DATE(o.begin_datetime) BETWEEN ? AND ?)
            GROUP BY CAST(DAY(o.begin_datetime) AS SIGNED) 
            ORDER BY CAST(DAY(o.begin_datetime) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }

    public function orTypeMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';
        
        $sql="SELECT
            COUNT(DISTINCT CASE WHEN(o.spclty='02') THEN o.operation_id END) AS 'SUR', #ศัลยกรรม
            COUNT(DISTINCT CASE WHEN(o.spclty='03') THEN o.operation_id END) AS 'OBS', #สูติกรรม
            COUNT(DISTINCT CASE WHEN(o.spclty='04') THEN o.operation_id END) AS 'GYN', #นรีเวชกรรม
            COUNT(DISTINCT CASE WHEN(o.spclty='06') THEN o.operation_id END) AS 'ENT', #โสต ศอ นาสิก
            COUNT(DISTINCT CASE WHEN(o.spclty='07') THEN o.operation_id END) AS 'EYE', #จักษุ
            COUNT(DISTINCT CASE WHEN(o.spclty='08') THEN o.operation_id END) AS 'ORTHO', #ออร์โธ
            COUNT(DISTINCT CASE WHEN(o.spclty='22') THEN o.operation_id END) AS 'NEURO', #ประสาท
            COUNT(DISTINCT CASE WHEN(o.spclty='11') THEN o.operation_id END) AS 'MAXILLO', #ทันตกรรม
            COUNT(DISTINCT CASE WHEN(o.spclty IS NULL OR o.spclty='' OR o.spclty NOT IN ('02','03','04','06','07','08','11','22')) THEN o.operation_id END) AS 'OTH' #ไม่ระบุ
            FROM operation_detail o 
            WHERE (DATE(o.begin_datetime) BETWEEN ? AND ?) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
}
