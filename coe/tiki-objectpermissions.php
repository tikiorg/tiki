<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
include_once ("tiki-setup.php");
error_reporting(E_ALL);
if (!empty($_REQUEST['objectType']) && $_REQUEST['objectType'] != 'global') {
	if (!isset($_REQUEST['objectName']) || empty($_REQUEST['objectId'])) {
		$smarty->assign('msg', tra("Not enough information to display this page"));
		$smarty->display("error.tpl");
		die;
	}
}
if (empty($_REQUEST['objectType'])) {
	 $_REQUEST['objectType'] = 'global';
	 $_REQUEST['objectName'] = '';
	 $_REQUEST['objectId'] = '';
}


$auto_query_args = array(
	'referer',
	'reloff',
	'objectName',
	'objectType',
	'permType',
	'objectId',
	'filegals_manager',
	//'show_disabled_features',	// this seems to cause issues - the $_GET version overrides the $_POST one...
);
$perm = 'tiki_p_assign_perm_' . str_replace(' ', '_', $_REQUEST['objectType']);
if ($_REQUEST['objectType'] == 'wiki page') {
	if ($tiki_p_admin_wiki == 'y') {
		$special_perm = 'y';
	} else {
		$info = $tikilib->get_page_info($_REQUEST['objectName']);
		$tikilib->get_perm_object($_REQUEST['objectId'], $_REQUEST['objectType'], $info);
	}
} else if ($_REQUEST['objectType'] == 'global') {
	if ($tiki_p_admin != 'y') {						// is there a better perm for this?
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied you cannot assign permissions for this object"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	$tikilib->get_perm_object($_REQUEST['objectId'], $_REQUEST['objectType']);
	if ($_REQUEST['objectType'] == 'tracker') {
		global $trklib;
		include ('lib/trackers/trackerlib.php');
		if ($groupCreatorFieldId = $trklib->get_field_id_from_type($_REQUEST['objectId'], 'g', '1%')) {
			$smarty->assign('group_tracker', 'y');
		}
	}
}
if (!($tiki_p_admin_objects == 'y' || (isset($$perm) && $$perm == 'y') || (isset($special_perm) && $special_perm == 'y'))) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot assign permissions for this object"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["referer"])) {
	if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'tiki-objectpermissions.php') === false) {
		$_REQUEST["referer"] = $_SERVER['HTTP_REFERER'];
	} else {
		unset($_REQUEST["referer"]);
	}
}
if (isset($_REQUEST["referer"])) {
	$smarty->assign('referer', $_REQUEST["referer"]);
} else {
	$smarty->assign('referer', '');
}
$_REQUEST["objectId"] = urldecode($_REQUEST["objectId"]);
$_REQUEST["objectType"] = urldecode($_REQUEST["objectType"]);
$_REQUEST["permType"] = !empty($_REQUEST['permType']) ? urldecode($_REQUEST["permType"]) : 'all';
$smarty->assign('objectName', $_REQUEST["objectName"]);
$smarty->assign('objectId', $_REQUEST["objectId"]);
$smarty->assign('objectType', $_REQUEST["objectType"]);
$smarty->assign_by_ref('permType', $_REQUEST["permType"]);

if( $_REQUEST['objectType'] == 'wiki' ) {
	$_REQUEST['objectType'] = 'wiki page';
}

require_once 'lib/core/lib/Perms/Applier.php';
require_once 'lib/core/lib/Perms/Reflection/Factory.php';

$objectFactory = Perms_Reflection_Factory::getDefaultFactory();
$currentObject = $objectFactory->get( $_REQUEST['objectType'], $_REQUEST['objectId'] );

$permissionApplier = new Perms_Applier;
$permissionApplier->addObject( $currentObject );

if( $restrictions = perms_get_restrictions() ) {
	$permissionApplier->restrictPermissions( $restrictions );
}

