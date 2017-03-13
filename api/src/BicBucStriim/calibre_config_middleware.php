<?php

namespace BicBucStriim;

use Psr\Log\LoggerInterface;

class CalibreConfigMiddleware {

    private $logger;
    private $calibre;
    private $config;

    /**
     * Set the LoggerInterface instance.
     *
     * @param LoggerInterface   $logger     Logger
     * @param Calibre           $calibre    Calibre instance
     * @param Array             $config     User configuration
     */
    public function __construct(LoggerInterface $logger, Calibre $calibre = null, Array $config) {
        $this->logger = $logger;
        $this->calibre = $calibre;
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
        $cdir = $this->config[AppConstants::CALIBRE_DIR];

        $path = $request->getUri()->getPath();
        if (substr($path, 0,6) === '/token' || substr($path, 0,5) === '/dump' || substr($path, 0,6) === '/admin') {
            // No Calibre needed in these parts
            return $next($request, $response);
        } else {
            if (empty($cdir)) {
                $data = array(code => AppConstants::ERROR_NO_CALIBRE_PATH, reason => 'No Calibre library path configured.');
                return $response->withStatus(500, 'No Calibre library path configured.')->withJson($data);
            } elseif (is_null($this->calibre)) {
                $data = array(code => AppConstants::ERROR_BAD_CALIBRE_DB, reason => 'Error while opening Calibre DB.');
                return $response->withStatus(500, 'Error while opening Calibre DB.')->withJson($data);
            } else {
                return $next($request, $response);
            }
        }
    }
}
?>
