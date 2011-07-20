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

function smarty_prefilter_log_tpl($source, &$smarty) {
	global $prefs;

	if ($prefs['log_tpl'] != 'y' || $smarty->_current_file == 'evaluated template' ||
			in_array($smarty->_current_file, array('tiki.tpl','error.tpl'))) {	// suppress log comment for templates that generate a DOCTYPE which must be output first
		return $source;
	}
	return '<!-- TPL: '.$smarty->_current_file.' -->'.$source.'<!-- /TPL: '.$smarty->_current_file.' -->';
}
