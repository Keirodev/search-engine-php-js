<?php

use cebe\jssearch\Searcher;

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
 * get $index & $files
 */
require_once('data/search-engine-index.php');

/**
 * Search query
 */
if (isset($_GET['query'])) {

    $query = htmlspecialchars($_GET['query']);

    $hasLimit = (isset($_GET['limit']) && is_numeric(htmlspecialchars($_GET['limit'])));
    if ($hasLimit) {
        $searcher = new Searcher($index, $files, (int)htmlspecialchars($_GET['limit']));
    } else {
        $searcher = new Searcher($index, $files);
    }

    $searcher->getResults($query);
}



