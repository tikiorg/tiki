<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

require_once('Exception.php');

/**
 * @package   Tiki
 * @subpackage    Language
 * Provides methods to update translations in the database and
 * to export translations from the database to language.php
 * files. Used by Interactive Translation and tiki-edit_languages.php
 */
class LanguageTranslations extends TikiDb_Bridge
{
	/**
	 * @var string language code
	 */
	public $lang;

	/**
	 * @var string path to language.php file
	 */
	protected $filePath;

	/**
	 * @var string path to custom.php file
	 */
	protected $customFilePath;

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
	function __construct($lang = null)
	{
		global $user, $user_preferences;

		if (! is_null($lang)) {
			$this->lang = $lang;
		} elseif (isset($user) && isset($user_preferences[$user]['language'])) {
			$this->lang = $user_preferences[$user]['language'];
		} else {
			global $prefs;
			$this->lang = $prefs['language'];
		}

		$this->filePath = "lang/{$this->lang}/language.php";
		$this->customFilePath = "lang/{$this->lang}/custom.php";
	}

	/**
	 * Update a translation
	 * If $originalStr is not found, a new entry is added. Otherwise,
	 * if $translatedStr is empty the entry is deleted,
	 * or if $translatedStr is not empty the entry is updated with the new translation (if needed).
	 *
	 * @param string $originalStr the original string
	 * @param string $translatedStr the translated string
	 * @param array $optionalParameters Associative array of extra parameters:
	 * 	bool|null 'general': true if the translation can be contributed to Tiki, false if it is specific to this instance
	 * @return null
	 */
	public function updateTrans($originalStr, $translatedStr, $optionalParameters = [])
	{
		global ${"lang_$this->lang"}, $user, $tikilib;

		$general = null; // default value
		foreach ($optionalParameters as $name => $value) {
			switch ($name) {
				case 'general':
					if (! is_null($value) && ! is_bool($value)) {
						throw new InvalidArgumentException('general should be a boolean.');
					}
					$general = $value;
					$generalDefinedByCaller = true;
					break;
				default:
					throw UnexpectedValueException;
			}
		}
		/** @var boolean $generalDefinedByCaller true if the $general variable's value was defined by the caller, to distinguish the caller passing null from the caller not passing anything */
		$generalDefinedByCaller = isset($generalDefinedByCaller);

		// only the user name is globally available? not the user_id?
		$userId = $tikilib->get_user_id($user);

		// initialize language (used when this function is called by tiki-interactive_translation.php)
		if (! isset(${"lang_$this->lang"})) {
			init_language($this->lang);
		}

		// don't change anything if $originalStr and $translatedStr are equal
		if ($originalStr == $translatedStr) {
			return;
		}

		// If the translation is not in the database and the new translation is the same as the translation defined by the filesystem, ignore it (do not insert in the database)
		if (isset(${"lang_$this->lang"}[$originalStr]) && ${"lang_$this->lang"}[$originalStr] == $translatedStr) {
			{
			static $initialDatabaseTranslations = [];

				// Build $initialDatabaseTranslations for the given language
			if (! isset($initialDatabaseTranslations[$this->lang])) {
				$initialDatabaseTranslationsForThisLanguage = [];
				$resultSet = $this->query('SELECT `source`, `tran` FROM `tiki_language` WHERE lang=?', [$this->lang]);
				while ($row = $resultSet->fetchRow()) {
					$initialDatabaseTranslationsForThisLanguage[$row['source']] = $row['tran'];
				}
				$initialDatabaseTranslations[$this->lang] = $initialDatabaseTranslationsForThisLanguage;
			}
			}

			if (! isset($initialDatabaseTranslations[$this->lang][$originalStr])) {
				return;
			}
		}

		$query = 'select * from `tiki_language` where `lang`=? and binary `source` = ?';
		$result = $this->query($query, [$this->lang, $originalStr]);

		if (! $result->numRows()) {
			$query = 'insert into `tiki_language` (`source`, `lang`, `tran`, `changed`, `general`, `userId`, `lastModif`) values (?, ?, ?, ?, ?, ?, ?)';
			$result = $this->query($query, [$originalStr, $this->lang, $translatedStr, 1, $general, $userId, $tikilib->now]);
		} else {
			if (strlen($translatedStr) == 0) {
				$query = 'delete from `tiki_language` where binary `source`=? and `lang`=?';
				$result = $this->query($query, [$originalStr, $this->lang]);
			} else {
				$query = 'update `tiki_language` set `tran`=?, `changed`=?, `userId`=?, `lastModif`=?';
				if ($generalDefinedByCaller) {
					$query .= ', `general`=?';
				}
				$query .= ' where binary `source`=? and `lang`=?';

				$boundVariables = [$translatedStr, 1, $userId, $tikilib->now];
				if ($generalDefinedByCaller) {
					array_push($boundVariables, $general);
				}
				array_push($boundVariables, $originalStr, $this->lang);

				$result = $this->query($query, $boundVariables);
			}
		}

		// remove from untranslated table if present
		$query = 'delete from `tiki_untranslated` where binary `source`=? and `lang`=?';
		$this->query($query, [$originalStr, $this->lang]);
	}

