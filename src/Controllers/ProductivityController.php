<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use Respect\Validation\Validator as v;
use App\Models\Productivity;
use App\Models\PeriodStaff;
use App\Models\Ward;

class ProductivityController extends Controller
{
    public function getSummary($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql = "SELECT ward, period, day(product_date) as product_day, productivity
                FROM productivities
                WHERE (product_date between ? and ?)
                order by ward, period ";
        
        $wards = [];
        $ws = Ward::whereNotIn('ward', ['03','04','13','14','15','17'])->orderBy('ward')->get(['ward', 'name']);
        foreach ($ws as $key => $value) {
            for($p = 1; $p <= 3; $p++){
                array_push($wards, [
                    'ward'  => $value->ward,
                    'name'  => $value->name,
                    'period'  => $p,
                ]);
            }
        }

        return $res->withJson([
            'product' => DB::connection('pharma')->select($sql, [$sdate, $edate]),
            'wards' => $wards,
        ]);
    }

    public function getProductWard($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT * from productivities 
                WHERE (product_date BETWEEN ? AND ?)
                AND (ward = ?) 
                ORDER BY product_date, period";

        return $res->withJson([
            'product' => DB::connection('pharma')->select($sql, [$sdate, $edate, $args['ward']]),
            'wards' => Ward::whereNotIn('ward', ['03','04','13','14','15','17'])->get(),
        ]);
    }

    public function getIpType($req, $res, $args)
    {
        $regtime = '';
        $dchtime = '';
        if($args['period'] == 1) {
            $regtime = '15:30:00';
            $dchtime = '08:30:00';
        } else if($args['period'] == 2) {
            $regtime = '23:59:59';
            $dchtime = '16:30:00';
        } else if($args['period'] == 3) {
            $regtime = '07:30:00';
            $dchtime = '00:30:00';
        }

        $sql = "SELECT ip.*,concat(p.pname,p.fname,' ',p.lname) as patient_name,
                p.birthday, w.name as ward_name
                FROM (
                    select i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime,i.ward,t.icnp_classification_id 
                    from ipt i
                    left join ipt_icnp t on (i.an=t.an)
                    where (
                        (i.regdate < '".$args['date']."')
                        or (i.regdate = '".$args['date']."' and i.regtime <= '".$regtime."')
                    )
                    AND (i.dchdate is null
                        or (i.dchdate > '".$args['date']."')
                        or (i.dchdate = '".$args['date']."' AND i.dchtime >= '".$dchtime."')
                    )
                    AND (i.ward = ?)
                ) AS ip 
                left join patient p on (ip.hn=p.hn)
                left join ward w on (ip.ward=w.ward)";

                if($args['type'] == '0') {
                    $sql .= "WHERE (ip.icnp_classification_id='' or ip.icnp_classification_id is null)";
                } else {
                    $sql .= "WHERE (ip.icnp_classification_id='".$args['type']."')";
                }

                $sql .= "ORDER BY ip.regdate ";

        return $res->withJson(DB::select($sql, [$args['ward']]));
    }

    public function getProductAdd($req, $res, $args)
    {
        return $res->withJson(Ward::whereNotIn('ward', ['03','04','13','14','15','17'])->get());
    }

    public function getWorkload($req, $res, $args)
    {
        $regtime = '';
        $dchtime = '';
        if($args['period'] == 1) {
            $regtime = '15:30:00';
            $dchtime = '08:30:00';
        } else if($args['period'] == 2) {
            $regtime = '23:59:59';
            $dchtime = '16:30:00';
        } else if($args['period'] == 3) {
            $regtime = '07:30:00';
            $dchtime = '00:30:00';
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
                        (i.regdate < '".$args['date']."')
                        or (i.regdate = '".$args['date']."' and i.regtime <= '".$regtime."')
                    )
                    AND (i.dchdate is null
                        or (i.dchdate > '".$args['date']."')
                        or (i.dchdate = '".$args['date']."' AND i.dchtime >= '".$dchtime."')
                    )
                    and (i.ward = ?)
                ) AS ip ";

        return $res->withJson([
            'workload' => collect(DB::select($sql, [$args['ward']]))->first(),
            'staff' => PeriodStaff::where(['ward' => $args['ward'], 'period' => $args['period']])->first(),
        ]);
    }

    public function store($req, $res, $args)
    {   
        // Check validation data
        $validation = $this->validator->validate($req, [
            'ward' => v::notEmpty(),
            'period' => v::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 1,
                        'errors' => $validation->getMessages(),
                        'message' => 'Validation Error!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }

        $post = (array)$req->getParsedBody();

        // Check duplicate data
        $chkProduct = Productivity::where([
                            'ward' => $post['ward'],
                            'period' => $post['period'],
                            'product_date' => $post['product_date'],
                        ])->get();

        if ($chkProduct->count() > 0) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 2,
                        'errors' => '',
                        'message' => 'Duplication Error!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }

        $product = new Productivity();
        $product->ward = $post['ward'];
        $product->period = $post['period'];
        $product->product_date = $post['product_date'];
        $product->total_patient = $post['total_patient'];
        $product->type1 = $post['type1'];
        $product->type2 = $post['type2'];
        $product->type3 = $post['type3'];
        $product->type4 = $post['type4'];
        $product->type5 = $post['type5'];
        $product->xtype1 = $post['xtype1'];
        $product->xtype2 = $post['xtype2'];
        $product->xtype3 = $post['xtype3'];
        $product->xtype4 = $post['xtype4'];
        $product->xtype5 = $post['xtype5'];
        $product->xtotal = $post['xtotal'];
        $product->rn = $post['rn'];
        $product->pn = $post['pn'];
        $product->total_staff = $post['total_staff'];
        $product->xstaff = $post['xstaff'];
        $product->productivity = $post['productivity'];
        $product->created_user = $post['user'];
        $product->updated_user = $post['user'];

        if($product->save()) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'errors' => '',
                        'message' => 'Insertion successfully',
                        'product' => $product
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}
