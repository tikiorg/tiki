<?php
require_once ('tiki-setup.php');
include_once ('lib/admin/magiclib.php');

$featureChain = $_REQUEST['featurechain'];
$featurePage = explode('/', $featureChain);
$featureId = $featurePage[count($featurePage) - 1];

$feature = $magiclib->get_feature($featureId);
$smarty->assign('feature', $feature);
$smarty->assign('title', $feature['feature_name']);

if ($$feature['permission'] != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$headerlib->add_cssfile('css/spanky.css');
$template ='tiki-magic';

// If there's an assigned template for a feature;  show it if the feature has no children (i.e. it's a leaf feature), or if 
// the feature is a container type (to allow overriding the setting list behaviour).
// For a feature/system thing; the template shouldn't be used, as that would prevent you from configuring it.
if ($feature['template'] != '' && ($feature['feature_count'] == 0 || $magiclib->is_container($feature))) {
	// The value of $feature is sometimes clobbered,  so store these values before the include.
	$is_container = $magiclib->is_container($feature);
	$template = $feature['template'];
	
	include_once($template . '.php');
	$smarty->assign('mid', $template . '.tpl');
	
	// Containers need to display the tiki, and that's just the way it is.
	if ($is_container) {
		$smarty->display("tiki.tpl");
	}
	return;
}

$enumerations = array();
// since these are static, populate them each time.  Maybe.  There might be lots, soon...
$enumerations['commentordering'] = array('points_desc'=>tra('points'), 'commentDate_desc'=>tra('newest'), 'commentDate_asc'=>tra('oldest'));
$enumerations['alignment'] = array('left'=>tra('on left side'), 'center'=>tra('on center'), 'right'=>tra('on right side'));

$enumerations['bloguser'] = array('disabled'=>tra('Disabled'),'text'=>tra('Plain text'), 'link'=>tra('Link to user information'), 'avatar'=>tra('User avatar'));
$enumerations['blogorder'] = array('created_desc'=>tra('Creation date (desc)'), 'lastModif_desc'=>tra('Last modification date (desc)'), 'title_asc'=>tra('Blog title (asc)'), 'posts_desc'=>tra('Number of posts (desc)'), 'hits_desc'=>tra('Visits (desc)'),'activity_desc'=>tra('Activity (desc)'));

$enumerations['barlocation'] = array('top'=>tra('Top bar'), 'bottom'=>tra('Bottom bar'), 'both'=>('Both'));
$enumerations['cachelength'] = array('0'=>tra('no cache'), '60'=>'1 ' . tra('minute'), '300'=>'5 ' . tra('minutes'), '600'=>'10 ' . tra('minutes'), '900'=>'15 '. tra('minutes'), '1800'=>'30 ' . tra('minutes'), '3600'=>'1 ' . tra('hour'), '7200'=>'2 ' . tra('hours'));
$enumerations['wikiauthor'] = array('classic'=>tra('as Creator &amp; Last Editor'), 'business'=>tra('Business style'), 'collaborative'=>tra('Collaborative style'), 'lastmodif'=>tra('Page last modified on'), 'none'=>tra('no (disabled)'));
$enumerations['idletimeout'] = array(1=>1,2=>2,5=>5,10=>10,15=>15,30=>30);
$enumerations['wikitablesyntax'] = array('old'=>tra('|| for rows'),'new'=>tra('\n for rows'));
$enumerations['wikidiffs'] = array('old'=>tra('Only with last version'), 'minsidediff'=>tra('Any 2 versions'));
$enumerations['wikilinkformat'] = array('complete'=>tra('complete'), 'full'=>tra('latin'), 'strict'=>tra('english'));

$enumerations['calendartimespan'] = array('1'=>'1 ' . tra('minute'), '5'=>'5 ' . tra('minutes'), '10'=>'10 ' . tra('minutes'), '15'=>'15 ' . tra('minutes'), '30'=>'30 ' . tra('minutes'));
$enumerations['calendarviewmode'] = array('day'=>tra('Day'), 'week'=>tra('Week'), 'month'=>tra('Month'), 'quarter'=>tra('Quarter'), 'semester'=>tra('Semester'), 'year'=>tra('Year'));
$enumerations['firstdayofweek'] = array('6'=>tra('Saturday'),'0'=>tra('Sunday'), '1'=>tra('Monday'),'user'=>tra('Depends user language'));

$pagefeatures = array($feature);
$containers = array();
$hasCategories = false;
$hasLanguages = false;

if ($featureId != '' && is_numeric($featureId)) {
	get_features($featureId);
} else {
	get_features(0);
	$featureId = 0;
}

if ($_POST['submit'] != '') {
	check_ticket('tiki_magic');
	foreach($pagefeatures as $feature) {
		if ($feature['feature_type'] == 'flag') {
			simple_set_toggle($feature['setting_name']);
		} else if ($feature['feature_type'] == 'int') {
			simple_set_int($feature['setting_name']);
		} else if ($feature['feature_type'] == 'simple') {
			simple_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'byref') {
			byref_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'feature') {
			simple_set_toggle($feature['setting_name']); // save the toggling of features.
		} else {
			// All of the special settings are simple underneath.
			simple_set_value($feature['setting_name']);
		}
	}
	
	// Reset the list of features.
	$pagefeatures = array($magiclib->get_feature($featureId));
	get_features($featureId);
}
ask_ticket('tiki_magic');
$smarty->assign_by_ref('containers', $containers);
$smarty->assign('features', $pagefeatures);

// lazy load categories when they are needed.
if ($hasCategories) {
	include_once ('lib/categories/categlib.php');
	$catree = $categlib->get_all_categories();
	$smarty->assign('catree', $catree);
}
if ($hasLanguages) {
	$languages = array();
	$languages = $tikilib->list_languages(false,null,true);
	$smarty->assign_by_ref("languages", $languages);
}

foreach($enumerations as $key=>$value) {
	$smarty->assign($key, $value);
}

// Display the template

$smarty->assign('mid', 'tiki-magic.tpl');
$smarty->display("tiki.tpl");

// Recursively get the features underneath the specified feature id.
function get_features($featureid) {
	global $magiclib, $pagefeatures, $containers, $enumerations, $hasCategories, $hasLanguages;
	$features = $magiclib->get_child_features($featureid);

	if ($features) {
		foreach($features as $feature) {
			if ($magiclib->is_container($feature)) {
				$containers[] = $feature;
			}
			if ($feature['feature_type'] == 'limitcategory' || $feature['feature_type'] == 'selectcategory') $hasCategories = true;
			if ($feature['feature_type'] == 'languages') $hasLanguages = true;

			if (array_key_exists($feature['feature_type'], $enumerations)) {
				$feature['enumeration'] = $enumerations[$feature['feature_type']];
			}
			$pagefeatures[] = $feature;
			get_features($feature['feature_id'], '');
		}
	}
}

// These are helper functions, pretty much as-ganked from tiki-admin.php

function simple_set_toggle($feature) {
	global $_POST, $tikilib, $smarty, $tikifeedback, $prefs;
	$setting = $feature;
	if (isset($_POST[$setting]) && $_POST[$setting] == "on") {
		if ((!isset($prefs[$setting]) || $prefs[$setting] != 'y')) {
			// not yet set at all or not set to y
			$tikilib->set_preference($setting, 'y');
			$prefs[$setting] = 'y';
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s enabled"),$feature));
		}
	} else {
		if ((!isset($prefs[$setting]) || $prefs[$setting] != 'n')) {
			// not yet set at all or not set to n
			$tikilib->set_preference($feature, 'n');
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s disabled"),$feature));
		}
	}
}

function simple_set_value($feature, $pref = '', $isMultiple = false) {
	global $_POST, $tikilib, $prefs;
	
	if (isset($_POST[$feature])) {
		if ( $pref != '' ) {
			$tikilib->set_preference($pref, $_POST[$feature]);
			$prefs[$feature] = $_POST[$feature];
		} else {
			$tikilib->set_preference($feature, $_POST[$feature]);
		}
	} else if( $isMultiple ) {
		// Multiple selection controls do not exist if no item is selected.
		// We still want the value to be updated.
		if ( $pref != '' ) {
			$tikilib->set_preference($pref, array());
			$prefs[$feature] = $_POST[$feature];
		} else {
			$tikilib->set_preference($feature, array());
		}
	}
}

function simple_set_int($feature) {
    global $_POST, $tikilib;
	if (isset($_POST[$feature]) && is_numeric($_POST[$feature])) {
		$tikilib->set_preference($feature, $_POST[$feature]);
	}
}

function byref_set_value($feature, $pref = "") {
	global $_POST, $tikilib;
	simple_set_value($feature, $pref);
}
?>