	/**
	 * Write the new translated strings to the actual
	 * language.php file and remove the translations
	 * from the database
	 *
	 * @param bool $generalOnly true if only strings to contribute upstream should be considered, false for all
	 * @return array number of modified strings (key 'modif') and new
	 * strings (key 'new') or null if not possible to write to file
	 */
	public function writeLanguageFile($generalOnly = false)
	{
		set_time_limit(0);

		if (is_writable($this->filePath)) {
			$langFile = file($this->filePath);
			if ($generalOnly) {
				$translationsFilter = 'AND `general`=TRUE';
			} else {
				$translationsFilter = '';
			}
			$dbTrans = self::PHPEscape($this->_getDbTranslations('source_asc', -1, 0, false, $translationsFilter));
			$stats = ['modif' => 0, 'new' => 0];

			// add new strings to the language.php
			$lastStr = array_search(");\n", $langFile);

			if ($lastStr === false) {
				// file has no line with "###end###\"=>\"###end###\") marking the end of the array
				throw new Language_Exception(
					tr("The file lang/%0/language.php is not correctly formatted. Run get_strings.php?lang=%0 and then try to export the translations again.", $this->lang)
				);
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

			// convert every entry in the array $dbTrans (translations that are not present in language.php)
			// to a string in the format '"original string" => "translation"'
			$newTrans = [];
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
			$this->deleteTranslations($generalOnly);

			return $stats;
		} else {
			throw new Exception(sprintf(tra('Error: unable to write to lang/%s/language.php'), $this->lang));
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
	 * @param string $searchQuery SQL query fragment to limit the results
	 * @return array
	 */
	protected function _getDbTranslations(
		$sort_mode = 'source_asc',
		$maxRecords = -1,
		$offset = 0,
		$originalTranslations = false,
		$searchQuery = ''
	) {

		if ($originalTranslations) {
			// load $lang with all translations excluding database translations to compare changes
			$lang = $this->getFileTranslations();
		}

		$bindvars = [$this->lang];

		$query = "SELECT * FROM `tiki_language` WHERE `lang`=? AND `source` != '' AND `changed` = 1 $searchQuery ORDER BY " .
								$this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);

		if (isset($result->numrows) && $result->numrows > 0) {
			$this->hasDbTranslations = true;
		}

		$translations = [];

		while ($res = $result->fetchRow()) {
			if ($res['userId']) {
				$tikilib = TikiLib::lib('tiki');
				$res['user'] = $tikilib->get_user_login($res['userId']);
			}

			if ($originalTranslations && isset($lang[$res['source']]) && $lang[$res['source']]['tran'] != $res['tran']) {
				require_once('lib/diff/difflib.php');
				$res['originalTranslation'] = $lang[$res['source']]['tran'];
				$res['diff'] = $this->_diff($res['originalTranslation'], $res['tran']);
			}
			if (isset($res['general'])) {
				$res['general'] = (bool) $res['general'];
			}
			$translations[$res['source']] = $res;
		}

		return $translations;
	}

	/**
	 * Return the difference of to strings.
	 *
	 * @param $original original string
	 * @param $new new string
	 * @return string
	 */
	protected function _diff($original, $new)
	{
		return diff2($original, $new, 'htmldiff');
	}

	/**
	 * Escapes strings for usage in double-quoted PHP strings
	 * @param string[] $strings Associative array defining translations
	 * @return string[] The initial array with its key and value strings escaped
	 */
	protected static function PHPEscape($strings)
	{
		$final = [];
		foreach ($strings as $key => $value) {
			$final[Language::addPhpSlashes($value['source'])] = Language::addPhpSlashes($value['tran']);
		}
		return $final;
	}

	/**
	 * Delete translations from the current language
	* @param bool $generalOnly true if only translations to contribute upstream should be deleted, false for all
	 */
	public function deleteTranslations($generalOnly = false)
	{
		$query = 'DELETE FROM `tiki_language` WHERE `lang`=?';
		if ($generalOnly) {
			$query .= ' AND `general` = 1';
		}
		$this->query($query, [$this->lang]);
	}

	/**
	 * Delete one translations from database
	 *
	 * @param string $source original string
	 * @return void
	 */
	public function deleteTranslation($source)
	{
		$query = 'delete from `tiki_language` where binary `source`=? and `lang`=?';
		$result = $this->query($query, [$source, $this->lang]);
	}

	/**
	 * Create a custom.php file for the current language
	 * and remove the translations from the database
	 *
	 * @return string the content of the new custom.php file
	 */
	public function createCustomFile()
	{
		$strings = self::PHPEscape($this->_getDbTranslations());
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
	protected function _removeSpaces($data)
	{
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
	public function getDbTranslations($sort_mode = 'source_asc', $maxRecords = -1, $offset = 0, $search = null)
	{
		global $tikilib;

		$translations = [];
		$bindvars = [$this->lang];
		$searchQuery = '';

		if ($search) {
			$search = $this->qstr("%$search%");
			$searchQuery = " and (`source` like $search or `tran` like $search)";
		}

		$translations = $this->_getDbTranslations($sort_mode, $maxRecords, $offset, true, $searchQuery);

		$total = count($translations);

		return ['translations' => $translations, 'total' => $total];
	}

	/**
	 * Delete all entries from unstranslated database (tiki_untranslatated)
	 * for the current language
	 *
	 * @return void
	 */
	public function deleteAllUntranslated()
	{
		$this->query('DELETE FROM `tiki_untranslated` WHERE `lang` = ?', [$this->lang]);
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
	public function getFileUntranslated()
	{
		$cachelib = $this->_getCacheLib();
		$cacheKey = 'untranslatedStrings.' . $this->lang . '.' . $this->_getFileHash();
		$info = $cachelib->getSerialized($cacheKey, 'untranslatedStrings');

		if ($info) {
			return $info;
		}

		$contents = file($this->filePath);
		$untranslated = [];

		foreach ($contents as $line) {
			// match untranslated string in a language.php file
			if (preg_match('|^//\s*?"(.+)"\s*=>\s*".+".*|', $line, $matches)) {
				$source = Language::removePhpSlashes($matches[1]);
				$untranslated[$source] = ['source' => $source, 'tran' => null];
			}
		}

		$cachelib->cacheItem($cacheKey, serialize($untranslated), 'untranslatedStrings');

		return $untranslated;
	}

	/**
	 * Get the md5 hash for the language file.
	 *
	 * @return string md5 hash for the language file
	 */
	protected function _getFileHash()
	{
		return md5_file($this->filePath);
	}

	/**
	 * Return untranslated strings recorded in the
	 * database (if feature "record_untranslated is enabled)
	 *
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $searchQuery
	 * @return array recorded untranslated strings
	 */
	protected function _getDbUntranslated($maxRecords = -1, $offset = 0, $searchQuery = null)
	{
		global $prefs;

		if ($prefs['record_untranslated'] != 'y') {
			return [];
		}

		$translations = [];

		$query = "SELECT `source` FROM `tiki_untranslated` WHERE `lang`=? $searchQuery ORDER BY source asc";
		$result = $this->query($query, [$this->lang], $maxRecords, $offset);

		while ($res = $result->fetchRow()) {
			$translations[$res['source']] = ['source' => $res['source'], 'tran' => null];
		}

		return $translations;
	}

	/**
	 * Return array of untranslated strings stored in the database.
	 *
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search
	 * @return array untranslated strings stored in the database
	 */
	public function getDbUntranslated($maxRecords = -1, $offset = 0, $search = null)
	{
		global $prefs;

		$untranslated = [];
		$total = 0;

		if ($prefs['record_untranslated'] == 'y') {
			$searchQuery = '';
			if ($search) {
				$search = $this->qstr("%$search%");
				$searchQuery = " AND `source` like $search ";
			}

			$untranslated = $this->_getDbUntranslated($maxRecords, $offset, $searchQuery);

			$query = "select count(*) from `tiki_untranslated` where `lang`=? $searchQuery";
			$total = $this->getOne($query, [$this->lang]);
		}

		return ['translations' => $untranslated, 'total' => $total];
	}

	/**
	 * Get all untranslated strings (from language.php and db).
	 * Only get untranslated strings from db if preference
	 * record_untranslated is enabled.
	 *
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search
	 * @return array
	 */
	public function getAllUntranslated($maxRecords = -1, $offset = 0, $search = null)
	{
		$fileUntranslated = $this->getFileUntranslated();

		// remove from $fileUntranslated strings translated in the database
		$dbTranslations = $this->_getDbTranslations();
		foreach ($fileUntranslated as $key => $value) {
			if (array_key_exists($key, $dbTranslations)) {
				unset($fileUntranslated[$key]);
			}
		}

		$dbUntranslated = $this->_getDbUntranslated();
		$untranslatedStrings = array_merge($fileUntranslated, $dbUntranslated);

		return $this->_filterStrings($untranslatedStrings, $maxRecords, $offset, $search);
	}

	/**
	 * Return all the translations from language.php
	 * and custom.php (if existent).
	 *
	 * @return array translations
	 */
	public function getFileTranslations()
	{
		$lang = [];

		if (is_file($this->filePath)) {
			require($this->filePath);
		}

		// custom.php
		if (is_file($this->customFilePath)) {
			require($this->customFilePath);
		}

		global $tikidomain;
		if (! empty($tikidomain) && is_file("lang/$this->lang/$tikidomain/custom.php")) {
			require("lang/$this->lang/$tikidomain/custom.php");
		}

		// remove last entry from language.php used only for get_strings.php
		if (isset($lang['###end###'])) {
			unset($lang['###end###']);
		}

		$lang = $this->_convertTranslationsArray($lang);

		return $lang;
	}

	/**
	 * Get all translations (db + custom.php + language.php)
	 *
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string $search return only results that matches the searched string
	 * @return array translations and total number
	 */
	public function getAllTranslations($maxRecords = -1, $offset = 0, $search = null)
	{
		$fileTranslations = $this->getFileTranslations();
		$dbTranslations = $this->_getDbTranslations('source_asc', -1, 0, true);
		$translations = array_merge($fileTranslations, $dbTranslations);

		return $this->_filterStrings($translations, $maxRecords, $offset, $search);
	}

	/**
	 * Receives an array of strings and filter then based on the
	 * values of $maxRecords, $offset and $search. Return an array
	 * with the total number of strings (key 'total') and the filtered
	 * strings (key 'translations').
	 *
	 * @param array $strings
	 * @param int $maxRecords
	 * @param int $offset
	 * @param string|null $search
	 * @return array the filtered array
	 */
	protected function _filterStrings($strings, $maxRecords, $offset, $search)
	{
		// display only translations that match the searched string if any
		if (isset($search) && strlen($search) > 0) {
			$pattern = "/.*$search.*/i";

			// search source strings
			$keys = preg_grep($pattern, array_keys($strings));
			$sources = [];
			foreach ($keys as $key) {
				$sources[$key] = $strings[$key];
			}

			// search translation strings
			$translations = [];
			foreach ($strings as $key => $string) {
				if (! empty($string['tran']) && strpos($string['tran'], $search) !== false) {
					$translations[$key] = $strings[$key];
				}
			}

			// join matches against source string and translation
			$strings = array_merge($translations, $sources);
		}

		$total = count($strings);

		uksort($strings, 'strcasecmp');

		$length = ($maxRecords > 0) ? $maxRecords : null;

		$strings = array_slice($strings, $offset, $length);

		//TODO: key 'translations' should be renamed to 'strings' for consistency
		return ['translations' => $strings, 'total' => $total];
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
	protected function _convertTranslationsArray($translations)
	{
		$newFormat = [];

		foreach ($translations as $source => $tran) {
			$newItem = [
				'source' => $source,
				'tran' => $tran,
			];

			$newFormat[$source] = $newItem;
		}

		return $newFormat;
	}

	/**
	 * Return a Cachelib object. Used to be able to
	 * mock cachelib por test purposes.
	 *
	 * @return Cachelib cachelib object
	 */
	protected function _getCacheLib()
	{
		return TikiLib::lib('cache');
	}
}
