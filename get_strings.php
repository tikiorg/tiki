<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * $Id: /cvsroot/tikiwiki/tiki/get_strings.php
 * \brief Update the language.php files
 * call example: get_strings.php?lang=fr&close
 * or on commande line: php get_strings.php \param lang=fr module


 * \param lang=xx    : Only translate lang 'xx' - if the parameter is not given all languages are translated

 * \param comments   : Generate all comments (equal to close&module)

 * \param close      : Looks for similar strings that are already translated and generate a comment if a 'match' is made

 * \param module     : Generate comments that describe in which .php and/or .tpl\n module(s) a certain string was found (useful for checking translations in context)

 * \param patch      : Looks for the file 'language.patch' in the same directory as the corresponding language.php and overrides any strings in language.php - good if a user does not agree with some translations or if only changes are sent to the maintainer

 * \param spelling   : Generate a file spellcheck_me.txt in the applicable languages directory that contains all the words used in the translated text. This makes it simple to use a spellchecker on the resulting file

 * \param groupwrite : Sets the generated files permissions to allow the generated language.php also be group writable. This is good for translators if they do not have root access to tiki but are in the same group as the webserver. Please remember to have write access removed when translation is finished for security reasons. (Run script again whithout this parameter)

 * \param sort ='n'  : Don't sort the filenames 

 * \param completion=y: Produce only the completion status

 * \param nosections : Don't print sections delimiters

 * \param nohelp     : Don't print help section

 * \param tagunused  : Tags the unused strings with "// ## UNUSED".

 * \param verbose=y  : Display content of language files as they are created.

 * \param quiet  : Do not display listing all files even if completion != y

 */

require_once('lib/language/Language.php');

////////////////////////////////////////////////////////////////////////////
/// functions

// Only accept to call this script:
// - through command line interface
// - through the web interface if authenticated as an admin
//
$script_mode = ! isset( $_SERVER['REQUEST_METHOD'] ) && isset($_SERVER['argc']);

$punctuations = array(':', '!', ';', '.', ',', '?'); // Modify lib/init/tra.php accordingly

/**
 * Reads all the permission descriptions in tiki database and writes
 *   it to the file $file. All the strings will be surrounded by smarty translate tags
 *     ex: {tr}perm description{/tr}
 *
 * @param $file string: target file for the perms
 * @returns: nothing but creates the file with the perms (take care about the acl's in the target directory !)
 */
function collect_perms_desc($file)
{
	global $tikilib;
	if ( isset($tikilib) ) {

		$result = $tikilib->query("SELECT DISTINCT(permDesc) FROM users_permissions ORDER BY permDesc");

		$perm_strings = array();
		while( $row = $result->fetchRow() )
			$perm_strings[] = $row['permDesc'];

	} elseif ( is_readable('db/tiki.sql') ) {

		// Used when called in $script_mode if no DB has been found
		$matches = array();
		preg_match_all(
				'/insert\s+into\s+\`?users_permissions\`?\s*\([^\)]+\)\s*values\s*\(\'(tiki_p_[^\'"]+)\',\s*\'(.*)\',/Uim',
				file_get_contents('db/tiki.sql'),
				$matches
				);

		foreach ( $matches[2] as $permDesc ) {
			$perm_strings[] = str_replace("\'", "'", $permDesc);
		}
		unset($matches);

	} else {
		die('File db/tiki.sql is missing');
	}

	$pstr = fopen($file,'w');
	if (!$pstr) {
		echo "The file $file can not be written";
	} else {
		foreach ($perm_strings as $strg)
		{
			fwrite ($pstr,  "{tr}" . $strg . "{/tr}" . "\n");
		}
		fclose($pstr);
	}
}

/**
 * Get all preferences names from get_default_prefs() function or reads them all from tiki database
 * and writes it to the file $file. All the strings will be surrounded by smarty translate tags
 *     ex: {tr}preference name{/tr}
 *
 * @param $file string: target file for the pref names
 * @returns: nothing but creates the file with the pref names (take care about the acl's in the target directory !)
 */
