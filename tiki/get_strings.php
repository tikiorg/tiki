<?php

// $Header: /cvsroot/tikiwiki/tiki/get_strings.php,v 1.26 2003-09-03 21:24:21 docekal Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/** \file
 * $Header: /cvsroot/tikiwiki/tiki/get_strings.php
 * \brief Update the language.php files
 * call example: get_strings.php?lang=fr&close
 * \param lang=xx : only translate lang 'xx' - if the parameter is not given all languages are translated
 * \param comments : generate all comments (equal to close&module)
 * \param close    : look for similar strings that are allready translated and generate a commet if a 'match' is made
 * \param module   : generate comments that describes in which .php and/or .tpl\n module(s) a certain string was found (useful for checking translations in context)
 * \param patch    : looks for the file 'language.patch' in the same directory as the corresponding language.php and overrides any strings in language.php - good if a user does not agree with some translations or if only changes are sent to the maintaner
 */



////////////////////////////////////////////////////////////////////////////
/// functions

$addPHPslashes = Array ("\n" => '\n',
			"\r" => '\r',
			"\t" => '\t',
			'\\' => '\\\\',
			'$'  => '\$',
			'"'  => '\"');

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


function writeFile_and_User ($fd, $outstring) {
  print (nl2br ($outstring));
  fwrite ($fd, $outstring);
}

function writeTranslationPair ($fd, $key, $val) {
  writeFile_and_User ($fd, 
		      '"' . addphpslashes ($key) . '"' . " => " .
		      '"' . addphpslashes ($val) . '",');
}

////////////////////////////////////////////////////////////////////////////

require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
  die("You need to be admin to run this script");
}

$comments = isset ($_REQUEST['comments']);
$close    = isset ($_REQUEST['close'])  || $comments;
$module   = isset ($_REQUEST['module']) || $comments;
$patch    = isset ($_REQUEST['patch']);

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
    print("$lang ");  
    $languages[] = $lang;
  }
  closedir ($handle);
}    	
print("<br/>");  


$files = Array();  

