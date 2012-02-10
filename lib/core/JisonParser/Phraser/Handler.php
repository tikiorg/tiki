<?php
class JisonParser_Phraser_Handler extends JisonParser_Phraser
{
	var $chars = array();
	var $words = array();
	var $currentWord = -1;
	var $wordsChars = array();
	var $indexes = array();
	var $parsed = "";
	var $cache = array();
	
	function tagHandler($tag)
	{
		return $tag;
	}
	
	function wordHandler($word)
	{
		$this->currentWord++;
		$this->words[] = $word;
		
		foreach($this->indexes as $i => $index) {
			if (
				$this->currentWord >= $index['start'] && 
				$this->currentWord <= $index['end']
			) {
				$word = '<span class="phrase phrase' . $i . '" style="border: none;">' . $word . '</span>';
			}
			
			if ($this->currentWord == $index['start']) {
				$word = '<span class="phraseStart phraseStart' . $i . '" style="border: none; font-weight: cold;"></span>' . $word;
			}
			
			if ($this->currentWord == $index['end']) {
				$word = $word . '<span class="phraseEnd phraseEnd' . $i . '" style="border: none;"></span>';	       						
			}
		}
		
		return $word;
	}
	
	function charHandler($char)
	{
		if (empty($this->wordsChars[$this->currentWord])) $this->wordsChars[$this->currentWord] = "";
		$this->wordsChars[$this->currentWord] .= $char;
		$this->chars[] = $char;
		
		foreach($this->indexes as $i => $index) {
			if (
				$this->currentWord >= $index['start'] && 
				$this->currentWord <= $index['end']
			) {
				$char = '<span class="phrases phrase' . $i . '" style="border: none;">' . $char . '</span>';
			}
		}
		
		
		return $char;
	}
	
	function isUnique($html, $phrase)
	{
		$htmlParts = $this->getParts($html);
        $phraseParts = $this->getParts($phrase);
   		
		$this->clearIndexes();
		
   		$this->addIndexes($phraseParts['words'], $htmlParts['words'], true);
        
        if (count($this->indexes) > 1) {
        	return false;
        } else {
        	return true;
        }
	}
	
	function findPhrases($parent, $phrases)
	{
       	$parentParts = $this->getParts($parent);
       	$phrasesParts = array();
		
		$this->clearIndexes();
		
       	foreach($phrases as $phrase) {
       		$phraseParts = $this->getParts($phrase);
			
			$this->addIndexes($phraseParts['words'], $parentParts['words']);
			$phrasesParts[] = $phraseParts;
       	}
		
		if (!empty($this->indexes)) {
			$parent = $this->parse($parent);
		}
		
		return $parent;
	}
	
	function matchLength($array1, $i, $array2, $j)
	{
    	$matchLength = 0;
    	$stop = false;
    	for(; $i < count($array1) && $stop == false; $i++){
			for(; $j < count($array2) && $stop == false; $j++){
				if ($array1[$i] == $array2[$j] || $this->match($array1[$i],$array2[$j]) == true) {
					$matchLength++;
					$i++;
				} else {
					$stop = true;
				}
			}
    	}
    	
    	return $matchLength;
    }
    
    function getParts($val)
    {
    	global $JisonParser_Phraser_Cache;
		if (!isset($JisonParser_Phraser_Cache)) $JisonParser_Phraser_Cache = array();
    	$words = array();
    	$chs = array();
    	$i = 0;
		
		if (!isset($JisonParser_Phraser_Cache[$val])) {
			$parser = new JisonParser_Phraser_Handler();
			$parser->parse($val);
			
			$JisonParser_Phraser_Cache[$val] = array(
	   			'words'=> $parser->words,
	   			'chs'=> $parser->wordsChars
	   		);
		}
		
		$parser = new JisonParser_Phraser_Handler();
		$parser->parse($val);
		
   		return $JisonParser_Phraser_Cache[$val];
    }
	
	function clearIndexes()
	{
		$this->indexes = array();
	}
	
	function addIndexes($phraseWords, $parentWords, $allMatches = false)
	{
        $phraseLength = count($phraseWords) - 1;
		$phraseConcat = implode($phraseWords, "|");
		$parentConcat = implode($parentWords, "|");
        
		$boundaries = explode($phraseConcat, $parentConcat);
		
		for($i = 0, $j = count($boundaries);$i < $j; $i++) {
			$boundaryLength = substr_count($boundaries[$i], "|");
			$this->indexes[] = array(
				"start"=> $boundaryLength,
				"end"=> $boundaryLength + $phraseLength
			);
			
			$i++;
		}
    }
	
	function match($subject, $pattern)
	{
		preg_match('/' . $pattern . '/', $subject, $match);
		
		return (empty($match) ? false : true);
	}
	
	static function hasPhrase($parent, $phrase)
	{
		$parent = self::sanitizeToWords($parent);
		$phrase = self::sanitizeToWords($phrase);

		$parent = implode('|', $parent);
		$phrase = implode('|', $phrase);
		
		return (strpos($parent, $phrase) !== false ? true : false);
	}
	
	static function sanitizeToWords($html)
	{
		$sanitized = preg_replace('/<(.|\n)*?>/', " ", $html);
		$sanitized = preg_replace('/\W/', " ", $sanitized);
		$sanitized = split(" ", $sanitized);
		$sanitized = array_filter($sanitized);
		
		return $sanitized;
	}
}
