<?php

$composerAutoload = [
    __DIR__ . '/vendor/autoload.php', // standalone with "composer install" run
    __DIR__ . '/../../autoload.php', // script is installed as a composer binary
];
foreach ($composerAutoload as $autoload) {
    if (file_exists($autoload)) {
        require($autoload);
        break;
    }
}


/**
 * This file return responses to a query (word want to find)
 */

use cebe\jssearch\tokenizer\StandardTokenizer;

/**
 * get $index & $files
 */
//$data = require_once('./jssearch.index.php');
require_once('./jssearch.index.php');


$query = 'completely';
//$query = 'compl';


// first we remove the stopWords & tokenize the string
//

/*
function tokenizeString($query) {

    $words = preg_split ("#[\s.,;:\\\/\[\](){}]+#", $query);
    $words = array_map(function ($value) {
        return strtolower($value);
    } , $words);
    $words = array_filter(function($value) {
        return in_array($value, \cebe\jssearch\tokenizer\StandardTokenizer::class)
    }, $words)
//    $sanitize = filter_var($query, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);


    return $words;

}*/

$tokenizedString = new StandardTokenizer();
$words = $tokenizedString->tokenize($query);

if (empty($words)) {
    return false;
}

$queryWords = array_map(function ($value) {
    return $value['t'];
}, $words);


// completeWords
function completeWords(array $words, array $index)
{
    $result = [];

    foreach ($words as $word) {
        $wordLength = mb_strlen($word);

        if (!array_key_exists($word, $index) && $wordLength > 2) {

            // complete this word adding all words available in the index with same start letters
            foreach ($index as $key => $indexTokenArray) {
                $indexWordSubstring = substr($key, 0, $wordLength);
                if ($indexWordSubstring === $word) {
                    $result[] = ['t' => $key, 'w' => 1];
                }
            }
        } else {
            // keep existing words
            // TODO WIP t / w  or f / w  ?? t = le mot alors que f = index
            $result[] = (array)$index[$word][0];
        }
    }

    return $result;
}


function searchForWords($words, $index, $files)
{
    $result = [];
    foreach ($words as $word) {
        var_dump($word[$index]);
        if (in_array($word, $index)) {

            foreach ($index[$word] as $file) {
                if ($result[$file['f']]) {
                    $result[$file['f']]['weight'] *= $file['w'] * $word['w'];
                } else {
                    $result[$file['f']] = [
                        'file' => $files[$file['f']],
                        'weight' => $file['w'] * $word['w'],
                    ];
                }
            }
        }
    }
    return $result;
}


var_dump(completeWords($queryWords, (array)$index));
//var_dump(searchForWords($queryWords, (array)$index, (array)$files));




