<?php
// Routes

$app->get('/admin/configuration', function ($request, $response, $args) {
    $config = getConfig($this);
    return $response->withJson($config);
});

$app->put('/admin/configuration', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $data = $request->getParsedBody();
    if ($data === null) {
        $this->logger->error("/admin/configuration: unable to decode json input");
        return $response->withJson(['code' => \BicBucStriim\AppConstants::ERROR_BAD_JSON,
            'reason' => json_last_error_msg()], 400);
    }
    $this->logger->debug('configuration change requested: ' . var_export($data, true));

    $errors = [];
    $config = getConfig($this);
    foreach ($data as $key => $value) {
        if (array_key_exists($key, $config)) {
            $result = processNewConfig($this, $config, $data, $key, $value);
            if ($result > 0) {$errors[$key] = $result;}
        } else {
            $errors[$key] = \BicBucStriim\AppConstants::ERROR_UNKNOWN_CONFIG;
        }
    }
    if (sizeof($errors) > 0) {
        $this->logger->error("/admin/configuration: invalid configuration");
        return $response->withJson(['code' => \BicBucStriim\AppConstants::ERROR_BAD_INPUT,
            'reason' => $errors], 400);
    } else {
        # Save changes
        if (sizeof($data) > 0) {
            $this->bbs->saveConfigs($data);
            foreach ($data as $key => $value) {
                $config[$key] = $value;
            }
            $this->config = $config;
        }
        $this->logger->debug('/admin/configuration: changes saved');
        return $response->withJson($config);
    }
});


$app->get('/{name}', function ($request, $response, $args) {
    $ret = $this->bbs->dbOk();
    // Sample log message
    $this->logger->info("dbok? ".$ret);
    // Render index view
    return $response->withJson($ret);
});
