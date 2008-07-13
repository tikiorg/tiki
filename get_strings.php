<?php

// $Id: /cvsroot/tikiwiki/tiki/get_strings.php,v 1.49.2.1 2007-12-26 17:27:37 pkdille Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/** \file
 * $Id: /cvsroot/tikiwiki/tiki/get_strings.php
 * \brief Update the language.php files
 * call example: get_strings.php?lang=fr&close

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

 */



////////////////////////////////////////////////////////////////////////////
/// functions

$addPHPslashes = Array ("\n" => '\n',
			"\r" => '\r',
			"\t" => '\t',
			'\\' => '\\\\',
			'$'  => '\$',
			'"'  => '\"');


/**
  * Reads all the permission descriptions in tikiwiki database and writes
  *   it to the file $file. All the strings will be surrounded by smarty translate tags
  *     ex: {tr}perm description{/tr}
  *
  * @param $file string: target file for the perms
  * @returns: nothing but creates the file with the perms (take care about the acl's in the target directory !)
  */
function collect_perms_desc($file)
{
  global $tikilib;

  $result = $tikilib->query("SELECT DISTINCT(permDesc) FROM users_permissions ORDER BY permDesc");

  $perm_strings = array();
  while( $row = $result->fetchRow() )
    $perm_strings[] = $row['permDesc'];

  $pstr = fopen($file,'w');
  foreach ($perm_strings as $strg)
  {
    fwrite ($pstr,  "{tr}" . $strg . "{/tr}" . "\n");
  }
  fclose($pstr);
}

function addphpslashes ($string) {
  // Translate as in "Table 7-1 Escaped characters" in the PHP manual
  // $string = str_replace ("\n", '\n',   $string);
  // $string = str_replace ("\r", '\r',   $string);
  // $string = str_replace ("\t", '\t',   $string);
  // $string = str_replace ('\\', '\\\\', $string);
  // $string = str_replace ('$',  '\$',   $string);
  // $string = str_replace ('"',  '\"',   $string);
  // We skip the exotic regexps for octal an hexadecimal
  // notation - \{0-7]{1,3} and \x[0-9A-Fa-f]{1,2} -
  // since they should not apper in english strings.
  // return $string;
  global $addPHPslashes;
  return strtr ($string, $addPHPslashes);
}


$removePHPslashes = Array ('\n'   => "\n",
			   '\r'   => "\r",
			   '\t'   => "\t",
			   '\\\\' => '\\',
			   '\$'   => '$',
			   '\"'   => '"');

function removephpslashes ($string) {
  // $string = str_replace ('\n',   "\n", $string); 
  // $string = str_replace ('\r',   "\r", $string);
  // $string = str_replace ('\t',   "\t", $string);
  // $string = str_replace ('\\\\', '\\', $string);
  // $string = str_replace ('\$',   '$',  $string);
  // $string = str_replace ('\"',   '"',  $string);
  // We skip the exotic regexps for octal an hexadecimal
  // notation - \{0-7]{1,3} and \x[0-9A-Fa-f]{1,2} - since they 
  // should not apper in english strings.
  if (preg_match ('/\{0-7]{1,3}|\x[0-9A-Fa-f]{1,2}/', $string, $match)) {
    trigger_error ("Octal or hexadecimal string '".$match[1]."' not supported",
		   E_WARNING);

  }
  // return $string;
  global $removePHPslashes;
  return strtr ($string, $removePHPslashes);
}

function hardwire_file ($file)
{
  global $files, $completion;
  $files[] = $file;
  if (!$completion)
	  print "File (hardwired): $file<br />";
}

