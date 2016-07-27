<?php
/**
 * Tiki translation functions
 *
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

global $interactive_collected_strings;
$interactive_collected_strings = array();

/**
 * needs a description
 * @param string $content
 * @return mixed|string
 */function tr($content)
{
	$args = func_get_args();
	return tra($content, '', false, array_slice($args, 1));
}

/**
 * translate an English string
 * @param string $content English string
 * @param string $lg      language - if not specify = global current language
 * @param bool   $unused
 * @param array  $args
 *
 * @return mixed|string
 */
function tra($content, $lg = '', $unused = false, $args = array())
{
	global $prefs;
	static $languages = array();

	if ($lg == '') {
		if (! empty($prefs['language'])) {
			$lang = $prefs['language'];
		} elseif(! empty($prefs['site_language'])) {
			$lang = $prefs['site_language'];
		} else {
			$lang = 'en';
		}
	} else {
		$lang = $lg;
	}

	if ( ! isset($languages[$lang]) ) {
		$languages[ $lang ] = true;
		init_language($lang);
	}

	$out = tra_impl($content, $lang, $args);

	record_string($content, $out);

	return $out;
}

/**
 * initialize language $lg
 * @param string $lg
 */
function init_language( $lg )
{
	global $tikidomain, $prefs;
	if (is_file("lang/$lg/language.php")) {
		global ${"lang_$lg"};

		$lang = array();
		include("lang/$lg/language.php");

		// include mods language files if any
		$files = glob("lang/$lg/language_*.php");
		if (is_array($files)) {
			global $lang_mod;
			foreach ($files as $file) {
				require($file);
				$lang = array_merge($lang, $lang_mod);
			}
		}

		$customfile = "lang/$lg/custom.php";
		if (is_file($customfile)) {
			if (! check_file_BOM($customfile)) {
				include_once($customfile);
			}
		}

		$customfile = "lang/$lg/$tikidomain/custom.php";
		if (!empty($tikidomain) && is_file($customfile)) {
			if (! check_file_BOM($customfile)) {
				include_once($customfile);
			}
		}

		$files = glob("addons/*/lang/$lg/addon.php");
		if (is_array($files)) {
			global $lang_addon;
			foreach ($files as $file) {
				require($file);
				$lang = array_merge($lang, $lang_addon);
			}
		}

		if ( isset( $prefs['lang_use_db'] ) && $prefs['lang_use_db'] == 'y' ) {

			$tikilib = TikiLib::lib('tiki');
			if (isset($tikilib)) {
				$query = "select `source`, `tran` from `tiki_language` where `lang`=?";
				$result = $tikilib->fetchAll($query, array($lg));

				foreach ( $result as $row ) {
					$lang[ $row['source'] ] = $row['tran'];
				}
			}
		}

		${"lang_$lg"} = $lang;
	}
}

/**
 * needs description
 * @param        $content
 * @param string $lg
 * @param array  $args
 *
 * @return mixed|string
 */
function tra_impl($content, $lg = '', $args = array())
{
	global $prefs, $tikilib;

	if (empty($content) && $content !== '0') {
		return '';
	}

	global ${"lang_$lg"};

	if ($lg and isset(${"lang_$lg"}[$content])) {
		return tr_replace(${"lang_$lg"}[$content], $args);
	} else {
		// If no translation has been found and if the string ends with a punctuation,
		//   try to translate punctuation separately (e.g. if the content is 'Login:' or 'Login :',
		//   then it will try to translate 'Log In' and ':' separately).
		// This should avoid duplicated strings like 'Log In' and 'Log In:' that were needed before
		//   (because there is no space before ':' in english, but there is one in others like french)
		$lastCharacter = $content[strlen($content) - 1];
		if (in_array($lastCharacter, array(':', '!', ';', '.', ',', '?'))) { // Modify get_strings.php accordingly
			$new_content = substr($content, 0, -1);
			if ( isset(${"lang_$lg"}[$new_content]) ) {
				return tr_replace(
					${"lang_$lg"}[$new_content] . ( isset(${"lang_$lg"}[$lastCharacter])
					? ${"lang_$lg"}[$lastCharacter]
					: $lastCharacter ), $args
				);
			}
		}
	}

	// ### Trebly:B00624-01:added test on tikilib existence : on the first launch of tra tikilib is not yet set
	if (isset($prefs['record_untranslated']) && $prefs['record_untranslated'] == 'y' && $lg != 'en' && isset($tikilib)) {
		$query = 'select `id` from `tiki_untranslated` where `source`=? and `lang`=?';
      	if (!$tikilib->getOne($query, array($content, $lg))) {
      		$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
      		$tikilib->query($query, array($content, $lg), -1, -1, false);
      	}
	}

	return tr_replace($content, $args);
}

/**
 * needs description
 * @param $content
 * @param $args
 *
 * @return mixed
 */
function tr_replace( $content, $args )
{
	if ( ! count($args) ) {
		$out = $content;
	} else {
		$needles = array();
		$replacements = $args;

		foreach ( array_keys($args) as $num )
			$needles[] = "%$num";

		$out = str_replace($needles, $replacements, $content);
	}

	return $out;
}

/**
 * needs a proper description
 * @param $original
 * @param $printed
 */
function record_string( $original, $printed )
{
	global $interactive_collected_strings;
	if ( interactive_enabled() ) {
		$interactive_collected_strings[ md5($original . '___' . $printed) ] = array( $original, html_entity_decode($printed) );
	}
}

/**
 * needs a proper description
 * @return bool
 */
function interactive_enabled()
{
	return isset( $_SESSION['interactive_translation_mode'] ) && $_SESSION['interactive_translation_mode'] != 'off';
}

/**
 * needs a proper description
 * @return array
 */
function get_collected_strings()
{
	global $interactive_collected_strings;
	return $interactive_collected_strings;
}

/**
 * Checks a php file for a Byte Order Mark (BOM) and trigger error (and report error for admin)
 *
 * @param string $filename		full path of file to check
 * @param bool $try_to_fix		if file perms allow remove BOM if found
 *
 * @return bool					true if file still has a BOM
 */
function check_file_BOM($filename, $try_to_fix = true) {
	$BOM_found = false;

	if (is_readable($filename)) {
		$file = @fopen($filename, "r");
		$BOM_found = (fread($file, 3) === "\xEF\xBB\xBF");

		if ($try_to_fix && $BOM_found && is_writable($filename)) {
			$content = fread($file, filesize($filename));
			fclose($file);
			file_put_contents($filename, $content);
			trigger_error('File "' . $filename . '" contained a BOM which has been fixed.');
			$BOM_found = false;
		} else {
			fclose($file);
		}
	}
	if ($BOM_found) {
		$message = 'Warning: File "' . $filename . '" contains a BOM which cannot be fixed. Please re-edit and save as "UTF-8 without BOM"';
		if (Perms::get()->admin) {
			TikiLib::lib('errorreport')->report($message);
		}
		trigger_error($message);
	}

	return $BOM_found;
}

