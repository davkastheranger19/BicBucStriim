<?php

namespace Tests\Unit;
/**
 * OPDS Generator test suite.
 *
 * Needs the follwoing external tools installed:
 * - opds_validator (https://github.com/zetaben/opds-validator)
 */

use BicBucStriim\BicBucStriim;
use BicBucStriim\Calibre;
use BicBucStriim\OpdsGenerator;
use BicBucStriim\L10n;
use BicBucStriim\CalibreFilter;
use SimpleXMLElement;
use XMLReader;

class TestOfOpdsGenerator extends \PHPUnit\Framework\TestCase
{
    const OPDS_RNG = __DIR__ . '/../fixtures/opds_catalog_1_1.rng';
    const DB2 = __DIR__ . '/../fixtures/data2.db';
    const CDB2 = __DIR__ . '/../fixtures/lib2/metadata.db';
    const TOOLS = __DIR__ . '/../tools';
    const PUBLICS = __DIR__ . '/../../public';


    const WORK = __DIR__ . '/../twork';
    const PUBLICP = __DIR__ . '/../twork/public';
    const THUMB = __DIR__ .'/../twork/public/thumbnails';
    const THUMBA = __DIR__ .'/../twork/public/thumbnails/authors';
    const THUMBT = __DIR__ .'/../twork/public/thumbnails/titles';
    const DATA = __DIR__ . '/../twork/data';
    const TESTSCHEMA = __DIR__ . '/../twork/data/schema.sql';
    const DATADB = __DIR__ . '/../twork/data/data.db';

    var $bbs;
    var $gen;

    function setUp()
    {
        if (file_exists(self::WORK))
            system("rm -rf ".self::WORK);
        mkdir(self::DATA, 0777, true);
        copy(self::DB2, self::DATADB);
        system("cp -r ".self::PUBLICS." ".self::WORK);
        $this->bbs = new BicBucStriim(self::DATADB,self::PUBLICP, true);
        $this->calibre = new Calibre(self::CDB2);
        $l10n = new L10n('en');
        $this->gen = new OpdsGenerator('/bbs', '0.9.0',
            $this->calibre->calibre_dir,
            date(DATE_ATOM, strtotime('2012-01-01T11:59:59')),
            $l10n);
    }

    function tearDown()
    {
        $this->calibre = NULL;
        $this->bbs = NULL;
        system("rm -rf ".self::WORK);
    }

    # Validation helper: validate relaxng
    function opdsValidateSchema($feed)
    {
        $xml = \XMLReader::open($feed);
        $xml->setRelaxNGSchema(self::OPDS_RNG);
        $xml->setParserProperty(XMLReader::VALIDATE, true);

        return $xml->isValid();
    }


    # Validation helper: validate opds
    function opdsValidate($feed, $version)
    {
        $cmd = 'cd '.self::TOOLS.';java -jar OPDSValidator.jar -v' . $version . ' ' . realpath($feed);
        $res = system($cmd);
        if ($res != '') {
            echo 'OPDS validation error: ' . $res;
            return false;
        } else
            return true;
    }

