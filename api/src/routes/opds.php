<?php

use BicBucStriim\OpdsGenerator;

$app->group('/opds', function() {
    /**
     * Generate and send the OPDS root navigation catalog
     */
    $this->get('/', function($request, $response, $args) {
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->rootCatalog(NULL);
        return mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Format and send the OpenSearch descriptor document
     */
    $this->get('/opensearch.xml', function($request, $response, $args) {
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->searchDescriptor(NULL, '/opds/searchlist/0/');
        return mkOpdsResponse($response, $cat, OpdsGenerator::OPENSEARCH_MIME);
    });
    /**
     * Generate and send the OPDS 'newest' catalog. This catalog is an
     * acquisition catalog with a subset of the title details.
     *
     * Note: OPDS acquisition feeds need an acquisition link for every item,
     * so books without formats are removed from the output.
     */
    $this->get('/newest/', function($request, $response, $args) {
        $lang = $this->c['userLanguage'] || 'en';
        $psize = $this->c['config'][\BicBucStriim\AppConstants::PAGE_SIZE];
        $filter = getFilter();
        $just_books = $this->calibre->last30Books($lang, $psize, $filter);
        $books1 = array();
        foreach ($just_books as $book) {
            $record = $this->c->calibre->titleDetailsOpds($book);
            if (!empty($record['formats']))
                array_push($books1, $record);
        }
        $books = array_map('checkThumbnailOpds', $books1);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->newestCatalog(NULL, $books, false);
        return mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    $this->get('/titleslist/:id/', 'opdsByTitle');
    $this->get('/authorslist/', 'opdsByAuthorInitial');
    $this->get('/authorslist/:initial/', 'opdsByAuthorNamesForInitial');
    $this->get('/authorslist/:initial/:id/:page/', 'opdsByAuthor');
    $this->get('/serieslist/', 'opdsBySeriesInitial');
    $this->get('/serieslist/:initial/', 'opdsBySeriesNamesForInitial');
    $this->get('/serieslist/:initial/:id/:page/', 'opdsBySeries');
    $this->get('/searchlist/:id/', 'opdsBySearch');
    $this->get('/tagslist/', 'opdsByTagInitial');
    $this->get('/tagslist/:initial/', 'opdsByTagNamesForInitial');
    $this->get('/tagslist/:initial/:id/:page/', 'opdsByTag');
    $this->get('/titles/:id/', 'title');
    $this->get('/titles/:id/cover/', 'cover');
    $this->get('/titles/:id/file/:file', 'book');
    $this->get('/titles/:id/thumbnail/', 'thumbnail');
});
