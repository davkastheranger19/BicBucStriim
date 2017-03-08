<?php

namespace BicBucStriim;

use BicBucStriim\AppConstants;

class OwnConfigMiddleware {

    /**
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        global $container;
        $config_status = $this->check_config_db($container->bbs, $container->config, $container->logger);
        if ($config_status == 0) {
            $data = array(code => AppConstants::ERROR_BAD_DB, reason => 'No or bad configuration database.');
            return $response->withStatus(500, 'No or bad configuration database.')->withJson($data);
        } elseif ($config_status == 2) {
            $data = array(code => AppConstants::ERROR_BAD_SCHEMA_VERSION, reason => 'Different db schema version detected.');
            return $response->withStatus(500, 'Different db schema version detected.')->withJson($data);
        } else {
            return $next($request, $response);
        }
    }

	protected function check_config_db($bbs, $currentConfig, $logger) {
		if ($bbs->dbOk()) {
			$we_have_config = 1;
			$css = $bbs->configs();
			$logger->debug(var_export($currentConfig));
			foreach ($css as $config) {
				if (array_key_exists($config->name, $currentConfig)) {
                    $logger->debug("own_config_middleware: configuring value {$config->val} for {$config->name}");
                    $currentConfig[$config->name] = $config->val;
                } else {
                    $logger->warn("own_config_middleware: unknown configuration, name: {$config->name}, value: {$config->val}");
                }
			}
			if ($currentConfig[AppConstants::DB_VERSION] != AppConstants::DB_SCHEMA_VERSION) {
				$logger->warn("own_config_middleware: different db schema detected, should be ".AppConstants::DB_SCHEMA_VERSION.", is {$currentConfig[DB_VERSION]}. please check");
				$we_have_config =  2;
			} else {
                $logger->debug("own_config_middleware: config loaded");
            }
		} else {
			$we_have_config = 0;
		}
		return $we_have_config;
	}

}
?>