if ($_REQUEST['objectType'] == 'wiki page') {
	global $structlib;
	include_once ('lib/structures/structlib.php');
	$pageInfoTree = $structlib->s_get_structure_pages($structlib->get_struct_ref_id($_REQUEST['objectId']));
	if (count($pageInfoTree) > 1) {
		$smarty->assign('inStructure', 'y');
	}

	// If assign to structure is requested, add subelements to the applier
	if (!empty($_REQUEST['assignstructure']) && $_REQUEST['assignstructure'] == 'on' && !empty($pageInfoTree)) {
		foreach( $pageInfoTree as $subPage ) {
			$sub = $objectFactory->get( $_REQUEST['objectType'], $subPage['pageName'] );
			$permissionApplier->addObject( $sub );
		}
	}
}

if( $_REQUEST['objectType'] == 'category' && isset($_REQUEST['propagate_category']) ) {
	global $categlib; require_once 'lib/categories/categlib.php';
	$descendants = $categlib->get_category_descendants( $_REQUEST['objectId'] );

	foreach( $descendants as $child ) {
		$o = $objectFactory->get( $_REQUEST['objectType'], $child );
		$permissionApplier->addObject( $o );
	}
}

// apply feature filter change
if (isset($_REQUEST['feature_select'])) {
	if (!isset($_REQUEST['feature_filter'])) {
		$_REQUEST['feature_filter'] = array();
	}
	$tikilib->set_user_preference($user, 'objectperm_admin_features', serialize($_REQUEST['feature_filter']));
	$cookietab = '1';
	if ($_REQUEST['permType'] != 'all' && (count($_REQUEST['feature_filter']) > 1 || !in_array($_REQUEST['permType'], $_REQUEST['feature_filter']))) {
		$_REQUEST['permType'] = 'all';
		$_GET['permType'] = 'all';		// for auto_query_args?
	}
}

$feature_filter = unserialize($tikilib->get_user_preference($user, 'objectperm_admin_features'));

// apply group filter change
if (isset($_REQUEST['group_select'])) {
	if (!isset($_REQUEST['group_filter'])) {
		$_REQUEST['group_filter'] = array();
	}
	$tikilib->set_user_preference($user, 'objectperm_admin_groups', serialize($_REQUEST['group_filter']));
	$cookietab = '1';
}

$group_filter = unserialize($tikilib->get_user_preference($user, 'objectperm_admin_groups'));

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'id_asc', '', '', 'n');
$smarty->assign_by_ref('groups', $groups["data"]);

$OBJECTPERM_ADMIN_MAX_GROUPS = 4;

if ($group_filter === false) {
	$c = 0;
	foreach($groups["data"] as $g) {	//	filter out if too many groups and hide Admins by default
		if ($c < $OBJECTPERM_ADMIN_MAX_GROUPS && $g['groupName'] != 'Admins') {
			$group_filter[] = $g['id'];
			$c++;
		}
	}
	if (count($groups["data"]) > $OBJECTPERM_ADMIN_MAX_GROUPS) {
		$cookietab = '2';
		$smarty->assign('groupsFiltered', 'y');
	}
	$tikilib->set_user_preference($user, 'objectperm_admin_groups', serialize($group_filter));
}

// Process the form to assign a new permission to this object
if (isset($_REQUEST['assign']) && !isset($_REQUEST['quick_perms'])) {
	check_ticket('object-perms');
	foreach($_REQUEST['perm'] as $group => $gperms) {
		foreach($gperms as $perm) {
			if ($tiki_p_admin_objects != 'y' && !$userlib->user_has_permission($user, $perm)) {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra('Permission denied'));
				$smarty->display('error.tpl');
				die;
			}
		}
	}
	
	$newPermissions = get_assign_permissions();
	$permissionApplier->apply( $newPermissions );
	if (isset($_REQUEST['group'])) {
		$smarty->assign('groupName', $_REQUEST['group']);
	}
}

if (isset($_REQUEST['remove'])) {
	check_ticket('object-perms');
	
	$newPermissions = new Perms_Reflection_PermissionSet;
	$permissionApplier->apply( $newPermissions );

}

