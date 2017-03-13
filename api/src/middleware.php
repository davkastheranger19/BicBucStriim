<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

require_once 'BicBucStriim/own_config_middleware.php';
require_once 'BicBucStriim/calibre_config_middleware.php';

use Gofabian\Negotiation\NegotiationMiddleware;

$app->add(new \BicBucStriim\CalibreConfigMiddleware($app->getContainer()->logger, $app->getContainer()->calibre, $app->getContainer()->config));
$app->add('HttpBasicAuthentication');
$app->add('JwtAuthentication');
$app->add(new \BicBucStriim\OwnConfigMiddleware($app->getContainer()->logger, $app->getContainer()->bbs, $app->getContainer()->config));
// only JSON requests will be accepted
$app->add(new NegotiationMiddleware([
    'accept' => ['application/json']
]));