function collect_prefs_names($file) {

	global $tikilib;
	if ( isset($tikilib) ) {

		$prefs_strings = array();
		$result = $tikilib->query("select `name` from `tiki_preferences`");
		while ( $row = $result->fetchRow() ) $prefs_strings[] = $row['name'];

	} elseif ( function_exists('get_default_prefs') ) {

		// Used when called in $script_mode if no DB has been found
		$prefs_strings = array_keys(get_default_prefs());

	} else {
		die("No 'get_default_prefs' function is available");
	}

	$pstr = fopen($file,'w');
	if (!$pstr) {
		echo "The file $file can not be written";
	} else {
		foreach ($prefs_strings as $strg)
		{
			fwrite ($pstr,  "{tr}" . str_replace('_',' ',$strg) . "{/tr}" . "\n");
		}
		fclose($pstr);
	}
}

function hardwire_file ($file) {
	global $files, $completion, $script_mode, $quiet;
	$files[] = $file;
	if ( ! $completion && ! $quiet ) {
		formatted_print("File (hardwired): $file\n");
	}
}

function collect_files ($dir)
{
	global $files, $completion, $script_mode, $quiet;

	$handle = opendir ($dir);
	while (false !== ($file = readdir ($handle))) {
		// Skip current and parent directory
		// also skip other directories which may contain source code
		// that should not be translated (the directories normally contain
		// temporary results etc.)
		// Please note that these directories will be skipped on all levels
		if ('.'  === $file || '..' === $file || 'Zend' === $file || 
				'htmlpurifier' === $file || 'adodb' === $file || 'smarty' === $file ||
				'ezcomponents' === $file || 'phpcas' === $file || 
				'jscalendar' === $file || 'pclzip' === $file || 'jquery' === $file ||
				'pear' === $file || 'ckeditor' === $file ||
				'lang' === $file || 'templates_c' === $file || 'dump'  === $file || 
				'temp' === $file || 'img' === $file || 'cache' === $file ||
				'test' === $file || 'codemirror' === $file) {
			continue;
		}

		$filepath = $dir . '/' . $file;
		if (preg_match ("/.*\.(tpl|php)$/", $file)) {
			if ( ! $completion && ! $quiet ) {
				formatted_print("File: $filepath\n");
			}
			$files[] = $filepath; 
		}
		else {
			if (is_dir ($filepath)) {
				collect_files ($filepath);
			}
		}
	}
	closedir ($handle);
}

function addToWordlist (&$wordlist, &$sentence) {
	global $spelling;
	if ($spelling) {
		// Perhaps regexphandling must be improved?!
		// Spellcheckers seems to handle special chars quite OK however.
		$words = preg_split ("!\s+!", $sentence);

		foreach ($words as $dummy => $word) {
			if (function_exists('mb_strtolower')) {
				$wordlist[mb_strtolower($word)] = 1;
			} else {
				$wordlist[$word] = 1;
			}
		}
	}
}


function writeFile_and_User (&$fd, $outstring) {
	global $verbose;
	if ( $verbose ) {
		formatted_print($outstring);
	}
	fwrite ($fd, $outstring);
}

function writeTranslationPair (&$fd, &$key, &$val) {
	writeFile_and_User ($fd, 
			'"' . Language::addPhpSlashes ($key) . '"' . " => " .
			'"' . Language::addPhpSlashes ($val) . '",');
}

/* \brief: give the closest translation
 * \return the closest translated string
 * \param closeEnglish: the English string of the return string
 */
function leven($key, $dictionary, $closeEnglish) {
	$dist = 256;
	foreach ($dictionary as $english=>$trans) {
		$d = levenshtein (strtolower (substr ($key, 0, 255)),
				strtolower (substr ($english, 0, 255)));
		if ($d < $dist) {
			$dist = $d;
			$closeTrans   = $trans;
			$closeEnglish = $english;
		}
	}

	if ($dist < 1 + strlen ($key)/5)
		return $closeTrans;
	else
		return '';
}

