<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Created on Jan 27, 2009
 *
 */

class Multilingual_MachineTranslation_GoogleTranslateWrapper implements Multilingual_MachineTranslation_Interface
{
  	const SERVICE_URL = "https://www.googleapis.com/language/translate/v2";

	//wiki markup (keep this regex in case we decide to translate wiki markup and not html)
	//	const WIKI_MARKUP = "/<[^>]*>| ?[\`\!\@\#\$\%\^\&\*\[\]\:\;\"\'\<\,\>\/\|\\\=\-\+\_\(\)]{2,} ?|\(\([\s\S]*?\)\)|\~[a-z]{2,3}\~[\s\S]*?\~\/[a-z]{2,3}\~|\~hs\~|\~\~[\s\S]*?\:|\~\~|[[^\|]*?\||\[[^|\]]*\]|\{\*[^\}\*]*?\*\}|\{[^\}]*?\}|^;|!/m";
	const WIKI_MARKUP = "/<[^>]*>| ?[\`\!\@\#\$\%\^\&\*\[\]\:\;\"\'\<\,\>\/\|\\\=\-\+\_\(\)]{2,} ?|\(\([\s\S]*?\)\)|\~\/?[a-z]{2,3}\~|\~hs\~|\~\~[\s\S]*?\:|\~\~|[[^\|]*?\||\[[^|\]]*\]|\{\*[^\}\*]*?\*\}|\{[^\}]*?\}|^;|!/m";

	//Google doesn't return parens upon translation
	//Include spaces in markup (Google adds some, and they will be stripped later. Want to preserve the original ones)
	const HTML_MARKUP = "/ ?<[^>]*> ?| ?\(|\) ?/";
	const TITLE_TAG = "/(<[Hh][\d][^>]*>(<[^>]*>)*)([^<]*)/";
  	const NO_TRANSLATE_STRING = "<span class='notranslate'>\$0</span>";
	const NO_TRANSLATE_PATTERN = "/ <span class='notranslate'>(.*)<\/span> |^<span class='notranslate'>(.*)<\/span> | <span class='notranslate'>(.*)<\/span>\$|^<span class='notranslate'>(.*)<\/span>\$|<span class='notranslate'>(.*)<\/span>/Um";

	private $key;
  	private $sourceLang;
  	private $targetLang;
  	private $markup;
  	private $translatingHTML = true;
	private $arrayOfUntranslatableStringsAndTheirIDs = array();
	private $currentID = 169;

	function __construct ($key, $sourceLang, $targetLang, $html = true)
	{
		$this->key = $key;
		$this->sourceLang = $sourceLang;
		$this->targetLang = $targetLang;
		if ($html) {
			$this->markup = self::HTML_MARKUP;
		} else {
			$this->translatingHTML = false;
			$this->markup = self::WIKI_MARKUP;
		}
	}


	function getSupportedLanguages()
	{
		return array(
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
			'vi' => 'Vietnamese'
		);
	}


	function translateText($text)
	{
		$text = $this->escape_untranslatable_text($text);

		$urlencodedText = urlencode($text);

		if (strlen($urlencodedText) < 1800) {
			$chunks = array($text);
		} else {
			$chunks = $this->splitInLogicalChunksOf450CharsMax($text);
		}

		$result = "";
		foreach ($chunks as $textToTranslate) {
			$result .= $this->getTranslationFromGoogle($textToTranslate)." ";
		}

		$result = $this->remove_notranslateTags_and_reverse_to_original_markup($result);
		return trim($result);
	}


	private function translateSentenceBySentence($text)
	{
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
		$sentences = $segmentor->segment($text);
		$result = "";
		foreach ($sentences as $textToTranslate) {
			$result .= $this->getTranslationFromGoogle($textToTranslate);
		}

		return $result;
	}

	private function getTranslationFromGoogle($text)
	{
		require_once 'lib/ointegratelib.php';
		$ointegrate = new OIntegrate();
		$params = array(
			'key' => $this->key,
			'target' => $this->targetLang,
			'q' => $text,
			'format' => ($this->markup === self::HTML_MARKUP) ? 'html' : 'text',
		);

		if ($this->sourceLang != Multilingual_MachineTranslation::DETECT_LANGUAGE) {
			$params['source'] = $this->sourceLang;
		}

		$url = self::SERVICE_URL . '?' . http_build_query($params, '', '&');

		$oi_result = $ointegrate->performRequest($url);
		$result = $oi_result->data['data']['translations'];

		return implode(
			'',
			array_map(
				function ($entry) {
					return $entry['translatedText'];
				},
				$result
			)
		);
	}

	private function splitInLogicalChunksOf450CharsMax($text)
	{
		$chunks = array();
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
		$sentences = $segmentor->segment($text);
		$ii = 0;
		$chunk = $sentences[$ii];
		while ($ii < (count($sentences)-1)) {
			$ii++;
			if (strlen(urlencode($chunk)) < 450) {
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
	private function escape_untranslatable_text($text)
	{
		//Title is all between <hx> tags. Put it in lower case, so Google doesn't
		//take the capitalized words as proper names
		if (preg_match_all(self::TITLE_TAG, $text, $matchesT) !=0 ) {
			foreach ($matchesT[0] as $i => $completeMatch) {
				$text = str_replace($completeMatch, $matchesT[1][$i].strtolower($matchesT[3][$i]), $text);
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
				$this->arrayOfUntranslatableStringsAndTheirIDs[$id] = $matched_markup;
				$this->currentID = $id;
			}
		}

		foreach ($this->arrayOfUntranslatableStringsAndTheirIDs as $id => $markup) {
			$id = "id".$id;

			//adding dot after </ul> to have it segmented properly. otherwise when the html contains only lists,
			//sentence segmentor can't find where to segment the text
			if ($markup == "</ul>") {
				$text = preg_replace("/".preg_quote($markup, '/')."/", $id.".", $text);
			} else {
				$text = preg_replace("/".preg_quote($markup, '/')."/", $id, $text);
			}
		}

		$text = preg_replace("/(id[\d]+\.?(id[\d]+)*)/", self::NO_TRANSLATE_STRING, $text);
		return $text;
	}


	private function remove_notranslateTags_and_reverse_to_original_markup($text)
	{
		//Google adds spaces before and after notranslate span

		preg_match_all(self::NO_TRANSLATE_PATTERN, $text, $matches);

		foreach ($matches as $i => $match) {
			foreach ($matches[0] as $index => $found) {
				if (!empty($match[$index])) {
					$text = str_replace($found, $match[$index], $text);
				}
			}
		}

		foreach ($this->arrayOfUntranslatableStringsAndTheirIDs as $id => $markup) {
			$id = "id".$id;
			$text = preg_replace("/$id/", $markup, $text); //str replace better
		}

		//trimming leading spaces in each line (wiki syntax doesn't work unless)
		if (!$this->translatingHTML) {
			$textArray = preg_split('/\<br(\s*)?\/?\>/i', $text);
			$textArray = array_map('trim', $textArray);
			$text = implode("\n", $textArray);
		}
		return $text;
	}
}
