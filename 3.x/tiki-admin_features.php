<?php
include_once ('lib/admin/magiclib.php');

if ($prefs['feature_magic'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_magic");
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
?>
