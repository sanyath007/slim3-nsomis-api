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
    private function getAvaibleWards()
    {
        return Ward::whereNotIn('ward', ['03','04','16','17'])->get();
    }

    private function getAvaibleWardsWithPeriod()
    {
        $wards = [];
        $ws = Ward::whereNotIn('ward', ['03','04','16','17','20'])->orderBy('ward')->get(['ward', 'name']);
        foreach ($ws as $key => $value) {
            for($p = 1; $p <= 3; $p++){
                array_push($wards, [
                    'ward'      => $value->ward,
                    'name'      => $value->name,
                    'period'    => $p,
                ]);
            }
        }

        return $wards;
    }

    public function getOverAll($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql = "select ward,
                count(case when(day(product_date) = 1) then period end) as t1,
                sum(case when(day(product_date) = 1) then productivity end) as p1,
                count(case when(day(product_date) = 2) then period end) as t2,
                sum(case when(day(product_date) = 2) then productivity end) as p2,
                count(case when(day(product_date) = 3) then period end) as t3,
                sum(case when(day(product_date) = 3) then productivity end) as p3,
                count(case when(day(product_date) = 4) then period end) as t4,
                sum(case when(day(product_date) = 4) then productivity end) as p4,
                count(case when(day(product_date) = 5) then period end) as t5,
                sum(case when(day(product_date) = 5) then productivity end) as p5,
                count(case when(day(product_date) = 6) then period end) as t6,
                sum(case when(day(product_date) = 6) then productivity end) as p6,
                count(case when(day(product_date) = 7) then period end) as t7,
                sum(case when(day(product_date) = 7) then productivity end) as p7,
                count(case when(day(product_date) = 8) then period end) as t8,
                sum(case when(day(product_date) = 8) then productivity end) as p8,
                count(case when(day(product_date) = 9) then period end) as t9,
                sum(case when(day(product_date) = 9) then productivity end) as p9,
                count(case when(day(product_date) = 10) then period end) as t10,
                sum(case when(day(product_date) = 10) then productivity end) as p10,
                count(case when(day(product_date) = 11) then period end) as t11,
                sum(case when(day(product_date) = 11) then productivity end) as p11,
                count(case when(day(product_date) = 12) then period end) as t12,
                sum(case when(day(product_date) = 12) then productivity end) as p12,
                count(case when(day(product_date) = 13) then period end) as t13,
                sum(case when(day(product_date) = 13) then productivity end) as p13,
                count(case when(day(product_date) = 14) then period end) as t14,
                sum(case when(day(product_date) = 14) then productivity end) as p14,
                count(case when(day(product_date) = 15) then period end) as t15,
                sum(case when(day(product_date) = 15) then productivity end) as p15,
                count(case when(day(product_date) = 16) then period end) as t16,
                sum(case when(day(product_date) = 16) then productivity end) as p16,
                count(case when(day(product_date) = 17) then period end) as t17,
                sum(case when(day(product_date) = 17) then productivity end) as p17,
                count(case when(day(product_date) = 18) then period end) as t18,
                sum(case when(day(product_date) = 18) then productivity end) as p18,
                count(case when(day(product_date) = 19) then period end) as t19,
                sum(case when(day(product_date) = 19) then productivity end) as p19,
                count(case when(day(product_date) = 20) then period end) as t20,
                sum(case when(day(product_date) = 20) then productivity end) as p20,
                count(case when(day(product_date) = 21) then period end) as t21,
                sum(case when(day(product_date) = 21) then productivity end) as p21,
                count(case when(day(product_date) = 22) then period end) as t22,
                sum(case when(day(product_date) = 22) then productivity end) as p22,
                count(case when(day(product_date) = 23) then period end) as t23,
                sum(case when(day(product_date) = 23) then productivity end) as p23,
                count(case when(day(product_date) = 24) then period end) as t24,
                sum(case when(day(product_date) = 24) then productivity end) as p24,
                count(case when(day(product_date) = 25) then period end) as t25,
                sum(case when(day(product_date) = 25) then productivity end) as p25,
                count(case when(day(product_date) = 26) then period end) as t26,
                sum(case when(day(product_date) = 26) then productivity end) as p26,
                count(case when(day(product_date) = 27) then period end) as t27,
                sum(case when(day(product_date) = 27) then productivity end) as p27,
                count(case when(day(product_date) = 28) then period end) as t28,
                sum(case when(day(product_date) = 28) then productivity end) as p28,
                count(case when(day(product_date) = 29) then period end) as t29,
                sum(case when(day(product_date) = 29) then productivity end) as p29,
                count(case when(day(product_date) = 30) then period end) as t30,
                sum(case when(day(product_date) = 30) then productivity end) as p30,
                count(case when(day(product_date) = 31) then period end) as t31,
                sum(case when(day(product_date) = 31) then productivity end) as p31
                from pharma_db.productivities
                where (product_date between ? and ?)
                group by ward
                order by ward";

        return $res->withJson([
            'product'   => DB::connection('pharma')->select($sql, [$sdate, $edate]),
            'wards'     => $this->getAvaibleWards(),
        ]);
    }

    public function getSummary($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql = "SELECT ward, period, day(product_date) as product_day, productivity
                FROM productivities
                WHERE (product_date between ? and ?)
                order by ward, period ";

        return $res->withJson([
            'product'   => DB::connection('pharma')->select($sql, [$sdate, $edate]),
            'wards'     => $this->getAvaibleWardsWithPeriod(),
        ]);
    }

    public function getInitForm($req, $res, $args)
    {
        return $res->withJson([
            'wards' => $this->getAvaibleWards(),
        ]);
    }

    public function getProductWard($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql="SELECT * from productivities 
                WHERE (product_date BETWEEN ? AND ?)
                AND (ward = ?) 
                ORDER BY product_date, period";

        return $res->withJson([
            'product' => DB::connection('pharma')->select($sql, [$sdate, $edate, $args['ward']])
        ]);
    }

    public function getIpType($req, $res, $args)
    {
        $regtime = '';
        $dchtime = '';
        if($args['period'] == 1) { // เวรดึก
            $regtime = '07:30:00';
            $dchtime = '00:30:00';
        } else if($args['period'] == 2) { // เวรเช้า
            $regtime = '15:30:00';
            $dchtime = '08:30:00';
        } else if($args['period'] == 3) { // เวรบ่าย
            $regtime = '23:59:59';
            $dchtime = '16:30:00';
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
                    )";

                    // Group วอร์ดที่อยู่ใช้ทรัพยากรร่วมกัน
                    if($args['ward'] == '01') {
                        $sql .= "and (i.ward in ('01'))";
                    } else if($args['ward'] == '02') {
                        $sql .= "and (i.ward in ('02','17'))";
                    } else if($args['ward'] == '08') {
                        $sql .= "and (i.ward in ('08'))";
                    } else {
                        $sql .= "and (i.ward = '".$args['ward']."')";
                    }

        $sql .= ") AS ip 
                left join patient p on (ip.hn=p.hn)
                left join ward w on (ip.ward=w.ward)";

                if($args['type'] == '0') {
                    $sql .= "WHERE (ip.icnp_classification_id='' or ip.icnp_classification_id is null)";
                } else {
                    $sql .= "WHERE (ip.icnp_classification_id='".$args['type']."')";
                }

                $sql .= "ORDER BY ip.regdate ";

        return $res->withJson(DB::select($sql));
    }

    public function getProductAdd($req, $res, $args)
    {
        return $res->withJson($this->getAvaibleWards());
    }

    public function getProduct($req, $res, $args)
    {
        return $res->withJson([
            'product'   => Productivity::find($args['id']),
            'wards'     => $this->getAvaibleWards()
        ]);
    }

    public function getWorkload($req, $res, $args)
    {
        $regtime = '';
        $dchtime = '';
        if($args['period'] == 1) { // เวรดึก
            $regtime = '07:30:00';
            $dchtime = '00:30:00';
        } else if($args['period'] == 2) {  // เวรเช้า
            $regtime = '15:30:00';
            $dchtime = '08:30:00';
        } else if($args['period'] == 3) {  // เวรบ่าย
            $regtime = '23:59:59';
            $dchtime = '16:30:00';
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
                    from ipt i left join ipt_icnp t on (i.an=t.an)
                    where (
                        (i.regdate < '".$args['date']."')
                        or (i.regdate = '".$args['date']."' and i.regtime <= '".$regtime."')
                    )
                    AND (i.dchdate is null
                        or (i.dchdate > '".$args['date']."')
                        or (i.dchdate = '".$args['date']."' AND i.dchtime >= '".$dchtime."')
                    )";
                    
                    // Group วอร์ดที่อยู่ใช้ทรัพยากรร่วมกัน
                    if($args['ward'] == '01') {
                        $sql .= "and (i.ward in ('01'))";
                    } else if($args['ward'] == '02') {
                        $sql .= "and (i.ward in ('02','17'))";
                    } else if($args['ward'] == '08') {
                        $sql .= "and (i.ward in ('08'))";
                    } else {
                        $sql .= "and (i.ward = '".$args['ward']."')";
                    }

                $sql .= ") AS ip ";

        return $res->withJson([
            'workload' => collect(DB::select($sql))->first(),
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

    
    public function update($req, $res, $args)
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

        $product = Productivity::find($args['id']);
        // $product->ward = $post['ward'];
        // $product->period = $post['period'];
        // $product->product_date = $post['product_date'];
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
        } else {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 1,
                        'errors' => '',
                        'message' => 'Error occur',
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }

    public function delete($req, $res, $args)
    {
        if(Productivity::find($args['id'])->delete()) {
            return $res->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'errors'    => '',
                        'message'   => 'Deletion successfully',
                        'id'        => $args['id']
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        } else {
            return $res->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode([
                'status'    => 1,
                'errors'    => '',
                'message'   => 'Error occur',
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}
