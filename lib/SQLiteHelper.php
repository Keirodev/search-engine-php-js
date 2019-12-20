<?php

namespace cebe\jssearch;

use Exception;
use PDO;

/**
 * Class SQLiteHelper
 * @package cebe\jssearch
 * @doc https://www.sqlitetutorial.net/
 */
class SQLiteHelper
{
    private $db;

    public function __construct()
    {
        // todo : choose output directory for the database
        // check if database exists, if not we create it
        try {
            $this->db = new PDO('sqlite:' . dirname(__FILE__) . '/engine.sqlite');
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
        } catch (Exception $e) {
            echo "Cannot access to SQLite DB : " . $e->getMessage();
            die();
        }
    }

    /**
     * @param Indexer $indexer (index, files)
     */
    public function insert(Indexer $indexer)
    {
        // create tables if they don't exist
        $this->db->query("CREATE TABLE IF NOT EXISTS `index` ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        word NOT NULL TEXT,
	        f INT,
	        w FLOAT
	        );"
        );
        $this->db->query("CREATE INDEX index_word ON `index`(word);");

        $this->db->query("CREATE TABLE IF NOT EXISTS `files` ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        url TEXT NOT NULL UNIQUE,
	        title TEXT
	        );"
        );


        // add data to database
        $nbErrors['files'] = 0;
        foreach ($indexer->files as $file) {

            $request = $this->db->prepare("INSERT INTO `files` (url, title) VALUES (:url, :title)");
            $result = $request->execute([
                'url' => $file['url'],
                'title' => $file['title']
            ]);

            if (!$result) {
                ++$nbErrors['files'];
            }
        }

        $nbErrors['index'] = 0;
        foreach ($indexer->index as $word => $details) {

            // a word can be found in various files, so it cantains several $details
            foreach ($details as $detail) {
                $request = $this->db->prepare("INSERT INTO `index` (word, f, w) VALUES (:word, :f, :w)");
                $result = $request->execute([
                    'word' => $word,
                    'f' => $detail['f'],
                    'w' => $detail['w']
                ]);

                if (!$result) {
                    ++$nbErrors['index'];
                }
            }
        }

        if ($nbErrors['index'] > 0 || $nbErrors['files']) {
            var_dump($nbErrors);
        }
    }

    /**
     * @param array $words
     * @return array [url, title] array
     */
    public function searchWords($words)
    {
        $request = $this->db->prepare("
            select
               substr(url, 3) as url
             , title
            from \"index\" i
            join files f on i.f = f.id
            where word in (:words)
            order by w desc"
        );

        $request->execute(['words' => explode(',', $words)]);
        return $request->fetchAll();
    }

    /**
     * @param $word
     * @return bool
     */
    public function searchExactWord($word) {
        $request = $this->db->prepare("
            select 
                case when EXISTS (select word from `index` where word = :word)
                    then CAST(1 AS BIT)
                    else CAST(0 AS BIT) 
                END AS isFound"
        );

        $request->execute(['word' => $word]);
        return (bool)$request->fetch(PDO::FETCH_OBJ)->isFound;
    }
}