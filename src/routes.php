<?php

/**
 * ================== Use CORS middleware ==================
 */
$app->options('/{routes:.+}', function($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
/**
 * ================== Use CORS middleware ==================
 */

$app->get('/', 'HomeController:home')->setName('home');

$app->post('/login', 'LoginController:login')->setName('login');

$app->get('/dashboard/op-visit/{date}', 'DashboardController:opVisitDay');
$app->get('/dashboard/op-visit-type/{date}', 'DashboardController:opVisitTypeDay');
$app->get('/dashboard/ip-visit/{date}', 'DashboardController:ipVisitDay');
$app->get('/dashboard/ip-class/{date}', 'DashboardController:ipClassDay');
$app->get('/dashboard/referin/{date}', 'DashboardController:referInDay');
$app->get('/dashboard/referout/{date}', 'DashboardController:referOutDay');
$app->get('/dashboard/op-visit-month/{month}', 'DashboardController:opVisitMonth');
$app->get('/dashboard/op-visit-type-month/{month}', 'DashboardController:opVisitTypeMonth');
$app->get('/dashboard/ip-visit-month/{month}', 'DashboardController:ipVisitMonth');
$app->get('/dashboard/ip-class-month/{month}', 'DashboardController:ipClassMonth');
$app->get('/dashboard/referin-month/{month}', 'DashboardController:referInMonth');
$app->get('/dashboard/referout-month/{month}', 'DashboardController:referOutMonth');
$app->get('/dashboard/or-visit/{month}', 'DashboardController:orVisitMonth');
$app->get('/dashboard/or-type/{month}', 'DashboardController:orTypeMonth');
$app->get('/dashboard/error-op-day/{day}', 'DashboardController:errorOpDay');
$app->get('/dashboard/error-ip-day/{day}', 'DashboardController:errorIpDay');
$app->get('/dashboard/error-op-month/{month}', 'DashboardController:errorOpMonth');
$app->get('/dashboard/error-ip-month/{month}', 'DashboardController:errorIpMonth');
$app->get('/dashboard/ip-visit-year/{year}', 'DashboardController:ipVisitYear');
$app->get('/dashboard/ip-class-year/{year}', 'DashboardController:ipClassYear');

$app->get('/ip/class/{date}', 'IpController:ipclassDay');
$app->get('/ip/class/{sdate}/{edate}', 'IpController:ipclass');
$app->get('/ip/admdc-day/{date}', 'IpController:getAdmDcDay');
$app->get('/ip/admdc-month/{month}', 'IpController:getAdmDcMonth');
$app->get('/ip/admdate-month/{month}', 'IpController:getAdmdateMonth');
$app->get('/ip/bedocc-year/{year}', 'IpController:getBedoccYear');
$app->get('/ip/bedocc-month/{month}', 'IpController:getBedoccMonth');
$app->get('/ip/bedempty-day/{date}', 'IpController:getBedEmptyDay');
$app->get('/ip/ptdchbyward/{sdate}/{edate}/{ward}', 'IpController:ptDchByWard');
$app->get('/ip/ptlosbycare/{sdate}/{edate}/{ward}', 'IpController:ptLosByCare');
$app->get('/ip/ip-lists/{date}/{ward}', 'IpController:getIpList');

$app->get('/products/init-form', 'ProductivityController:getInitForm');
$app->get('/product-ward/{month}/{ward}', 'ProductivityController:getProductWard');
$app->get('/product-add', 'ProductivityController:getProductAdd');
$app->get('/product-workload/{date}/{period}/{ward}', 'ProductivityController:getWorkload');
$app->post('/product', 'ProductivityController:store');
$app->get('/product/{id}', 'ProductivityController:getProduct');
$app->put('/product/{id}', 'ProductivityController:update');
$app->delete('/product/{id}', 'ProductivityController:delete');
$app->get('/product-sum/{month}', 'ProductivityController:getSummary');
$app->get('/ip-type/{date}/{period}/{ward}/{type}', 'ProductivityController:getIpType');

$app->get('/op/visit/{year}', 'OpController:opvisit');
$app->get('/op/visit-type/{year}', 'OpController:opVisitType');

$app->get('/er/visit/{month}/month', 'ErController:getVisitMonth');
$app->get('/er/emergency/{month}/month', 'ErController:getEmergencyMonth');
$app->get('/er/visit/{year}', 'ErController:getVisitYear');
$app->get('/er/emergency/{year}', 'ErController:getEmergencyYear');
$app->get('/er/sum-period/{sdate}/{edate}', 'ErController:sumPeriod');

$app->get('/or/visit/{year}', 'OrController:orvisit');
$app->get('/or/or-type/{year}', 'OrController:orType');
$app->get('/or/sum-year/{year}', 'OrController:getSumYear');
$app->get('/or/emergency-year/{year}', 'OrController:getEmergencyYear');
$app->get('/or/expenses/{sdate}/{edate}', 'OrController:expenses');
$app->get('/or/expenses/{income}/{sdate}/{edate}', 'OrController:expensesDetail');
$app->get('/or/cataract-list/{sdate}/{edate}', 'OrController:getOrCataractList');

$app->get('/scope/sum-year/{year}', 'ScopeController:getSumYear');

$app->get('/refer/referin-year/{year}', 'ReferController:referInYear');
$app->get('/refer/referout-year/{year}', 'ReferController:referOutYear');
$app->get('/refer/referin-month/{month}', 'ReferController:referInMonth');
$app->get('/refer/referout-month/{month}', 'ReferController:referOutMonth');

$app->get('/eye/visio2020/{sdate}/{edate}', 'EyeController:getVision2020');
$app->get('/eye/visio2020/followup/{hn}/{vn}/{isAdmit}', 'EyeController:getFollowup');

$app->get('/error/chart-send/{sdate}/{edate}', 'ErrorDataController:chartSend');
$app->get('/error/chart-sent-list/{sdate}/{edate}/{status}/{ward}', 'ErrorDataController:getChartSentList');

$app->get('/pharma/op/{listId}/{sdate}/{edate}', 'PharmaController:opMonth');
$app->get('/pharma/ip/{listId}/{sdate}/{edate}', 'PharmaController:ipMonth');
$app->get('/pharma/user-drug-list/{user}', 'PharmaController:getUserDrugList');
$app->get('/pharma/user-drug-list/{id}/detail', 'PharmaController:getUserDrugListDetail');
$app->post('/pharma/store-drug-list', 'PharmaController:storeUserDrugList');
$app->get('/pharma/store-drug-list-file', 'PharmaController:storeUserDrugListFile');
$app->delete('/pharma/user-drug-list/{id}', 'PharmaController:removeUserDrugList');

$app->get('/drug-items', 'DrugItemController:getAll');

$app->get('/nurses', 'NurseController:getAll');
$app->get('/nurses/init/form', 'NurseController:getInitForm');
$app->get('/nurses/{id}/profile', 'NurseController:getProfile');
$app->get('/nurses/gen-list', 'NurseController:getGenList');
$app->get('/nurses-update', 'NurseController:updateDB');
$app->post('/nurses', 'NurseController:store');
$app->put('/nurses/{id}', 'NurseController:update');
$app->delete('/nurses/{id}', 'NurseController:delete');
$app->put('/nurses/{id}/move', 'NurseController:move');
$app->put('/nurses/{id}/transfer', 'NurseController:transfer');
$app->put('/nurses/{id}/leave', 'NurseController:leave');
$app->put('/nurses/{id}/unknown', 'NurseController:unknown');
$app->get('/nurses/card-stat', 'NurseController:getCardStat');
$app->get('/nurses/stat/depart', 'NurseController:getNumByDepart');

$app->get('/supports', 'SupportController:getAll');
$app->get('/supports/init/form', 'SupportController:getInitForm');
$app->get('/supports/{id}/profile', 'SupportController:getProfile');
$app->get('/supports/gen-list', 'SupportController:getGenList');
$app->get('/supports-update', 'SupportController:updateDB');
$app->post('/supports', 'SupportController:store');
$app->put('/supports/{id}', 'SupportController:update');
$app->delete('/supports/{id}', 'SupportController:delete');
$app->put('/supports/{id}/move', 'SupportController:move');
$app->put('/supports/{id}/transfer', 'SupportController:transfer');
$app->put('/supports/{id}/leave', 'SupportController:leave');
$app->put('/supports/{id}/unknown', 'SupportController:unknown');

$app->get('/moves/{personId}', 'MoveController:getAll');
$app->get('/moves/{personId}/init/form', 'MoveController:getInitForm');
$app->get('/moves/{personId}/{id}', 'MoveController:getById');
$app->post('/moves', 'MoveController:store');
$app->put('/moves/{id}', 'MoveController:update');
$app->delete('/moves/{id}', 'MoveController:delete');

$app->get('/in-positions', 'PersonPositionController:getAll');
$app->get('/in-positions/init/form', 'PersonPositionController:getInitForm');
$app->get('/in-positions/{id}', 'PersonPositionController:getById');
$app->post('/in-positions', 'PersonPositionController:store');
$app->put('/in-positions/{id}', 'PersonPositionController:update');
$app->delete('/in-positions/{id}', 'PersonPositionController:delete');

$app->get('/arrears-op/{sdate}/{edate}', 'ArrearController:getOpArears');
$app->get('/arrears-ip/{sdate}/{edate}', 'ArrearController:getIpArears');
$app->get('/arrears-payment/{type}/{vn}/{hn}', 'ArrearController:getPaymentArears');
$app->post('/arrears-payment/{vn}/{hn}', 'ArrearController:storeArrear');
$app->get('/arrears-paid/{type}/{vn}/{hn}', 'ArrearController:getArrearPaid');

$app->get('/covid/num-tambon/{date}', 'CovidController:getNumTambon');
$app->get('/covid/{tambon}/tambon', 'CovidController:getPatientsTambon');
$app->get('/covid/discharge/{tambon}/tambon', 'CovidController:getDischargesTambon');
$app->get('/covid/num-bed', 'CovidController:getNumBed');
$app->get('/covid/card-stat', 'CovidController:getCardStat');
$app->get('/covid/{ward}/ward', 'CovidController:getPatientsward');
$app->get('/covid/{type}/all', 'CovidController:getPatientsAll');
$app->get('/covid/register/{month}/month', 'CovidController:getRegMonth');
$app->get('/covid/register/ward/{month}/month', 'CovidController:getRegWardMonth');
$app->get('/covid/register/{week}/epi-week', 'CovidController:getRegWeek');

$app->get('/persons', 'PersonController:getAll');

$app->group('/api', function(Slim\App $app) { 
    $app->get('/users', 'UserController:index');
    $app->get('/users/{loginname}', 'UserController:getUser');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
