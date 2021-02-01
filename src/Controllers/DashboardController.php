<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class DashboardController extends Controller
{
    protected $clinics = [
        '01' => ['OPD ชั้น 1', " '003','004','010','021','035','057','062','111','112','114','117','118','124','125','142','063','110' "],
        '02' => ['OPD ชั้น 2', " '113','115','116','128','092','034','061','059','036', '060','129','130','131','143','144','145','037','050','091','093','099','100','056','071' "],
        '03' => ['ห้อง ER'," '011','070','135' "],
        '04' => ['คลินิก NCD/Asthma/COPD/Wafarin'," '001','002','065','066','043','067','064','132' "],		
        '05' => ['คลีนิก ARV', " '045' "],
        '06' => ['คลีนิก TB'," '055' "],
        '07' => ['ศูนย์ไตเทียม'," '058','120' "],
        '08' => ['OPD ตา'," '031' "],            
        '09' => ['ห้องคลอด'," '017' "],
        '10' => ['ห้องผ่าตัด'," '014' "],
        '11' => ['ห้องส่องกล้อง',"'097'"],
        '12' => ['OPD นอกเวลา'," '133' "],
        '13' => ['ER-POOL'," '134'"],
    ];

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

    public function errorOpDay($req, $res, $args)
    {
        // Check if current month, set sdate to yesterday
        // if (date('Y-m') == date('Y-m', strtotime($args['day']))) {
        //     $sdate = date('Y-m-d', strtotime("-1 days", strtotime($args['day'])));
        // } else {
            $sdate = $args['day'];
        // }
        
        $data = [];
        foreach($this->clinics as $key => $cl) {
            $sql="SELECT 
                COUNT(case when (vn.pdx='' or vn.pdx is null) then vn.vn END) as nodx,
                COUNT(case when (vn.vn in (select vn from opdscreen where (cc is null and pe is null and bpd is null and bps is null and pulse is null 	and temperature is null and rr is null))) then vn.vn end) as noscreen,
                COUNT(case when ((vn.age_y > 3) 
                    and (vn.vn in (select vn from opdscreen)) 
                    and (vn.vn in (select vn from opdscreen where (pulse is null or temperature is null or bpd is null or bps is null or rr is null)))
                    and (vn.vn not in (select vn from opdscreen where (cc LIKE '%รับยาแทน%')))) 
                then vn.vn end) as inc_screen
                FROM hos2.vn_stat vn 
                LEFT JOIN hos2.ovst vs ON (vn.vn=vs.vn)
                WHERE (vn.vstdate=?) 
                AND (vs.main_dep IN (" .$cl[1]. "))
                AND (vs.an is null or vs.an='')";

            $errorData = DB::select($sql, [$sdate]);
            array_push($data, [
                'id' => $key,
                'name' => $cl[0], 
                'nodx' => $errorData[0]->nodx,
                'noscreen' => $errorData[0]->noscreen,
                'inc_screen' => $errorData[0]->inc_screen,
            ]);
        }

        return $res->withJson($data);
    }
    
    public function errorIpDay($req, $res, $args)
    {
        //sdate equal to the day of started day of month of edate
        $sdate =  date('Y-m', strtotime("-1 days", strtotime($args['day']))). '-01';
        //Check when case that was discharged for 7 day ago
        $edate = date('Y-m-d', strtotime("-1 days", strtotime($args['day'])));

        $sql="SELECT ip.ward, w.name,
            COUNT(case when (DATEDIFF(now(), ip.dchdate) < 7) then ip.an end) AS less7,
            COUNT(case when (DATEDIFF(now(), ip.dchdate) between 7 and 14) then ip.an end) AS gr7to14,
            COUNT(case when (DATEDIFF(now(), ip.dchdate) between 15 and 21) then ip.an end) AS gr15to21,
            COUNT(case when (DATEDIFF(now(), ip.dchdate) > 21) then ip.an end) AS gr21
            FROM hos2.ipt ip
            LEFT JOIN hos2.ward w ON (ip.ward=w.ward)
            LEFT JOIN hos2.an_stat a ON (ip.an=a.an)
            WHERE (ip.dchdate between ? and ?)
            and (ip.chart_state=1)
            GROUP BY ip.ward, w.name ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }

    public function errorOpMonth($req, $res, $args)
    {
        $sdate = $args['month']. '-01';

        // Check if current month
        if (date('Y-m') == $args['month']) {
            $edate = (date('d') == 1) ? $args['month']. '-01' : $args['month']. '-' .(date('d')-1);
        } else {
            $edate = $args['month']. '-31';
        }
        
        $data = [];
        foreach($this->clinics as $key => $cl) {
            $sql="SELECT 
                COUNT(case when (vn.pdx='' or vn.pdx is null) then vn.vn END) as nodx,
                COUNT(case when (vn.vn in (select vn from opdscreen where (cc is null and pe is null and bpd is null and bps is null and pulse is null 	and temperature is null and rr is null))) then vn.vn end) as noscreen,
                COUNT(case when ((vn.age_y > 3) 
                    and (vn.vn in (select vn from opdscreen)) 
                    and (vn.vn in (select vn from opdscreen where (pulse is null or temperature is null or bpd is null or bps is null or rr is null)))
                    and (vn.vn not in (select vn from opdscreen where (cc LIKE '%รับยาแทน%')))) 
                then vn.vn end) as inc_screen
                FROM hos2.vn_stat vn 
                LEFT JOIN hos2.ovst vs ON (vn.vn=vs.vn)
                WHERE (vn.vstdate BETWEEN ? AND ?) 
                AND (vs.main_dep IN (" .$cl[1]. "))
                AND (vs.an is null or vs.an='')";

            $errorData = DB::select($sql, [$sdate, $edate]);
            array_push($data, [
                'id' => $key,
                'name' => $cl[0], 
                'nodx' => $errorData[0]->nodx,
                'noscreen' => $errorData[0]->noscreen,
                'inc_screen' => $errorData[0]->inc_screen,
            ]);
        }

        return $res->withJson($data);
    }
    
    public function errorIpMonth($req, $res, $args)
    {
        //sdate equal to the day of started budget year
        $sdate = $args['month']. '-01';
        //Check when case that was discharged for 7 day ago
        if (date('d') > 7) {
            $edate = date('Y-m-d', strtotime("-7 days"));
        } else {
            $edate = $args['month']. '-' .date('d');
        }

        $sql="SELECT ip.ward, w.name,
            COUNT(ip.an) as total,
            COUNT(case when (ip.chart_state=2) then ip.an end) AS send,
            COUNT(case when (ip.chart_state=1) then ip.an end) AS notsend
            FROM ipt ip
            LEFT JOIN ward w ON (ip.ward=w.ward)
            LEFT JOIN an_stat a ON (ip.an=a.an)				
            WHERE (ip.dchdate BETWEEN ? AND ?)
            GROUP BY ip.ward, w.name ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
}
