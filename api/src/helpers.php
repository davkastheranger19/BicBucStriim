<?php

/**
 * Check for all books if thumbnails are available
 * @param array                         $books book records
 * @param \BicBucStriim\BicBucStriim    $bbs
 * @return array
 */
function checkThumbnailOpds($books, $bbs)
{
    $checkThumbnailOpds = function ($record) use ($bbs) {
        $record['book']->thumbnail = $bbs->isTitleThumbnailAvailable($record['book']->id);
        return $record;
    };
    return array_map($checkThumbnailOpds, $books);
}

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


/**
 * Return a tag/language filter for Calibre according to user data, an empty filter if there is no user
 * @param $container
 * @return \BicBucStriim\CalibreFilter
 */
function getFilter($container)
{
    $lang = null;
    $tag = null;
    $user = $container->user;
    if (isset($user)) {
        $container->logger->debug('getFilter: ' . var_export($user, true));
        if (!empty($user['languages']))
            $lang = $container->calibre->getLanguageId($user->languages);
        if (!empty($user['tags']))
            $tag = $container->calibre->getTagId($user->tags);
        $container->logger->debug('getFilter: Using language ' . $lang . ', tag ' . $tag);
    }
    return new \BicBucStriim\CalibreFilter($lang, $tag);
}

/**
 * Calcluate the next page number for search results
 * @param  array $tl search result
 * @return int       page index or NULL
 */
function getNextSearchPage($tl)
{
    if ($tl['page'] < $tl['pages'] - 1)
        $nextPage = $tl['page'] + 1;
    else
        $nextPage = NULL;
    return $nextPage;
}

/**
 * Caluclate the last page numberfor search results
 * @param  array $tl search result
 * @return int            page index
 */
function getLastSearchPage($tl)
{
    if ($tl['pages'] == 0)
        $lastPage = 0;
    else
        $lastPage = $tl['pages'] - 1;
    return $lastPage;
}


/**
 * Initialize the OPDS generator
 * @param $container
 * @param $request current request
 * @return \BicBucStriim\OpdsGenerator
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
        // TODO check if basePath and path are really separate
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