function collect_files ($dir)
{
  global $files;
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
      print("File: $filepath<br/>");
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

collect_files ('.');

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
      die ("No patch file .../$origPatch exisits");
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

  $fw = fopen("lang/$sel/new_language.php",'w');
  
  print("&lt;");
  fwrite($fw,"<");
  writeFile_and_User ($fw, "?php");
  // The comment coding:utf-8 is for the benefit of emacs
  // and must be on the very first line in the file
  // we leave this comment in even if comments are off since
  // editing files with the wrong encoding causes commical effects at best.
  writeFile_and_User ($fw, " // -*- coding:utf-8 -*-\n");

  if (!$nohelp) {
    // Good to have instructions for translators in the release file.
    // The comments get filtered away by Smarty anyway
    writeFile_and_User ($fw, "// parameters:\n");
    writeFile_and_User ($fw, "// lang=xx  : only tranlates language 'xx',\n");
    writeFile_and_User ($fw, "//            if not given all languages are translated\n");
    writeFile_and_User ($fw, "// comments : generate all comments (equal to close&module)\n");
    writeFile_and_User ($fw, "// close    : look for similar strings that are allready translated and\n");
    writeFile_and_User ($fw, "//            generate a commet if a 'match' is made\n");
    writeFile_and_User ($fw, "// module   : generate comments that describes in which .php and/or .tpl\n");
    writeFile_and_User ($fw, "//            module(s) a certain string was found (useful for checking\n");
    writeFile_and_User ($fw, "//            translations in context)\n");

    writeFile_and_User ($fw, "// patch    : looks for the file 'language.patch' in the same directory\n");
    writeFile_and_User ($fw, "//            as the corresponding language.php and overrides any strings\n");
    writeFile_and_User ($fw, "//            in language.php - good if a user does not agree with\n");
    writeFile_and_User ($fw, "//            some translations or if only changes are sent to the maintaner\n");
    writeFile_and_User ($fw, "// Examples:\n");
    writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv\n");
    writeFile_and_User ($fw, "// Will translate langauage 'sv' and (almost) avoiding comment generation\n\n");

    writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv&comments\n");
    writeFile_and_User ($fw, "// Will translate langauage 'sv' and generate all possible comments.\n");
    writeFile_and_User ($fw, "// This is the most usefull mode when working on a translation.\n\n");
    writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?lang=sv&nohelp&nosections\n");
    writeFile_and_User ($fw, "// These options will only provide the minimal amout of comments.\n");
    writeFile_and_User ($fw, "// Usefull mode when preparing a translation for distribution.\n\n");
    writeFile_and_User ($fw, "// http://www.neonchart.com/get_strings.php?nohelp&nosections\n");
    writeFile_and_User ($fw, "// Prepare all languages for release \n\n");
  }


  // Start generating the lang array
  writeFile_and_User ($fw, "\n\$lang=Array(\n");  
  
  foreach ($files as $file) {
    $fp = fopen ($file, "r");
    $data = fread ($fp, filesize ($file));
    fclose ($fp);

    unset ($words);   $words   = Array ();
    unset ($uqwords); $uqwords = Array ();
    unset ($sqwords); $sqwords = Array ();
    unset ($dqwords); $dqwords = Array ();


    // PM for unusual regexps
    // (?m) sets PCRE_MULTILINE which makes '^' and '$' ignore '\n'
    // (?s) sets PCRE_DOTALL which makes that '.' also matches '\n'
    // ?: below makes that the pharentesis is not extracted int the outarray
    // +? below is the nongreedy version of the ? operator


    if (preg_match ("/\.php$/", $file)) {
      // Do not translate PHP comments (we only filter the "safe" cases)
      // Calling php -w <filename> would take care of all comments,
      // but that does not go well with safe-mode.
      $data = preg_replace ("/(?s)\/\*.*?\*\//", "", $data);  // C comments
      $data = preg_replace ("/(?m)^\s*\/\/.*\$/", "", $data); // C++ comments
      $data = preg_replace ("/(?m)^\s*\#.*\$/",   "", $data); // shell comments

      // Only extract tra () and hawtra () in .php-files

      // Extract from SINGLE qouted strings
      preg_match_all ('/(?s)[^a-zA-Z0-9_\x7f-\xff](?:haw)?tra\s*\(\s*\'(.+?)\'\s*\)/', $data, $sqwords);

      // Extract from DOUBLE quoted strings
      preg_match_all ('/(?s)[^a-zA-Z0-9_\x7f-\xff](?:haw)?tra\s*\(\s*"(.+?)"\s*\)/', $data, $dqwords);
    }

    if (preg_match ("/\.tpl$/", $file)) {
      // Do not translate text in Smarty comments: {* Smarty comment *}
      $data = preg_replace ('/(?s)\{\*.*?\*\}/', '', $data); // Smarty comment 

      // Strings of the type {tr}{$perms[user].type}{/tr} need (should)
      // not be translated
      $data = preg_replace ('/(?s)\{tr\}\s*\{[$][^\}]*?\}\s*\{\/tr\}/','',$data);

      // Only extract {tr} ... {/tr} in .tpl-files
      preg_match_all ('/(?s)\{tr\}(.+?)\{\/tr\}/', $data, $uqwords);
    }

    // Transfer UNqouted words (if any) to the words array
    if (count ($uqwords) > 0) {
      $words = $uqwords[1];
    }

    // Transfer SINGLEqouted words (if any) to the words array
    if (count ($sqwords) > 0) {
      foreach (array_unique ($sqwords[1]) as $sqword) {
	// Strip the extracted strings from escapes
	// (these will not be reinserted during generation, since ' need
	// not be escaped when string delimeters are double quotes)
	$word = preg_replace ("/\\'/", "'", $sqword);
	$words[$word] = $word;                               
      }
    }

    // Transfer DOUBLEqouted words (if any) to the words array
    if (count ($dqwords) > 0) {
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
      
      if (isset ($modulename[$word])) {
	if (!strpos ($modulename[$word], $file)) {
	  $modulename[$word] = $modulename[$word] .', '. $file;
	}
      }
      else {
	$modulename[$word] = $file;
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

  unset ($unused['']);
  if (count ($unused) > 0) {
    writeFile_and_User ($fw, "// ### start of unused words\n");
    writeFile_and_User ($fw, "// ### please remove manually!\n");
    foreach ($unused as $key => $val) {
      writeTranslationPair ($fw, $key, $val);
      writeFile_and_User ($fw, "\n");
    }
    writeFile_and_User ($fw, "// ### end of unused words\n\n");
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
	    $closeText = " // ## CLOSE: $closeEnglish=>$closeTrans";
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
      if ($module) { 
	writeFile_and_User ($fw, ' // '. $modulename[$key]);
      }
      writeFile_and_User ($fw, "\n");
    }
  }
  writeFile_and_User ($fw, '"'.$endMarker.'"=>"'.$endMarker.'");'."\n");
  print ("?&gt;<br/>\n");  
  fwrite ($fw, '?>'."\n");  
  fclose ($fw);

  @unlink ("lang/$sel/old.php");
  rename ("lang/$sel/language.php","lang/$sel/old.php");
  rename ("lang/$sel/new_language.php","lang/$sel/language.php");
}
?>