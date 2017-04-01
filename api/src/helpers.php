<?php

/**
 * Retrieve the current configuration
 *
 * @param $c        Container
 * @return mixed    Array of key-value pairs
 */
function getConfig($c) {
    $sconfig = $c->bbs->configs();
    $config = $c->config;
    foreach ($sconfig as $sc) {
        $config[$sc->name] = $sc->val;
    }
    return $config;
}

//


/**
 * Initialize the OPDS generator
 * @param $container
 * @param $request current request
 * @return OpdsGenerator
 */
function mkOpdsGenerator($container, $request)
{
    $root = mkRootUrl($request, (bool)$container->config[\BicBucStriim\AppConstants::RELATIVE_URLS]);
    $version = $container->settings['bbs']['version'];
    $cdir = $container->calibre->calibre_dir;
    $clm = $container->calibre->calibre_last_modified;
    $gen = new \BicBucStriim\OpdsGenerator($root,
        $version,
        $cdir,
        date(DATE_ATOM, $clm),
        $container->l10n);
    return $gen;
}


/**
 * Create and send the typical OPDS response
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param string $content
 * @param string $type
 * @return \Psr\Http\Message\ResponseInterface
 */
function mkOpdsResponse($response, $content, $type)
{
    return $response->withStatus(200)
            ->withHeader('Content-Type', $type)
            ->withHeader('Content-Length', strlen($content))
            ->write($content);
}

/**
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param bool $relativeUrls
 * @return string root URL
 */
function mkRootUrl($request, $relativeUrls=true)
{
    $uri = $request->getUri();
    if ($relativeUrls) {
        $root = rtrim($uri->getPath(), "/");
    } else {
        $root = rtrim($uri->getBasePath() . $uri->getPath(), "/");
    }
    return $root;
}

/**
 * Validate and process important configuration changes
 *
 * @param $c            Container
 * @param $config       current configuration
 * @param $newConfig    new configuration
 * @param $key          config key
 * @param $value        config value
 * @return int          0 = no error, >0 error
 */
function processNewConfig($c, $config, $newConfig, $key, $value) {
    switch($key) {
        case \BicBucStriim\AppConstants::CALIBRE_DIR:
            ## Check for consistency - calibre directory
            # Calibre dir is  empty --> error
            if (empty($value)) {
                return \BicBucStriim\AppConstants::ERROR_NO_CALIBRE_PATH;
            }
            # Calibre dir changed, check it for existence, delete thumbnails of old calibre library
            if ($value!= $config[\BicBucStriim\AppConstants::CALIBRE_DIR]) {
                if (!\BicBucStriim\Calibre::checkForCalibre($value)) {
                    return \BicBucStriim\AppConstants::ERROR_BAD_CALIBRE_DB;
                } else {
                    $c->bbs->clearThumbnails();
                }
            }
            break;
        case \BicBucStriim\AppConstants::KINDLE:
            # Switch off Kindle feature, if no valid email address supplied
            if ($value == "1") {
                if (empty($newConfig[\BicBucStriim\AppConstants::KINDLE_FROM_EMAIL])) {
                    return \BicBucStriim\AppConstants::ERROR_NO_KINDLEFROM;
                } elseif (!isEMailValid($newConfig[\BicBucStriim\AppConstants::KINDLE_FROM_EMAIL])) {
                    return \BicBucStriim\AppConstants::ERROR_BAD_KINDLEFROM;
                }
            }
            break;
        case \BicBucStriim\AppConstants::THUMB_GEN_CLIPPED:
            ## Check for a change in the thumbnail generation method
            if ($value != $config[\BicBucStriim\AppConstants::THUMB_GEN_CLIPPED]) {
                # Delete old thumbnails if necessary
                $c->bbs->clearThumbnails();
            }
            break;
        case \BicBucStriim\AppConstants::PAGE_SIZE:
            ## Check for a change in page size, min 1, max 100
            if ($value != $config[\BicBucStriim\AppConstants::PAGE_SIZE]) {
                if ($value < 1 || $value > 100) {
                    return \BicBucStriim\AppConstants::ERROR_BAD_PAGESIZE;
                }
            }
            break;
        default:
            return 0;
    }
}

# Check for valid email address format
function isEMailValid($mail)
{
    return (filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE);
}
