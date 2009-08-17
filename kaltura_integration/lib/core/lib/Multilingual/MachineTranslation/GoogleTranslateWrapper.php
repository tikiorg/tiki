<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
 require_once 'lib/ointegratelib.php';
 require_once 'Multilingual/Aligner/SentenceSegmentor.php'; 
 
class Multilingual_MachineTranslation_GoogleTranslateWrapper {
   	var $sourceLang;
   	var $targetLang; 
   	var $markup;
   	var $translatingHTML = true;
   	var $googleAjaxUrl = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0";

//wiki markup (keep this regex in case we decide to translate wiki markup and not html)    
//	var $markup = "/<[^>]*>|[\s]?[\`\!\@\#\$\%\^\&\*\[\]\:\;\"\'\<\,\>\.\?\/\|\\\=\-\+\_\(\)]{2,}[\s]?|\{[^*]*?\}|\(\([\s\S]*?\)\)|\~[a-z]{2}\~[\s\S]*?\~\/[a-z]{2}\~|\~hs\~|\~\~[\s\S]*?\:|\~\~|[[^\|]*?\||\[[^|\]]*\]|\{[\*]?[^\}]*?[\*]?\}|\*|;/";

	var $wikiMarkup = "/<[^>]*>| ?[\`\!\@\#\$\%\^\&\*\[\]\:\;\"\'\<\,\>\/\|\\\=\-\+\_\(\)]{2,} ?|\(\([\s\S]*?\)\)|\~[a-z]{2}\~[\s\S]*?\~\/[a-z]{2}\~|\~hs\~|\~\~[\s\S]*?\:|\~\~|[[^\|]*?\||\[[^|\]]*\]|\{\*[^\}\*]*?\*\}|\{[^\}]*?\}|^;|!/m";

//Google doesn't return parens upon translation
//Include spaces in markup (Google adds some, and they will be stripped later. Want to preserve the original ones)
	var $htmlMarkup = "/ ?<[^>]*> ?| ?\(|\) ?/";

	var $titleTag = "/(<[Hh][\d][^>]*>(<[^>]*>)*)([^<]*)/";
	
		
   	var $escapeUntranslatableStrings = "<span class='notranslate'>$0</span>";
   	
    var $notranslateTag = "/(<span class='notranslate'>(.*)<\/span>)/U";
	var $notranslateTagWithSpaces = "/ <span class='notranslate'>(.*)<\/span> |^<span class='notranslate'>(.*)<\/span> | <span class='notranslate'>(.*)<\/span>$|^<span class='notranslate'>(.*)<\/span>$|<span class='notranslate'>(.*)<\/span>/Um";
   	var $arrayOfUntranslatableStringsAndTheirIDs = array();
   	var $currentID = 169;
   	
   	function __construct ($sourceLang, $targetLang, $html=true) {
   		$this->sourceLang = $sourceLang;
   		$this->targetLang = $targetLang;
   		if($html) {
   			$this->markup = $this->htmlMarkup;
   		} else {
   			$this->translatingHTML = false;
   			$this->markup = $this->wikiMarkup;
   		}
   	}
   	
   	
   	function translateText($text) {
   		$langpair = $this->sourceLang."|".$this->targetLang;
   		$text = $this->escape_untranslatable_text($text);
   		$urlencodedText = urlencode($text); 
   		$result = "";
   		$chunks = array();
   		if (strlen($urlencodedText) < 1800) {
   			$result = $this->getTranslationFromGoogle($urlencodedText, urlencode($langpair));
   		} else {
   			$chunks = $this->splitInLogicalChunksOf1800CharsMax($text);
   			$ii = 0;
   			while ($ii < sizeof($chunks)) {
   		  		$textToTranslate = $chunks[$ii];
				$chunkTranslation = $this->getTranslationFromGoogle(urlencode($textToTranslate), urlencode($langpair))." ";
				$result .= $chunkTranslation;
          		$ii++;
   			}  
   		}
 		$result = $this->remove_notranslateTags_and_reverse_to_original_markup($result);
		return trim($result);
   	}
   	
   	function translateSentenceBySentence($text) {
   		$langpair = $this->sourceLang."|".$this->targetLang;
   		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
   		$sentences = $segmentor->segment($text); 
   		$ii = 0;
   		$result = "";
   		while ($ii < sizeof($sentences)) {
   		  $textToTranslate = $sentences[$ii];	
   		  $result .= $this->getTranslationFromGoogle(urlencode($textToTranslate), urlencode($langpair));
          $ii++;
   		}  
        
        return $result;		
   	} 
   	
