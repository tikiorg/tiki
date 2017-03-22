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

function smarty_prefilter_log_tpl($source, $smarty)
{
	global $prefs;
	if ($prefs['log_tpl'] != 'y') {
		return $source;
	}

	$current_file = $smarty->_current_file;

	// Refrain from logging for some templates
	if (
			strpos($smarty->template_resource, 'eval:') === 0 || 	// Evaluated templates 
			strpos($source, '<!DOCTYPE ') === 0 || // templates that generate a DOCTYPE, which must be output first
			strpos($current_file, '/mail/') !== false // email tpls
			) {
		return $source;
	}
	return '<!-- TPL: ' . $current_file . ' -->' . $source . '<!-- /TPL: ' . $current_file . ' -->';
}