if (isset($_REQUEST['copy'])) {
	$newPermissions = get_assign_permissions();
	$to_copy = array('perms' => $newPermissions->getPermissionArray(), 'object' => $_REQUEST['objectId'], 'type' => $_REQUEST['objectType']);
	$_SESSION['perms_clipboard'] = serialize($to_copy);
}

if (!empty($_SESSION['perms_clipboard'])) {
	$perms_clipboard = unserialize($_SESSION['perms_clipboard']);
	$smarty->assign('perms_clipboard_source', $perms_clipboard['type'] . (empty($perms_clipboard['object']) ? '' : ' : ') . $perms_clipboard['object']);

	if (isset($_REQUEST['paste'])) {
		unset($_SESSION['perms_clipboard']);
		
		$set = new Perms_Reflection_PermissionSet;
	
		if( isset( $perms_clipboard['perms'] ) ) {
			foreach( $perms_clipboard['perms'] as $group => $gperms ) {
				foreach( $gperms as $perm ) {
					$set->add( $group, $perm );
				}
			}
		}
		$permissionApplier->apply( $set );
		$smarty->assign('perms_clipboard_source', '');
	}

}


// Prepare display
// Get the individual object permissions if any

$displayedPermissions = get_displayed_permissions();


//Quickperms {{{
//Test to map permissions of ile galleries into read write admin admin levels.
if( $prefs['feature_quick_object_perms'] == 'y' ) {
	require_once 'lib/core/lib/Perms/Reflection/Quick.php';

	$qperms = quickperms_get_data();
	$smarty->assign('quickperms', $qperms);
	$quickperms = new Perms_Reflection_Quick;

	foreach( $qperms as $type => $data ) {
		$quickperms->configure( $type, $data['data'] );
	}

	if (isset($_REQUEST['assign']) && isset($_REQUEST['quick_perms'])) {
		check_ticket('object-perms');
	
		$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	
		$userInput = array();
		foreach($groups['data'] as $group) {
			if(isset($_REQUEST["perm_".$group['groupName']])) {
				$group = $group['groupName'];
				$permission = $_REQUEST["perm_".$group];

				$userInput[$group] = $permission;
			}
		}

		$current = $currentObject->getDirectPermissions();
		$newPermissions = $quickperms->getPermissions( $current, $userInput );
		$permissionApplier->apply( $newPermissions );
	}
}
//Quickperm groups stuff
if( $prefs['feature_quick_object_perms'] == 'y' ) {
	$groupNames = array();
	foreach($groups['data'] as $key=>$group) {
		$groupNames[] = $group['groupName'];
	}

	$map = $quickperms->getAppliedPermissions( $displayedPermissions, $groupNames );
		
	foreach($groups['data'] as $key=>$group) {
		$groups['data'][$key]['groupSumm'] = $map[ $group['groupName'] ];
	}
}

//Quickperm END }}}


// get groupNames etc - TODO: jb will tidy...
//$checkboxInfo = array();
$permGroups = array();
$groupNames = array();
$groupIndices = array();
$groupInheritance = array();

foreach($groups['data'] as &$row) {
	if ($group_filter !== false && in_array($row['id'], $group_filter)) {
		$groupNames[] = $row['groupName'];
		$permGroups[] = 'perm['.$row['groupName'].']';
		$groupInheritance[] = $userlib->get_included_groups($row['groupName']);
		$inh = $userlib->get_included_groups($row['groupName']);
	
		$groupIndices[] = $row['groupName'] . '_hasPerm';
		
		$row['in_group_filter'] = 'y';
	} else {
		$row['in_group_filter'] = 'n';
	}

	// info for nested group treetable
	$parents = array_merge(array($row['groupName']), $userlib->get_included_groups($row['groupName']));
	$parents = preg_replace('/[\s,]+/', '_', $parents);
	$parents = implode(",", array_reverse($parents));
	$row['parents'] = $parents;

// More TODO - merge all this into a single array - but that means considerable changes to treetable (soon)
//	$checkboxInfo[] = array('name' => $row['groupName'],
//						 'key' => 'perm['.$row['groupName'].']',
//						 'index' => $groupIndex,
//						 'inheritance' => $inh);

}