   	function getTranslationFromGoogle($encodedText, $encodedLangpair) {
   		$url = $this->googleAjaxUrl."&q=".$encodedText."&langpair=".$encodedLangpair;
        $ointegrate = new OIntegrate();
        $oi_result = $ointegrate->performRequest($url);
        $result = $oi_result->data['responseData']['translatedText'];
   		return $result;
   	}
   	
   	function splitInLogicalChunksOf1800CharsMax($text) {
   		$chunks = array();
   		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
   		$sentences = $segmentor->segment($text); 
   		$ii = 0;
   		$chunk = $sentences[$ii];
   		while ($ii < (sizeof($sentences)-1)) {
   			$ii++;
   			if (strlen (urlencode($chunk)) < 1800) {
   				$chunk = $chunk.$sentences[$ii];
   			} else {
   				$chunks[] = $chunk; 
   				$chunk = $sentences[$ii];
   			}
   		}
   		$chunks[] = $chunk; 
   		return $chunks;
   		
   	}
   	
   	function escape_untranslatable_text($text) {
		//Title is all between <hx> tags. Put it in lower case, so Google doesn't
		//take the capitalized words as proper names
		if (preg_match_all($this->titleTag, $text, $matchesT) !=0 ){
			$i = 0;
			while ($i < count($matchesT[0])) {
				$text = str_replace($matchesT[0][$i], $matchesT[1][$i].strtolower($matchesT[3][$i]), $text);
				$i++;
			}
		}
		
		if (!$this->translatingHTML) {
			$text = nl2br($text);
		}
   		preg_match_all($this->markup, $text, $matches);
   		
		foreach ($matches[0] as $matched_markup) {
			$id = array_search($matched_markup, $this->arrayOfUntranslatableStringsAndTheirIDs);
			if ($id == false) {
				$id = (int)$this->currentID + 1;
				$this->arrayOfUntranslatableStringsAndTheirIDs[$id]=$matched_markup;
				$this->currentID = $id;
			} 
		}
		
		foreach ($this->arrayOfUntranslatableStringsAndTheirIDs as $id => $markup) {		
   			$id = "id".$id;

//adding dot after </ul> to have it segmented properly. otherwise when the html contains only lists, 
//sentence segmentor can't find where to segment the text
   			if ($markup == "</ul>") {
   				$text = preg_replace("/".preg_quote($markup,'/')."/", $id.".", $text);
   			} else {
				$text = preg_replace("/".preg_quote($markup,'/')."/", $id, $text);
   			}
		}
		
		$text = preg_replace("/(id[\d]+\.?(id[\d]+)*)/", $this->escapeUntranslatableStrings, $text);
		return $text;
   	}


	function remove_notranslateTags_and_reverse_to_original_markup($text){
		//Google adds spaces before and after notranslate span
		
		preg_match_all($this->notranslateTagWithSpaces, $text, $matches);

   		$i=1;
   		while ($i < count($matches)) {
   			$index = 0;
   			while ($index < count($matches[0])) {
   				if (!empty($matches[$i][$index])) {
					$text = str_replace($matches[0][$index],$matches[$i][$index],$text);
   				}
   				$index++;
   			}
		$i++;
   		}
		
   		foreach ($this->arrayOfUntranslatableStringsAndTheirIDs as $id => $markup) {
   			$id = "id".$id;
			$text = preg_replace("/$id/", $markup, $text); //str replace better 
   		}
   		
   		//trimming leading spaces in each line (wiki syntax doesn't work unless)
		if (!$this->translatingHTML) {
			$textArray = explode("<br />", $text);
			array_walk($textArray,array(&$this, '_trim'));
			$text = implode("<br />", $textArray);
			$text = $this->br2nl($text);
		}
//		$text = str_replace("<br />",'<br />\n',$text);
   		return $text;
	}
	
	
	function mynl2br($text) {
   		$text = strtr($text, array('\n' => '<br />', '\r\n' =>'<br />'));
   		return $text;
	}
	
	public function _trim(&$value) {
    	$value = trim($value);   
	}

	function br2nl($string) {
    	return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}

}