function formatted_print($string) {
	global $script_mode;
	print $script_mode ? $string : nl2br( htmlspecialchars( $string ) );
}
////////////////////////////////////////////////////////////////////////////

if ( $script_mode ) {
	
	$_REQUEST = array();
	for ( $k = 1 ; $k < $_SERVER['argc'] ; $k++ ) {
		@list($key, $value) = explode('=', $_SERVER['argv'][$k], 2);
		$_REQUEST[$key] = $value ? $value : 'y';
	}

	$quiet = isset ($_REQUEST['quiet']);

	if ( ! $quiet ) {
		require_once('lib/setup/timer.class.php');
		$tiki_timer = new timer();
		$tiki_timer->start();
	}

	if ( file_exists('db/local.php') ) {
		require_once('db/tiki-db.php');
		$tikilib = TikiDb::get();
	} else {
		require_once('lib/init/tra.php');
		require_once('lib/setup/prefs.php'); // Used to get default prefs
	}


} else {
	require_once('tiki-setup.php');
	$quiet = isset ($_REQUEST['quiet']);
	if ( $tiki_p_admin != 'y' ) die("You need to be admin to run this script");
}

if ( ! $script_mode ) {
	echo '<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>
		';
}

$completion = isset($_REQUEST['completion']) && $_REQUEST['completion']=='y';
if ( ! $completion && ! $quiet ) {
	formatted_print("Initialization time: " . $tiki_timer->elapsed() . " seconds\n");
}
if ( ! $quiet ) $tiki_timer->start("files");

$comments = isset ($_REQUEST['comments']);
$close    = isset ($_REQUEST['close'])  || $comments;
$module   = isset ($_REQUEST['module']) || $comments;
$patch    = isset ($_REQUEST['patch']);
$spelling = isset ($_REQUEST['spelling']);
$group_w  = isset ($_REQUEST['groupwrite']);
$tagunused= isset ($_REQUEST['tagunused']);
$verbose  = isset ($_REQUEST['verbose']);
$nohelp     = isset ($_REQUEST['nohelp']);
$nosections = isset ($_REQUEST['nosections']);

// Get the language(s)
$languages = Array();
formatted_print("Languages: ");
if (isset ($_REQUEST["lang"])) {
	$lang = $_REQUEST["lang"];
	$languages[] = $lang;
	formatted_print("$lang");  
}
else {
	$handle=opendir ('lang');
	while (false !== ($lang = readdir ($handle))) {
		if($lang == '.' || $lang == '..')
			continue;
		if( is_dir( "lang/$lang" ) && is_file( "lang/$lang/language.php" ) )
		{
			formatted_print("$lang ");  
			$languages[] = $lang;
		}
	}
	closedir ($handle);
}

$files = Array();
$wordlist = Array();

## When collecting files we need to add a file since the directory which it
## is placed in is excluded. We should keep hardwiring to a minimum.
## In a normal case a file that should be translated should not exist in
## a (sub)directory that is excluded. In this (unfortunate) case it seems that
## the files are placed in a logical location.
if ( ! $completion ) formatted_print("\n" . ( $quiet ? "Parsing Tiki files..." : '' ) );
collect_files ('.');
hardwire_file ('./lang/langmapping.php');
hardwire_file ('./img/flags/flagnames.php');

## Adding a file in ./temp which contains all the perms descriptions
## This file is called permstrings.tpl. The extension has to be .tpl in order to be
##   taken in charge by the script (tpl or php)
## This file is, of course, temporary and will be deleted during the next cache clear !

$permsfile = "./temp/permstrings.tpl";
$permsstrgs = collect_perms_desc($permsfile);
$prefsfile = "./temp/prefnames.tpl";
collect_prefs_names($prefsfile);
hardwire_file ($permsfile);
hardwire_file ($prefsfile);

