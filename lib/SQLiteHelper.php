<?php

namespace cebe\jssearch;

use Exception;
use PDO;
use PDOException;

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
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
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
        $this->db->beginTransaction();
        // create tables if they don't exist
        $this->db->query("CREATE TABLE IF NOT EXISTS `index` ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        word TEXT NOT NULL,
	        f INT,
	        w FLOAT
	        );"
        );
        $this->db->query("CREATE INDEX IF NOT EXISTS index_word ON `index`(word);");
        $this->db->query("CREATE TABLE IF NOT EXISTS `files` ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        url TEXT NOT NULL UNIQUE,
	        title TEXT
	        );"
        );
        $this->db->commit();

        // add data to database
        // FILES
        $nbErrors['files'] = 0;
        $dataToInsert = [];
        $nbInsert = 0;

        foreach ($indexer->files as $file) {
            array_push($dataToInsert,
                [
                    $this->db->quote($file['url']),
                    $this->db->quote($file['title'])
                ]);
            ++$nbInsert;
        }

        $dataToInsert = array_map(function ($entryToInsert) {
            return '(' . implode(',', $entryToInsert) . ')';
        },$dataToInsert);

        $sql = 'INSERT INTO `files` (url, title) VALUES ' . implode(',', $dataToInsert);
        $this->db->beginTransaction();

        try {
            $this->db->query($sql);
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            ++$nbErrors['files'];
        }
        echo "Processing - Inserting a batch of $nbInsert enties in files\n";


        // Test to split in batch of max entries (ex : INSERT per batch of 10.000 entries)
        /*for ($i = 1; $i <= $nbFiles; $i++) {
            $nbFilesLoop = 0;
            // prepare a batch of $maxInsert values to insert
            $dataToInsert = [];
            while ($i <= $nbFiles && $nbFilesLoop < $maxInsert) {
                array_push($dataToInsert,
                    [
                        "'" . $indexer->files[$i]['url'] . "'"
                        , "'" . $indexer->files[$i]['title'] . "'"
                    ]);
                $i++;
                $nbFilesLoop++;
            }
            $i--; // because the previous $i++ will double the for ($i++ on next loop)

            $dataToInsert = array_map(function ($entryToInsert) {
                return '(' . implode(',', $entryToInsert) . ')';
            },$dataToInsert);

            $sql = 'INSERT INTO `files` (url, title) VALUES ' . implode(',', $dataToInsert);

            $this->db->beginTransaction();
            try {
                $this->db->query($sql);
                $this->db->commit();
            } catch (PDOException $e) {
                $this->db->rollBack();
                ++$nbErrors['files'];
            }
            echo "Processing - Inserting a batch of $nbFilesLoop enties in files\n";
        }*/

        // INDEX prepare batch of $maxInsert values to insert
        $nbErrors['index'] = 0;
        $dataToInsert = [];
        $nbIndexLoop = 0;

        foreach ($indexer->index as $word => $details) {
            // a word can be found in various files, so it contains several $details
            foreach ($details as $detail) {
                array_push($dataToInsert,
                    [
                        $this->db->quote($word)
                        , $detail['f']
                        , $detail['w']
                    ]);
                $nbIndexLoop++;
            }
        }

        // from ['foo', 'bar'] to ["'foo', 'bar'"]
        $dataToInsert = array_map(function ($entryToInsert) {
            return '(' . implode(',', $entryToInsert) . ')';
        },$dataToInsert);

        $sql = 'INSERT INTO `index` (word, f, w) VALUES ' . implode(',', $dataToInsert);

        $this->db->beginTransaction();
        try {
            $this->db->query($sql);
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            ++$nbErrors['index'];
        }
        echo "Processing - Inserting a batch of $nbIndexLoop entries in index\n";

        if ($nbErrors['index'] > 0 || $nbErrors['files']) {
            var_dump($nbErrors);
        }
    }

    /**
     * @param string $words
     * @return array [url, title, w, f] array
     */
    public function searchWords($words)
    {
        $request = $this->db->query("
            select
               substr(url, 3) as url
             , title
             , w
             , f.id as f
            from `index` i
                join files f on i.f = f.id
            where word in ($words)
            order by w desc"
        );

        return $request->fetchAll();
    }

    /**
     * @param $wordToComplete
     * @return array [$word] array
     */
    public function searchWordsStartingBy($wordToComplete)
    {
        // NOTE :  || in sql is a concatenation operator
        $request = $this->db->prepare("select distinct word from `index` where word like :word || '%'");
        $request->execute(['word' => $wordToComplete]);
        return $request->fetchAll();
    }

    /**
     * @param $word
     * @return bool
     */
    public function searchExactWord($word)
    {
        $request = $this->db->prepare("
            select 
                case when EXISTS (select word from `index` where word = :word)
                    then CAST(1 AS BIT)
                    else CAST(0 AS BIT) 
                END AS isFound"
        );

        $request->execute(['word' => $word]);
        return (bool)$request->fetch()->isFound;
    }
}