$smarty->assign('permGroups', implode(',', $permGroups));
$smarty->assign('permGroupCols', $groupIndices);
$smarty->assign('groupNames', implode(',', $groupNames));
//$smarty->assign('groupInheritance', $groupInheritance);


// Get the big list of permissions
if (isset($_REQUEST['show_disabled_features']) && ($_REQUEST['show_disabled_features'] == 'on' || $_REQUEST['show_disabled_features'] == 'y')) {
	$show_disabled_features = 'y';
} else {
	$show_disabled_features = 'n';
}
$smarty->assign('show_disabled_features', $show_disabled_features);

// get "master" list of all perms
$candidates = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"], '', $show_disabled_features != 'y' ? true : false);

// list of all features
$ftemp = $userlib->get_permission_types();
$features = array();
foreach($ftemp['data'] as $f) {
	$features[] = array('featureName' => $f['type'], 'in_feature_filter' => $feature_filter === false || in_array($f['type'], $feature_filter) ? 'y' : 'n');
}
$features_enabled = array();

// build $masterPerms list and used (enabled) features
$masterPerms = array();

foreach ($candidates['data'] as $perm) {
	$perm['label'] = tra($perm['permDesc']) . ' <em>(' . $perm['permName'] . ')</em>' . '<span style="display:none;">' . tra($perm['level'] . '</span>');

	foreach( $groupNames as $index => $groupName ) {
		$p = $displayedPermissions->has( $groupName, $perm['permName'] ) ? 'y' : 'n';
		$perm[$groupName . '_hasPerm'] = $p;
		$perm[$groupIndices[$index]] = $p;
	}

	if (($feature_filter === false || in_array( $perm['type'], $feature_filter)) && ($restrictions === false || in_array( $perm['permName'], $restrictions ))) {
		$masterPerms[] = $perm;
	}
	if ($show_disabled_features != 'y' && !in_array($perm['type'], $features_enabled)) {
		// perms can be dependant on multiple features
		if (isset($perm['feature_check'])) {
			foreach(explode(',', $perm['feature_check']) as $fchk) {
				if ($prefs[$fchk] == 'y') {
					$features_enabled[] = $perm['type'];
					break;
				}
			}
		} else {	// if no feature check you can't turn them off (?)
			$features_enabled[] = $perm['type'];
		}
	}
}

if ($show_disabled_features != 'y') {
	$features_filtered = array();
	foreach($features as $f) {
		if (in_array($f['featureName'], $features_enabled) && !in_array($f, $features_filtered) ) {
			$features_filtered[] = $f;
		}
	}
	$features = $features_filtered;
}
$smarty->assign_by_ref('perms', $masterPerms);
$smarty->assign_by_ref('features', $features);

// Create JS to set up checkboxs (showing group inheritance)
$js = '$jq("#perms_busy").show();
';
$i = 0;
foreach( $groupNames as $groupName ) {
	$groupName = addslashes($groupName);
	$beneficiaries = '';
	foreach( $groupInheritance as $index => $gi ) {
		if ( is_array($gi) && in_array($groupName, $gi) ) {
			$beneficiaries .= !empty($beneficiaries) ? ',' : '';
			$beneficiaries .='input[name="perm['. addslashes($groupNames[$index]).'][]"]';
		}
	}

	$js .= <<< JS
\$jq('input[name="perm[$groupName][]"]').eachAsync({
			delay: 10,
			bulk: 0,
JS;
	if ($i == count($groupNames)-1) {
		$js .= <<< JS

			end: function () {
				\$jq('#perms_busy').hide();
			},
JS;
	}
	$js .= <<< JS

			loop: function() { 		// each one of this group

	if (\$jq(this).attr('checked')) {
		\$jq('input[value="'+\$jq(this).val()+'"]').					// other checkboxes of same value (perm)
			filter('$beneficiaries').									// which inherit from this
			attr('checked',\$jq(this).attr('checked')).					// check and disable
			attr('disabled',\$jq(this).attr('checked') ? 'disabled' : '');
	}
		
	\$jq(this).change( function() {									// bind click event
	
		if (\$jq(this).attr('checked')) {
			\$jq('input[value="'+\$jq(this).val()+'"]').			// same...
				filter('$beneficiaries').
				attr('checked','checked').							// check?
				attr('disabled','disabled');						// disable
		} else {
			\$jq('input[value="'+\$jq(this).val()+'"]').			// same...
				filter('$beneficiaries').
				attr('checked','').									// check?
				attr('disabled','');								// disable
}
	});
			}
});

JS;
	$i++;
}	// end of for $groupNames loop

