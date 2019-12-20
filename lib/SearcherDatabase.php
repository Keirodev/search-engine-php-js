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
     * @return array|bool
     */
    protected function searchIn()
    {
        // complete words
        $this->words = $this->completeWords();
        return $this->dbHelper->searchWords($this->words);
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

                // recherche le mot dans la DB
                $wordExistsInDb = $this->dbHelper->searchExactWord($word);

                // TODO : WIP
                var_dump($wordExistsInDb);
                exit();

                // if words does not exists in our db
                if (!array_key_exists($word, $this->index)) {

                    // complete this word adding all words available in the index with same start letters
                    foreach ($this->index as $wordIndex => $wordIndexTokenized) {
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
        foreach ($this->words as $tokenizedWord) {
            $tokenizedWordAsObject = (object)$tokenizedWord;
            $tokenizedWord = $tokenizedWordAsObject->t;

            /*if (array_key_exists($tokenizedWord, $this->index)) {
                foreach ($this->index[$tokenizedWord] as $file) {

                    $key = strval($file->f);
                    if (array_key_exists($key, $result)) {
                        $result[$key]['weight'] *= $file->w * $tokenizedWordAsObject->w;
                    } else {
                        $result[$key] = [
                            'file' => $this->files->{$key},
                            'weight' => $file->w * $tokenizedWordAsObject->w
                        ];
                    }
                }
            }*/
        }
        return $result;
    }

}