// Sort files to make generated strings appear in language.php in the same 
// order across different systems
if ((!isset($_REQUEST["sort"]) || $_REQUEST["sort"] != 'n') && ! $completion) {
	formatted_print("\nSorting files... ");
	flush();
	sort($files);
	if ( ! $quiet ) formatted_print( count($files) . " items done.\nTiki directory parsed in: " . $tiki_timer->stop("files") . " seconds\n\n" );
	flush();
}
if ( ! $quiet ) $tiki_timer->start("processing");

if ( $completion ) {
	if ( $script_mode ) {
		echo "\n\nLanguage | Completion | Translated | To Translate | Unused";
	} else {
		echo "<table border='1'>";
		echo "<tr>";
		echo "<td><b>Language</b></td>";
		echo "<td><b>Completion</b></td>";
		echo "<td><b>Translated</b></td>";
		echo "<td><b>To Translate</b></td>";
		echo "<td><b>Unused</b></td>";
		echo "</tr>";
	}
}

$oldEndMarker = '##end###';
$endMarker = '###end###';
foreach ($languages as $ksel => $sel) {
	unset ($lang);
	unset ($to_translate);
	unset ($translated); 
	unset ($modulename);
	unset ($unused);
	unset ($dictionary);
	$to_translate = Array ();
	$modulename   = Array ();
	$translated   = Array ();

	if ($patch) {
		$origPatch = "lang/$sel/language.patch";
		if (!file_exists ($origPatch)) {
			die ("No patch file .../$origPatch exists");
		}
		require ($origPatch);
		$patchLang = $lang;
		unset ($lang);
	}

	if ( $quiet && ! $completion ) {
		if ( $ksel == 0 ) formatted_print("\nUpdating language files:");
		formatted_print(" $sel");
	}
	require("lang/$sel/language.php");

	if (isset ($lang[$oldEndMarker])) {
		unset ($lang[$oldEndMarker]);
	}
	if (isset ($lang[$endMarker])) {
		unset ($lang[$endMarker]);
	}

	$unused     = $lang;
	$dictionary = $lang;


	if ($group_w) {
		// We set umask to zero value to allow proper chmod later
		// (Is this really necessary? Does not chmod work independently of umask?)
		$old_umask = umask (0); 
	}

	if (!$completion) {
		$fw = fopen("lang/$sel/new_language.php",'w');
		if ( ! $fw ) die("\nThe file lang/$sel/new_language.php can not be written\n");

		if ( $verbose ) formatted_print('<');
		fwrite($fw,"<");
		writeFile_and_User ($fw, "?php");
		// The comment coding:utf-8 is for the benefit of emacs
		// and must be on the very first line in the file
		// we leave this comment in even if comments are off since
		// editing files with the wrong encoding causes commical effects at best.
		writeFile_and_User ($fw, " // -*- coding:utf-8 -*-\n");
	}

	if (!$nohelp && !$completion) {
		// Good to have instructions for translators in the release file.
		// The comments get filtered away by Smarty anyway
		writeFile_and_User ($fw, "// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project\n");
		writeFile_and_User ($fw, "// \n");
		writeFile_and_User ($fw, "// All Rights Reserved. See copyright.txt for details and a complete list of authors.\n");
		writeFile_and_User ($fw, "// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.\n");
		writeFile_and_User ($fw, "// \$Id$");
		writeFile_and_User ($fw, "\n");
		writeFile_and_User ($fw, "// Parameters:\n\n");
		writeFile_and_User ($fw, "// lang=xx    : only translates language 'xx',\n");
		writeFile_and_User ($fw, "//              if not given all languages are translated\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// comments   : generate all comments (equal to close&module)\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// close      : look for similar strings that are already translated and\n");
		writeFile_and_User ($fw, "//              generate a comment if a 'match' is made\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// module     : generate comments that describe in which .php and/or .tpl\n");
		writeFile_and_User ($fw, "//              module(s) a certain string was found (useful for checking\n");
		writeFile_and_User ($fw, "//              translations in context)\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// patch      : looks for the file 'language.patch' in the same directory\n");
		writeFile_and_User ($fw, "//              as the corresponding language.php and overrides any strings\n");
		writeFile_and_User ($fw, "//              in language.php - good if a user does not agree with\n");
		writeFile_and_User ($fw, "//              some translations or if only changes are sent to the maintainer\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// spelling   : generates a file 'spellcheck_me.txt' that contains the\n");
		writeFile_and_User ($fw, "//              words used in the translation. It is then easy to check this\n");
		writeFile_and_User ($fw, "//              file for spelling errors (corrections must be done in\n");
		writeFile_and_User ($fw, "//              'language.php, however)\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// groupwrite : Sets the generated files permissions to allow the generated\n");
		writeFile_and_User ($fw, "//              language.php also be group writable. This is good for\n");
		writeFile_and_User ($fw, "//              translators if they do not have root access to tiki but\n");
		writeFile_and_User ($fw, "//              are in the same group as the webserver. Please remember\n");
		writeFile_and_User ($fw, "//              to have write access removed when translation is finished\n");
		writeFile_and_User ($fw, "//              for security reasons. (Run script again without this\n");
		writeFile_and_User ($fw, "//              parameter)\n");
		writeFile_and_User ($fw, "\n");

		writeFile_and_User ($fw, "// Examples:\n");
		writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv\n");
		writeFile_and_User ($fw, "// Will translate language 'sv' and (almost) avoiding comment generation\n\n");

		writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv&comments\n");
		writeFile_and_User ($fw, "// Will translate language 'sv' and generate all possible comments.\n");
		writeFile_and_User ($fw, "// This is the most usefull mode when working on a translation.\n\n");
		writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv&nohelp&nosections\n");
		writeFile_and_User ($fw, "// These options will only provide the minimal amout of comments.\n");
		writeFile_and_User ($fw, "// Usefull mode when preparing a translation for distribution.\n\n");
		writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?nohelp&nosections\n");
		writeFile_and_User ($fw, "// Prepare all languages for release\n\n\n");
		
		writeFile_and_User ($fw, "//  or on commande line:\n");
		writeFile_and_User ($fw, "//  php get_strings.php \param lang=fr module\n\n");
		writeFile_and_User ($fw, "\n");
	

		writeFile_and_User ($fw, "// ### Note for translators about translation of text ending with punctuation\n");
		writeFile_and_User ($fw, "// ###\n");
		writeFile_and_User ($fw, "// ### The current list of concerned punctuation can be found in 'lib/init/tra.php'\n");
		writeFile_and_User ($fw, "// ### On 2009-03-02, it is: (':', '!', ';', '.', ',', '?')\n");
		writeFile_and_User ($fw, "// ### For clarity, we explain here only for colons: ':' but it is the same for the rest\n");
		writeFile_and_User ($fw, "// ###\n");
		writeFile_and_User ($fw, "// ### Short version: it is not a problem that string \"Login:\" has no translation. Only \"Login\" needs to be translated.\n");
		writeFile_and_User ($fw, "// ###\n");
		writeFile_and_User ($fw, "// ### Technical justification:\n");
		writeFile_and_User ($fw, "// ### If a string ending with colon needs translating (like \"{tr}Login:{/tr}\")\n");
		writeFile_and_User ($fw, "// ### then Tiki tries to translate 'Login' and ':' separately.\n");
		writeFile_and_User ($fw, "// ### This allows to have only one translation for \"{tr}Login{/tr}\" and \"{tr}Login:{/tr}\"\n");
		writeFile_and_User ($fw, "// ### and it still allows to translate \":\" as \"&nbsp;:\" for languages that\n");
		writeFile_and_User ($fw, "// ### need it (like French)\n");

		// Start generating the lang array
		writeFile_and_User ($fw, "\n\$lang=Array(\n");  
	}
	foreach ($files as $file) {
		$data = file_get_contents($file);

		unset ($words);   $words   = Array ();
		unset ($uqwords); $uqwords = Array ();
		unset ($sqwords); $sqwords = Array ();
		unset ($dqwords); $dqwords = Array ();


		// PM for unusual regexps
		// (?m) inline or m after regex delimiter sets PCRE_MULTILINE which makes '^' and '$' ignore '\n'
		// (?s) inline or s after regex delimiter sets PCRE_DOTALL which makes that '.' also matches '\n'
		// ?: below makes that the pharentesis is not extracted int the outarray
		// +? and *? below are nongreedy versions of the + and * operators accordingly

		if (preg_match ("/\.php$/", $file)) {
			// Do not translate PHP comments (we only filter the "safe" cases)
			// Calling php -w <filename> would take care of all comments,
			// but that does not go well with safe-mode.
			$data = preg_replace ("!/\*.*?\*/!s", "", $data);  // C comments
			/* the "unused strings" - the strings that will be translated later through a variable are marked with //get_strings tra("string") */
			$data = preg_replace ("!^\s*//get_strings(.*)\$!m", "$1", $data);
			$data = preg_replace ("!^\s*//.*\$!m", "", $data); // C++ comments
			$data = preg_replace ("!^\s*\#.*\$!m", "", $data); // shell comments

			// Only extract tra () and hawtra () in .php-files
			// tr() function also exists for strings with arguments

			// Extract from SINGLE quoted strings
			preg_match_all ('!\W(?:haw)?tra?\s*\(\s*\'(.+?)\'\s*[\),]!s', $data, $sqwords);

			// Extract from DOUBLE quoted strings
			preg_match_all ('!\W(?:haw)?tra?\s*\(\s*"(.+?)"\s*[\),]!s', $data, $dqwords);
		}

		if (preg_match ("/\.tpl$/", $file)) {
			// Do not translate text in Smarty comments: {* Smarty comment *}
			// except if it is an "unused string marked {*get_strings {tr}string{/tr} *} 
			$data = preg_replace('/(?s)\{\*get_strings(.*?)\*\}/', '$1', $data);
			$data = preg_replace ('/(?s)\{\*.*?\*\}/', '', $data); // Smarty comment 

			// Strings of the type {tr}{$perms[user].type}{/tr} need (should)
			// not be translated
			$data = preg_replace ('/(?s)\{tr\}\s*\{[$][^\}]*?\}\s*\{\/tr\}/','',$data);

			// Only extract {tr} ... {/tr} in .tpl-files
			// Also match {tr [args]} ...{/tr}
			preg_match_all ('/\{tr(?:\s+[^\}]*)?\}(.+?)\{\/tr\}/s', $data, $uqwords);
		}

		// Transfer unquoted words (if any) to the words array
		// counting recursive, because we have two empty subarrays after failed match
		if (count($uqwords, COUNT_RECURSIVE) > 2) {
			$words = $uqwords[1];
		}

		// Transfer SINGLE quoted words (if any) to the words array
		if (count ($sqwords, COUNT_RECURSIVE) > 2) {
			foreach (array_unique ($sqwords[1]) as $sqword) {
				// Strip the extracted strings from escapes
				// (these will not be reinserted during generation, since ' need
				// not be escaped when string delimeters are double quotes)
				$word = preg_replace ("/\\'/", "'", $sqword);
				$words[$word] = $word;                               
			}
		}

		// Transfer DOUBLE quoted words (if any) to the words array
		if (count ($dqwords, COUNT_RECURSIVE) > 2) {
			foreach (array_unique ($dqwords[1]) as $dqword) {
				// Strip the extracted strings from escapes
				// (these will be reinserted during generation)

				$word = Language::removePhpSlashes ($dqword);
				$words["$word"] = "$word";                               
			}
		}

		foreach (array_unique ($words) as $word) {

			if (isset ($lang[$word])) {
				if (!isset ($translated[$word])) {
					$translated[$word] = $lang[$word];
				}
				unset ($unused[$word]);
			} else {

				// Handle punctuations at the end of the string (cf. comments in lib/init/tra.php)
				// For example, if word == 'Login:', we don't keep it if we also have a string 'Log In'
				//   (except if we already have an explicit translation for 'Log In:')
				//
				$word_length = strlen($word);
				$word_last_char = $word[$word_length - 1];
				if ( in_array($word_last_char, $punctuations) ) {
					$word = substr($word, 0, $word_length - 1);
					if ( isset($lang[$word]) ) {
	               $translated[$word] = $lang[$word];
	               unset ($unused[$word]);
	               continue;
					}
				}

				if (!isset ($to_translate[$word])) {
					$to_translate[$word]=$word;
				}

			}

			if ($module) {
				if (isset ($modulename[$word])) {
					if (!strpos ($modulename[$word], $file)) {
						$modulename[$word] = $modulename[$word] .', '. $file;
					}
				} else {
					$modulename[$word] = $file;
				}
			}
		}
	} // foreach ($files as $file)

	//////////////////////////////////////////////
	if ($patch) {
		foreach ($unused as $key => $val) {
			if (isset ($patchLang[$key])) {
				$unused[$key] = $patchLang[$key];
			}
		}

		foreach ($to_translate as $key => $val) {
			if (isset ($patchLang[$key])) {
				// $to_translate[$key] = $patchLang[$key];
				// We are removing words from the to_translate list,
				// since they are provided by the patch
				unset ($to_translate[$key]);
			}
		}

		foreach ($translated as $key=>$val) {
			if (isset ($patchLang[$key])) {
				$translated[$key] = $patchLang[$key];
			}
		}
	}

	if ( $completion ) {
		if ( $script_mode ) {
			echo "\n" . $sel;
			echo " | " . round((count($translated)*100)/(count($translated)+count($to_translate))) . '%';
			echo " | " . count($translated);
			echo " | " . count($to_translate);
			echo " | " . count ($unused);
		} else {
			echo "<tr>";
			echo "<td style='text-align:center;'>$sel</td>";
			echo "<td style='text-align:center;'>" . round((count($translated)*100)/(count($translated)+count($to_translate))) . '%' . "</td>";
			echo "<td style='text-align:right;'>" . count($translated)    . "</td>";
			echo "<td style='text-align:right;'>" . count($to_translate)  . "</td>";
			echo "<td style='text-align:right;'>" . count ($unused)       . "</td>";
			echo "</tr>";
		}
		continue;
	}

	unset ($unused['']);
	if (count ($unused) > 0) {
		if ('en' != $sel && !$nosections) {
			writeFile_and_User ($fw, "// ### Start of unused words\n");
			writeFile_and_User ($fw, "// ### Please remove manually!\n");
			writeFile_and_User ($fw, "// ### N.B. Legitimate strings may be marked");
			writeFile_and_User ($fw, "// ### as unused!\n");
			writeFile_and_User ($fw, "// ### Please see http://tiki.org/UnusedWords for further info\n");
		}
		foreach ($unused as $key => $val) {
			writeTranslationPair ($fw, $key, $val);
			addToWordlist ($wordlist, $val);
			if ($tagunused || $tagunused == "y") {
				writeFile_and_User ($fw, " // ## UNUSED \n");
			}
			else
			{
				writeFile_and_User ($fw, "\n");
			}

		}
		if ('en' != $sel && !$nosections) {
			writeFile_and_User ($fw, "// ### end of unused words\n\n");
		}
		unset($unused); // free memory
	}

	unset ($to_translate['']);
	if (count ($to_translate) > 0) {
		if ('en' != $sel && !$nosections) {
			writeFile_and_User ($fw, "// ### start of untranslated words\n");
			writeFile_and_User ($fw,
					"// ### uncomment value pairs as you translate\n");
		}
		foreach ($to_translate as $key => $val) {
			writeFile_and_User ($fw, "// ");
			writeTranslationPair ($fw, $key, $val);
			if ($module || $close) {
				$closeText  = "";
				$moduleText = "";
				if ($close) {
					$dist = 256;
					foreach ($dictionary as $english=>$trans) {
						$d = levenshtein (strtolower (substr ($key, 0, 255)),
								strtolower (substr ($english, 0, 255)));
						if ($d < $dist) {
							$dist = $d;
							$closeTrans   = $trans;
							$closeEnglish = $english;
						}
					}

					if ($dist < 1 + strlen ($key)/5) {
						$closeText = ' // ## CLOSE: "' . Language::addPhpSlashes ($closeEnglish) .
							'" => "' . Language::addPhpSlashes ($closeTrans) . '",';
					}
				}

				if ($module) {
					$moduleText = " // ## MODULES " . $modulename[$key];
				}

				writeFile_and_User ($fw, "${closeText}${moduleText}");
			}
			writeFile_and_User ($fw, "\n");
		}
		if ('en' != $sel && !$nosections) {
			writeFile_and_User ($fw, "// ### end of untranslated words\n");
			writeFile_and_User ($fw, "// ###\n\n");
		}
	}

	if ('en' != $sel && !$nosections) {
		writeFile_and_User($fw, "// ###\n");
		writeFile_and_User ($fw,"// ### start of possibly untranslated words\n");
		writeFile_and_User($fw, "// ###\n\n");
	}
	foreach ($translated as $key => $val) {
		if ($key == $val) {
			writeTranslationPair ($fw, $key, $val);
			addToWordlist ($wordlist, $val);
			if ($module) {
				writeFile_and_User ($fw, ' // '. $modulename[$key]);
			}
			writeFile_and_User ($fw, "\n");
		}
	}
	if ('en' != $sel && !$nosections) {
		writeFile_and_User($fw, "// ###\n");
		writeFile_and_User($fw, "// ### end of possibly untranslated words\n");
		writeFile_and_User($fw, "// ###\n\n");
	}

	foreach($translated as $key => $val) {
		if ($key != $val) {
			writeTranslationPair ($fw, $key, $val);
			addToWordlist ($wordlist, $val);
			if ($module) { 
				writeFile_and_User ($fw, ' // '. $modulename[$key]);
			}
			writeFile_and_User ($fw, "\n");
		}
	}
	writeFile_and_User ($fw, '"'.$endMarker.'"=>"'.$endMarker.'");'."\n");
	fclose ($fw);

	if ($spelling) {
		$fw = fopen("lang/$sel/spellcheck_me.txt", 'w');
		if ( ! $fw ) die("The file lang/$sel//spellcheck_me.txt can not be written");
		ksort ($wordlist);
		reset ($wordlist);
		foreach ($wordlist as $word => $dummy) {
			fwrite ($fw, "$word\n");
		}

		fclose ($fw);
	}

	@unlink ("lang/$sel/old.php");
	rename ("lang/$sel/language.php","lang/$sel/old.php");
	rename ("lang/$sel/new_language.php","lang/$sel/language.php");

	if ($group_w) {
		// chmod the file to be writeable also by group for users that do not
		// have root access
		chmod ("lang/$sel/language.php", 0664); 

		umask($old_umask); // Reset umask back to original value
	}
}
if ( ! $completion && ! $quiet ) {
	formatted_print("\nProcessing time: " . $tiki_timer->stop("processing") . " seconds");
	formatted_print("\nTotal time spent: " . $tiki_timer->elapsed() . " seconds\n");
} elseif ( $completion && ! $script_mode ) {
	echo "</table>";
} else {
	echo "\n";
}

if ( ! $script_mode ) echo '</body>';