$headerlib->add_jq_onready($js);

ask_ticket('object-perms');
setcookie('tab', $cookietab);
$smarty->assign('cookietab', $cookietab);

// setup smarty remarks flags

// Display the template
$smarty->assign('mid', 'tiki-objectpermissions.tpl');
if (isset($_REQUEST['filegals_manager']) && $_REQUEST['filegals_manager'] != '') {
	$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
	$smarty->display("tiki-print.tpl");
} else {
	$smarty->display("tiki.tpl");
}


function get_assign_permissions() {
	global $objectFactory;

	// get existing perms
	$currentObject = $objectFactory->get( $_REQUEST['objectType'], $_REQUEST['objectId'] );
	$currentPermissions = $currentObject->getDirectPermissions();

	// set any checked ones
	if( isset( $_REQUEST['perm'] ) ) {
		foreach( $_REQUEST['perm'] as $group => $gperms ) {
			foreach( $gperms as $perm ) {
				$currentPermissions->add( $group, $perm );
			}
		}
	}

	// unset any old_perms not there now
	if( isset( $_REQUEST['old_perm'] ) ) {
		foreach( $_REQUEST['old_perm'] as $group => $gperms ) {
			foreach( $gperms as $perm ) {
				if (!in_array($perm, $_REQUEST['perm'][$group])) {
					$currentPermissions->remove( $group, $perm );
				}
			}
		}
	}

	return $currentPermissions;
}

function quickperms_get_data() {
	if($_REQUEST["permType"]=="file galleries") {
		return quickperms_get_filegal();
	} else {
		return quickperms_get_generic();
	}
}

function quickperms_get_filegal() {
	return array(
		'admin' => array(
			'name' => 'admin',
			'data' => array(
				'tiki_p_admin_file_galleries' => 'tiki_p_admin_file_galleries',
				'tiki_p_assign_perm_file_gallery' => 'tiki_p_assign_perm_file_gallery',
				'tiki_p_batch_upload_files' => 'tiki_p_batch_upload_files',
				'tiki_p_batch_upload_file_dir' => 'tiki_p_batch_upload_file_dir',
				'tiki_p_create_file_galleries' => 'tiki_p_create_file_galleries',
				'tiki_p_download_files' => 'tiki_p_download_files',
				'tiki_p_edit_gallery_file' => 'tiki_p_edit_gallery_file',
				'tiki_p_list_file_galleries' => 'tiki_p_list_file_galleries',
				'tiki_p_upload_files' => 'tiki_p_upload_files',
				'tiki_p_view_fgal_explorer' => 'tiki_p_view_fgal_explorer',
				'tiki_p_view_fgal_path' => 'tiki_p_view_fgal_path',
				'tiki_p_view_file_gallery' => 'tiki_p_view_file_gallery',
			),
		),
		'write' => array(
			'name' => 'write',
			'data' => array(
				'tiki_p_batch_upload_files' => 'tiki_p_batch_upload_files',
				'tiki_p_batch_upload_file_dir' => 'tiki_p_batch_upload_file_dir',
				'tiki_p_create_file_galleries' => 'tiki_p_create_file_galleries',
				'tiki_p_download_files' => 'tiki_p_download_files',
				'tiki_p_edit_gallery_file' => 'tiki_p_edit_gallery_file',
				'tiki_p_list_file_galleries' => 'tiki_p_list_file_galleries',
				'tiki_p_upload_files' => 'tiki_p_upload_files',
				'tiki_p_view_fgal_explorer' => 'tiki_p_view_fgal_explorer',
				'tiki_p_view_fgal_path' => 'tiki_p_view_fgal_path',
				'tiki_p_view_file_gallery' => 'tiki_p_view_file_gallery',
			),
		),
		'read' => array(
			'name' => 'read',
			'data' => array(
				'tiki_p_download_files' => 'tiki_p_download_files',
				'tiki_p_list_file_galleries' => 'tiki_p_list_file_galleries',
				'tiki_p_view_fgal_explorer' => 'tiki_p_view_fgal_explorer',
				'tiki_p_view_fgal_path' => 'tiki_p_view_fgal_path',
				'tiki_p_view_file_gallery' => 'tiki_p_view_file_gallery',
			),
		),
		'none' => array(
			'name' => 'none',
			'data' => array(
			),
		),
	);
}

