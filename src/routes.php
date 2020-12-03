<?php

$app->get('/', 'HomeController:home')->setName('home');
$app->get('/users', 'UserController:index')->setName('list');

$app->get('/ip/visit/{month}', 'IpController:ipvisit')->setName('ipvisit');
$app->get('/ip/classification/{month}', 'IpController:ipclassification')->setName('ipclassification');
$app->get('/ip/admdate', 'IpController:admdate')->setName('admdate');

$app->get('/op/visit/{month}', 'OpController:opvisit')->setName('opvisit');
$app->get('/op/visit-type/{month}', 'OpController:opVisitType')->setName('opVisitType');

$app->get('/op/referin/{month}', 'OpController:referIn')->setName('referin');
$app->get('/op/referout/{month}', 'OpController:referOut')->setName('referout');

$app->get('/er/visit/{month}', 'ErController:ervisit')->setName('ervisit');
$app->get('/er/emergency/{month}', 'ErController:emergency')->setName('emergency');

$app->get('/or/visit/{month}', 'OrController:orvisit')->setName('orvisit');
$app->get('/or/or-type/{month}', 'OrController:orType')->setName('orType');
