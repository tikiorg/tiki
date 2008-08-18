<?php
include_once ('lib/admin/magiclib.php');

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
$topLevelId = $featurePage[2];
if (count($featurePage) > 3) {
	$secondLevelId = $featurePage[3];
}

$topLevelFeatures = $magiclib->get_child_features(1, 'containers');
$secondLevelFeatures = '';

if ($topLevelId != '' && is_numeric($topLevelId)) {
	$secondLevelFeatures = $magiclib->get_child_features($topLevelId, 'containers');
}

$smarty->assign_by_ref('toplevelfeatures', $topLevelFeatures);
$smarty->assign_by_ref('secondlevel', $secondLevelFeatures);
$smarty->assign('toplevel', $topLevelId);
if (count($featurePage) > 1) {
	$smarty->assign('secondlevelId', $secondLevelId);
}
?>
