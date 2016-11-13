<?php
/**
 * Tiki typography functions
 *
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: tra.php 60081 2016-10-28 15:34:40Z jonnybradley $

/**
 * Apply typography transforms to a string containing (html tags are left alone).
 * Note: if $ui_flag is set, smarty tags are also left alone and the approximative 
 * quote feature is disabled even if enabled in preferences.
 * @param string $content string
 * @param string $lg      language - if not specify = global current language
 * @param bool   $ui_flag indicates whether this is a user interface string
 *
 * @return string
 */
function typography($content, $lg = '', $ui_flag = false)
{
	global $prefs;
	static $smartypants_parsers = array();

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

	$parser_key = "$ui_flag:$lang";
	if ( ! isset($prefs['feature_typo_quotes']) ) {
		// preferences not available (yet?), so we temporarily have no parser to use
		$sp = false;
	} else if ( ! isset($smartypants_parsers[$parser_key]) ) {
		$sp = get_typography_parser($lang, $ui_flag);
		$smartypants_parsers[$parser_key] = $sp;
	} else {
		$sp = $smartypants_parsers[$parser_key];
	}
	
	if ($sp === false) {
		// no typography transforms to apply
	} else if ($ui_flag) {
		// temporarily wrap smarty tags in HTML-like tags SmartyPants will recognize
		// so that quotes used to delimit tag attributes are ignored.
		$content = preg_replace('/{(?:.+?|"[^"]*"|\'[^\']*\')+?}/', '<$SMARTYPANTSESCAPE \0$>', $content);
		$content = $sp->transform($content);
		$content = preg_replace('/<\$SMARTYPANTSESCAPE ({(?:.+?|"[^"]*"|\'[^\']*\')+?})\$>/', '\1', $content);
	} else {
		$content = $sp->transform($content);
	}
	return $content;
}

/**
 * get the SmartyPants parser for typographic adjustments in a given language,
 * or false if there are no typographic transforms to apply
 * @param string $lang    language - must be specified
 * @param bool   $ui_flag indicates whether this for user interface strings
 *
 * @return mixed
 */
function get_typography_parser($lang, $ui_flag)
{
	global $prefs;
	
	if ($prefs['feature_typo_quotes'] != 'y' && $prefs['feature_typo_approximative_quotes'] != 'y' &&
		$prefs['feature_typo_dashes_and_ellipses'] == 'y' && $prefs['feature_typo_nobreak_spaces'] != 'y')
	{
		// shortcut for when all typography transforms are disabled: don't load the parser or the settings
		return false;
	}

	// create parser with default settings
	$sp = new \Michelf\SmartyPantsTypographer(1);
	
	// apply language-specific configuration
	$language_config = get_typography_parser_config($lang);
	foreach ($language_config as $key => $value) {
		$sp->$key = $value;
	}
	
	// then disable options not enabled in preferences 
	// "double" and 'single' quotes (and apostrophes) are replaced with curly ones
	if ($prefs['feature_typo_quotes'] != 'y') {
		$sp->do_quotes = 0;
		$sp->do_geresh_gershayim = 0;
	}
	// ``approximative'' ,,quotes`` <<are>> >>replaced<< with typographic ones		
	// Note: always disabled for ui strings
	if ($ui_flag || $prefs['feature_typo_approximative_quotes'] != 'y') {
		$sp->do_backticks = 0;
		$sp->do_comma_quotes = 0;
		$sp->do_guillemets = 0;
	}
	// double hyphen -- converted to em dash
	if ($prefs['feature_typo_dashes_and_ellipses'] != 'y') {
		$sp->do_dashes = 0;
		$sp->do_ellipses = 0;
	}
	// replace normal spaces with no-break spaces (will not insert a space)
	if ($prefs['feature_typo_nobreak_spaces'] != 'y') {
		$sp->do_space_colon = 0;
		$sp->do_space_semicolon = 0;
		$sp->do_space_marks = 0;
		$sp->do_space_frenchquote = 0;
		$sp->do_space_thousand = 0;
		$sp->do_space_unit = 0;
	}
	
	// finally: don't litter strings with entities
	// (because entities often get escaped later in the process)
	$sp->decodeEntitiesInConfiguration();
	
	return $sp;
}

/**
 * get typography configuration for language $lg
 * @param string $lg 
 */
function get_typography_parser_config( $lg )
{
	static $typography_config = array();
	if ( isset($typography_config[$lg]) ) {
		return $typography_config[$lg];
	} else {
		$typography = array();
		
		if (is_file("lang/$lg/typography.php")) {
			include("lang/$lg/typography.php");
		}
		if (is_file("lang/$lg/custom_typography.php")) {
			include("lang/$lg/custom_typography.php");
		}
		if (!is_array($typography)) {
			$typography = array();
		}
		return $typography;
	}
}
