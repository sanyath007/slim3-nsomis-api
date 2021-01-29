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

$app->get('/ip/class/{sdate}/{edate}', 'IpController:ipclass');
$app->get('/ip/admdate/{sdate}/{edate}', 'IpController:admdate');
$app->get('/ip/ptdchbyward/{sdate}/{edate}/{ward}', 'IpController:ptDchByWard');
$app->get('/ip/ptlosbycare/{sdate}/{edate}/{ward}', 'IpController:ptLosByCare');

$app->get('/op/visit/{year}', 'OpController:opvisit');
$app->get('/op/visit-type/{year}', 'OpController:opVisitType');

$app->get('/er/visit/{year}', 'ErController:ervisit');
$app->get('/er/emergency/{year}', 'ErController:emergency');
$app->get('/er/sum-period/{sdate}/{edate}', 'ErController:sumPeriod');

$app->get('/or/visit/{year}', 'OrController:orvisit');
$app->get('/or/or-type/{year}', 'OrController:orType');
$app->get('/or/num-day/{sdate}/{edate}', 'OrController:numDay');
$app->get('/or/emergency-year/{year}', 'OrController:getEmergencyYear');
$app->get('/or/expenses/{sdate}/{edate}', 'OrController:expenses');
$app->get('/or/expenses/{income}/{sdate}/{edate}', 'OrController:expensesDetail');

$app->get('/refer/referin-year/{year}', 'ReferController:referInYear');
$app->get('/refer/referout-year/{year}', 'ReferController:referOutYear');
$app->get('/refer/referin-month/{month}', 'ReferController:referInMonth');
$app->get('/refer/referout-month/{month}', 'ReferController:referOutMonth');

$app->get('/error/chart-send/{sdate}/{edate}', 'ErrorDataController:chartSend');

$app->get('/pharma/op/{listId}/{sdate}/{edate}', 'PharmaController:opMonth');
$app->get('/pharma/ip/{listId}/{sdate}/{edate}', 'PharmaController:ipMonth');
$app->get('/pharma/user-drug-list/{user}', 'PharmaController:getUserDrugList');
$app->get('/pharma/user-drug-list/{id}/detail', 'PharmaController:getUserDrugListDetail');
$app->post('/pharma/store-drug-list', 'PharmaController:storeUserDrugList');
$app->get('/pharma/store-drug-list-file', 'PharmaController:storeUserDrugListFile');

$app->get('/drug-items', 'DrugItemController:getAll');

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
