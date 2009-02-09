<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
 require_once 'lib/ointegratelib.php';
 require_once 'Multilingual/Aligner/SentenceSegmentor.php'; 
 
class Multilingual_MachineTranslation_GoogleTranslateWrapper {
   	var $source_lang;
   	var $target_lang; 
   	var $google_ajax_url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0";
   	
   	function __construct ($source_lang, $target_lang) {
   		$this->source_lang = $source_lang;
   		$this->target_lang = $target_lang;
   	}
   	
   	
   	function translateText($text) {
   		$langpair = $this->source_lang."|".$this->target_lang;
   		$urlencoded_text = urlencode($text); 
   		$result = "";
   		$chunks = array();
   		if (strlen($urlencoded_text) < 1800) {
   			$result = $this->getTranslationFromGoogle($urlencoded_text, urlencode($langpair));
   		} else {
   			$chunks = $this->splitInLogicalChunksOf1800CharsMax($text);
   			$ii = 0;
   			while ($ii < sizeof($chunks)) {
   		  		$text_to_translate = $chunks[$ii];	
   		  		$result .= $this->getTranslationFromGoogle(urlencode($text_to_translate), urlencode($langpair))." ";
          		$ii++;
   			}  
   		}
		return trim($result);
   	}
   	
   	function translateSentenceBySentence($text) {
   		$langpair = $this->source_lang."|".$this->target_lang;
   		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
   		$sentences = $segmentor->segment($text); 
   		$ii = 0;
   		$result = "";
   		while ($ii < sizeof($sentences)) {
   		  $text_to_translate = $sentences[$ii];	
   		  $result .= $this->getTranslationFromGoogle(urlencode($text_to_translate), urlencode($langpair));
          $ii++;
   		}  
        
        return $result;		
   	} 
   	
   	function getTranslationFromGoogle($encoded_text, $encoded_langpair) {
   		$url = $this->google_ajax_url."&q=".$encoded_text."&langpair=".$encoded_langpair;
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
}
?>
