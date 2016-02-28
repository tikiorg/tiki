<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @package   Tiki
 * @subpackage    Language
 *
 * Generic methods for managing languages in Tiki
 */
class Language extends TikiDb_Bridge
{
	/**
	 * Return a list of languages available in Tiki
	 *
	 * @return array list of languages
	 */
	public static function getLanguages()
	{
		require_once('lib/init/tra.php');
		global $langmapping; require_once('lang/langmapping.php');
		return array_keys($langmapping);
	}

	/**
	 * Return a list of languages with translations
	 * in the database
	 *
	 * @return array list of languages with at least one string translated
	 */
	public static function getDbTranslatedLanguages()
	{
        $lang = new Language();
		$languages = array();
		$result = $lang->fetchAll('SELECT DISTINCT `lang` FROM `tiki_language` ORDER BY `lang` asc');

		foreach ($result as $res) {
			$languages[] = $res['lang'];
		}

		return $languages;
	}

	/**
	 * Translate as in "Table 7-1 Escaped characters" in the PHP manual
	 * $string = str_replace ("\n", '\n',   $string);
	 * $string = str_replace ("\r", '\r',   $string);
	 * $string = str_replace ("\t", '\t',   $string);
	 * $string = str_replace ('\\', '\\\\', $string);
	 * $string = str_replace ('$',  '\$',   $string);
	 * $string = str_replace ('"',  '\"',   $string);
	 * We skip the exotic regexps for octal an hexadecimal
	 * notation - \{0-7]{1,3} and \x[0-9A-Fa-f]{1,2} -
	 * since they should not apper in english strings.
	 *
	 * @param string $string
	 * @return string modified string;
	 */
	public static function addPhpSlashes($string)
	{
		$addPHPslashes = array(
			"\n" => '\n',
			"\r" => '\r',
			"\t" => '\t',
			'\\' => '\\\\',
			'$'  => '\$',
			'"'  => '\"'
		);

		return strtr($string, $addPHPslashes);
	}

	/**
	 * $string = str_replace ('\n',   "\n", $string);
	 * $string = str_replace ('\r',   "\r", $string);
	 * $string = str_replace ('\t',   "\t", $string);
	 * $string = str_replace ('\\\\', '\\', $string);
	 * $string = str_replace ('\$',   '$',  $string);
	 * $string = str_replace ('\"',   '"',  $string);
	 * We skip the exotic regexps for octal an hexadecimal
	 * notation - \{0-7]{1,3} and \x[0-9A-Fa-f]{1,2} - since they
	 * should not appear in english strings.
	 */
	public static function removePhpSlashes ($string)
	{
		$removePHPslashes = array(
			'\n'   => "\n",
			'\r'   => "\r",
			'\t'   => "\t",
			'\\\\' => '\\',
			'\$'   => '$',
			'\"'   => '"'
		);

		if (preg_match('/\{0-7]{1,3}|\x[0-9A-Fa-f]{1,2}/', $string, $match)) {
			trigger_error("Octal or hexadecimal string '" . $match[1] . "' not supported", E_WARNING);
		}

		return strtr($string, $removePHPslashes);
	}
	
	/**
	 * isLanguageRTL
	 * Determine if a language is an RTL language
	 *
	 * @param mixed $langCode Language code to check, e.g. "en"
	 * @return bool true if the language is RTL, otherwise false
	 *
	 */	
	public static function isLanguageRTL ($langCode)
	{
		switch ($langCode)
		{
			case 'ar':
			case 'fa':
			case 'he':
			case 'ku':
			case 'ug':
				return true;
		}
		return false;
	}	
	
	
	/**
	 * isRTL
	 * Determine if the current language is RTL
	 * @return bool true if the language is RTL, otherwise false
	*/
	public static function isRTL()
	{
		global $prefs;
		return self::isLanguageRTL($prefs['language']);
	}
	
