<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \BicBucStriim\CalibreConfigMiddleware($app->getContainer()->logger, $app->getContainer()->calibre, $app->getContainer()->config));
$app->add('HttpBasicAuthentication');
$app->add('JwtAuthentication');
$app->add(new \BicBucStriim\OwnConfigMiddleware($app->getContainer()->logger, $app->getContainer()->bbs, $app->getContainer()->config));
// NOTE supply argument trusted proxies, if necessary?
$app->add(new \RKA\Middleware\SchemeAndHost());
// NOTE only JSON and OPDS requests will be accepted
$app->add(new \BicBucStriim\NegotiationMiddleware($app->getContainer(), $app->getContainer()->settings['bbs']['langs']));
