<?php
include_once ('lib/admin/magiclib.php');

if ($prefs['feature_magic'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_magic");
	$smarty->display("error.tpl");
	die;
}

$headerlib->add_cssfile('css/spanky.css');
$feature = $smarty->get_template_vars('feature');

// If the feature hasn't been set,  try get it from the query string.
if ($feature == null && isset($_REQUEST['featurechain'])) {
	$featureChain = $_REQUEST['featurechain'];
} else {
	$featureChain = $feature['feature_path'];
}

$featurePage = explode('/', $featureChain);
$featureId = $featurePage[count($featurePage) - 1];
if (isset($featurePage[2])) {
	$topLevelId = $featurePage[2];
} else {
	$topLevelId = '';
}
$smarty->assign('toplevel', $topLevelId);
if (count($featurePage) > 3) {
	$secondLevelId = $featurePage[3];
	$smarty->assign('secondlevelId', $secondLevelId);
}
if (count($featurePage) > 4) {
	$thirdLevelId = $featurePage[4];
	$smarty->assign('thirdlevelId', $thirdLevelId);
}

$topLevelFeatures = $magiclib->get_child_features(1, 'containers');

if ($topLevelId != '' && is_numeric($topLevelId)) {
	$secondLevelFeatures = $magiclib->get_child_features($topLevelId, 'containers');
} else {
	$secondLevelFeatures = '';	
}

if (isset($secondLevelId) && $secondLevelId != '' && is_numeric($secondLevelId)) {
	$thirdLevelFeatures = $magiclib->get_child_features($secondLevelId, 'containers');
} else {
	$thirdLevelFeatures = '';	
}

$smarty->assign_by_ref('toplevelfeatures', $topLevelFeatures);
$smarty->assign_by_ref('secondlevel', $secondLevelFeatures);
$smarty->assign_by_ref('thirdlevel', $thirdLevelFeatures);


?>
