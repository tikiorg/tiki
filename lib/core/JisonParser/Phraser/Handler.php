<?php
class JisonParser_Phraser_Handler extends JisonParser_Phraser
{
	var $chars = array();
	var $words = array();
	var $tags = array();
	var $currentWord = -1;
	var $wordsChars = array();
	var $indexes;
	var $parsed = "";
	
	function tagHandler($tag)
	{
		$this->tags[] = $tag;
		return $tag;
	}
	
	function wordHandler($word)
	{
		$this->currentWord++;
		$this->words[] = $word;
		
		if (!empty($this->indexes)) {
			if (
				$this->currentWord >= $this->indexes['start'] && 
				$this->currentWord <= $this->indexes['end']
			) {
				$word = '<span class="phrase" style="border: none;">' . $word . '</span>';
			}
			
			if ($this->currentWord == $this->indexes['start']) {
				$word = '<span class="phraseStart" style="border: none;"/>' . $word;
			}
			
			if ($this->currentWord == $this->indexes['end']) {
				$word = $word . '<span class="phraseEnd" style="border: none;"/>';	       						
			}
		}
		
		return $word;
	}
	
	function charHandler($char)
	{
		if (empty($this->wordsChars[$this->currentWord])) $this->wordsChars[$this->currentWord] = '';
		$this->wordsChars[$this->currentWord] .= $char;
		$this->chars[] = $char;
		
		if (!empty($this->indexes)) {
			if (
				$this->currentWord > $this->indexes['start'] && 
				$this->currentWord < $this->indexes['end']
			) {
				$char = '<span class="phrase new" style="border: none;">' . $char . '</span>';
			}
		}
		
		return $char;
	}
	
	function isUnique($html, $phrase)
	{
		$htmlParts = $this->getParts($html);
        $phraseParts = $this->getParts($phrase);
   		
   		$indexes = $this->phraseIndexes($phraseParts['words'], $htmlParts['words'], true);
        
        if (count($indexes) > 1) {
        	return false;
        } else {
        	return true;
        }
	}
	
	function findPhrases($html, $phrase)
	{
       	$htmlParts = $this->getParts($html);
       	$phraseParts = $this->getParts($phrase);
		
	    $this->indexes = $this->phraseIndexes($phraseParts['words'], $htmlParts['words']);
		
		if (!empty($this->indexes)) {
	        if ($this->indexes['start'] > -1 && $this->indexes['end'] > -1) {
				$html = $this->parse($html);
			}
		}
		
		return $html;
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
    	$words = array();
    	$chs = array();
    	$i = 0;
		
		$parser = new JisonParser_Phraser_Handler();
		$parser->parse($val);
		
   		return array(
   			'words'=> $parser->words,
   			'chs'=> $parser->wordsChars
   		);
    }
	
	function phraseIndexes($phraseWords, $parentWords, $allMatches = false)
	{
    	$start = -1;
    	$stop = false;
        $y = count($phraseWords) - 1;
        $matches = array();
        
        for ($i = 0; $i < count($parentWords) && $stop == false; $i++) {
        	if ($this->match($parentWords[$i], $phraseWords[0]) == true) {
        		$l = $this->matchLength($parentWords, $i, $phraseWords, 0);
        		if ($l > 10 || $l == count($phraseWords)) {
        			$matches[] = array(
        				"start"=> $i,
        				"end"=> $i + $y
        			);
        		}
        	}
        }
        
        if ($allMatches == false) {
        	return $matches[0];
        } else {
        	return $matches;
        }
    }
	
	function match($subject, $pattern)
	{
		preg_match('/' . $pattern . '/', $subject, $match);
		
		return (empty($match) ? false : true);
	}
}
