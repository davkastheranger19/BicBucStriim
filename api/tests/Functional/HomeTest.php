<?php

namespace Tests\Functional;

class HomeTest extends BaseTestCase
{

    /**
     * Test that the index route returns a JSON response
     */
    public function testGetHome()
    {
        $response = $this->runApp('GET', '/');

        print($response->getBody()->getContents());
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('SlimFramework', (string)$response->getBody());
        $this->assertNotContains('Hello', (string)$response->getBody());
    }

}