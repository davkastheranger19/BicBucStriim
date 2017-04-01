<?php

namespace Tests\Functional;

use BicBucStriim\AppConstants;

class OpdsTest extends BaseTestCase
{
    function setUp()
    {
        parent::setUp();
        $response = $this->runAppWithAdmin('POST', '/token');
        $bd = (string)$response->getBody();
        $answer = json_decode($bd, true);
        $this->token = $answer['token'];
        $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => 'tests/worklib/lib2'));
    }


    /**
     * Test that the index route returns a JSON response
     */
    public function testRootCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/');

        print($response->getBody()->getContents());
        $this->assertEquals(200, $response->getStatusCode());
    }

}