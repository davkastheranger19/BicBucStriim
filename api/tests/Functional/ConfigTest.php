<?php

namespace Tests\Functional;

use BicBucStriim\AppConstants;

class ConfigTest extends BaseTestCase
{
    function setUp()
    {
        parent::setUp();
        $response = $this->runAppWithAdmin('POST', '/token');
        $bd = (string)$response->getBody();
        $answer = json_decode($bd, true);
        $this->token = $answer['token'];
    }


    /**
     * Test that the initial configuration is returned
     */
    public function testGetConfig()
    {
        $response = $this->runApp('GET', '/admin/configuration');
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEmpty($answer[AppConstants::CALIBRE_DIR]);
        $this->assertEquals("3", $answer[AppConstants::DB_VERSION]);
        $this->assertEquals(0, $answer[AppConstants::KINDLE]);
        $this->assertEmpty($answer[AppConstants::KINDLE_FROM_EMAIL]);
        $this->assertEquals(1, $answer[AppConstants::THUMB_GEN_CLIPPED]);
        $this->assertEquals(30, $answer[AppConstants::PAGE_SIZE]);
        $this->assertEquals("BicBucStriim", $answer[AppConstants::DISPLAY_APP_NAME]);
        $this->assertEquals(2, $answer[AppConstants::MAILER]);
        $this->assertEmpty($answer[AppConstants::SMTP_USER]);
        $this->assertEmpty($answer[AppConstants::SMTP_PASSWORD]);
        $this->assertEmpty($answer[AppConstants::SMTP_SERVER]);
        $this->assertEquals(25, $answer[AppConstants::SMTP_PORT]);
        $this->assertEquals(0, $answer[AppConstants::SMTP_ENCRYPTION]);
        $this->assertEquals(0, $answer[AppConstants::METADATA_UPDATE]);
        $this->assertEquals(1, $answer[AppConstants::LOGIN_REQUIRED]);
        $this->assertEquals(AppConstants::TITLE_TIME_SORT_TIMESTAMP, $answer[AppConstants::TITLE_TIME_SORT]);
        $this->assertEquals(1, $answer[AppConstants::RELATIVE_URLS]);
    }

    /**
     * Test that modifying the calibre library works
     */
    public function testModifyCalibreLibrary()
    {
        $response = $this->runApp('GET', '/admin/configuration');
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEmpty($answer[AppConstants::CALIBRE_DIR], "calibre dir should be empty, initially");

        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => self::CALIBRE));
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(self::CALIBRE, $answer[AppConstants::CALIBRE_DIR], "calibre dir should be modified");

        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => 'tests/worklib/bla'));
        $bd = (string)$response->getBody();
        $this->assertEquals(400, $response->getStatusCode(), "changing to bad calibre dir should have failed");
        $answer = json_decode($bd, true);
        $this->assertEquals(AppConstants::ERROR_BAD_INPUT, $answer['code'], "code should be bad input");
        $this->assertEquals(AppConstants::ERROR_BAD_CALIBRE_DB, $answer['reason'][AppConstants::CALIBRE_DIR],
            "reason should be bad library dir");

        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::CALIBRE_DIR => ''));
        $bd = (string)$response->getBody();
        $this->assertEquals(400, $response->getStatusCode(), "changing to empty calibre dir should have failed");
        $answer = json_decode($bd, true);
        $this->assertEquals(AppConstants::ERROR_BAD_INPUT, $answer['code'], "code should be bad input");
        $this->assertEquals(AppConstants::ERROR_NO_CALIBRE_PATH, $answer['reason'][AppConstants::CALIBRE_DIR],
            "reason should be empty library dir");
    }

    /**
     * Test modifying the kindle settings
     */
    public function testModifyKindleSettings()
    {
        $response = $this->runApp('GET', '/admin/configuration');
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(0, $answer[AppConstants::KINDLE], "mailing to Kindle should be off, initially");
        $this->assertEmpty($answer[AppConstants::KINDLE_FROM_EMAIL], "no mailing -> no from address");

        $response = $this->runApp('PUT', '/admin/configuration', array(
            AppConstants::KINDLE => 1,
            AppConstants::KINDLE_FROM_EMAIL => 'foo@bar.com'
        ));
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(1, $answer[AppConstants::KINDLE], "mailing to Kindle should be on now");
        $this->assertEquals('foo@bar.com', $answer[AppConstants::KINDLE_FROM_EMAIL], "mailing on -> from address should exist");

        $response = $this->runApp('PUT', '/admin/configuration', array(
            AppConstants::KINDLE => 1,
            AppConstants::KINDLE_FROM_EMAIL => ''
        ));
        $bd = (string)$response->getBody();
        $this->assertEquals(400, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(AppConstants::ERROR_BAD_INPUT, $answer['code'], "code should be bad input");
        $this->assertEquals(AppConstants::ERROR_NO_KINDLEFROM, $answer['reason'][AppConstants::KINDLE],
            "reason should be no from address");

        $response = $this->runApp('PUT', '/admin/configuration', array(
            AppConstants::KINDLE => 1,
            AppConstants::KINDLE_FROM_EMAIL => 'aaaa'
        ));
        $bd = (string)$response->getBody();
        $this->assertEquals(400, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(AppConstants::ERROR_BAD_INPUT, $answer['code'], "code should be bad input");
        $this->assertEquals(AppConstants::ERROR_BAD_KINDLEFROM, $answer['reason'][AppConstants::KINDLE],
            "reason should be bad from address");

    }

    /**
     * Test modifying the page size
     */
    public function testModifyPagesize()
    {
        $response = $this->runApp('GET', '/admin/configuration');
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(30, $answer[AppConstants::PAGE_SIZE], "page size should be 30, initially");

        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::PAGE_SIZE => 1));
        $bd = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(1, $answer[AppConstants::PAGE_SIZE], "page size should 1 now");

        $response = $this->runApp('PUT', '/admin/configuration', array(AppConstants::PAGE_SIZE => 0));
        $bd = (string)$response->getBody();
        $this->assertEquals(400, $response->getStatusCode());
        $answer = json_decode($bd, true);
        $this->assertEquals(AppConstants::ERROR_BAD_INPUT, $answer['code'], "code should be bad input");
        $this->assertEquals(AppConstants::ERROR_BAD_PAGESIZE, $answer['reason'][AppConstants::PAGE_SIZE],
            "reason should be page size out of bounds");
    }
}