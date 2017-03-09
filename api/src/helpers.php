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
    return $sconfig;
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
                    return \BicBucStriim\AppConstants::ERROR_BAD_KINDLEFROM;
                } elseif (!isEMailValid($value)) {
                    return \BicBucStriim\AppConstants::ERROR_BAD_KINDLEFROM;
                }
            }
            break;
        case \BicBucStriim\AppConstants::THUMB_GEN_CLIPPED:
            ## Check for a change in the thumbnail generation method
            if ($value != $config[THUMB_GEN_CLIPPED]) {
                # Delete old thumbnails if necessary
                $c->bbs->clearThumbnails();
            }
            break;
        case \BicBucStriim\AppConstants::PAGE_SIZE:
            ## Check for a change in page size, min 1, max 100
            if ($value != $config[PAGE_SIZE]) {
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
