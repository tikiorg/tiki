<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
 
 
 require_once 'lib/ointegratelib.php';
 require_once 'Multilingual/Aligner/SentenceSegmentor.php'; 
 
class Multilingual_MachineTranslation_GoogleTranslateWrapper {

//this array should be updated as Google Translate
//adds more languages

	public $langsSupportedByGoogleTranslate = 
			array (
				'sq' => 'Albanian',
				'ar' => 'Arabic',
				'bg' => 'Bulgarian',
				'ca' => 'Catalan',
				'zh' => 'Chinese',
				'hr' => 'Croatian',
				'cs' => 'Czech',
				'da' => 'Danish',
				'nl' => 'Dutch',
				'en' => 'English',
				'et' => 'Estonian',
				'fil' => 'Filipino',
				'fi' => 'Finnish',
				'fr' => 'French',
				'gl' => 'Galician',
				'de' => 'German',
				'el' => 'Greek',
				'he' => 'Hebrew',
				'hi' => 'Hindi',
				'hu' => 'Hungarian',
				'id' => 'Indonesian',
				'it' => 'Italian',
				'ja' => 'Japanese',
				'ko' => 'Korean',
				'lv' => 'Latvian',
				'lt' => 'Lithuanian',
				'mt' => 'Maltese',
				'no' => 'Norwegian',
				'fa' => 'Persian',
				'pl' => 'Polish',
				'pt' => 'Portuguese',
				'ro' => 'Romanian',
				'ru' => 'Russian',
				'sr' => 'Serbian',
				'sk' => 'Slovak',
				'sl' => 'Slovenian',
				'es' => 'Spanish',
				'sv' => 'Swedish',
				'th' => 'Thai',
				'tr' => 'Turkish',
				'uk' => 'Ukrainian',
				'vi' => 'Vietnamese');

   	var $source_lang;
   	var $target_lang; 
   	var $google_ajax_url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0";
    
//	var $markup = "/<[^>]*>|[\`\!\@\#\$\%\^\&\*\{\[\}\]\:\;\"\'\<\,\>\.\?\/\|\\\=\-\+]{2,}|\{[\s\S]*?\}|\(\([\s\S]*?\)\)|\~[a-z]{2}\~[\s\S]*?\~\/[a-z]{2}\~|\~hs\~|\~\~[\s\S]*?\:|\~\~/";
	
	var $markup = "/<[^>]*>/";
		
   	var $escape_untranslatable_strings = "<span class='notranslate'>$0</span>";
   	
    var $notranslate_tag = "/(<span class='notranslate'>(.*)<\/span>)/U";
   	var $array_of_untranslatable_strings_and_their_ids = array();
   	var $current_id = 169;
   	
   	function __construct ($source_lang, $target_lang) {
   		$this->source_lang = $source_lang;
   		$this->target_lang = $target_lang;
   	}
   	
   	
   	function getLangsCandidatesForMachineTranslation($trads) {
   		global $langmapping, $prefs;
		$usedLangs = array();
		foreach( $trads as $trad )
			$usedLangs[] = $trad['lang'];
				
		$langsCandidatesForMachineTranslation = array();
		
		if (!empty($prefs['available_languages'])) {
		//restrict langs available for machine translation to those 
		//available on the site
			foreach ($prefs['available_languages'] as $availLang) {
				$langsCandidatesForMachineTranslationRaw[$availLang] = $langmapping[$availLang];
			}			
		} else {
			$langsCandidatesForMachineTranslationRaw = $langmapping;
		}
		
		//restrict langs available for machine translation to those
		//not already used for human translation
		foreach ( $usedLangs as $usedLang) 
			unset($langsCandidatesForMachineTranslationRaw[$usedLang]);
		
		
		//restrict langs available for machine translation to those 
		//available from Google Translate
		$langsSupportedByGoogleTranslate = $this->langsSupportedByGoogleTranslate;
		
		$i = 0;
		foreach (array_keys($langsCandidatesForMachineTranslationRaw) as $langCandidate) {
			if (in_array($langCandidate,array_keys($langsSupportedByGoogleTranslate))) {
				$langsCandidatesForMachineTranslation[$i]['lang'] = $langCandidate;
				$langsCandidatesForMachineTranslation[$i]['langName'] = $langsCandidatesForMachineTranslationRaw[$langCandidate][0];
				$i++;
			}
		}
		return $langsCandidatesForMachineTranslation;
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
   			$chunks = $this->splitInLogicalChunksOf450CharsMax($text);
   			$ii = 0;
   			while ($ii < sizeof($chunks)) {
   		  		$text_to_translate = $chunks[$ii];
				$chunk_translation = $this->getTranslationFromGoogle(urlencode($text_to_translate), urlencode($langpair))." ";
				$result .= $chunk_translation;
          		$ii++;
   			}  
   		}
 		$result = $this->remove_notranslate_tags_and_reverse_to_original_markup($result);
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
   	
   	function splitInLogicalChunksOf450CharsMax($text) {
   		$chunks = array();
   		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
   		$sentences = $segmentor->segment($text); 
   		$ii = 0;
   		$chunk = $sentences[$ii];
   		while ($ii < (sizeof($sentences)-1)) {
   			$ii++;
   			if (strlen (urlencode($chunk)) < 450) {
   				$chunk = $chunk.$sentences[$ii];
   			} else {
   				$chunks[] = $chunk; 
   				$chunk = $sentences[$ii];
   			}
   		}
   		$chunks[] = $chunk; 
   		return $chunks;
   		
   	}

	/* 
	 * Google Translate works best when wiki or html markup is first replaced with 
	 * a unique id (here something like this is used: id169) and then those ids 
	 * surrounded by Google's notranslate span tag. Upon translations span tags are 
	 * removed and ids reversed to the original markup. 
	 */

   	function escape_untranslatable_text($text) {
   		preg_match_all($this->markup, $text, $matches);
		foreach ($matches[0] as $matched_markup) {
			$id = array_search($matched_markup, $this->array_of_untranslatable_strings_and_their_ids);
			if ($id == false) {
				$id = (int)$this->current_id + 1;
				$this->array_of_untranslatable_strings_and_their_ids[$id]=$matched_markup;
				$this->current_id = $id;
			} 
		}
		
		foreach ($this->array_of_untranslatable_strings_and_their_ids as $id => $markup) {		
   			$id = "id".$id;

//adding dot after </ul> to have it segmented properly. otherwise when the html contains only lists, 
//sentence segmentor can't find where to segment the text
   			if ($markup == "</ul>") {
   				$text = preg_replace("/".preg_quote($markup,'/')."/", " ".$id.". ", $text);
   			} else {
   				$text = preg_replace("/".preg_quote($markup,'/')."/", " ".$id." ", $text);
   			}
		}
		
		$text = preg_replace("/(id[\d]+\.?(\s*id[\d]+)*)/", $this->escape_untranslatable_strings, $text);
		return $text;
   	}


	function remove_notranslate_tags_and_reverse_to_original_markup($text){
   		foreach ($this->array_of_untranslatable_strings_and_their_ids as $id => $markup) {
   			$id = "id".$id;
   			$text = preg_replace($this->notranslate_tag,'$2',$text);
			$text = preg_replace("/\s*$id\s*/", $markup, $text);
   		}
   		return $text;
	}


}
