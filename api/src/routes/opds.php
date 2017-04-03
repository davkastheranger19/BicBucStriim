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
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $filter = getFilter($this);
        $just_books = $this->calibre->last30Books($lang, $psize, $filter);
        $books1 = array();
        foreach ($just_books as $book) {
            $record = $this->calibre->titleDetailsOpds($book);
            if (!empty($record['formats']))
                array_push($books1, $record);
        }
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->newestCatalog(NULL, $books, false);
        return mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    /**
     * Return a page of the titles.
     *
     * Note: OPDS acquisition feeds need an acquisition link for every item,
     * so books without formats are removed from the output.
     *
     * @param  integer $index =0 page index
     */
    $this->get('/titleslist/{id}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $index = $args['id'];
        // parameter checking
        if (!is_numeric($index)) {
            $this->logger->warn('opdsByTitle: invalid page id ' . $index);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $params = $request->getQueryParams();
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $filter = getFilter($this);
        if (isset($params['search']))
            $tl = $this->calibre->titlesSlice($lang, $index, $psize, $filter, $params['search']);
        else
            $tl = $this->calibre->titlesSlice($lang, $index, $psize, $filter);
        $books1 = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->titlesCatalog(NULL, $books, false,
            $tl['page'], getNextSearchPage($tl), getLastSearchPage($tl));
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    /**
     * Return a page with author names initials
     */
    $this->get('/authorslist/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initials = $this->calibre->authorsInitials();
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->authorsRootCatalog(NULL, $initials);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Return a page with author names for a initial
     * @param string $initial single uppercase character
     */
    $this->get('/authorslist/{initial}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        // parameter checking
        if (!(ctype_upper($initial))) {
            $this->logger->warn('opdsByAuthorNamesForInitial: invalid initial ' . $initial);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $authors = $this->calibre->authorsNamesForInitial($initial);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->authorsNamesForInitialCatalog(NULL, $authors, $initial);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Return a feed with partial acquisition entries for the author's books
     * @param  string    initial initial character
     * @param  int        id      author id
     * @param  int        page    page number
     */
    $this->get('/authorslist/{initial}/{id}/{page}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        $id = $args['id'];
        $page = $args['page'];
        // parameter checking
        if (!is_numeric($id) || !is_numeric($page)) {
            $this->logger->warn('opdsByAuthor: invalid author id ' . $id . ' or page id ' . $page);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $filter = getFilter($this);
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $tl = $this->calibre->authorDetailsSlice($lang, $id, $page, $psize, $filter);
        $books1 = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->booksForAuthorCatalog(NULL, $books, $initial, $tl['author'], false,
            $tl['page'], getNextSearchPage($tl), getLastSearchPage($tl));
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    /**
     * Return a page with series initials
     */
    $this->get('/serieslist/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initials = $this->calibre->seriesInitials();
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->seriesRootCatalog(NULL, $initials);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Return a page with author names for a initial
     * @param string $initial "all" or single uppercase character
     */
    $this->get('/serieslist/{initial}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        // parameter checking
        if (!($initial == 'all' || ctype_upper($initial))) {
            $this->logger->warn('opdsBySeriesNamesForInitial: invalid initial ' . $initial);
            return $response->withStatus(400)->write('Bad parameter');
        }

        $tags = $this->calibre->seriesNamesForInitial($initial);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->seriesNamesForInitialCatalog(NULL, $tags, $initial);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);

    });
    /**
     * Return a feed with partial acquisition entries for the series' books
     * @param  string    initial initial character
     * @param  int        id        tag id
     * @param  int        page    page index
     */
    $this->get('/serieslist/{initial}/{id}/{page}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        $id = $args['id'];
        $page = $args['page'];
        // parameter checking
        if (!is_numeric($id) || !is_numeric($page)) {
            $this->logger->warn('opdsBySeries: invalid series id ' . $id . ' or page id ' . $page);
            return $response->withStatus(400)->write('Bad parameter');
        }

        $filter = getFilter($this);
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $tl = $this->calibre->seriesDetailsSlice($lang, $id, $page, $psize, $filter);
        $books1 = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->booksForSeriesCatalog(NULL, $books, $initial, $tl['series'], false,
            $tl['page'], getNextSearchPage($tl), getLastSearchPage($tl));
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    /**
     * Create and send the catalog page for the current search criteria.
     * The search criteria is a GET parameter string.
     *
     * @param  integer $index index of page in search
     */
    $this->get('/searchlist/{page}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $index = $args['page'];
        // parameter checking
        if (!is_numeric($index)) {
            $this->logger->warn('opdsBySearch: invalid page id ' . $index);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $params = $request->getQueryParams();
        if (!isset($params['search'])) {
            $this->logger->error('opdsBySearch called without search criteria, page ' . $index);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $filter = getFilter($this);
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $tl = $this->calibre->titlesSlice($lang, $index, $psize, $filter, $params['search']);
        $books1 = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->searchCatalog(NULL, $books, false,
            $tl['page'], getNextSearchPage($tl), getLastSearchPage($tl), $params['search'],
            $tl['total'], $psize);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
    /**
     * Return a page with tag initials
     */
    $this->get('/tagslist/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initials = $this->calibre->tagsInitials();
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->tagsRootCatalog(NULL, $initials);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Return a page with author names for a initial
     * @param string $initial single uppercase character
     */
    $this->get('/tagslist/{initial}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        // parameter checking
        if (!(ctype_upper($initial))) {
            $this->logger->warn('opdsByTagNamesForInitial: invalid initial ' . $initial);
            return $response->withStatus(400)->write('Bad parameter');
        }

        $tags = $this->calibre->tagsNamesForInitial($initial);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->tagsNamesForInitialCatalog(NULL, $tags, $initial);
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_NAV);
    });
    /**
     * Return a feed with partial acquisition entries for the tags's books
     * @param  string $initial initial character
     * @param  int $id tag id
     * @param  int $page page index
     */
    $this->get('/tagslist/{initial}/{id}/{page}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $initial = $args['initial'];
        $id = $args['id'];
        $page = $args['page'];
        // parameter checking
        if (!is_numeric($id) || !is_numeric($page)) {
            $this->logger->warn('opdsByTag: invalid series id ' . $id . ' or page id ' . $page);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $filter = getFilter($this);
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $tl = $this->calibre->tagDetailsSlice($lang, $id, $page, $psize, $filter);
        $books1 = $this->calibre->titleDetailsFilteredOpds($tl['entries']);
        $books = checkThumbnailOpds($books1, $this->bbs);
        $gen = mkOpdsGenerator($this, $request);
        $cat = $gen->booksForTagCatalog(NULL, $books, $initial, $tl['tag'], false,
            $tl['page'], getNextSearchPage($tl), getLastSearchPage($tl));
        mkOpdsResponse($response, $cat, OpdsGenerator::OPDS_MIME_ACQ);
    });
});
