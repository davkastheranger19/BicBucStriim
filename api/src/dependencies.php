<?php
require_once 'BicBucStriim/utilities.php';
use Slim\Middleware\JwtAuthentication;
use Slim\Middleware\HttpBasicAuthentication;
use \RKA\Middleware\SchemeAndHost;
use \Aura\Accept\AcceptFactory;

use BicBucStriim\BicBucStriim;
use BicBucStriim\Calibre;
use BicBucStriim\Mailer;
use BicBucStriim\AppConstants;
use BicBucStriim\Token;

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
    $logger = $c->get('logger');
    $dbd = $settings['dataDb'];
    $logger->debug('using bbs db '.$dbd);
    $bbs = new BicBucStriim($dbd, true);
    if (!$bbs->dbOk()) {
        $bbs->createDataDb($settings['dataDb']);
        $bbs = new BicBucStriim($settings['dataDb'], true);
    }
    return $bbs;
};

// user configuration
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
    $bbs = $c->get('bbs');
    $logger = $c->get('logger');
    if (!is_null($bbs) && $bbs->dbOk()) {
        $logger->debug("loading configuration");
        $css = $bbs->configs();
        foreach ($css as $cs) {
            if (array_key_exists($cs->name, $configs)) {
                $logger->debug("configuring value {$cs->val} for {$cs->name}");
                $configs[$cs->name] = $cs->val;
            } else {
                $logger->warn("ignoring unknown configuration, name: {$cs->name}, value: {$cs->val}");
            }
        }
    } else {
        $logger->debug("no configuration loaded");
    }
    return $configs;
};


// calibre
$container['calibre'] = function ($c) {
    $cdir = $c->get('config')[AppConstants::CALIBRE_DIR];
    $logger = $c->get('logger');
    if (!empty($cdir)) {
        try {
            $calibre = new Calibre($cdir.'/metadata.db');
        } catch (PDOException $ex) {
            $logger->error("Error opening Calibre library: ".var_export($ex, true));
            return null;
        }
        if ($calibre->libraryOk()) {
            $logger->debug('Calibre library ok');
        } else {
            $calibre = null;
            $logger->error(getcwd());
            $logger->error("Unable to open Calibre library at ".realpath($cdir));
        }
    } else {
        $logger->debug('No Calibre library');
        $calibre = null;
    }
    return $calibre;
};

use \Slim\Middleware\HttpBasicAuthentication\PdoAuthenticator;
$container["HttpBasicAuthentication"] = function ($container) {
    return new HttpBasicAuthentication([
        'path' => ["/token","/opds"],
        'passthrough' => ["/info", "/thumbnails"],
        'relaxed' => ['localhost'],
        // TODO make security configurable
        'secure' => false,
        'authenticator' => new PdoAuthenticator([
            'pdo' => $container->get('bbs')->mydb,
            'table' => 'user',
            'user' => 'username',
            'hash' => 'password'
        ]),
        "error" => function ($request, $response, $arguments) {
            $data['code'] = "error";
            $data['reason'] = $arguments["message"];
            return $response
                ->withStatus(401)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
        'callback' => function ($request, $response, $arguments) use ($container) {
            $container['logger']->debug('HttpBasicAuthentication got user ' . var_export($arguments['user'],true));
            $container['username']=$arguments['user'];
            $user = $container->bbs->userByName($arguments['user']);
            $container->user = $user;
        }
    ]);
};

$container['token'] = function ($container) {
    return new Token;
};


$container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
        "path" => ["/titles"],
        "passthrough" => ["/token", "/opds", "/info","/thumbnails"],
        // TODO make security configurable
        "secure" => false,
        // TODO change password secret handling
        "secret" => "supersecretkeyyoushouldnotcommittogithub",
        //"secret" => getenv("JWT_SECRET"),
        "algorithm" => ["HS256"],
        "logger" => $container["logger"],
        "relaxed" => ["localhost"],
        "error" => function ($request, $response, $arguments) {
            $data['code'] = "error";
            $data['reason'] = $arguments["message"];
            return $response
                ->withStatus(401)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
        "callback" => function ($request, $response, $arguments) use ($container) {
            $container["token"]->hydrate($arguments["decoded"]);
            $token =  $container['token'];
            if (isset($token) && !is_null($token->getUid())) {
                $container['logger']->debug('JwtAuthentication got user ' . var_export($token,true));
                $user = $container->bbs->user($token->getUid());
                $container['user'] = $user;
            }
        }
    ]);
};