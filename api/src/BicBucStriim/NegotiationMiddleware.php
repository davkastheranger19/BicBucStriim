<?php
/**
 * Created by PhpStorm.
 * User: rv
 * Date: 14.03.17
 * Time: 14:14
 */

namespace BicBucStriim;

use \Aura\Accept\AcceptFactory;
use Psr\Container\ContainerInterface;

class NegotiationMiddleware
{

    private $allowedLangs = [];
    private $c;

    /**
     * NegotiationMiddleware constructor.
     */
    public function __construct(ContainerInterface $container, array $langs)
    {
        $this->c = $container;
        $this->allowedLangs = $langs;
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
        $accept_factory = new AcceptFactory($request->getHeaders());
        $accept = $accept_factory->newInstance();

        # try to find a common language, if there is no fit use english
        $language = $accept->negotiateLanguage($this->allowedLangs);
        $this->c['logger']->debug('NegotiationMiddleware Found Language '.var_export($language, true));
        if ($language && !empty($language->getType())) {
            $this->c['l10n'] = new L10n($language->getType());
        } else {
            $this->c['l10n'] = new L10n('en');
        }

        // TODO is media negotiation helpful?
        /*
        $media = $accept->negotiateMedia($available);
        if ($media == false || empty($media->getValue())) {
            $data = array(
                'code' => AppConstants::ERROR_BAD_MEDIATYPE,
                'reason' => join(',', $request->getHeader('Accept')));
            return $response
                ->withStatus(406, 'No or wrong media type in Accept header')
                ->withJson($data);
        } else {
            return $next($request, $response);
        }
        */

        return $next($request, $response);
    }
}