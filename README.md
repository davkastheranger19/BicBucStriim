# BicBucStriim

BicBucStriim streams books, digital books. It fills a gap in the functionality of current NAS devices that provide access to music, videos and photos -- but not books. BicBucStriim fills this gap and provides web-based access to your e-book collection.

BicBucStriim was created when I bought a NAS device (Synology DS 512+) to store my media on it. NAS devices like the Synology DS typically include media servers that publish audio, video, photos, so that you can access your media from all kinds of devices (TV, smart phone, laptop ...) inside the house, which is very convenient. Unfortunately there is nothing like that for e-books. So BicBucStriim was created.

BicBucStriim is a web application that runs in the Apache/PHP environment provided by the NAS. It assumes that you manage your e-book collection with [Calibre](http://calibre-ebook.com/). The application reads the Calibre data and publishes it in HTML form. To access the e-book catalog you simply point your ebook reader to your NAS, select one of your e-books and download it. 

## Features & Issues

* shows the most recent titles of your library on the main page
* there are sections for browsing through book titles, authors, tags and series
* individual books can be downloaded or emailed 
* information about your favourite authors can be added (links, picture)
* global search 
* speaks Dutch, English, French, German, Galician, Italian and more
* is ready for mobile clients
* provides login-based access control 
* users can be restricted by book language and/or tag
* provides OPDS book catalogs for reading apps like Stanza
* has an admin GUI for configuration

* no support for Calibre's virtual libraries
* only simple custom columns supported

The frontend is a single-page web application, based on the Vue framework.

TODO The backend ...

## Project setup 

(All examples with yarn, but npm works too, of course.)

```
yarn install
```

### Compiles and hot-reloads for development
```
yarn run serve
```

### Compiles and minifies for production
```
yarn run build
```

### Run your tests
```
yarn run test
```

### Lints and fixes files
```
yarn run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
