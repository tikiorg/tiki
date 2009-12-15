<?php
require_once ('tiki-setup.php');
include_once ('lib/admin/magiclib.php');
$featureChain = $_REQUEST['featurechain'];
$featurePage = explode('/', $featureChain);
$featureId = $featurePage[count($featurePage) - 1];


if ($prefs['feature_magic'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_magic");
	$smarty->display("error.tpl");
	die;
}

$feature = $magiclib->get_feature($featureId);
$smarty->assign('feature', $feature);
$smarty->assign('title', $feature['feature_name']);
/*
 * $$feature['permission'] is slightly magic.  It's checking the value of the name of the variable that is in feature['permission'].
 * If feature['permission'] is 'tiki_p_wiki_admin', it is checking if $tiki_p_wiki_admin has the value 'y'.
 */
if ($$feature['permission'] != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if( isset( $_REQUEST['refresh'] ) ) {
	$magiclib->reload_features();
}

$headerlib->add_cssfile('css/spanky.css');
$template ='tiki-magic';

// If there's an assigned template for a feature;  show it if the feature has no children (i.e. it's a leaf feature), or if 
// the feature is a container type (to allow overriding the setting list behaviour).
// For a feature/system thing; the template shouldn't be used, as that would prevent you from configuring it.
if ($feature['template'] != '' 
	&& $feature['feature_count'] == 0 
	&& $magiclib->is_container($feature) ) {

	if ($feature['templateinclude'] != '') {
		include_once($feature['templateinclude']);
		$mid = $smarty->get_template_vars('mid');
		if( is_null( $mid ) ) {
			$smarty->assign('mid', $feature['smartytemplate']);
			
			// Containers need to display the tiki, and that's just the way it is.
			$smarty->display("tiki.tpl");
		}
	} else {
		// if there's a template, but it's not an include; redirect.
		header("HTTP/1.1 301 Moved Permanently");
		header ("Location:" . $feature['pageurl']);
	}
	exit;
}
$pagefeatures = array($feature);
$containers = array();
$hasCategories = false;
$hasLanguages = false;
$hasTimezones = false;

if ($featureId != '' && is_numeric($featureId)) {
	get_features($featureId);
} else {
	get_features(0);
	$featureId = 0;
}

if ($_POST['submit'] != '') { /* Warning Level Fix:  Check if the 'submit' is a key first */
	check_ticket('tiki_magic');
	foreach($pagefeatures as $feature) {
		if ($feature['feature_type'] == 'flag') {
			$magiclib->simple_set_toggle($feature['setting_name']);
		} else if ($feature['feature_type'] == 'int') {
			$magiclib->simple_set_int($feature['setting_name']);
		} else if ($feature['feature_type'] == 'simple') {
			$magiclib->simple_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'byref') {
			$magiclib->byref_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'feature' || $feature['feature_type'] == 'subfeature') {
			$magiclib->simple_set_toggle($feature['setting_name']); // save the toggling of features.
		} else {
			// All of the special settings are simple underneath.
			if($feature['multiple'] == 'on') {
				$magiclib->simple_set_value($feature['setting_name'],'',true);			
			}
			else {
				$magiclib->simple_set_value($feature['setting_name']);
			}
		}
	}
	
	// Reset the list of features.
	$pagefeatures = array($magiclib->get_feature($featureId));
	get_features($featureId);
}
ask_ticket('tiki_magic');
$smarty->assign_by_ref('containers', $containers);
$smarty->assign('features', $pagefeatures);

// Display the template
$smarty->assign('mid', 'tiki-magic.tpl');
$smarty->display("tiki.tpl");

// Recursively get the features underneath the specified feature id.
function get_features($featureid, $keepContainers = true) {
	global $magiclib, $pagefeatures, $containers, $prefs;
	$features = $magiclib->get_child_features($featureid);
	$cont = array();

	if ($features) {
		foreach($features as $feature) {
			if ($keepContainers && $magiclib->is_container($feature) && $feature['feature_count'] > 0) {
				$cont[] = $feature;
			} else {
				$pagefeatures[] = $feature;
				$pref = $feature['setting_name'];
				if( !isset($prefs[$pref]) || $prefs[$pref] == 'y' )
					get_features($feature['feature_id'], false);
			}
		}
		foreach($cont as $feature) {
			$containers[] = $feature;
			$pagefeatures[] = $feature;
			get_features($feature['feature_id'], false);
		}
	}
}
?>
