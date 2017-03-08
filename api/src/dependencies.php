<?php
require 'BicBucStriim/bicbucstriim.php';
require 'BicBucStriim/app_constants.php';
require 'BicBucStriim/mailer.php';

use BicBucStriim\BicBucStriim;
use BicBucStriim\Mailer;
use BicBucStriim\AppConstants;

// DIC configuration
$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// bicbucstriim
$container['bbs'] = function ($c) {
    $settings = $c->get('settings')['bbs'];
    $bbs = new BicBucStriim($settings['dataDb'], true);
    if (!$bbs->dbOk()) {
        $bbs->createDataDb($settings['dataDb']);
        $bbs = new BicBucStriim($settings['dataDb']);
    }
    return $bbs;
};

$container['config'] = function ($c) {
    $configs = array(
        AppConstants::CALIBRE_DIR => '',
        AppConstants::DB_VERSION => AppConstants::DB_SCHEMA_VERSION,
        AppConstants::KINDLE => 0,
        AppConstants::KINDLE_FROM_EMAIL => '',
        AppConstants::THUMB_GEN_CLIPPED => 1,
        AppConstants::PAGE_SIZE => 30,
        AppConstants::DISPLAY_APP_NAME => 'BicBucStriim',
        AppConstants::MAILER => Mailer::MAIL,
        AppConstants::SMTP_USER => '',
        AppConstants::SMTP_PASSWORD => '',
        AppConstants::SMTP_SERVER => '',
        AppConstants::SMTP_PORT => 25,
        AppConstants::SMTP_ENCRYPTION => 0,
        AppConstants::METADATA_UPDATE => 0,
        AppConstants::LOGIN_REQUIRED => 1,
        AppConstants::TITLE_TIME_SORT => AppConstants::TITLE_TIME_SORT_TIMESTAMP,
        AppConstants::RELATIVE_URLS => 1,
    );
    return $configs;
};

