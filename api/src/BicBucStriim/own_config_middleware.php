<?php

namespace BicBucStriim;

use Psr\Log\LoggerInterface;

class OwnConfigMiddleware {

    private $logger;
    private $bbs;
    private $config;

    /**
     * Set the LoggerInterface instance.
     *
     * @param LoggerInterface   $logger Logger
     * @param BicBucStriim      $bbs    BicBucStriim instance
     * @param Array             $config User configuration
     */
    public function __construct(LoggerInterface $logger, BicBucStriim $bbs, Array $config) {
        $this->logger = $logger;
        $this->bbs = $bbs;
        $this->config = $config;
    }

    /**
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        $config_status = $this->check_config_db($this->bbs, $this->config);
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

	protected function check_config_db($bbs, $currentConfig) {
		if ($bbs->dbOk()) {
			$we_have_config = 1;
			if ($currentConfig[AppConstants::DB_VERSION] != AppConstants::DB_SCHEMA_VERSION) {
				$this->logger->warn("own_config_middleware: different db schema detected, should be ".AppConstants::DB_SCHEMA_VERSION.", is {$currentConfig[DB_VERSION]}. please check");
				$we_have_config =  2;
			} else {
                $this->logger->debug("own_config_middleware: config loaded");
            }
		} else {
			$we_have_config = 0;
		}
		return $we_have_config;
	}

}
?>
