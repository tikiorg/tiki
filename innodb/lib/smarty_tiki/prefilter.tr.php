<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Smarty translation prefilter. This prefilter tries to offload the tr block from as much work as possible to keep
// the performance penalty of translation limited to compilation. It does not intervene if an argument is given (lang)
// and in some cases when translation may only be possible at runtime.

function smarty_prefilter_tr($source) {
	// The preg_replace() takes away the Smarty comments ({* *}) in case they have tr tags
	$return = preg_replace_callback('/(?s)\{tr\}(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $source));
	return $return;
}

function _translate_lang($matches) {
	include_once ('lib/init/tra.php');
	$s = tra($matches[1]);
	if ( $s == $matches[1] && strstr($matches[1], '{$') ) {
		// The string to translate is not plain English. It contains a Smarty variable, which may prevent translation at compile time.
		// Leave the whole match ("tr call") intact so block.tr.php can attempt a new translation at runtime.
		return $matches[0];
	} else {
		return $s;
    }
}
