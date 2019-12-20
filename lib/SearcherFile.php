<?php


namespace cebe\jssearch;

class SearcherFile extends Searcher
{
    private $index;
    private $files;

    /**
     * Searcher constructor.
     * @param object $index
     * @param object $files
     * @param int $resultLimit
     */
    public function __construct($index, $files, $resultLimit = 20)
    {
        $this->index = (array)$index;
        $this->files = $files;
        $this->resultLimit = $resultLimit;
    }

    /**
     * @return array|bool
     */
    protected function searchIn()
    {
        // complete words
        $this->words = $this->completeWords();

        //searchForWords
        $result = $this->searchForWords();

        // sort by weight
        usort($result, function ($a, $b) {

            $aWeight = $a['weight'];
            $bWeight = $b['weight'];

            return ($aWeight === $bWeight) ? 0 : ($aWeight < $bWeight) ? 1 : -1;
        });

        // return only the 'resultLimit' best results
        return array_slice($result, 0, $this->resultLimit);
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

            if (!array_key_exists($word, $this->index) && $wordLength > 2) {

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

            if (array_key_exists($tokenizedWord, $this->index)) {
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
            }
        }
        return $result;
    }

}