<?php
/**
 * Created by PhpStorm.
 * User: rv
 * Date: 03.04.17
 * Time: 22:56
 */

$app->group('/titles', function() {
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
            return $response->withStatus(404)->write('Book not found');
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
