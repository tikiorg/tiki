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
   	var $wiki_markup = array ("(===)");
//   	var $wiki_markup = array(
//   	"(___)", "(::)", "(~~\w+:)", "(~~)", "(\")", "(-+)","(+-)", "(===)", "(\^)", "(\{[^\{\}]\})", "(--)", 
//   	"(~(\/)?np~)", "(\*)", "(-=)", "(=-)");
   	var $escape_untranslatable_strings = "<span class='notranslate'> $0 </span>";
   	
   	var $notranslate_tag_left = "/(<span class='notranslate'>(.*)<\/span>\s)/U";
   	var $notranslate_tag_right = "/(\s<span class='notranslate'>(.*)<\/span>)/U";
   	
   	function __construct ($source_lang, $target_lang) {
   		$this->source_lang = $source_lang;
   		$this->target_lang = $target_lang;
   	}
   	
   	
   	function translateText($text) {
   		$langpair = $this->source_lang."|".$this->target_lang;
   		$text = $this->escape_untranslatable_text($text);
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
 		$result = $this->remove_notranslate_tags($result);
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
   	
   	function escape_untranslatable_text($text) {
   		return preg_replace($this->wiki_markup, $this->escape_untranslatable_strings, $text);
   	}

	function remove_notranslate_tags($text){
		$text = preg_replace($this->notranslate_tag_left,'$2',$text);
   		$text = preg_replace($this->notranslate_tag_right,'$2',$text);
   		return $text;
	}
}
?>
