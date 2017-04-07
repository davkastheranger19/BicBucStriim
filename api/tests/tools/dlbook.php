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
$file = urlencode('Die Glucksritter - Joseph von Eichendorff\\.epub');
$response = Request::get($root.'/opds/titles/4/format/EPUB/')
    ->authenticateWithBasic('admin', 'admin')
    ->send();
print($response->body);