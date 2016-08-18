<?php
/**
 * This file contains a subclass of the Zend_Search_Lucene_Analysis_TokenFilter class.
 * Its purpose is to help provide a corresponding PHP implementation of the Standard analyzer for
 * the Java implementation of Lucene. This filter, in conjunction with the filters also provided
 * in this standard analyzer package, provide a method for indexing documents with word Stemming,
 * lower-casing, and number handling. The lower-case and number handling is provided by the pre-
 * existing filters from Zend.
 *
 * License: see License.txt for a copy of the Zend License.
 *
 */

class StandardAnalyzer_Analysis_TokenFilter_EnglishStemmer implements ZendSearch\Lucene\Analysis\TokenFilter\TokenFilterInterface
{
    /**
     * Stop Words
     * @var array
     */
    /**
     * Constructs new instance of this filter.
     *
     * @param array $stopwords array (set) of words that will be filtered out
     */
	public function __construct()
	{
	}

    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param ZendSearch\Lucene\Analysis\Token $srcToken
     * @return ZendSearch\Lucene\Analysis\Token
     */
	public function normalize(ZendSearch\Lucene\Analysis\Token $srcToken)
	{

		$newToken = new ZendSearch\Lucene\Analysis\Token(
			PorterStemmer::stem($srcToken->getTermText()),
			$srcToken->getStartOffset(),
			$srcToken->getEndOffset()
		);

		$newToken->setPositionIncrement($srcToken->getPositionIncrement());

		return $newToken;
	}
}