    # Timestamp helper: generate proper timezone offsets
    # see http://www.php.net/manual/en/datetimezone.getoffset.php
    function genTimestampOffset($phpTime)
    {
        if (date_default_timezone_get() == 'UTC') {
            $offsetString = '+00:00'; // No need to calculate offset, as default timezone is already UTC
        } else {
            $millis = strtotime($phpTime); // Convert time to milliseconds since 1970, using default timezone
            $timezone = new DateTimeZone(date_default_timezone_get()); // Get default system timezone to create a new DateTimeZone object
            $offset = $timezone->getOffset(new DateTime($phpTime)); // Offset in seconds to UTC
            $offsetHours = round(abs($offset) / 3600);
            $offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60);
            $offsetString = ($offset < 0 ? '-' : '+')
                . ($offsetHours < 10 ? '0' : '') . $offsetHours
                . ':'
                . ($offsetMinutes < 10 ? '0' : '') . $offsetMinutes;
        }
        return $offsetString;
    }

    function testRootCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $xml = $this->gen->rootCatalog($feed);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }


    function testPartialAcquisitionEntry()
    {
        $expected = '<entry>
 <id>urn:bicbucstriim:/bbs/opds/titles/2</id>
 <title>Trutz Simplex</title>
 <dc:issued>2012</dc:issued>
 <updated>2012-01-01T11:59:59' . $this->genTimestampOffset('2012-01-01 11:59:59') . '</updated>
 <author>
  <name>Grimmelshausen, Hans Jakob Christoffel von</name>
 </author>
 <content type="text/html"></content>
 <dc:language>deu</dc:language>
 <link href="/bbs/thumbnails/titles/default_book.png" type="image/png" rel="http://opds-spec.org/image/thumbnail"/>
 <link href="/bbs/opds/titles/2/cover/" type="image/jpeg" rel="http://opds-spec.org/image"/>
 <link href="/bbs/opds/titles/2/format/EPUB/" type="application/epub+zip" rel="http://opds-spec.org/acquisition"/>
 <link href="/bbs/opds/titles/2/format/MOBI/" type="application/x-mobipocket-ebook" rel="http://opds-spec.org/acquisition"/>
 <category term="Biografien &amp; Memoiren" label="Biografien &amp; Memoiren"/>
</entry>
';
        $just_book = $this->calibre->title(2);
        #print_r($just_book);
        $book = $this->calibre->titleDetailsOpds($just_book);
        $this->gen->openStream(NULL);
        $this->gen->partialAcquisitionEntry($book, false);
        $result = $this->gen->closeStream();
        #print_r($result);
        $this->assertEquals($expected, $result);
    }

    function testNewestCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $just_books = $this->calibre->last30Books('en', 30, new CalibreFilter());
        $books = $this->calibre->titleDetailsFilteredOpds($just_books);
        $xml = $this->gen->newestCatalog($feed, $books, false);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testTitlesCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->titlesSlice('en', 0, 2, new CalibreFilter());
        $books = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $xml = $this->gen->titlesCatalog($feed, $books, false,
            $tl['page'], $tl['page'] + 1, $tl['pages'] - 1);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testTitlesCatalogOpenSearch()
    {
        $tl = $this->calibre->titlesSlice('en', 0, 2, new CalibreFilter());
        $books = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $xml = $this->gen->titlesCatalog(NULL, $books, false,
            $tl['page'], $tl['page'] + 1, $tl['pages'] - 1);
        $feed = new SimpleXMLElement($xml);
        $this->assertEquals(7, count($feed->link));
        $oslnk = $feed->link[0];
        $this->assertEquals(OpdsGenerator::OPENSEARCH_MIME, (string)$oslnk['type']);
        $this->assertTrue(strpos((string)$oslnk['href'], 'opensearch.xml') > 0);
    }


    function testAuthorsInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->authorsInitials();
        $xml = $this->gen->authorsRootCatalog($feed, $tl);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testAuthorsNamesForInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->authorsNamesForInitial('R');
        $xml = $this->gen->authorsNamesForInitialCatalog($feed, $tl, 'R');
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testAuthorsBooksForAuthorCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $adetails = $this->calibre->authorDetails(5);
        $books = $this->calibre->titleDetailsFilteredOpds($adetails['books']);
        $xml = $this->gen->booksForAuthorCatalog($feed, $books, 'E', $adetails['author'], false, 0, 1, 2);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testTagsInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->tagsInitials();
        $xml = $this->gen->tagsRootCatalog($feed, $tl);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testTagsNamesForInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->tagsNamesForInitial('B');
        $xml = $this->gen->tagsNamesForInitialCatalog($feed, $tl, 'B');
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testTagsBooksForTagCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $adetails = $this->calibre->tagDetails(9);
        $books = $this->calibre->titleDetailsFilteredOpds($adetails['books']);
        $xml = $this->gen->booksForTagCatalog($feed, $books, 'B', $adetails['tag'], false, 0, 1, 2);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testSeriesInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->seriesInitials();
        $xml = $this->gen->seriesRootCatalog($feed, $tl);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testSeriesNamesForInitialCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $tl = $this->calibre->seriesNamesForInitial('S');
        $xml = $this->gen->seriesNamesForInitialCatalog($feed, $tl, 'S');
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

    function testSeriesBooksForSeriesCatalogValidation()
    {
        $feed = self::DATA . '/feed.xml';
        $adetails = $this->calibre->seriesDetails(1);
        $books = $this->calibre->titleDetailsFilteredOpds($adetails['books']);
        $xml = $this->gen->booksForSeriesCatalog($feed, $books, 'S', $adetails['series'], false, 0, 1, 2);
        $this->assertTrue(file_exists($feed));
        $this->assertTrue($this->opdsValidateSchema($feed));
        $this->assertTrue($this->opdsValidate($feed, '1.0'));
        $this->assertTrue($this->opdsValidate($feed, '1.1'));
    }

}

?>
