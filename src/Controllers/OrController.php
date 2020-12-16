<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class OrController extends Controller
{
    public function orvisit($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
        $sql="SELECT CONCAT(YEAR(DATE(o.begin_datetime)),'-', MONTH(DATE(o.begin_datetime))) AS yearmonth, 
            COUNT(DISTINCT CASE WHEN (o.operation_type_id=1) THEN o.operation_id END) as minor,
            COUNT(DISTINCT CASE WHEN (o.operation_type_id IN (2,3,5)) THEN o.operation_id END) as major,
            COUNT(DISTINCT CASE WHEN (o.operation_type_id NOT IN (1,2,3,5) OR o.operation_type_id IS NULL OR o.operation_type_id='') THEN o.operation_id END) as other
            FROM operation_detail o
            WHERE (DATE(o.begin_datetime) BETWEEN ? AND ?)
            GROUP BY CONCAT(YEAR(DATE(o.begin_datetime)),'-', MONTH(DATE(o.begin_datetime))) ";

        return $res->withJson([
            'visit' => DB::select($sql, [$sdate, $edate]),
        ]);
    }

    public function orType($req, $res, $args)
    {
        $sdate = ($args['year'] - 1). '-10-01';
        $edate = $args['year']. '-09-30';
        
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

        return $res->withJson([
            'ortype' => DB::select($sql, [$sdate, $edate]),
        ]);
    }
    
    public function numDay($req, $res, $args)
    {        
        $sql="SELECT DATE(o.begin_datetime) as operation_date, 
				COUNT(DISTINCT o.operation_id) as num, 
				COUNT(DISTINCT CASE WHEN (o.operation_type_id=1) THEN o.operation_id END) as miner,
				COUNT(DISTINCT CASE WHEN (o.operation_type_id IN (2,3,5)) THEN o.operation_id END) as major,
				COUNT(DISTINCT CASE WHEN (operation_type_id NOT IN (1,2,3,5) OR o.operation_type_id IS NULL OR o.operation_type_id='') THEN o.operation_id END) as other,
				COUNT(DISTINCT CASE WHEN(o.spclty='02') THEN o.operation_id END) AS 'sur', #ศัลยกรรม
                COUNT(DISTINCT CASE WHEN(o.spclty='03') THEN o.operation_id END) AS 'obs', #สูติกรรม
                COUNT(DISTINCT CASE WHEN(o.spclty='04') THEN o.operation_id END) AS 'gyn', #นรีเวชกรรม
                COUNT(DISTINCT CASE WHEN(o.spclty='06') THEN o.operation_id END) AS 'ent', #โสต ศอ นาสิก
                COUNT(DISTINCT CASE WHEN(o.spclty='07') THEN o.operation_id END) AS 'eye', #จักษุ
                COUNT(DISTINCT CASE WHEN(o.spclty='08') THEN o.operation_id END) AS 'ort', #ออร์โธ
                COUNT(DISTINCT CASE WHEN(o.spclty='22') THEN o.operation_id END) AS 'neu', #ประสาท
                COUNT(DISTINCT CASE WHEN(o.spclty='11') THEN o.operation_id END) AS 'max', #ทันตกรรม
                COUNT(DISTINCT CASE WHEN(o.spclty NOT IN ('02','03','04','06','07','08','11','22') OR o.spclty IS NULL OR o.spclty='') THEN o.operation_id END) AS 'oth', #ไม่ระบุ
				COUNT(DISTINCT CASE WHEN (TIME(o.begin_datetime) BETWEEN '08:00:00' AND '12:00:00') THEN o.operation_id END) as morning,
				COUNT(DISTINCT CASE WHEN (TIME(o.begin_datetime) BETWEEN '12:00:01' AND '16:00:00') THEN o.operation_id END) as afternoon,
				COUNT(DISTINCT CASE WHEN (TIME(o.begin_datetime) BETWEEN '16:01:00' AND '23:59:59') THEN o.operation_id END) as evening,
				COUNT(DISTINCT CASE WHEN (TIME(o.begin_datetime) BETWEEN '00:00:01' AND '07:59:59') THEN o.operation_id END) as night
				FROM operation_detail o
				WHERE (DATE(o.begin_datetime) BETWEEN ? and ?)
				GROUP BY DATE(o.begin_datetime)";

        return $res->withJson([
            'numdays' => DB::select($sql, [$args['sdate'], $args['edate']]),
        ]);
    }
}
