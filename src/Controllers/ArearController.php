<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\ArrearPaid;

class ArearController extends Controller
{
    public function getIpArears($req, $res, $args)
    {
        $sql="select a.an,a.hn,a.regdate,a.dchdate,
            concat(convert(pat.pname,char(5)),convert(pat.fname,char(20)),space(2),convert(pat.lname,char(20))) as patname,
            a.pttype,ptt.name as pttname,a.paid_money,a.uc_money, a.income,
            concat(a.ward,'-',w.name) as ward, a.admdate 
            from an_stat a 
            left join patient pat on (a.hn=pat.hn) 
            left join pttype ptt on (a.pttype=ptt.pttype) 
            left join ward w on (a.ward=w.ward) 
            where (a.dchdate between ? and ?)
            and (a.pttype in ('10','20','21','42','45','46'))
            and (a.an not in (select vn from rcpt_print)) 
            order by a.ward,a.dchdate,a.pttype,a.an";

        return $res->withJson([
            'ips' => DB::select($sql, [$args['sdate'], $args['edate']]),
        ]);
    }
    
    public function getOpArears($req, $res, $args)
    {
        $sql="select a.vn,a.hn,a.vstdate,v.vsttime,pt.cid,
            concat(convert(pt.pname,char(5)),convert(pt.fname,char(20)),space(2),convert(pt.lname,char(20))) as patname,
            a.pttype,ptt.name as pttname,a.paid_money,a.uc_money,a.income,a.rcpt_money
            from vn_stat a 
            left join ovst v on (a.vn=v.vn) 
            left join patient pt on (a.hn=pt.hn) 
            left join pttype ptt on (a.pttype=ptt.pttype)
            where (a.vstdate between ? and ?)
            and (a.pttype in ('10','20','21','42','45','46'))
            and (a.vn not in (select vn from ipt))
            and (a.vn not in (select vn from rcpt_print)) 
            order by a.vn ";

        return $res->withJson([
            'ops' => DB::select($sql, [$args['sdate'], $args['edate']]),
        ]);
    }

    public function getPaymentArears($req, $res, $args)
    {
        if($args['type'] === 'op') {
            $visit = "select a.vn,a.hn,a.vstdate,v.vsttime,pt.cid,
                concat(convert(pt.pname,char(5)),convert(pt.fname,char(20)),space(2),convert(pt.lname,char(20))) as patname,
                a.pttype,ptt.name as pttname,a.paid_money,a.uc_money,a.income,a.rcpt_money 
                from vn_stat a 
                left join ovst v on (a.vn=v.vn)
                left join patient pt on (a.hn=pt.hn) 
                left join pttype ptt on (a.pttype=ptt.pttype) 
                where (a.vn = ? and a.hn = ?)";
        } else {
            $visit = "select a.an,a.hn,a.regdate,a.dchdate,pt.cid,
                concat(convert(pt.pname,char(5)),convert(pt.fname,char(20)),space(2),convert(pt.lname,char(20))) as patname,
                a.pttype,ptt.name as pttname,a.paid_money,a.uc_money,a.income, a.rcpt_money,
                concat(a.ward,'-',w.name) as ward, a.admdate 
                from an_stat a 
                left join patient pt on (a.hn=pt.hn) 
                left join pttype ptt on (a.pttype=ptt.pttype) 
                left join ward w on (a.ward=w.ward) 
                where (a.an = ? and a.hn = ?)";
        }

        $paid = "select * from arrear_paid where (an = ?) and (hn = ?) order by paid_date ";
        $notes = "select n.*, u.name as staff_name from ptnote n 
                left join opduser u on (n.note_staff=u.loginname)
                where (hn = ?) order by note_datetime desc limit 0, 5";
        $items = "";

        return $res->withJson([
            'visit' => collect(DB::select($visit, [$args['vn'], $args['hn']]))->first(),
            'paid' => DB::connection('arrear')->select($paid, [$args['vn'], $args['hn']]),
            'notes' => DB::select($notes, [$args['hn']]),
            'items' => '',
        ]);
    }

    public function storeArrear($req, $res, $args)
    {
        $post = (array)$req->getParsedBody();

        $paid = new ArrearPaid();
        $paid->vn = $post['vn'];
        $paid->an = $post['an'];
        $paid->hn = $post['hn'];
        $paid->paid_no = $post['paid_no'];
        $paid->bill_no = $post['bill_no'];
        $paid->paid_date = $post['paid_date'];
        $paid->paid_time = $post['paid_time'];
        $paid->paid_amount = $post['paid_amount'];
        $paid->remain = $post['remain'];
        $paid->cashier = $post['cashier'];
        $paid->remark = $post['remark'];

        if($paid->save()) {
            return $res->withJson([
                'paid' => $paid
            ]);
        }
    }
}
