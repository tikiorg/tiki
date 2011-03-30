<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class LanguageTranslations extends TikiDb_Bridge
{
	/**
	 * @var string language code
	 */
	public $lang;

	/**
	 * @var string path to the language file
	 */
	protected $filePath;
	
	/**
	 * Whether or not there is translations saved in the
	 * database for this language 
	 * 
	 * @var string
	 */
	public $hasDbTranslations = false;
	
	/**
	 * Set the language based on the paratemer or if
	 * no parameter given uses Tiki user or global preference
	 */
	function __construct($lang = null) {
		global $user, $user_preferences;

		if (!is_null($lang)) {
			$this->lang = $lang;
		} else if (isset($user) && isset($user_preferences[$user]['language'])) {
			$this->lang = $user_preferences[$user]['language'];
		} else {
			global $prefs;
			$this->lang = $prefs['language'];
		}
		
		$this->filePath = "lang/{$this->lang}/language.php";
	}

	/**
	 * Update a translation
	 * If $originalStr is not found, a new entry is added. Otherwise, 
	 * if $translatedStr is empty the entry is deleted, if $translatedStr
	 * is not empty but is equal to the actual translation nothing is done or if
	 * $translatedStr is not empty and different from the actual translation
	 * the entry is updated with the new translation.
	 *
	 * @param string $originalStr the original string
	 * @param string $translatedStr the translated string
	 * @return void
	 */
	public function updateTrans($originalStr, $translatedStr) {
		global ${"lang_$this->lang"}, $user, $tikilib;

		// only the user name is globally available? not the user_id?
		$userId = $tikilib->get_user_id($user);

		// initialize language (used when this function is called by tiki-interactive_translation.php)
		if (!isset(${"lang_$this->lang"})) {
			init_language($this->lang);
		}

		// don't change anything if $originalStr and $translatedStr are equal
		if ($originalStr == $translatedStr) {
			return;
		}
		
		// don't change anything in the database if the translation hasn't been changed
		if (isset(${"lang_$this->lang"}[$originalStr]) && ${"lang_$this->lang"}[$originalStr] == $translatedStr) {
			return;
		}

		$query = 'select * from `tiki_language` where `lang`=? and binary `source` = ?';
		$result = $this->query($query, array($this->lang, $originalStr));

		if (!$result->numRows()) {
			$query = 'insert into `tiki_language` (`source`, `lang`, `tran`, `changed`, `userId`, `lastModif`) values (?,?,?,?,?,?)';
			$result = $this->query($query, array($originalStr, $this->lang, $translatedStr, 1, $userId, $tikilib->now));
		} else {
			if (strlen($translatedStr) == 0) {
				$query = 'delete from `tiki_language` where binary `source`=? and `lang`=?';
				$result = $this->query($query, array($originalStr, $this->lang));
			} else {
				$query = 'update `tiki_language` set `tran`=?, `changed`=?, `userId`=?, `lastModif`=? where binary `source`=? and `lang`=?';
				$result = $this->query($query, array($translatedStr, 1, $userId, $tikilib->now, $originalStr, $this->lang));
			}
		}

		// remove from untranslated table if present
		$query = "delete from `tiki_untranslated` where binary `source`=? and `lang`=?";
		$this->query($query, array($originalStr, $this->lang));
	}

	/**
	 * Write the new translated strings to the actual
	 * language.php file and remove the translations
	 * from the database
	 *
	 * @return array number of modified strings (key 'modif') and new 
	 * strings (key 'new') or null if not possible to write to file
	 */
	public function writeLanguageFile() {
		set_time_limit(0);
		
		if (is_writable($this->filePath)) {
			$langFile = file($this->filePath);
			$dbTrans = $this->_getDbTranslationsEscaped();
			$stats = array('modif' => 0, 'new' => 0);

			// add new strings to the language.php
			$lastStr = array_search("\"###end###\"=>\"###end###\");\n", $langFile);

			if ($lastStr === FALSE) {
				// file has no line with "###end###\"=>\"###end###\") marking the end of the array
				throw new Exception(tra("The file lang/$this->lang/language.php is not well formated. Run get_strings.php?lang=$this->lang and then try to export the translations again."));
			}			
			
			// foreach translation in the database check each string in the language.php file
			// if the original string is present and the translation is diferent replace it
			//TODO: improve the algorithm (it interact over each entry in language.php file for each entry in the database)
			foreach ($dbTrans as $dbOrig => $dbNewStr) {
				foreach ($langFile as $key => $line) {
					// match a translate or untranslated string in a language.php file
					if (preg_match('|^/?/?\s*?"(.+)"\s*=>\s*"(.+)".*|', $line, $matches) && $matches[1] == $dbOrig) {
						// do something only if the new translation is different from the old translation
						if ($matches[2] != $dbNewStr) {
							$langFile[$key] = '"' . $matches[1] . '" => "' . $dbNewStr . "\",\n";
							
							// count number of new and updated strings
							if (strpos($line, '//') === 0) {
								$stats['new']++;
							} else {
								$stats['modif']++;
							}
						}
						unset($dbTrans[$dbOrig]);
					}
				}
			}

			// convert every entry in the array $dbTrans (translation that are not presente in language.php)
			// to a string in the format '"original string" => "translation"'
			$newTrans = array();
			foreach ($dbTrans as $orig => $trans) {
				$newTrans[] = '"' . $orig . '" => "' . $trans . "\",\n";
				$stats['new']++;
			}

			array_splice($langFile, $lastStr, 0, $newTrans);

			// write the new language.php file
			$f = fopen($this->filePath, 'w');

			foreach ($langFile as $line) {
				fwrite($f, $line);
			}

			fclose($f);
			$this->deleteTranslations();

			return $stats;
		} else {
			throw new Exception(sprintf(tra('ERROR: unable to write to lang/%s/language.php'), $this->lang));
		}
	}

	/**
	 * Return all the custom translations in the database
	 * for the current language and the original translations
	 * from language.php if existent. Also set $this->hasDbTranslations
	 * property to true if one or more translations exist.
	 *
	 * @param string $sort_mode
	 * @param int $maxRecords
	 * @param int $offset
	 * @param bool $originalTranslations if true include for each database translation the original translation from language.php
	 * @param string $searchQuery if set limit the results to 
	 * @return array
	 */
	protected function _getDbTranslations($sort_mode = 'source_asc', $maxRecords = -1, $offset = 0, $originalTranslations = false, $searchQuery = null) {
		global $tikilib;
		
		if ($originalTranslations) {
			// load $lang with all translations excluding database translations to compare changes
			$lang = array();
			require("lang/$this->lang/language.php");
			
			if (is_file("lang/$this->lang/custom.php")) {
				include_once("lang/$this->lang/custom.php");
			}
			
			global $tikidomain;
			if (!empty($tikidomain) && is_file("lang/$this->lang/$tikidomain/custom.php")) {
				include_once("lang/$this->lang/$tikidomain/custom.php");
			}
		}
		
		$bindvars = array($this->lang);
		
		$query = "SELECT * FROM `tiki_language` WHERE `lang`=? AND `source` != '' AND `changed` = 1 $searchQuery ORDER BY " . $this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);

		if (isset($result->numrows) && $result->numrows > 0) {
			$this->hasDbTranslations = true;
		}
		
		$translations = array();
		
		while ($res = $result->fetchRow()) {
			if ($res['userId']) {
				$res['user'] = $tikilib->get_user_login($res['userId']);
			}

			if ($originalTranslations && isset($lang[$res['source']]) && $lang[$res['source']] != $res['tran']) {
				require_once('lib/diff/difflib.php');
				$res['originalTranslation'] = $lang[$res['source']];
				$res['diff'] = diff2($res['originalTranslation'], $res['tran'], 'htmldiff');
			}

			$translations[$res['source']] = $res;
		}

		return $translations;
	}

	/**
	 * Return all the custom translations in the database with
	 * special characters escaped
	 *
	 * @return array
	 */
	protected function _getDbTranslationsEscaped() {
		require_once('lib/language/Language.php');
		
		$trans = $this->_getDbTranslations();
		$escapedTrans = array();

		foreach ($trans as $item) {
			$escapedTrans[Language::addPhpSlashes($item['source'])] = Language::addPhpSlashes($item['tran']);
		}

		return $escapedTrans;
	}

	/**
	 * Delete all the translations from the current language
	 */
	public function deleteTranslations() {
		$this->query('DELETE FROM `tiki_language` WHERE `lang`=?', array($this->lang));
	}

	/**
	 * Delete one translations from database
	 * 
	 * @param string $source original string
	 * @return void
	 */
	public function deleteTranslation($source) {
		$query = "delete from `tiki_language` where binary `source`=? and `lang`=?";
		$result = $this->query($query, array($source, $this->lang));
	}
	
	/**
	 * Create a custom.php file for the current language
	 * and remove the translations from the database
	 *
	 * @return string the content of the new custom.php file
	 */
	public function createCustomFile() {
		$strings = $this->_getDbTranslationsEscaped();

		$data = "<?php\n\$lang=";
		$data .= $this->_removeSpaces(var_export($strings, true));
		$data .= ";\n?>\n";

		return $data;
	}

	/**
	 * Remove the spaces added by var_export() in the beggining
	 * of the line to be similar with the file generated by get_strings.php
	 *
	 * @param string $data the content of a new php file with the translations
	 * @return string same as $data but without spaces in the beggining of the line
	 */
	protected function _removeSpaces($data) {
		return preg_replace('/^  /m', '', $data);
	}

	/**
	 * Return database translations
	 *
	 *  @param string $sort_mode
	 *  @param int $maxRecords
	 *  @param int $offset
	 *  @param string $search return only results that matches the searched string
	 *  @return array database translations ('translations' and 'total')
	 */
	public function getDbTranslations($sort_mode, $maxRecords, $offset, $search = null) {
		global $tikilib;
		
		$translations = array();
		$bindvars = array($this->lang);
		$searchQuery = '';

		if ($search) {
			$searchQuery = " and (`source` like '$search' or `tran` like '$search')";
		}

		$translations = $this->_getDbTranslations($sort_mode, $maxRecords, $offset, true, $searchQuery);

		$query = "select count(*) from `tiki_language` where `lang`=? $searchQuery";
		$total = $this->getOne($query, $bindvars);

		return array('translations' => $translations, 'total' => $total);
	}

	/**
	 * Delete all entries from unstranslated database (tiki_untranslatated)
	 * for the current language
	 * 
	 * @return void
	 */
	public function deleteAllUntranslated() {
        $this->query("DELETE FROM `tiki_untranslated` WHERE `lang` = ?", array($this->lang));
	}
	
	/**
	 * Return recorded untranslated strings (if feature
	 * "record_untranslated is enabled)
	 *
	 * @param string $sort_mode
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search return only results that matches the searched string
	 * @return array recorded untranslated strings ('translations' and 'total')
	 */
	public function getRecordedUntranslated($sort_mode, $maxRecords, $offset, $search = null) {
		global $prefs;

		if ($prefs['record_untranslated'] != 'y') {
			return;
		}

		$translations = array();
		$bindvars = array($this->lang);
		$searchQuery = '';

		if ($search) {
			$searchQuery = " and `source` like ?";
			$bindvars[] = '%' . $search . '%';
		}

		$query = "select * from `tiki_untranslated` where `lang`=? $searchQuery order by " . $this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);

		while ($res = $result->fetchRow()) {
			$translations[] = $res;
		}

		$query = "select count(*) from `tiki_untranslated` where `lang`=? $searchQuery";
		$total = $this->getOne($query, $bindvars);

		return array('translations' => $translations, 'total' => $total);
	}

	/**
	 * Return all translations (db + custom.php + language.php)
	 *
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search return only results that matches the searched string
	 * @return array translations
	 */
	public function getAllTranslations($maxRecords, $offset, $search = null) {
		global ${"lang_$this->lang"};
		
		if (!isset(${"lang_$this->lang"})) {
			init_language($this->lang);
		}

		$all_translations = ${"lang_$this->lang"};

		// display only translations that match the searched string
		if (isset($search) && strlen($search) > 0) {
			$pattern = "/.*$search.*/i";

			// search source strings
			$keys = preg_grep($pattern, array_keys($all_translations));
			$sources = array();
			foreach ($keys as $key) {
				$sources[$key] = $all_translations[$key];
			}

			// search translation strings
			$all_translations = preg_grep($pattern, $all_translations);

			$all_translations = array_merge($all_translations, $sources);
		}

		$total = count($all_translations);
		$translations = array_slice($all_translations, $offset, $maxRecords);
		
		$translations = $this->_convertTranslationsArray($translations);
		
		return array('translations' => $translations, 'total' => $total);
	}
	
	/**
	 * Get all translations (db + custom.php + language.php) plus
	 * untranslated strings from language.php
	 * 
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search return only results that matches the searched string
	 * @return array translations and untranslated strings
	 */
	public function getAllStrings(/*$maxRecord, $offset, $search = null*/)
	{
		$translations = $this->getAllTranslations(100, 0);
		$untranslated = $this->getUntranslatedFromFile();
		
		// merge the two arrays overwriting untranslated strings that
		// have been translated in the database
		$strings = array_merge($untranslated, $translations);
		
		ksort($strings);
		
		return $strings;
	}
	
	/**
	 * Convert the translations array from the format used all over Tiki (where
	 * the source string is the key and the translation is the value of one entry of an
	 * array) to the format used on tiki-edit_languages.php (a two dimensional array with 
	 * more information for database translations)
	 * 
	 * @param array $translations in the format used all over Tiki and created by init_language()
	 * @return array $newFormat translations in the new format used by tiki-edit_language.php
	 */
	protected function _convertTranslationsArray($translations) {
		$newFormat = array();
		
		$dbTranslations = $this->_getDbTranslations('source_asc', -1, 0, true);

		foreach ($translations as $source => $tran) {
			$newItem = array();
			
			// if string has been changed in the database
			if (isset($dbTranslations[$source])) {
				$newItem = $dbTranslations[$source];
			} else {
				$newItem['tran'] = $tran;
				$newItem['source'] = $source;
			}
			
			$newFormat[] = $newItem;
		}
		
		return $newFormat;
	}

	/**
	 * Return a Cachelib object. Used to be able to
	 * mock cachelib por test purposes.
	 * 
	 * @return Cachelib cachelib object
	 */
	protected function getCacheLib()
	{
		return TikiLib::lib('cache');
	}
	
	/**
	 * Parse a language.php file to get the untranslated strings,
	 * store the strings in a cache file and return them.
	 * 
	 * The untranslated strings are store in the keys of an array
	 * that has null values.
	 * 
	 * @return array untranslated strings
	 */
	public function getUntranslatedFromFile()
	{
		$cachelib = $this->getCacheLib();
		$hash = md5_file($this->filePath);
		$cacheKey = 'untranslatedStrings.' . $this->lang . $hash;
		$info = $cachelib->getSerialized($cacheKey, 'untranslatedStrings');

		if ($info) {
			return $info;
		}
		
		$contents = file($this->filePath);
		$untranslated = array();
		
		foreach ($contents as $line) {
			// match untranslated string in a language.php file
			if (preg_match('|^//\s*?"(.+)"\s*=>\s*".+".*|', $line, $matches)) {
				$untranslated[$matches[1]] = null;
			}
		}
		
		$cachelib->cacheItem($cacheKey, serialize($untranslated), 'untranslatedStrings');
		
		return $untranslated;
	}	
}