<?php
/**
 * Created by PhpStorm.
 * User: rv
 * Date: 06.04.17
 * Time: 10:53
 */

namespace BicBucStriim;

use Psr\Log\LoggerInterface;

class RequestLogMiddleware
{

    private $logger;
    private $bbs;
    private $config;

    /**
     * Set the LoggerInterface instance.
     *
     * @param LoggerInterface   $logger Logger
     * @param Array             $config User configuration
     */
    public function __construct(LoggerInterface $logger, Array $config) {
        $this->logger = $logger;
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
        $this->logger->debug('Request: ' . $request->getRequestTarget());
        $response = $next($request, $response);
        $this->logger->debug('Response: ' . $response->getStatusCode());
        return $response;
    }
}