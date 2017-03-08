<?php

use BicBucStriim\Calibre;
use BicBucStriim\AppConstants;

class CalibreConfigMiddleware {

    /**
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        global $container;
        $cdir = $container->config[AppConstants::CALIBRE_DIR];
        $logger = $container->logger;

        if ($request->getUri()->getPath() != '/login/') {
            # 'After installation' scenario: here is a config DB but no valid connection to Calibre
            if (empty($cdir)) {
                $logger->warn('calibre_config_mw: Calibre library path not configured.');
                if ($request->getUri()->getPath() != '/admin/configuration/') {
                    $data = array(code => AppConstants::ERROR_NO_CALIBRE_PATH, reason => 'No Calibre library path configured.');
                    return $response->withStatus(500, 'No Calibre library path configured.')->withJson($data);
                } else {
                    return $next($request, $response);
                }
            } else {
                # Setup the connection to the Calibre metadata db
                $clp = $cdir . '/metadata.db';
                $calibre = new Calibre($clp);
                if ($calibre->libraryOk()) {
                    $container['calibre'] = function ($c, $calibre) {
                        return $calibre;
                    };
                    $next($request, $response);
                } else {
                    $logger->error('calibre_config_mw: Exception while opening metadata db ' . $clp . '.');
                    // app->redirect not useable in middleware
                    $data = array(code => AppConstants::ERROR_BAD_CALIBRE_DB, reason => 'Error while opening Calibre DB.');
                    return $response->withStatus(500, 'Error while opening Calibre DB.')->withJson($data);
                }
            }
        } else {
            return $next($request, $response);
        }
    }
}
?>
