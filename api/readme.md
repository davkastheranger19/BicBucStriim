# BicBucStriim API

## URL structure

* /titles - title list
  * /{id} - title details
    * /cover - cover image
    * /{format} - book file in the selected format
* /authors - author list
  * /{id} - author details
    * /image - author image
* /tags - tag list
  * /{id} - tag details
* /series - series list
  * /{id} - series details
* /opds - OPDS root catalog
  * /titles - OPDS titles catalog
  * /authors - OPDS author initials catalog
    * /{initial} OPDS authors for initial catalog
      * /{id} OPDS books by author catalog
  * /tags - OPDS tag initials catalog
    * /{initial} OPDS tags for initial catalog
      * /{id} OPDS books for tag catalog
  * /series - OPDS series initials catalog
    * /{initial} OPDS series for initial catalog
      * /{id} OPDS books for series catalog  
* /thumbnails
  * /titles - title cover thumbnails
  * /authors - author image thumbnails
* /search
* /admin
* /token
* /info