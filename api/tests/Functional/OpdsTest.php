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
     * Test that the index route returns an OPDS root catalog
     */
    public function testRootCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>BicBucStriim Root Catalog</title>', $content);
    }

    /**
     * Test that the search route returns an OpenSearch form
     */
    public function testOpenSearch()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/opensearch.xml');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">', $content);
    }

    /**
     * Test that the newest route returns an OPDS catalog
     */
    public function testNewestCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/newest/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Newest</title>', $content);
    }

    /**
     * Test that the titles route returns an OPDS catalog
     */
    public function testTitlesCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/titleslist/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Title</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/titleslist/0/?search=Die');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Title</title>', $content);
        $this->assertContains('<title>Die Glücksritter</title>', $content);
        $this->assertNotContains('<title>Lob der Faulheit</title>', $content);
    }

    /**
     * Test that the authorlist route returns an OPDS catalog
     */
    public function testAuthorlistCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Author</title>', $content);
        $this->assertContains('<subtitle>Authors by their initials</subtitle>', $content);
    }

    /**
     * Test that the authorlist initial route returns an OPDS catalog
     */
    public function testAuthorlistInitialCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/E/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All Authors for &quot;E&quot;</title>', $content);
        $this->assertContains('<subtitle>Authors list</subtitle>', $content);
        $this->assertContains('<title>Joseph von Eichendorff</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/e/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

    /**
     * Test that the authorlist id route returns an OPDS catalog
     */
    public function testAuthorlistIdCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/E/5/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All books by &quot;Joseph von Eichendorff&quot;</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/E/X/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/authorslist/E/5/X/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

    /**
     * Test that the serieslist route returns an OPDS catalog
     */
    public function testSerieslistCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Series</title>', $content);
        $this->assertContains('<subtitle>Series by their initials</subtitle>', $content);
    }
    /**
     * Test that the serieslist initial route returns an OPDS catalog
     */
    public function testSerieslistInitialCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/all/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All series for &quot;all&quot;</title>', $content);
        $this->assertContains('<subtitle>Series list</subtitle>', $content);
        $this->assertContains('<title>Serie Grimmelshausen</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/S/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All series for &quot;S&quot;</title>', $content);
        $this->assertContains('<subtitle>Series list</subtitle>', $content);
        $this->assertContains('<title>Serie Grimmelshausen</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/e/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

    /**
     * Test that the serieslist id route returns an OPDS catalog
     */
    public function testSerieslistIdCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/S/1/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All books for series &quot;Serie Grimmelshausen&quot;</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/S/X/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/serieslist/S/1/X/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

    /**
     * Test that the search route returns an OPDS catalog
     */
    public function testSearchCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/searchlist/0/?search=Grimm');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Search: Grimm</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/searchlist/0/');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test that the tagslist route returns an OPDS catalog
     */
    public function testTagslistCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>By Tag</title>', $content);
        $this->assertContains('<subtitle>Tags by their initials</subtitle>', $content);
    }

    /**
     * Test that the tagslist initial route returns an OPDS catalog
     */
    public function testTagslistInitialCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/A/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All tags for &quot;A&quot;</title>', $content);
        $this->assertContains('<subtitle>Tags list</subtitle>', $content);
        $this->assertContains('<title>Architecture</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/a/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

    /**
     * Test that the tagslist id route returns an OPDS catalog
     */
    public function testTagslistIdCatalog()
    {
        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/A/21/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('<title>All books for tag &quot;Architecture&quot;</title>', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/A/X/0/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);

        $response = $this->runAppWithAdmin('GET', '/opds/tagslist/A/21/X/');
        $content = (string)$response->getBody();
        //print_r($content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Bad parameter', $content);
    }

}