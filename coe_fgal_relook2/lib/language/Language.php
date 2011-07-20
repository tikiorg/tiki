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

require_once('lib/core/TikiDb/Bridge.php');

//TODO: move language functions (like $tikilib->list_languages()) from $tikilib to this class
/**
 * Generic methods for managing languages in Tiki
 */
class Language extends TikiDb_Bridge
{
	/**
	 * Return a list of languages available in Tiki
	 *
	 * @return array list of languages
	 */
	public static function getLanguages() {
		global $langmapping; require_once('lang/langmapping.php');
		return array_keys($langmapping);
	}

	/**
	 * Return a list of languages with translations
	 * in the database
	 *
	 * @return array list of languages with at least one string translated
	 */
	public static function getDbTranslatedLanguages() {
		$languages = array();
		$result = self::fetchAll('SELECT DISTINCT `lang` FROM `tiki_language` ORDER BY `lang` asc');

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
	public static function addPhpSlashes($string) {
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
	 * should not apper in english strings.
	 */
	public static function removePhpSlashes ($string) {
		$removePHPslashes = Array ('\n'   => "\n",
				   '\r'   => "\r",
				   '\t'   => "\t",
				   '\\\\' => '\\',
				   '\$'   => '$',
				   '\"'   => '"');
	  
		if (preg_match ('/\{0-7]{1,3}|\x[0-9A-Fa-f]{1,2}/', $string, $match)) {
			trigger_error ("Octal or hexadecimal string '".$match[1]."' not supported", E_WARNING);
		}

		return strtr ($string, $removePHPslashes);
	}
}
