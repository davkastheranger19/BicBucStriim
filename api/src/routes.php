<?php
// Routes
$app->get('/{name}', function ($request, $response, $args) {
    $ret = $this->bbs->dbOk();
    // Sample log message
    $this->logger->info("dbok? ".$ret);
    // Render index view
    return $response->withJson($ret);
});
