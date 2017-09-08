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

// To disable for certain templates where this would break, temporarily set a log_tpl template variable to false.
function smarty_prefilter_log_tpl($source, $smarty)
{
	global $prefs;
	if ($prefs['log_tpl'] != 'y' || $smarty->getTemplateVars('log_tpl') === false) {
		return $source;
	}

	$resource = $smarty->template_resource;

	// Refrain from logging for some templates
	if (
			strpos($resource, 'eval:') === 0 || // Evaluated templates 
			strpos($resource, 'mail/') !== false // email tpls
			) {
		return $source;
	}
	
	// The opening comment cannot be inserted before the DOCTYPE in HTML documents; put it right after.
	$commentedSource = preg_replace('/^<!DOCTYPE .*>/i', '$0' . '<!-- TPL: ' . $resource . ' -->', $source, 1, $replacements);
	if ($replacements) {
		return $commentedSource . '<!-- /TPL: ' . $resource . ' -->';
	}
	
	return '<!-- TPL: ' . $resource . ' -->' . $source . '<!-- /TPL: ' . $resource . ' -->';
}
