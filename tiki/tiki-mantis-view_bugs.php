<?php
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/mantislib.php');

if ($feature_mantis != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_mantis");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_mantis_view != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

 
#	$c_offset = db_prepare_int( $f_offset );
#	$t_project_id = helper_get_current_project();

$smarty->assign('mid', 'tiki-mantis-view_bugs.tpl');
$smarty->display("tiki.tpl");

?>