function quickperms_get_generic() {
	global $userlib;

	$databaseperms = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"], '', true);
	foreach($databaseperms['data'] as $perm) {
		if ($perm['level']=='basic')
			$quickperms_['basic'][$perm['permName']] = $perm['permName'];
		elseif ($perm['level']=='registered')
			$quickperms_['registered'][$perm['permName']] = $perm['permName'];
		elseif ($perm['level']=='editors')
			$quickperms_['editors'][$perm['permName']] = $perm['permName'];
		elseif ($perm['level']=='admin')
			$quickperms_['admin'][$perm['permName']] = $perm['permName'];
	}

	if(!isset($quickperms_['basic']))
		$quickperms_['basic'] = array();
	if(!isset($quickperms_['registered']))
		$quickperms_['registered'] = array();
	if(!isset($quickperms_['editors']))
		$quickperms_['editors'] = array();
	if(!isset($quickperms_['admin']))
	$quickperms_['admin'] = array();

	$perms = array();
	$perms['basic']['name'] = "basic";
	$perms['basic']['data'] = array_merge($quickperms_['basic']);
	$perms['registered']['name'] = "registered";
	$perms['registered']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered']);
	$perms['editors']['name'] = "editors";
	$perms['editors']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered'], $quickperms_['editors']);
	$perms['admin']['name'] = "admin";
	$perms['admin']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered'], $quickperms_['editors'], $quickperms_['admin']);
	$perms['none']['name'] = "none";
	$perms['none']['data'] = array();

	return $perms;
}

function perms_get_restrictions() {
	global $userlib;
	$perms = Perms::get();

	if( $perms->admin_objects ) {
		return false;
	}

	$masterPerms = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"] );
	$masterPerms = $masterPerms['data'];

	$allowed = array();
	// filter out non-admin's unavailable perms
	foreach($masterPerms as $perm) {
		$name = $perm['permName'];

		if( $perms->$name ) {
			$allowed[] = $name;
		}
	}

	return $allowed;
}

function get_displayed_permissions() {
	global $objectFactory, $smarty;

	$currentObject = $objectFactory->get( $_REQUEST['objectType'], $_REQUEST['objectId'] );
	$displayedPermissions = $currentObject->getDirectPermissions();

	$comparator = new Perms_Reflection_PermissionComparator( $displayedPermissions, new Perms_Reflection_PermissionSet );

	$smarty->assign('permissions_displayed', 'direct');
	if( $comparator->equal() ) {
		$globPerms = $objectFactory->get( 'global', null )->getDirectPermissions();	// global perms
		$parent = $currentObject->getParentPermissions();							// inherited perms (could be category ones)
		$comparator = new Perms_Reflection_PermissionComparator( $globPerms, $parent );
		if( $comparator->equal() ) {												// parent == globals
			$smarty->assign('permissions_displayed', 'parent');
		} else {																	// parent not globals, so must be category
			$smarty->assign('permissions_displayed', 'category');
		}
		$displayedPermissions = $parent;
	}

	return $displayedPermissions;
}

