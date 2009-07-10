<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

include_once ('lib/admin/magiclib.php');
if ($prefs['feature_magic'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_magic");
	$smarty->display("error.tpl");
	die;
}
$feature = $smarty->get_template_vars('feature');
$features = $magiclib->get_child_features($feature['feature_id'], 'feature');
if ($_POST['submit'] != '') {
	check_ticket('admin_features');
	foreach($features as $feature) {
		simple_set_toggle($feature['setting_name']);
	}
}
$smarty->assign('features', $features);
ask_ticket('admin_features');
