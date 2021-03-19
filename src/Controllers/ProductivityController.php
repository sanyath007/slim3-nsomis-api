<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use Respect\Validation\Validator as v;
use App\Models\Productivity;
use App\Models\Ward;

class ProductivityController extends Controller
{
    public function getProductWard($req, $res, $args)
    {
        $sql="select * from productivities where (product_date <= ? and ward = ?)";

        return $res->withJson([
            'product' => DB::connection('pharma')->select($sql, [$args['date'], $args['ward']]),
            'wards' => Ward::where('ward', '<>', '03')->get(),
        ]);
    }
    
    public function getProductAdd($req, $res, $args)
    {
        return $res->withJson(Ward::where('ward', '<>', '03')->get());
    }

    public function getWorkload($req, $res, $args)
    {
        $period = '';
        $staff = null;
        if($args['period'] == 1) {
            $period = '16:00:00';
            $staff = [
                'rn' => 4,
                'pn' => 3,
                'total' => 7
            ];
        } else if($args['period'] == 2) {
            $period = '23:59:59';
            $staff = [
                'rn' => 4,
                'pn' => 3,
                'total' => 7
            ];
        } else if($args['period'] == 3) {
            $period = '07:59:59';
            $staff = [
                'rn' => 3,
                'pn' => 2,
                'total' => 5
            ];
        }
        
        $sql = "SELECT 
                COUNT(CASE WHEN (ip.icnp_classification_id='1') THEN ip.an END) AS type1,
                COUNT(CASE WHEN (ip.icnp_classification_id='2') THEN ip.an END) AS type2,
                COUNT(CASE WHEN (ip.icnp_classification_id='3') THEN ip.an END) AS type3,
                COUNT(CASE WHEN (ip.icnp_classification_id='4') THEN ip.an END) AS type4,
                COUNT(CASE WHEN (ip.icnp_classification_id='5') THEN ip.an END) AS type5,
                COUNT(CASE WHEN (ip.icnp_classification_id='' or ip.icnp_classification_id is null) THEN ip.an END) AS 'unknow',
                COUNT(ip.an) AS 'all'
                FROM (
                    select i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime,i.ward,t.icnp_classification_id 
                    from ipt i
                    left join ipt_icnp t on (i.an=t.an)
                    where (
                        (i.regdate < ? and i.dchdate is null)
                        or ((i.regdate = '".$args['date']."' and i.regtime <= '".$period."') and i.dchdate is null)
                        or (i.regdate <= '".$args['date']."' and (i.dchdate > '".$args['date']."'))
                        or (i.regdate <= '".$args['date']."' and (i.dchdate = '".$args['date']."' and i.dchtime > '".$period."') 
                        )
                    )
                    and (i.ward = ?)
                ) AS ip ";

        return $res->withJson([
            'workload' => collect(DB::select($sql, [$args['date'], $args['ward']]))->first(),
            'staff' => $staff
        ]);
    }

    public function getSummary($req, $res, $args)
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
                a.pttype,ptt.name as pttname,a.paid_money,a.uc_money,a.income,a.rcpt_money,
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

    public function store($req, $res, $args)
    {
        $validation = $this->validator->validate($req, [
            'ward' => v::notEmpty(),
            'period' => v::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'errors' => $validation->getMessages(),
                        'message' => 'Validation Error!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }

        $post = (array)$req->getParsedBody();

        $product = new Productivity();
        $product->ward = $post['ward'];
        $product->period = $post['period'];
        $product->product_date = $post['product_date'];
        $product->total_patient = $post['total_patient'];
        $product->t1 = $post['t1'];
        $product->t2 = $post['t2'];
        $product->t3 = $post['t3'];
        $product->t4 = $post['t4'];
        $product->t5 = $post['t5'];
        $product->tx10 = $post['tx10'];
        $product->tx35 = $post['tx35'];
        $product->tx55 = $post['tx55'];
        $product->tx75 = $post['tx75'];
        $product->tx120 = $post['tx120'];
        $product->txtotal = $post['txtotal'];
        $product->rn = $post['rn'];
        $product->pn = $post['pn'];
        $product->total_staff = $post['total_staff'];
        $product->staff_x7 = $post['staff_x7'];
        $product->productivity = $post['productivity'];
        $product->created_user = $post['user'];
        $product->updated_user = $post['user'];

        if($product->save()) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 1,
                        'errors' => '',
                        'message' => 'Insertion successfully',
                        'product' => $product
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}
