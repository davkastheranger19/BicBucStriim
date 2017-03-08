<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

require 'BicBucStriim/own_config_middleware.php';
require 'BicBucStriim/calibre_config_middleware.php';

$app->add(new CalibreConfigMiddleware());
$app->add(new \BicBucStriim\OwnConfigMiddleware());