<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
 require_once 'lib/ointegratelib.php';
 
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
   		$url = $this->google_ajax_url."&q=".urlencode($text)."&langpair=".urlencode($langpair);
        $ointegrate = new OIntegrate();
        $oi_result = $ointegrate->performRequest($url);
        $result = $oi_result->data['responseData']['translatedText'];
        return $result;		
   	} 
   	
}
?>
