<?php

namespace Tests\Functional;

use BicBucStriim\AppConstants;

class TitlesTest extends BaseTestCase
{
    function setUp()
    {
        parent::setUp();
        $response = $this->runAppWithAdmin('POST', '/token');
        $bd = (string)$response->getBody();
        $answer = json_decode($bd, true);
        $this->token = $answer['token'];
        $response2 = $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => '../worklib/lib2'));
        //print_r((string)$response2->getBody());
        //print_r($response2->getStatusCode());
    }

    /**
     * Test that the titles cover route returns an OPDS catalog
     */
    public function testCover()
    {
        $response = $this->runApp('GET', '/titles/1/cover/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(110333, $response->getHeader('Content-Length')[0]);

        $response = $this->runApp('GET', '/titles/a/cover/');
        $this->assertEquals(400, $response->getStatusCode());

        $response = $this->runApp('GET', '/titles/999/cover/');
        $this->assertEquals(404, $response->getStatusCode());

    }

    /**
     * Test that the titles thumb route returns an OPDS catalog
     */
    public function testThumbnail()
    {
        $response = $this->runApp('GET', '/titles/1/thumbnail/');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(29406, $response->getHeader('Content-Length')[0]);

        $response = $this->runApp('GET', '/titles/a/thumbnail/');
        $this->assertEquals(400, $response->getStatusCode());

        $response = $this->runApp('GET', '/titles/999/thumbnail/');
        $this->assertEquals(404, $response->getStatusCode());

    }
}