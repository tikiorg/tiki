<?php
require_once ('tiki-setup.php');
include_once ('lib/admin/magiclib.php');

if ($prefs['feature_magic'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_magic");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
	$headerlib->add_cssfile('css/spanky.css');

$features = $magiclib->get_child_features(0);

$featurelist = '';
get_features_tree(0);
function get_features_tree($featureid, $path) {
	global $magiclib, $featurelist;
	$features = $magiclib->get_child_features($featureid);
	if ($features) {
		foreach($features as $feature) {
			$featurelist .= '<li class="' . $feature['feature_type'] . '">';
			$featurelist .= $feature['feature_name'];
			if ($feature['setting_name'] != '') {
				$featurelist .= '(' . $feature['setting_name'] . ')';
			}
			$magiclib->update_feature_specials($feature, $path . '/' . $feature['feature_id']);
			$featurelist .= "<ul>";
				get_features_tree($feature['feature_id'], $path . '/' . $feature['feature_id']);
			$featurelist .= '</ul>';
			$featurelist .= '</li>';
		}
	}
}

$smarty->assign('featurelist', $featurelist);

// Display the template
$smarty->assign('mid', 'tiki-pandora.tpl');
$smarty->display("tiki.tpl");
?>
