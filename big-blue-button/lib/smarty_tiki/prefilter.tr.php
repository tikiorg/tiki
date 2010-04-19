<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_prefilter_tr($source) {
  // Now replace the matched language strings with the entry in the file
//  $return = preg_replace_callback('/\{tr[^\{]*\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
// correction in order to match when a variable is inside {tr} tags. Example: {tr}The newsletter was sent to {$sent} email addresses{/tr}, and where there are parameters with {tr} 
// take away the smarty comments {* *} in case they have tr tags
$return=$source;
  $return = preg_replace_callback('/(?s)(\{tr\})(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $return));
  return $return;
}

function _translate_lang($key) {
	include_once ('lib/init/tra.php');
	$s = tra($key[2]);
	if ( $s == $key[2] && strstr($key[2], '{$') ) {
		return $key[1].$key[2].'{/tr}';// keep the tags to be perhaps translated in block.tr.php
	} else {
		return $s;
    }
}
