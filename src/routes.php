<?php

$app->get('/', 'HomeController:home')->setName('home');
$app->get('/users', 'UserController:index')->setName('list');

$app->get('/dashboard/op-visit/{date}', 'DashboardController:opVisit');
$app->get('/dashboard/op-visit-type/{date}', 'DashboardController:opVisitType');
$app->get('/dashboard/ip-visit/{date}', 'DashboardController:ipVisit');
$app->get('/dashboard/ip-class/{date}', 'DashboardController:ipClass');
$app->get('/dashboard/referin/{date}', 'DashboardController:referIn');
$app->get('/dashboard/referout/{date}', 'DashboardController:referOut');

$app->get('/dashboard/op-visit-month/{month}', 'DashboardController:opVisitMonth');
$app->get('/dashboard/op-visit-type-month/{month}', 'DashboardController:opVisitTypeMonth');
$app->get('/dashboard/ip-visit-month/{month}', 'DashboardController:ipVisitMonth');
$app->get('/dashboard/ip-class-month/{month}', 'DashboardController:ipClassMonth');
$app->get('/dashboard/referin-month/{month}', 'DashboardController:referInMonth');
$app->get('/dashboard/referout-month/{month}', 'DashboardController:referOutMonth');

$app->get('/ip/visit/{year}', 'IpController:ipvisit')->setName('ipvisit');
$app->get('/ip/classification/{year}', 'IpController:ipclassification')->setName('ipclassification');
$app->get('/ip/admdate/{sdate}/{edate}', 'IpController:admdate')->setName('admdate');

$app->get('/op/visit/{year}', 'OpController:opvisit')->setName('opvisit');
$app->get('/op/visit-type/{year}', 'OpController:opVisitType')->setName('opVisitType');

$app->get('/op/referin/{year}', 'OpController:referIn')->setName('referin');
$app->get('/op/referout/{year}', 'OpController:referOut')->setName('referout');

$app->get('/er/visit/{year}', 'ErController:ervisit')->setName('ervisit');
$app->get('/er/emergency/{year}', 'ErController:emergency')->setName('emergency');

$app->get('/or/visit/{year}', 'OrController:orvisit')->setName('orvisit');
$app->get('/or/or-type/{year}', 'OrController:orType')->setName('orType');
