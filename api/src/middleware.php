<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

require_once 'BicBucStriim/own_config_middleware.php';
require_once 'BicBucStriim/calibre_config_middleware.php';

$app->add(new \BicBucStriim\CalibreConfigMiddleware($app->getContainer()->logger, $app->getContainer()->calibre, $app->getContainer()->config));
$app->add(new \BicBucStriim\OwnConfigMiddleware($app->getContainer()->logger, $app->getContainer()->bbs, $app->getContainer()->config));
