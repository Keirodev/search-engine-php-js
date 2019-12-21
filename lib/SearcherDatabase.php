<?php


namespace cebe\jssearch;

class SearcherDatabase extends Searcher
{
    /**
     * @var SQLiteHelper
     */
    private $dbHelper;

    /**
     * Searcher constructor.
     * @param int $resultLimit
     */
    public function __construct($resultLimit = 20)
    {
        $this->resultLimit = $resultLimit;
        $this->dbHelper = new SQLiteHelper();
    }

    /**
     * Complete a word to known index (ex: lop => lopsem)
     * @return array
     */
    protected function completeWords()
    {
        $result = [];

        foreach ($this->words as $tokenizedWord) {
            $tokenizedWordAsObject = (object)$tokenizedWord;

            $word = $tokenizedWordAsObject->t;
            $wordLength = mb_strlen($word);

            // ignore word length <= 2
            if ($wordLength > 2) {

                // if words does not exists in our db
                if (!$this->dbHelper->searchExactWord($word)) {

                    // get all words in index with same start & give them a weight of 1
                    $wordsCompleted = $this->dbHelper->searchWordsStartingBy($word);

                    foreach ($wordsCompleted as $wordCompleted) {
                        $result[] = ['t' => $wordCompleted->word, 'w' => 1];
                    }
                } else {
                    // keep existing word
                    $result[] = $tokenizedWord;
                }
            }
        }

        return $result;
    }

    /**
     * Look for exact exact word in our index
     * @return array
     */
    protected function searchForWords()
    {
        $result = [];

        // transform to a string like "'example','exerci'"
        $wordsPreparedForSql = implode(",", array_map(function ($value) {
            return "'" . $value['t'] . "'";
        }, $this->words));
        /**
         * @var [object(url, title, w, f)] array
         */
        $dbResults = $this->dbHelper->searchWords($wordsPreparedForSql);

        foreach ($dbResults as $file) {

            $key = strval($file->f);
            if (array_key_exists($key, $result)) {
                $result[$key]['weight'] *= $file->w;
            } else {
                $result[$key] = [
                    'file' => ['url' => $file->url, 'title' => $file->title],
                    'weight' => $file->w
                ];
            }
        }

        return $result;
    }

}