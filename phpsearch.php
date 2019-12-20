<?php

use cebe\jssearch\Searcher;
use cebe\jssearch\SearcherDatabase;
use cebe\jssearch\SearcherFile;

$composerAutoload = [
    __DIR__ . '/vendor/autoload.php', // standalone with "composer install" run
    __DIR__ . '/../../autoload.php', // script is installed as a composer binary
];
foreach ($composerAutoload as $autoload) {
    if (file_exists($autoload)) {
        require $autoload;
        break;
    }
}


/**
 *
 *  WITH SQLLITE DB
 *
 */

// Search query
if (isset($_GET['query'])) {

    $query = htmlspecialchars($_GET['query']);

    $hasLimit = (isset($_GET['limit']) && is_numeric(htmlspecialchars($_GET['limit'])));
    if ($hasLimit) {
        $searcher = new SearcherDatabase((int)htmlspecialchars($_GET['limit']));
    } else {
        $searcher = new SearcherDatabase();
    }

    $searcher->getResults($query);
}



/**
 *
 *  WITH LOCAL FILE
 *
 */

/*
// get $index & $files
require_once('data/search-engine-index.php');

// Search query
if (isset($_GET['query'])) {

    $query = htmlspecialchars($_GET['query']);

    $hasLimit = (isset($_GET['limit']) && is_numeric(htmlspecialchars($_GET['limit'])));
    if ($hasLimit) {
        $searcher = new SearcherFile($index, $files, (int)htmlspecialchars($_GET['limit']));
    } else {
        $searcher = new SearcherFile($index, $files);
    }

    $searcher->getResults($query);
}

*/
