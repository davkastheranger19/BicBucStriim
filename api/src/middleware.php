<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

require_once 'BicBucStriim/own_config_middleware.php';
require_once 'BicBucStriim/calibre_config_middleware.php';
require_once 'BicBucStriim/negotiation_middleware.php';

$app->add(new \BicBucStriim\CalibreConfigMiddleware($app->getContainer()->logger, $app->getContainer()->calibre, $app->getContainer()->config));
$app->add('HttpBasicAuthentication');
$app->add('JwtAuthentication');
$app->add(new \BicBucStriim\OwnConfigMiddleware($app->getContainer()->logger, $app->getContainer()->bbs, $app->getContainer()->config));
// NOTE supply argument trusted proxies, if necessary?
$app->add(new \RKA\Middleware\SchemeAndHost());
// NOTE only JSON and OPDS requests will be accepted
$app->add(new \BicBucStriim\NegotiationMiddleware($app->getContainer(), $app->getContainer()->settings['bbs']['langs']));
