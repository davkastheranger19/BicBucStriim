<?php
/**
 * Created by PhpStorm.
 * User: rv
 * Date: 05.04.17
 * Time: 17:40
 */
require __DIR__ . '/../../vendor/autoload.php';
use Httpful\Request;
use BicBucStriim\AppConstants;

const CALIBRE = '/mnt/lib2';
$root = 'http://localhost:8080';
$response = Request::post($root.'/token')
    ->authenticateWithBasic('admin', 'admin')
    ->expectsJson()
    ->send();
$token = $response->body->token;
print($token);
Request::put($root . '/admin/configuration')
    ->addHeader('Authorization', 'Bearer '.$token)
    ->sendsJson()
    ->body(array(AppConstants::CALIBRE_DIR => CALIBRE))
    ->send();