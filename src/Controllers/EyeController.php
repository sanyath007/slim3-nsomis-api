<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class EyeController extends Controller
{
    public function getVision2020($req, $res, $args)
    {
        $sql="select ov.vn, ov.vstdate, vs.pdx, ov.hn,
            concat(p.pname, p.fname, ' ', p.lname) as ptname, 
            p.cid, p.birthday, floor(datediff(now(), p.birthday)/365.5) as ageY,
            concat(p.addrpart, ' ม.', p.moopart, ' ', p.road, ' ', ta.full_name , ' ', p.po_code) as address,
            concat(ov.pttype, '-', pt.name) as pttype,
            o.nextdate, o.note, d.name as doctor,
            es.r01, es.rph, es.l01, es.lph
            from ovst ov
            left join vn_stat vs on (ov.vn=vs.vn)
            left join patient p on (ov.hn=p.hn)
            left join oapp o on (ov.vn=o.vn)
            left join doctor d on (o.doctor=d.code)
            left join eye_screen es on (ov.vn=es.vn)
            left join thaiaddress ta on (p.chwpart=ta.chwpart and p.amppart=ta.amppart and p.tmbpart=ta.tmbpart)
            left join pttype pt on (ov.pttype=pt.pttype)
            where (ov.vstdate between ? and ?)
            and (o.note like '%ต้อกระจก%')
            order by ov.hn";

        return $res->withJson([
            'vision' => DB::select($sql, [$args['sdate'], $args['edate']]),
        ]);
    }

    public function getFollowup($req, $res, $args)
    {
        $sql = "SELECT o.*, es.r01, es.rph, es.l01, es.lph
            FROM oapp o left join eye_screen es on (o.vn=es.vn) 
            WHERE (o.hn = ?) AND (o.vn > ?) 
            AND (o.clinic='007') ";

        if($args['isAdmit'] === 1) {
            $sql .= "AND (o.an IS NOT NULL) ";
        }    
            
        $sql .= "ORDER BY o.oapp_id ";
        
        return $res->withJson(DB::select($sql, [$args['hn'], $args['vn']]));
    }
}
