<?php
/**
 * Created by PhpStorm.
 * User: rv
 * Date: 03.04.17
 * Time: 22:56
 */

use BicBucStriim\SimplePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;

$app->group('/titles', function() {

    /**
     * Return a list of book titles. The number of titles is dictated by the page size configuration.
     * The page number and sort order are parameters.
     */
    $this->get('/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $params = $request->getQueryParams();
        $index = getQueryParam( $params, 'page', 1);
        $sort_order = getQueryParam($params, 'sort', \BicBucStriim\AppConstants::TITLE_ALPHA_SORT);
        $search = trim(getQueryParam($params, 'search', null));

        // parameter checking
        if (!is_numeric($index) || $index < 1) {
            $this->logger->warn('titles: invalid page id ' . $index);
            return $response->withStatus(400)->write('Bad parameter');
        }
        if (!array_search($sort_order, array(\BicBucStriim\AppConstants::TITLE_ALPHA_SORT,
            \BicBucStriim\AppConstants::TITLE_TIME_SORT_LASTMODIFIED,
            \BicBucStriim\AppConstants::TITLE_TIME_SORT_PUBDATE,
            \BicBucStriim\AppConstants::TITLE_TIME_SORT_TIMESTAMP))) {
            $this->logger->warn('titles: invalid sort order ' . $sort_order);
            return $response->withStatus(400)->write('Bad parameter');
        }

        // Find titles
        $filter = getFilter($this);
        $lang = $this->l10n->user_lang || 'en';
        $psize = $this->config[\BicBucStriim\AppConstants::PAGE_SIZE];
        $clipped = $this->config[\BicBucStriim\AppConstants::THUMB_GEN_CLIPPED];
        switch ($sort_order) {
            case \BicBucStriim\AppConstants::TITLE_TIME_SORT_TIMESTAMP:
                $tl = $this->calibre->timestampOrderedTitlesSlice($lang, $index-1, $psize, $filter, $search);
                break;
            case \BicBucStriim\AppConstants::TITLE_TIME_SORT_PUBDATE:
                $tl = $this->calibre->pubdateOrderedTitlesSlice($lang, $index-1, $psize, $filter, $search);
                break;
            case \BicBucStriim\AppConstants::TITLE_TIME_SORT_LASTMODIFIED:
                $tl = $this->calibre->lastmodifiedOrderedTitlesSlice($lang, $index-1, $psize, $filter, $search);
                break;
            default:
                $tl = $this->calibre->titlesSlice($lang, $index-1, $psize, $filter, $search);
                break;
        }

        // Add thumbnail info
        $books = checkThumbnail($tl['entries'], $this->bbs, $this->calibre, $clipped);

        // Render the result
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer());
        $resource = new Collection($books, new \BicBucStriim\BookTransformerShort(), 'books');
        $paginator = new SimplePaginator();
        $paginator->setCurrentPage($tl['page']+1)
            ->setPageSize($psize)
            ->setTotal($tl['total'])
            ->setSortOrder($sort_order)
            ->setSearch($search)
            ->setUrl(mkRootUrl($request, $this->config[\BicBucStriim\AppConstants::RELATIVE_URLS]).'/titles/');
        $resource->setPaginator($paginator);
        $data = $manager->createData($resource)->toArray();
        return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

    $this->get('/{id}/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $id = $args['id'];
        // parameter checking
        if (!is_numeric($id)) {
            $this->logger->warn('titles: invalid title id ' . $id);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $lang = $this->l10n->user_lang || 'en';
        $details = $this->calibre->titleDetails($lang, $id);
        if (is_null($details)) {
            $this->logger->warn("title: book not found: " + $id);
            return $response->withStatus(404)->write('Book not found');
        }
        // for people trying to circumvent filtering by direct access
        if (title_forbidden($this->config[\BicBucStriim\AppConstants::LOGIN_REQUIRED], $this->user, $details)) {
            $this->logger->warn("title: requested book not allowed for user: " . $id);
            return $response->withStatus(403);
        }
        // Show ID links only if there are templates and ID data
        $idtemplates = $this->bbs->idTemplates();
        $id_tmpls = array();
        if (count($idtemplates) > 0 && count($details['ids']) > 0) {
            foreach ($idtemplates as $idtemplate) {
                $id_tmpls[$idtemplate->name] = array($idtemplate->val, $idtemplate->label);
            }
        }
        $this->logger->debug('titleDetails custom columns: ' . count($details['custom']));

        // Render the result
        $manager = new Manager();
        // TODO add language, langcodes
        $manager->parseIncludes('series,tags,formats,identifiers,comment');
        $manager->setSerializer(new JsonApiSerializer());
        $resource = new Item($details, new \BicBucStriim\BookTransformer, 'books');
        $data = $manager->createData($resource)->toArray();
        return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

    /**
     * Return the cover for the book with ID. Calibre generates only JPEGs, so we always return a JPEG.
     * If there is no cover, return 404.
     */
    $this->get('/{id}/cover/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $id = $args['id'];
        // parameter checking
        if (!is_numeric($id)) {
            $this->logger->warn('cover: invalid title id ' . $id);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $book = $this->calibre->title($id);
        if (is_null($book)) {
            $this->logger->debug("cover: book not found: " + $id);
            return $response->withStatus(404)->write('Book for cover not found');
        }
        if ($book->has_cover) {
            $cover = $this->calibre->titleCover($id);
            $fh = fopen($cover, 'rb');
            $stream = new \Slim\Http\Stream($fh); // create a stream instance for the response body
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'image/jpeg;base64')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Length', filesize($cover))
                ->withBody($stream); // all stream contents will be sent to the response
        } else {
            return $response->withStatus(404)->write('No cover');
        }
    });
    $this->get('/{id}/file/{file}', 'book');
    /**
     * Return the cover for the book with ID. Calibre generates only JPEGs, so we always return a JPEG.
     * If there is no cover, return 404.
     */
    $this->get('/{id}/thumbnail/', function(Psr\Http\Message\ServerRequestInterface $request, $response, $args) {
        $id = $args['id'];
        // parameter checking
        if (!is_numeric($id)) {
            $this->logger->warn('thumbnail: invalid title id ' . $id);
            return $response->withStatus(400)->write('Bad parameter');
        }
        $book = $this->calibre->title($id);
        if (is_null($book)) {
            $this->logger->debug("thumbnail: book not found: " + $id);
            return $response->withStatus(404)->write('Book not found');
        }
        if ($book->has_cover) {
            $clipped = $this->config[\BicBucStriim\AppConstants::THUMB_GEN_CLIPPED];
            $cover = $this->calibre->titleCover($id);
            $thumb = $this->bbs->titleThumbnail($id, $cover, $clipped);
            $fh = fopen($thumb, 'rb');
            $stream = new \Slim\Http\Stream($fh); // create a stream instance for the response body
            return $response->withHeader('Content-Type', 'image/png;base64')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Length', filesize($thumb))
                ->withBody($stream); // all stream contents will be sent to the response
        } else {
            return $response->withStatus(404)->write('No cover');
        }
    });
});
