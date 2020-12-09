<?php

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
$app->get('/dashboard/ip-visit-year/{year}', 'DashboardController:ipVisitYear');
$app->get('/dashboard/ip-class-year/{year}', 'DashboardController:ipClassYear');

$app->get('/ip/class/{sdate}/{edate}', 'IpController:ipclass')->setName('ipclass');
$app->get('/ip/admdate/{sdate}/{edate}', 'IpController:admdate')->setName('admdate');

$app->get('/op/visit/{year}', 'OpController:opvisit')->setName('opvisit');
$app->get('/op/visit-type/{year}', 'OpController:opVisitType')->setName('opVisitType');

$app->get('/op/referin/{year}', 'OpController:referIn')->setName('referin');
$app->get('/op/referout/{year}', 'OpController:referOut')->setName('referout');

$app->get('/er/visit/{year}', 'ErController:ervisit')->setName('ervisit');
$app->get('/er/emergency/{year}', 'ErController:emergency')->setName('emergency');

$app->get('/or/visit/{year}', 'OrController:orvisit')->setName('orvisit');
$app->get('/or/or-type/{year}', 'OrController:orType')->setName('orType');
$app->get('/or/num-day/{sdate}/{edate}', 'OrController:numDay')->setName('orNumDay');

$app->group('/api', function(Slim\App $app) {
    $app->get('/users', 'UserController:index')->setName('userList');
    $app->get('/users/{loginname}', 'UserController:getUser')->setName('user');
});
