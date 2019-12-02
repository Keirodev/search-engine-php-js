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

//class tokenizedWord extends stdClass {public $t; public $w;}
// $test = new tokenizedWord($tokenizedWord);
function completeWords(array $words, array $index)
{
    $result = [];

    foreach ($words as $tokenizedWord) {
        $tokenizedWordAsObject = (object)$tokenizedWord;

        $word = $tokenizedWordAsObject->t;
        $wordLength = mb_strlen($word);

        if (!array_key_exists($word, $index) && $wordLength > 2) {

            // complete this word adding all words available in the index with same start letters
            foreach ($index as $wordIndex => $wordIndexTokenized) {
                $indexWordSubstring = substr($wordIndex, 0, $wordLength);
                if ($indexWordSubstring === $word) {
                    $result[] = ['t' => $wordIndex, 'w' => 1];
                }
            }
        } else {
            // keep existing word
            $result[] = $tokenizedWord;
        }
    }

    return $result;
}


function searchForWords(array $words, array $index, $files)
{
    $result = [];
    foreach ($words as $tokenizedWord) {
        $tokenizedWordAsObject = (object)$tokenizedWord;
        $tokenizedWord = $tokenizedWordAsObject->t;

        if (array_key_exists($tokenizedWord, $index)) {
            foreach ($index[$tokenizedWord] as $file) {

                $key = strval($file->f);
                if (array_key_exists($key, $result)) {
                    $result[$key]['weight'] *= $file->w * $tokenizedWordAsObject->w;
                } else {
                    $result[$key] = [
                        'file' => $files->{$key},
                        'weight' => $file->w * $tokenizedWordAsObject->w
                    ];
                }
            }
        }
    }
    return $result;
}


$query = 'compl';
//$query = 'compl';


$tokenizedString = new StandardTokenizer();
$words = $tokenizedString->tokenize($query);

if (empty($words)) {
    return false;
}

/*
 $queryWords = array_map(function ($value) {
    return $value['t'];
}, $words);
*/

// complete words
$words = completeWords($words, (array)$index);

//searchForWords
$result = searchForWords($words, (array)$index, $files);

var_dump($result);

// sort by weight


// THE END


//var_dump(searchForWords($queryWords, (array)$index, (array)$files));




