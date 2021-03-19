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
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql = "SELECT ward, period,
                case when (day(product_date) = '01') then productivity end as '1',
                case when (day(product_date) = '02') then productivity end as '2',
                case when (day(product_date) = '03') then productivity end as '3',
                case when (day(product_date) = '04') then productivity end as '4',
                case when (day(product_date) = '05') then productivity end as '5',
                case when (day(product_date) = '06') then productivity end as '6',
                case when (day(product_date) = '07') then productivity end as '7',
                case when (day(product_date) = '08') then productivity end as '8',
                case when (day(product_date) = '09') then productivity end as '9',
                case when (day(product_date) = '10') then productivity end as '10',
                case when (day(product_date) = '11') then productivity end as '11',
                case when (day(product_date) = '12') then productivity end as '12',
                case when (day(product_date) = '13') then productivity end as '13',
                case when (day(product_date) = '14') then productivity end as '14',
                case when (day(product_date) = '15') then productivity end as '15',
                case when (day(product_date) = '16') then productivity end as '16',
                case when (day(product_date) = '17') then productivity end as '17',
                case when (day(product_date) = '18') then productivity end as '18',
                case when (day(product_date) = '19') then productivity end as '19',
                case when (day(product_date) = '20') then productivity end as '20',
                case when (day(product_date) = '21') then productivity end as '21',
                case when (day(product_date) = '22') then productivity end as '22',
                case when (day(product_date) = '23') then productivity end as '23',
                case when (day(product_date) = '24') then productivity end as '24',
                case when (day(product_date) = '25') then productivity end as '25',
                case when (day(product_date) = '26') then productivity end as '26',
                case when (day(product_date) = '27') then productivity end as '27',
                case when (day(product_date) = '28') then productivity end as '28',
                case when (day(product_date) = '29') then productivity end as '29',
                case when (day(product_date) = '30') then productivity end as '30',
                case when (day(product_date) = '31') then productivity end as '31'
                FROM productivities
                WHERE (product_date between ? and ?)
                group by ward, period order by ward, period ";

        return $res->withJson([
            'product' => DB::connection('pharma')->select($sql, [$sdate, $edate]),
            'wards' => Ward::where('ward', '<>', '03')->get(),
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
