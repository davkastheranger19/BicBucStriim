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
        $response2 = $this->runApp('PUT', '/admin/configuration',
            array(AppConstants::CALIBRE_DIR => self::CALIBRE));
        //print_r((string)$response2->getBody());
        //print_r($response2->getStatusCode());
    }

    public function testTitles() {
        $response = $this->runApp('GET', '/titles/?page=1&sort=timestamp');
        $content = (string)$response->getBody();
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $answer);
        $this->assertEquals(7, sizeof($answer['data']));
        $book = $answer['data'][0];
        $this->assertEquals('books', $book['type']);
        $this->assertEquals(7, $book['id']);
        $this->assertEquals('Stones of Venice, Volume II, The', $book['attributes']['title']);
        $this->assertEquals('2012-06-22 08:20:04.638912+00:00', $book['attributes']['pubdate']);
        $this->assertEquals('Ruskin, John', $book['attributes']['author']);
        $this->assertEquals('(Englisch; EPUB,MOBI,PDF)', $book['attributes']['additional']);
        $this->assertArrayHasKey('meta', $answer);
        $pagination = $answer['meta']['pagination'];
        $this->assertEquals(7, $pagination['total']);
        $this->assertEquals(0, $pagination['count']);
        $this->assertEquals(30, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(1, $pagination['total_pages']);
        $this->assertArrayHasKey('links', $answer);
        $links = $answer['links'];
        $this->assertEquals('/titles/?page=1&sort=timestamp', $links['self']);
        $this->assertEquals('/titles/?page=1&sort=timestamp', $links['first']);
        $this->assertEquals('/titles/?page=1&sort=timestamp', $links['last']);
    }

    public function testTitlesWithSearch() {
        $response = $this->runApp('GET', '/titles/?page=1&sort=timestamp&search=Stones');
        $content = (string)$response->getBody();
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $answer);
        $this->assertEquals(1, sizeof($answer['data']));
        $book = $answer['data'][0];
        $this->assertEquals('books', $book['type']);
        $this->assertEquals(7, $book['id']);
        $this->assertEquals('Stones of Venice, Volume II, The', $book['attributes']['title']);
        $this->assertArrayHasKey('meta', $answer);
        $pagination = $answer['meta']['pagination'];
        $this->assertEquals(1, $pagination['total']);
        $this->assertEquals(0, $pagination['count']);
        $this->assertEquals(30, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(1, $pagination['total_pages']);
        $this->assertArrayHasKey('links', $answer);
        $links = $answer['links'];
        $this->assertEquals('/titles/?page=1&sort=timestamp&search=Stones', $links['self']);
        $this->assertEquals('/titles/?page=1&sort=timestamp&search=Stones', $links['first']);
        $this->assertEquals('/titles/?page=1&sort=timestamp&search=Stones', $links['last']);
    }

    public function testTitleDefaultIncludes() {
        $response = $this->runApp('GET', '/titles/7/');
        $content = (string)$response->getBody();
        //print_r($content);
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $answer);
        $this->assertEquals(4, sizeof($answer['data']));
        $book = $answer['data'];
        $this->assertEquals('books', $book['type']);
        $this->assertEquals(7, $book['id']);
        $this->assertEquals('Stones of Venice, Volume II, The', $book['attributes']['sort']);
        $this->assertEquals('The Stones of Venice, Volume II', $book['attributes']['title']);
        $this->assertEquals('2012-06-22 08:20:04.638901+00:00', $book['attributes']['timestamp']);
        $this->assertEquals('2012-06-22 08:20:04.638912+00:00', $book['attributes']['pubdate']);
        $this->assertEquals('2013-08-20 09:50:53.238730+00:00', $book['attributes']['lastModified']);
        $this->assertEquals('1.0', $book['attributes']['seriesIndex']);
        $this->assertEmpty($book['attributes']['isbn']);
        $this->assertEquals('/titles/cover/7', $book['attributes']['cover']);
        $this->assertEquals(4, sizeof($book['relationships']));
        $this->assertEquals(1, sizeof($book['relationships']['authors']['data']));
        $authors = $book['relationships']['authors']['data'][0];
        $this->assertEquals('authors', $authors['type']);
        $this->assertArrayHasKey('included', $answer);
        $included = $answer['included'][0];
        $this->assertEquals(10, sizeof($answer['included']));
        $this->assertEquals('authors', $included['type']);
        $this->assertEquals(10, $included['id']);
        $this->assertEquals('Ruskin, John', $included['attributes']['sort']);
        $this->assertEquals('John Ruskin', $included['attributes']['name']);
    }

    public function testTitleSeries() {
        $response = $this->runApp('GET', '/titles/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $answer);
        $this->assertEquals(4, sizeof($answer['data']));
        $book = $answer['data'];
        $this->assertEquals('books', $book['type']);
        $this->assertEquals(1, $book['id']);
        $this->assertEquals('1.0', $book['attributes']['seriesIndex']);
        $this->assertEquals(5, sizeof($book['relationships']));
        // series
        $this->assertEquals(2, sizeof($book['relationships']['series']['data']));
        $series = $book['relationships']['series']['data'];
        $this->assertEquals('series', $series['type']);
        $this->assertArrayHasKey('included', $answer);
        $included = $answer['included'][1];
        $this->assertEquals(4, sizeof($answer['included']));
        $this->assertEquals('series', $included['type']);
        $this->assertEquals(4, $included['id']);
        $this->assertEquals('Serie Lessing', $included['attributes']['sort']);
        $this->assertEquals('Serie Lessing', $included['attributes']['name']);
    }

    public function testTitleTags() {
        $response = $this->runApp('GET', '/titles/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $book = $answer['data'];
        // tags
        $this->assertEquals(1, sizeof($book['relationships']['tags']['data']));
        $tag = $book['relationships']['tags']['data'][0];
        $this->assertEquals('tags', $tag['type']);
        $included = $answer['included'][2];
        $this->assertEquals('tags', $included['type']);
        $this->assertEquals(18, $included['id']);
        $this->assertEquals('Belletristik & Literatur', $included['attributes']['name']);
    }

    public function testTitleFormats() {
        $response = $this->runApp('GET', '/titles/1/');
        $content = (string)$response->getBody();
        //print_r($content);
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $book = $answer['data'];
        // tags
        $this->assertEquals(1, sizeof($book['relationships']['formats']['data']));
        $tag = $book['relationships']['formats']['data'][0];
        $this->assertEquals('formats', $tag['type']);
        $included = $answer['included'][3];
        $this->assertEquals('formats', $included['type']);
        $this->assertEquals(18, $included['id']);
        $this->assertEquals('EPUB', $included['attributes']['format']);
        $this->assertEquals(3174, $included['attributes']['size']);
    }

    public function testTitleIdentifiers() {
        $response = $this->runApp('GET', '/titles/6/');
        $content = (string)$response->getBody();
        //print_r($content);
        $answer = json_decode($content, true);
        //print_r($answer);
        $this->assertEquals(200, $response->getStatusCode());
        $book = $answer['data'];
        // tags
        $this->assertEquals(3, sizeof($book['relationships']['identifiers']['data']));
        $tag = $book['relationships']['identifiers']['data'][0];
        $this->assertEquals('identifiers', $tag['type']);
        $included = $answer['included'][3];
        $this->assertEquals('identifiers', $included['type']);
        $this->assertEquals(6, $included['id']);
        $this->assertEquals('test4', $included['attributes']['type']);
        $this->assertEquals('neuesleben4', $included['attributes']['value']);
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