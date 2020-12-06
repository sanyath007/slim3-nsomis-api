<?php

$app->get('/', 'HomeController:home')->setName('home');
$app->get('/users', 'UserController:index')->setName('list');

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
