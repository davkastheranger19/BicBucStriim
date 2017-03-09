<?php

namespace Tests\Functional;

require 'src/BicBucStriim/app_constants.php';

use BicBucStriim\AppConstants;

class ConfigTest extends BaseTestCase
{

    /**
     * Test that the configurations is returned
     */
    public function testGetConfig()
    {
        $response = $this->runApp('GET', '/admin/configuration');
        $bd = (string)$response->getBody();
        $answer = json_decode($bd, true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("3", $answer[AppConstants::DB_VERSION]);
        $this->assertEquals(0, $answer[AppConstants::KINDLE]);
        $this->assertEquals(1, $answer[AppConstants::THUMB_GEN_CLIPPED]);
    }

    /**
     * Test that the index route returns a JSON response
     */
    public function testModifyConfig()
    {
        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => '/calibre'));
        $answer = json_decode($response->getBody()->getContents());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/calibre', $answer[AppConstants::CALIBRE_DIR]);
    }

}