	/**
	 * @param bool $path
	 * @param null $short
	 * @param bool $all
	 * @return array|mixed
	 */
	static function list_languages($path = false, $short=null, $all=false)
	{
		global $prefs;

		$args = func_get_args();
		$key = 'disk_languages' . implode(',', $args) . $prefs['language'];
		$cachelib = TikiLib::lib('cache');

		if (! $languages = $cachelib->getSerialized($key)) {
			$languages = self::list_disk_languages($path);
			$languages = self::format_language_list($languages, $short, $all);

			$cachelib->cacheItem($key, serialize($languages));
		}

		return $languages;
	}

	/**
	 * @param $path
	 * @return array
	 */
	private static function list_disk_languages($path)
	{
		$languages = array();

		if (!$path)
			$path = "lang";

		if (!is_dir($path))
			return array();

		$h = opendir($path);

		while ($file = readdir($h)) {
			if (strpos($file, '.') === false && $file != 'CVS' && $file != 'index.php' && is_dir("$path/$file") && file_exists("$path/$file/language.php")) {
				$languages[] = $file;
			}
		}

		closedir($h);

		return $languages;
	}

	/**
	 * @return array
	 */
	static function get_language_map()
	{
		$languages = self::list_languages();

		$map = array();
		foreach ($languages as $lang) {
			$map[$lang['value']] = $lang['name'];
		}

		return $map;
	}

	/**
	 * @param $language
	 * @return bool
	 */
	function is_valid_language( $language )
	{
		return preg_match("/^[a-zA-Z-_]*$/", $language)
			&& file_exists('lang/' . $language . '/language.php');
	}
	
	/**
	 * Comparison function used to sort languages by their name in the current locale.
	 * @param $a
	 * @param $b
	 * @return int
	 */
	static function formatted_language_compare($a, $b)
	{
		return strcasecmp($a['name'], $b['name']);
	}
	
	/**
	 * Returns a list of languages formatted as a twodimensionel array with 'value' being the language code and 'name' being the name of the language. If $short is 'y' returns only the localized language names array
	 * @param $languages
	 * @param null $short
	 * @param bool $all
	 * @return array
	 */
	static function format_language_list($languages, $short=null, $all=false)
	{
		// The list of available languages so far with both English and
		// translated names.
		global $langmapping, $prefs;
		include("lang/langmapping.php");
		$formatted = array();

		// run through all the language codes:
		if (isset($short) && $short == "y") {
			foreach ($languages as $lc) {
				if ( $prefs['restrict_language'] === 'n' || empty($prefs['available_languages'] ) || (!$all and in_array($lc, $prefs['available_languages']))) {
					if (isset($langmapping[$lc]))
						$formatted[] = array('value' => $lc, 'name' => $langmapping[$lc][0]);
					else
						$formatted[] = array('value' => $lc, 'name' => $lc);
				}
				usort($formatted, array('language', 'formatted_language_compare'));
			}
			return $formatted;
		}
		foreach ($languages as $lc) {
			if ( $prefs['restrict_language'] === 'n' || empty($prefs['available_languages']) || (!$all and in_array($lc, $prefs['available_languages'])) or $all) {
				if (isset($langmapping[$lc])) {
					// known language
					if ($langmapping[$lc][0] == $langmapping[$lc][1]) {
						// Skip repeated text, 'English (English, en)' looks silly.
						$formatted[] = array(
								'value' => $lc,
								'name' => $langmapping[$lc][0] . " ($lc)"
								);
					} else {
						$formatted[] = array(
								'value' => $lc,
								'name' => $langmapping[$lc][1] . " (" . $langmapping[$lc][0] . ', ' . $lc . ")"
								);
					}
				} else {
					// unknown language
					$formatted[] = array(
							'value' => $lc,
							'name' => tra("Unknown language"). " ($lc)"
							);
				}
			}
		}

		// Sort the languages by their name in the current locale
		usort($formatted, array('language', 'formatted_language_compare'));
		return $formatted;
	}
}