function collect_files ($dir)
{
  global $files, $completion;
 
  $handle = opendir ($dir);
  while (false !== ($file = readdir ($handle))) {
    // Skip current and parent directory
    // also skip other directories which may contain source code
    // that should not be translated (the directories normally contain
    // temporary results etc.)
    // Please note that these directories will be skipped on all levels
    if ('.'   == $file || '..'          == $file || 
       'lang' == $file || 'templates_c' == $file || 'dump'  == $file ||
       'temp' == $file || 'img'         == $file || 'cache' == $file) {
      continue;
    }

    $filepath = $dir . '/' . $file;
    if (preg_match ("/.*\.(tpl|php)$/", $file)) {
	  if (!$completion)
		  print("File: $filepath<br />");
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
  print (nl2br ($outstring));
  fwrite ($fd, $outstring);
}

function writeTranslationPair (&$fd, &$key, &$val) {
  writeFile_and_User ($fd, 
		      '"' . addphpslashes ($key) . '"' . " => " .
		      '"' . addphpslashes ($val) . '",');
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
////////////////////////////////////////////////////////////////////////////

require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
  die("You need to be admin to run this script");
}

$completion = isset($_REQUEST['completion']) && $_REQUEST['completion']=='y';
if (!$completion)
	echo "Initialization time: ", $tiki_timer->elapsed(), " seconds<br />\n";
$tiki_timer->start("files");

$comments = isset ($_REQUEST['comments']);
$close    = isset ($_REQUEST['close'])  || $comments;
$module   = isset ($_REQUEST['module']) || $comments;
$patch    = isset ($_REQUEST['patch']);
$spelling = isset ($_REQUEST['spelling']);
$group_w  = isset ($_REQUEST['groupwrite']);
$tagunused= isset ($_REQUEST['tagunused']);

$nohelp     = isset ($_REQUEST['nohelp']);
$nosections = isset ($_REQUEST['nosections']);

// Get the language(s)
$languages = Array();
print("Languages: ");
if (isset ($_REQUEST["lang"])) {
  $lang = $_REQUEST["lang"];
  $languages[] = $lang;
  print ("$lang");  
}
else {
  $handle=opendir ('lang');
  while (false !== ($lang = readdir ($handle))) {
    if($lang == '.' || $lang == '..')
      continue;
    if( is_dir( "lang/$lang" ) && is_file( "lang/$lang/language.php" ) )
    {
	print("$lang ");  
	$languages[] = $lang;
    }
  }
  closedir ($handle);
}    	
print("<br />");  


$files = Array();

$wordlist = Array ();

## When collecting files we need to add a file since the directory which it
## is placed in is excluded. We should keep hardwiring to a minimum.
## In a normal case a file that should be translated should not exist in
## a (sub)directory that is excluded. In this (unfortunate) case it seems that
## the files are placed in a logical location.
collect_files ('.');
hardwire_file ('./lang/langmapping.php');
hardwire_file ('./img/flags/flagnames.php');

## Adding a file in ./temp which contains all the perms descriptions
## This file is called permstrings.tpl. The extension has to be .tpl in order to be
##   taken in charge by the scipt (tpl or php)
## This file is, of course, temporary and will be deleted during the next cache clear !

$permsfile = "./temp/permstrings.tpl";
$permsstrgs = collect_perms_desc($permsfile);
hardwire_file ($permsfile);

// Sort files to make generated strings appear in language.php in the same 
// order across different systems
if ((!isset($_REQUEST["sort"]) || $_REQUEST["sort"] != 'n') && !$completion) {
	echo "Sorting files...";
	flush();
	sort($files);
	echo count($files), " items done.<br />\nTiki directory parsed in: ", $tiki_timer->stop("files"), " seconds<br />\n<br />\n";
	flush();
}
$tiki_timer->start("processing");

if ( $completion) {
    echo "<table border='1'>";
      echo "<tr>";
        echo "<td><b>Language</b></td>";
        echo "<td><b>Completion</b></td>";
        echo "<td><b>Translated</b></td>";
        echo "<td><b>To Translate</b></td>";
        echo "<td><b>Unused</b></td>";
      echo "</tr>";
  }

$oldEndMarker = '##end###';
$endMarker = '###end###';
foreach ($languages as $sel) {
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
  
    print("&lt;");
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
    writeFile_and_User ($fw, "// Parameters:\n\n");
    writeFile_and_User ($fw, "// lang=xx    : only tranlates language 'xx',\n");
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
    writeFile_and_User ($fw, "// Prepare all languages for release \n\n\n");

		writeFile_and_User ($fw, "// ### Note for translators about translation of text ending with colons (':')\n");
		writeFile_and_User ($fw, "// ###\n");
		writeFile_and_User ($fw, "// ### Short version: it is not a problem that string \"Login:\" has no translation. Only \"Login\" needs to be translated.\n");
		writeFile_and_User ($fw, "// ###\n");
		writeFile_and_User ($fw, "// ### Technical justification:\n");
		writeFile_and_User ($fw, "// ### If a string ending with colon needs translating (like \"{tr}Login:{/tr}\")\n");
		writeFile_and_User ($fw, "// ### then TikiWiki tries to translate 'Login' and ':' separately.\n");
		writeFile_and_User ($fw, "// ### This allows to have only one translation for \"{tr}Login{/tr}\" and \"{tr}Login:{/tr}\"\n");
		writeFile_and_User ($fw, "// ### and it still allows to translate \":\" as \"&nbsp;:\" for languages that \n");
		writeFile_and_User ($fw, "// ### need it (like french)\n");

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

      // Extract from SINGLE qouted strings
      preg_match_all ('!\W(?:haw)?tra\s*\(\s*\'(.+?)\'\s*[\),]!s', $data, $sqwords);

      // Extract from DOUBLE quoted strings
      preg_match_all ('!\W(?:haw)?tra\s*\(\s*"(.+?)"\s*[\),]!s', $data, $dqwords);
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
      preg_match_all ('/(?s)\{tr\}(.+?)\{\/tr\}/', $data, $uqwords);
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
	
	$word = removephpslashes ($dqword);
	$words["$word"] = "$word";                               
      }
    }

    foreach (array_unique ($words) as $word) {
      if (isset ($lang[$word])) {
	if (!isset ($translated[$word])) {
	  $translated[$word] = $lang[$word];
	}
	unset ($unused[$word]);
      }
      else {
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

  if ($completion) {
    echo "<tr>";
      echo "<td style='text-align:center;'>$sel</td>";
      echo "<td style='text-align:center;'>" . round((count($translated)*100)/(count($translated)+count($to_translate))) . '%' . "</td>";
      echo "<td style='text-align:right;'>" . count($translated)    . "</td>";
      echo "<td style='text-align:right;'>" . count($to_translate)  . "</td>";
      echo "<td style='text-align:right;'>" . count ($unused)       . "</td>";
    echo "</tr>";
    continue;
  }

  unset ($unused['']);
  if (count ($unused) > 0) {
    if ('en' != $sel && !$nosections) {
      writeFile_and_User ($fw, "// ### Start of unused words\n");
      writeFile_and_User ($fw, "// ### Please remove manually!\n");
      writeFile_and_User ($fw, "// ### N.B. Legitimate strings may be marked");
      writeFile_and_User ($fw, "// ### as unused!\n");
      writeFile_and_User ($fw, "// ### Please see http://tikiwiki.org/tiki-index.php?page=UnusedWords for further info\n");
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
	    $closeText = ' // ## CLOSE: "' . addphpslashes ($closeEnglish) .
	                 '" => "' . addphpslashes ($closeTrans) . '",';
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
  print ("?&gt;<br />\n");  
  fwrite ($fw, '?>'."\n");  
  fclose ($fw);

  if ($spelling) {
    $fw = fopen("lang/$sel/spellcheck_me.txt", 'w');
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
if (!$completion) {
	echo "Processing time: ", $tiki_timer->stop("processing"), " seconds<br />\n";
	echo "Total time spent: ", $tiki_timer->elapsed(), " seconds<br />\n";
 }
 else
 {
   echo "</table>";
 }
?>
