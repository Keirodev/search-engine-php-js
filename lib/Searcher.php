<?php


namespace cebe\jssearch;

use cebe\jssearch\tokenizer\StandardTokenizer;

abstract class Searcher
{
    protected $words;
    protected $resultLimit;

    public function getResults($query)
    {
        $result = $this->search($query);

        header('Content-Type: application/json;charset=utf-8');
        header('Status: 200');
        echo json_encode($result);
    }

    /**
     * Main search function, acting like a Controller
     * @param string $query
     * @return array|bool
     */
    protected function search($query)
    {
        // Tokenize the query (= separate words) and remove stop words
        $tokenizedString = new StandardTokenizer();
        $this->words = $tokenizedString->tokenize($query);

        if (empty($this->words)) {
            return false;
        }

        return $this->searchIn();
    }

    /**
     * Must implement the search logic in files or database
     * @return array
     */
    abstract protected function searchIn();

    abstract protected function completeWords();

    /**
     * Look for exact exact word in our index
     * @return array
     */
    abstract protected function searchForWords();
}
