<?php

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
$data = require_once('data/search-engine-index.php');



$query = 'ips';
//$query = 'compl';

$searcher = new \cebe\jssearch\Searcher()
$searcher->getResults($query);


// THE END




