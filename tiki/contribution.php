<?php
// $Header: /cvsroot/tikiwiki/tiki/contribution.php,v 1.3 2006-03-09 20:31:19 sylvieg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra('This script cannot be called directly'));
  $smarty->display('error.tpl');
  die;
}

require_once('tiki-setup.php'); 

if ($feature_contribution == 'y') {
	global $contributionlib; include_once('lib/contribution/contributionlib.php');
	$contributions = $contributionlib->list_contributions();
	if (!empty($_REQUEST['contributions'])) {
		for ($i = $contributions['cant'] - 1; $i >= 0; -- $i) {
			if ($contributions['data'][$i]['description'])
				$contributionHelp .= $contributions['data'][$i]['name'].": ".$contributions['data'][$i]['description'];
			if (in_array($contributions['data'][$i]['contributionId'], $_REQUEST['contributions']))
				$contributions['data'][$i]['selected'] = 'y';
		}
	}
	$smarty->assign_by_ref('contributions', $contributions['data']);
}
?>