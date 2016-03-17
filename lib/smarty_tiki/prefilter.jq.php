<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Prefilter {jq} contents - replace {{ with {literal} etc
 * @param $source from smarty (raw)
 * @return unknown_type
 *
 * Doesn't check $prefs['feature_jquery'] here as prefilter only loaded if enabled (in lib/setup/javascript.php)
 */

function smarty_prefilter_jq($source)
{
	if (strpos($source, '{jq') === false) {
		return $source;			// quick escape if no jq tags
	}
	$return = preg_replace_callback('/(?s)(\{jq.*?\})(.+?)\{\/jq\}/', '_escape_smarty_jq', $source);

	return $return;
}

function _escape_smarty_jq($key)
{
	$s = $key[2];
	if (preg_match('/\{literal\}/Ums', $s)) {
		return $key[1].$s.'{/jq}';	// don't parse {{s if already escaped
	}
	$s = preg_replace('/(?s)\{\*.*?\*\}/', '', $s);
	$s = preg_replace('/(?s)\{\{/', '{/literal}{', $s);					// replace {{ with {/literal}{ and wrap with {literal}
	$s = preg_replace('/(?s)\}\}/', '}{literal}', $s);					// close }}s
	$s = preg_replace('/(?s)\{literal\}\s*\{\/literal\}/', '', $s);		// remove empties
	return !empty($s) ? $key[1].'{literal}'.$s.'{/literal}{/jq}' : '';	// wrap
}
