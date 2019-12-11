<?php

namespace cebe\jssearch;

use Exception;
use PDO;

class SQLiteHelper
{
    private $db;

    public function __construct()
    {
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
     * @param Indexer $indexer
     */
    public function insert(Indexer $indexer)
    {
//        var_dump($indexer->index);
        var_dump($indexer->files);
        exit();

        // TODO check fields
        // create tables
        $this->db->query("CREATE TABLE IF NOT EXISTS index ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        word VARCHAR(MAX),
	        f int,
	        w float
	        );"
        );

        $this->db->query("CREATE TABLE IF NOT EXISTS files ( 
            id INTEGER PRIMARY KEY AUTOINCREMENT,
	        url VARCHAR(MAX),
	        title VARCHAR(MAX)
	        );"
        );



        $stmt = $pdo->prepare("INSERT INTO posts (titre, created) VALUES (:titre, :created)");
        $result = $stmt->execute(array(
            'titre'			=> "Lorem ipsum",
            'created'		=> date("Y-m-d H:i:s")
        ));


    }
}