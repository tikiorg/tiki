<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include 'tiki-setup.php';
include_once ('lib/trackers/trackerlib.php');
$access->check_feature('feature_gmap');
$auto_query_args = array('for', 'itemId', 'fieldId', 'trackerId', 'view_user', 'fromPage');

if ($tiki_p_admin == 'y' and isset($_REQUEST['view_user']) and $userlib->user_exists($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
	$smarty->assign('watch',$userwatch);
} else {
	$userwatch = $user;
}

if ($prefs["feature_ajax"] == 'y') {
	// Ajax version using new plugin
	$smarty->assign('userwatch', $userwatch);
} else {
	// Old non-ajax version which can be removed once Ajax becomes always on 
if (!isset($_REQUEST['for']))
	$_REQUEST['for'] = '';

if (isset($_REQUEST['set_default']) && ($user == $userwatch || $tiki_p_admin =='y')) {
	$pointx = $_REQUEST['point']['x'];
	$pointy = $_REQUEST['point']['y'];
	$pointz = $_REQUEST['point']['z'];
	$tikilib->set_user_preference($userwatch, 'gmap_defx', $pointx);
	$tikilib->set_user_preference($userwatch, 'gmap_defy', $pointy);
	$tikilib->set_user_preference($userwatch, 'gmap_defz', $pointz);
	$smarty->assign('extraquery','?for=user');
	$smarty->assign('backurl','tiki-user_preferences.php?view_user=' . $userwatch);
	$smarty->assign('backlink',tra('Back to preferences'));
} elseif (isset($_REQUEST['reset_default'])) {
	$pointx = $tikilib->get_user_preference($userwatch, 'gmap_defx', $prefs['gmap_defaultx']);
	$pointy = $tikilib->get_user_preference($userwatch, 'gmap_defy', $prefs['gmap_defaulty']);
	$pointz = $tikilib->get_user_preference($userwatch, 'gmap_defz', $prefs['gmap_defaultz']);
} elseif (isset($_REQUEST['reset_site_default'])) {
	$pointx = $prefs['gmap_defaultx'];
	$pointy = $prefs['gmap_defaulty'];
	$pointz = $prefs['gmap_defaultz'];
} elseif (isset($_REQUEST['center'])) {
	$pointx = $_REQUEST['point']['x'];
	$pointy = $_REQUEST['point']['y'];
	$pointz = $_REQUEST['point']['z'];
} elseif (isset($_REQUEST['for']) && $_REQUEST['for'] == 'user') {
	if (isset($_REQUEST['point']) and is_array($_REQUEST['point']) && ($user == $userwatch || $tiki_p_admin =='y')) {
		$p = $_REQUEST['point'];
		if ($p['x'] > -180 and $p['x'] < 180) { $tikilib->set_user_preference($userwatch, 'lon', $p['x']); }
		if ($p['y'] > -180 and $p['y'] < 180) { $tikilib->set_user_preference($userwatch, 'lat', $p['y']); }
		if ($p['z'] >= 0 and $p['z'] < 20) { $tikilib->set_user_preference($userwatch, 'zoom', $p['z']); }
	}
	$pointx = $tikilib->get_user_preference($userwatch,'lon','');
	$pointy = $tikilib->get_user_preference($userwatch,'lat','');
	$pointz = $tikilib->get_user_preference($userwatch,'zoom',$prefs['gmap_defaultz']);
	$smarty->assign('extraquery','?for=user');
	$smarty->assign('backurl','tiki-user_preferences.php?view_user=' . $userwatch);
	$smarty->assign('backlink',tra('Back to preferences'));
} elseif (isset($_REQUEST['for']) && $_REQUEST['for'] == 'item' && !empty($_REQUEST['itemId'])) {
	if(!empty($_REQUEST['fieldId']) && !empty($_REQUEST['trackerId'])){
		if (isset($_REQUEST['point']) and is_array($_REQUEST['point'])) {
			$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker');
			if ($tiki_p_modify_tracker_items == 'y') {
				$p = $_REQUEST['point'];
				if ( ($p['x'] > -180 and $p['x'] < 180) && ($p['y'] > -180 and $p['y'] < 180) && ($p['z'] >= 0 and $p['z'] < 20)      ){
					$G_query="UPDATE `tiki_tracker_item_fields` SET `value`=? WHERE `itemId`=? AND `fieldId`=?";
					$trklib->query($G_query,array($p['x'].','.$p['y'].','.$p['z'], (int)$_REQUEST['itemId'], (int)$_REQUEST['fieldId']));
				}
			}
  		}
		$xyz = explode(',', $trklib->get_item_value($_REQUEST['trackerId'],$_REQUEST['itemId'],$_REQUEST['fieldId']));
  		$pointx = $xyz['0'];
  		$pointy = $xyz['1'];
  		$pointz = $xyz['2'];
  		$smarty->assign('extraquery','?for=item&amp;itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId'].'&amp;fieldId='.$_REQUEST['fieldId']);
		global $wikilib; include_once( 'lib/wiki/wikilib.php');
  		if (!empty($_REQUEST['fromPage'])) {
  			$smarty->assign('backurl', $wikilib->sefurl($_REQUEST['fromPage'], true) . 'itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId']);
  			$smarty->assign('fromPage',  $_REQUEST['fromPage']);
  		} else {
	  		$smarty->assign('backurl','tiki-view_tracker_item.php?itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId']);
  		}
  		$smarty->assign('backlink',tra('Back to item'));
	}
} elseif (isset($_REQUEST['for']) && $_REQUEST['for'] == 'item' && empty($_REQUEST['itemId']) && !empty($_REQUEST['fieldId']) && !empty($_REQUEST['trackerId'])) {
	$smarty->assign('extraquery','?for=item&amp;itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId'].'&amp;fieldId='.$_REQUEST['fieldId']);
	$smarty->assign('backurl','tiki-view_tracker_item.php?itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId']);
	$smarty->assign('backlink',tra('Back to item'));
}
if (!isset($pointx)) {
	$pointx = isset($prefs['gmap_defx'])? $prefs['gmap_defx']: $prefs['gmap_defaultx'];
	$pointy = isset($prefs['gmap_defy'])? $prefs['gmap_defy']: $prefs['gmap_defaulty'];
	$pointz = isset($prefs['gmap_defz'])? $prefs['gmap_defz']: $prefs['gmap_defaultz'];
}
if (!isset($pointz)) { $pointz = 11; }	// trackers cope with only x & y
$smarty->assign_by_ref('pointx',$pointx);
$smarty->assign_by_ref('pointy',$pointy);
$smarty->assign_by_ref('pointz',$pointz);
if (($_REQUEST['for'] == 'user' && ($user == $userwatch || $tiki_p_admin == 'y')) || ($_REQUEST['for'] == 'item' && ($tiki_p_admin_trackers == 'y' || ($tiki_modify_tracker_items == 'y') && !empty($_REQUEST['itemId'])))) {
	$smarty->assign('input','y');
}
$smarty->assign('for',$_REQUEST['for']);
} //end if feature_ajax
$smarty->assign('mid','tiki-gmap_locator.tpl');
$smarty->display('tiki.tpl');
