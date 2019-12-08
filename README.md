js-search
=========

This is a client side search engine for use on static pages.

It uses a pre-compiled search index to add a fulltext search to static HTML pages such as
[github pages][] or offline API documentation. The index is built by a PHP script using a
similar yet much more simplified and dump approach than the popular search engine [Lucene].

To see how it looks like, check out the [demo][].

[github pages]: https://pages.github.com/
[Lucene]: http://lucene.apache.org/
[demo]: http://cebe.github.io/js-search/#demo


Installation
------------

PHP 5.6 or higher is required to run the index generator.

Installation is recommended to be done via [composer](https://getcomposer.org/) by adding the following to the `require` section in your `composer.json`:

```json
{ "require" : 
  {
  "keiro/php-js-search": "~1.0"
  }
}
```

Alternatively run `composer require "keiro/php-js-search"`.

Generate the index
----
Using the command line tool:
```
vendor/bin/generateindex <path-to-your-html-files>
```

#### js
This will generate a `jssearch.index.js` file that you have to include in the HTML header.

#### php
This will generate a `search-engine-index.php` in data folder.


Usage
-----

See [example.html](example.html) for a js implementation.  
See [example-php.html](example-php.html) for a php implementation.