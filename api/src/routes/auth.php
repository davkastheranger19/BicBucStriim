<?php

require_once __DIR__ . '/../BicBucStriim/UserTransformer.php';

use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

$app->post('/token', function ($request, $response, $args) {
    $this->logger->debug("Looking for user ".$this->username);
    $userFound = $this->bbs->userByName($this->username);
    $this->logger->debug("Found user ".var_export($userFound, true));
    $now = new DateTime();
    // TODO make token expiry configurable by user
    $future = new DateTime("now +2 hours");
    $jti = Uuid::uuid4();
    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "sub" => $userFound->username,
        "uid" => $userFound->id
    ];
    // TODO change password secret handling
    //$secret = getenv("JWT_SECRET");
    $secret = "supersecretkeyyoushouldnotcommittogithub";
    $token = JWT::encode($payload, $secret, "HS256");
    $data["status"] = "ok";
    $data["token"] = $token;
    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

/* This is just for debugging, not usefull in real life. */
$app->get("/dump/token", function ($request, $response, $arguments) {
    print_r($this->token);
});

/* This is just for debugging, not usefull in real life. */
$app->get("/dump/user", function ($request, $response, $arguments) {
    $manager = new Manager();
    $manager->setSerializer(new DataArraySerializer());

    // Make a resource out of the data and
    $resource = new Item($this->user, new \BicBucStriim\UserTransformer(), 'user');

    // Run all transformers
    $data = $manager->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
