<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
// Utility functions for language, translation related controllers

class Services_Language_Utilities
{
	/**
	 * Attach (link) translation source object to target object (eg: relation between two wiki pages that are translations of each other)
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $source The id of an instance of the object type, eg: a wiki page id
	 * @param int $target The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return
	 */
	function insertTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$sourceLang = $this->getLanguage($type, $source);
		$sourceId = $this->toInternalId($type, $source);

		$targetLang = $this->getLanguage($type, $target);
		$targetId = $this->toInternalId($type, $target);

		$out = $multilinguallib->insertTranslation($type, $sourceId, $sourceLang, $targetId, $targetLang);

		return !$out;
	}

	/**
	 * Detach (unlink) translation source object from target object (eg: relation between two wiki pages that are translations of each other)
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $source The id of an instance of the object type, eg: a wiki page id
	 * @param int $target The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return
	 */
	function detachTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$targetId = $this->toInternalId($type, $target);

		$multilinguallib->detachTranslation($type, $targetId);
	}

	/**
	 * Get translations of an object
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $object The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return array List of language codes, eg: en, hu, de, etc
	 */
	function getTranslations($type, $object)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$langLib = TikiLib::lib('language');

		$objId = $this->toInternalId($type, $object);

		$translations = $multilinguallib->getTrads($type, $objId);
		$languages = $langLib->get_language_map();

		foreach ($translations as & $trans) {
			$trans['objId'] = $this->toExternalId($type, $trans['objId']);
			$trans['language'] = $languages[$trans['lang']];
		}

		return $translations;
	}

	/**
	 * Get language for an object
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $object The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return string A language code, eg: en
	 *
	 * @throws Services_Exception
	 */
	function getLanguage($type, $object)
	{
		$lang = null;
		switch ($type) {
			case 'wiki page':
				$info = TikiLib::lib('tiki')->get_page_info($object);
				$lang = $info['lang'];
				break;
			case 'article':
				$info = TikiLib::lib('art')->get_article($object);
				$lang = $info['lang'];
				break;
			case 'trackeritem':
				$info = TikiLib::lib('trk')->get_tracker_item($object);
				$definition = Tracker_Definition::get($info['trackerId']);

				if ($field = $definition->getLanguageField()) {
					$lang = $info[$field];
				}
				break;
			case 'forum post':
				$object = TikiLib::lib('comments')->get_comment_forum_id($object);
			// no break: drop through to forum
			case 'forum':
				$info = TikiLib::lib('comments')->get_forum($object);
				$lang = $info['forumLanguage'];
				break;
		}

		if (!$lang) {
			throw new Services_Exception(tr('The object has no language indicated and cannot be translated'), 400);
		}

		return $lang;
	}

	/**
	 * Get id of a tiki object TODO: this should not be here, should be called from a core controller
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $object The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return int
	 */
	private function toInternalId($type, $object)
	{
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			return $tikilib->get_page_id_from_name($object);
		} else {
			return $object;
		}
	}

	/**
	 * Get name of a tiki object TODO: this should not be here, should be called from a core controller
	 *
	 * @param string $type Tiki object type, eg: wiki page
	 * @param int $object The id of an instance of the object type, eg: a wiki page id
	 *
	 * @return
	 */
	private function toExternalId($type, $object)
	{
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			return $tikilib->get_page_name_from_id($object);
		} else {
			return $object;
		}
	}

	/**
	 * Get formatted list of languages
	 *
	 * @param string $language Optional language code (eg: en)
	 *
	 * @return array $languages List of languages with names
	 *
	 * @internal param $none
	 *
	 */
	function getLanguages($language = '')
	{
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages(false, null, true);
		return $languages;
	}

	/**
	 * Get language directory generally and for a specific language too
	 *
	 * @param string $language Optional language code (eg: en)
	 *
	 * @return string $langDir The directory path languages or to a language specifically
	 *
	 */
	function getLanguageDirectory($language = '')
	{
		$langDir = "lang/";

		if (!empty($language)) {
			$langDir .= "$language/";
		}

		global $tikidomain;
		if (!empty($tikidomain)) {
			$langDir .= "$tikidomain/";
		}

		return $langDir;
	}

	/**
	 * Check if lang/ directory is readable generally and for a specific language too
	 *
	 * @param $language Optional language code (eg: en)
	 *
	 * @return boolean $langDirIsReadable True/False
	 *
	 * @throws exception
	 */
	function checkLangDirIsReadable($language = '')
	{
		$directory = $this->getLanguageDirectory($language);
		$langDirIsReadable = is_readable($directory);

		if (!$langDirIsReadable) {
			throw new Services_Exception(tra('The language directory is not readable'), 400);
		} else {
			return $langDirIsReadable;
		}
	}

	/**
	 * Check if lang/ directory is writeable generally and for a specific language too
	 *
	 * @param $language Language code (eg: en)
	 *
	 * @return boolean $langDirIsWritable True/False
	 *
	 * @throws exception
	 */
	function checkLangDirIsWritable($language = '')
	{
		$directory = $this->getLanguageDirectory($language);
		$langDirIsWritable = is_writable($directory);

		if (!$langDirIsWritable) {
			throw new Services_Exception(tra('The language directory is not writeable'), 400);
		} else {
			return $langDirIsWritable;
		}
	}

	/**
	 * Get the count of database stored translations for a language
	 *
	 * @param string $language Language code (eg: en)
	 *
	 * @return int $db_translation_count Count of translations in the database
	 */
	function getDbTranslationCount($language)
	{
		$db_language = new LanguageTranslations($language);
		$db_language_translations = $db_language->getDbTranslations();
		$db_translation_count = $db_language_translations["total"];

		return $db_translation_count;
	}

	/**
	 * Get translations from the custom.php file for a language
	 *
	 * @param string $language Language code (eg: en)
	 *
	 * @return array $lang_custom The array of translations from the custom.php file for the language
	 */
	function getCustomPhpTranslations($language)
	{
		$custom_file = $this->getLanguageDirectory($language);
		$custom_file .= 'custom.php';

		if (file_exists($custom_file)) {
			include($custom_file);
			if ($lang_custom) {
				return $lang_custom;
			}
		} else {
			return null;
		}
	}

	/**
	 * Get the count of items in the custom.php translation file for a language
	 *
	 * @param string $language Language code (eg: en)
	 *
	 * @return int $item_count The count of translation items in the the custom.php file for the language
	 */
	function getCustomPhpTranslationCount($language)
	{
		$lang_array = $this->getCustomPhpTranslations($language);
		if (is_null($lang_array)) {
			return 0;
		} else {
			$item_count = count($lang_array);
			return $item_count;
		}
	}

	/**
	 * Write translations to the custom.php file for a language TODO: error handling and error display
	 *
	 * @param string $language Language code (eg: en)
	 * @param array $data Set of translations
	 *
	 * @return boolean True
	 *
	 * @throws exception
	 */
	function writeCustomPhpTranslations($language, $data)
	{
		//prepare custom file path
		$custom_file = $this->getLanguageDirectory($language);

		//add file name
		$custom_file .= 'custom.php';

		//prepare php file
		$custom_code = "<?php\r\n\$lang_custom = array(\r\n";

		if (!is_array($data)) {
			throw new Services_Exception(tr('String translation set is not an array'), 400);
		}

		//add translations
		foreach ($data as $from => $to) {
			if (!empty($from)) {
				$custom_code .= '"' . str_replace('"', '\\"', $from) . '" => "' . str_replace('"', '\\"', $to) . "\",\r\n";
			}
		}

		//finish php file
		$custom_code .= ");\r\n";

		//Commented out as it thorws php fatal error (unsupported operand)
		//$custom_code .= '$lang = $lang_custom + $lang;';

		//write the strings to custom.php file
		if (!($fp = fopen($custom_file, 'w+'))) {
			throw new Services_Exception(tra('Can not fopen custom.php'), 400);
		} else {
			if (!fwrite($fp, $custom_code)) {
				throw new Services_Exception(tra('Can not fwrite custom.php'), 400);
			}
			fclose($fp);
			return true;
		}
	}

	/**
	 * Get the default file name for JSON formatted custom translation file
	 *
	 * @return string custom.json
	 *
	 */
	function getJsonCustomTranslationFileName()
	{
		$jsonCustomTranslationFileName = 'custom.json';
		return $jsonCustomTranslationFileName;
	}

	/**
	 * Create a translation file from php array to JSON for a language into the lang directory
	 *
	 * @param string $language Language code (eg: en)
	 * @param array $translation A set of translations
	 *
	 * @return boolean True
	 *
	 */
	function writeJsonTranslationFile($language, $translations)
	{
		//convert translation to JSON
		$jsonTranslations = json_encode($translations);
		//check if lang directory is writeable
		$isWritable = $this->checkLangDirIsWritable($language);
		if ($isWritable) {
			//get file name
			$file = $this->getJsonCustomTranslationFileName();
			//open file, if it does not exist, create it
			$file = fopen($file, 'w+');
			//write JSON
			fwrite($file, $jsonTranslations);
			//close file
			fclose($file);
			//return true to indicate success
			return true;
		}
	}

	/**
	 * Get an existing JSON custom translation file from lang directory for a language
	 *
	 * @param string $language Language code (eg: en)
	 *
	 * @return boolean True
	 *
	 */
	function getCustomJsonTranslationFile($language)
	{
		//check if language directory is readable
		$isReadable = $this->checkLangDirIsReadable($language);
		if ($isReadable) {
			//set file name
			$file = $this->getJsonCustomTranslationFileName();
			//open file, if it does not exist, create it
			$file = fopen($file, 'w+');
			//write JSON
			fwrite($file, $jsonTranslations);
			//close file
			fclose($file);
			//return true to indicate success
			return true;
		}
	}

	/**
	 * Convenience function to process two sets of string translations for a language in various ways
	 *
	 * @param array $sourceStringTranslationSet The source set of translation strings
	 * @param array $targetStringTranslationSet The target set of translation strings
	 * @param char $process_type How to process the the two set of translations
	 *
	 * @return array $updatedStringTranslationSet
	 *
	 */
	function processStringTranslationSets($sourceStringTranslationSet, $targetStringTranslationSet, $process_type)
	{
		//validate input
		if (!is_array($sourceStringTranslationSet)) {
			throw new Services_Exception(tr('Source string translation set is not an array'), 400);
		}
		if (!is_array($targetStringTranslationSet)) {
			throw new Services_Exception(tr('Target string translation set is not an array'), 400);
		}
		//merge means that existing translations in the target are replaced and new ones are added to the set
		if ($process_type === 'merge') {
			$updatedStringTranslationSet = array_merge($targetStringTranslationSet, $sourceStringTranslationSet);
		}
		//diff means that only those items are returned from source that are not in target
		elseif ($process_type === 'diff') {
			$updatedStringTranslationSet = array_diff($sourceStringTranslationSet, $targetStringTranslationSet);
		}
		//intersect_merge means that values for matching keys are overwritten, but values for keys in the source set that do not exist in the target set are not added
		elseif ($process_type === 'intersect_merge') {
			$updatedStringTranslationSet = array_intersect($targetStringTranslationSet, $sourceStringTranslationSet);
			$updatedStringTranslationSet = array_merge($updatedStringTranslationSet, $sourceStringTranslationSet);
		}
		//replace means that source set is replace by the target set (you will probably just overwrite the variable in your function. This option is left here not to run into error in case for some reason the type is called)
		elseif ($process_type === 'replace') {
			$updatedStringTranslationSet = $sourceStringTranslationSet;
		}
		else {
			throw new Services_Exception(tr('Invalid process type'), 400);
		}
		return $updatedStringTranslationSet;
	}

	/**
	 * Convenience function to get list of process types for string translations sets
	 *
	 * @return array $stringTranslationSetProcessTypes
	 *
	 */
	//define upload types for the uploaded file
	function getStringTranslationSetProcessTypes()
	{
		$stringTranslationSetProcessTypes = array(
			'merge' => tra('Add new and update existing'),
			'diff' => tra('Add new only'),
			'intersect_merge' => tra('Update existing only'),
			'replace' => tra('Replace all'),
		);
		return $stringTranslationSetProcessTypes;
	}
}

