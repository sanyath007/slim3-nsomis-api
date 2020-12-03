<?php

namespace App\Controllers;

use App\Contoller;
use Illuminate\Database\Capsule\Manager as DB;

class OrController extends Controller
{
    public function orType($req, $res, $args)
    {
        $sql="SELECT
            COUNT(DISTINCT CASE WHEN (request_doctor IN ('0102','0065','0867','1073')) THEN operation_id END) as 'eye',
            COUNT(DISTINCT CASE WHEN (request_doctor IN ('0489','0371','0869','0570')) THEN operation_id END) as 'ออร์โธฯ',
            COUNT(DISTINCT CASE WHEN (request_doctor IN ('0120','0220','0568','0865')) THEN operation_id END) as 'C/S',
            COUNT(DISTINCT CASE WHEN (request_doctor NOT IN (
                                                        '0102','0065','0867','1073',
                                                        '0489','0371','0869','0570',
                                                        '0120','0220','0568','0865'
            )) THEN operation_id END) as 'อื่นๆ'
            FROM operation_list 
            WHERE (operation_date BETWEEN '2019-10-01' and '2020-09-30')
            AND (status_id=3) ";

        return $res->withJson([
            'ortype' => DB::select($sql),
        ]);
    }
    
    public function orDay($req, $res, $args)
    {
        $sql="SELECT operation_date, 
            COUNT(DISTINCT operation_id) as num, 
            COUNT(DISTINCT CASE WHEN (operation_type_id=1) THEN operation_id END) as small,
            COUNT(DISTINCT CASE WHEN (operation_type_id=3) THEN operation_id END) as large,
            COUNT(DISTINCT CASE WHEN (operation_type_id NOT IN (1,3) OR operation_type_id is null) THEN operation_id END) as other,
            COUNT(DISTINCT CASE WHEN (request_doctor NOT IN ('0489','0371','0869','0570','0102','0065','0867','1073')) THEN operation_id END) as gen,
            COUNT(DISTINCT CASE WHEN (request_doctor IN ('0102','0065','0867','1073')) THEN operation_id END) as eye,
            COUNT(DISTINCT CASE WHEN (request_doctor IN ('0489','0371','0869','0570')) THEN operation_id END) as orth,
            #COUNT(DISTINCT CASE WHEN (vn NOT IN (SELECT vn from ipt)) THEN operation_id END) as orth #OPD
            COUNT(DISTINCT CASE WHEN (leave_time BETWEEN '08:00:00' AND '12:00:00') THEN operation_id END) as morning,
            COUNT(DISTINCT CASE WHEN (leave_time BETWEEN '12:00:01' AND '16:00:00') THEN operation_id END) as afternoon,
            COUNT(DISTINCT CASE WHEN (leave_time BETWEEN '16:01:00' AND '23:59:59') THEN operation_id END) as evening,
            COUNT(DISTINCT CASE WHEN (leave_time BETWEEN '00:00:01' AND '07:59:59') THEN operation_id END) as night
            FROM operation_list 
            WHERE (operation_date BETWEEN '2019-10-01' and '2020-09-01')
            AND (status_id=3)
            GROUP BY operation_date ";

        return $res->withJson([
            'orday' => DB::select($sql),
        ]);
    }
    
    public function referIn($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM vn_stat
            WHERE (vn IN (SELECT vn FROM referin WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')))
            GROUP BY CONCAT(YEAR(vstdate),'-', MONTH(vstdate)) ";

        return $res->withJson([
            'referin' => DB::select($sql),
        ]);
    }
    
    public function referOut($req, $res, $args)
    {
        $sql="SELECT CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) AS yearmonth,
            COUNT(DISTINCT vn) as num_pt
            FROM referout
            WHERE(refer_date BETWEEN '2019-10-01' AND '2020-09-30')
            GROUP BY CONCAT(YEAR(refer_date),'-', MONTH(refer_date)) ";

        return $res->withJson([
            'referout' => DB::select($sql),
        ]);
    }
}
