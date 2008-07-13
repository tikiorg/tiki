<?php
include 'tiki-setup.php';
include_once ('lib/trackers/trackerlib.php');

if ($prefs['feature_gmap'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').": feature_gmap");
	$smarty->display("error.tpl");
	die;
}

// setup default view
if ($tiki_p_admin == 'y' and isset($_REQUEST['view_user']) and $userlib->user_exists($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
	$smarty->assign('watch',$userwatch);
} else {
	$userwatch = $user;
}

if ($user and isset($_REQUEST['default'])) {
	$d = $_REQUEST['default'];
	$tikilib->set_user_preference($userwatch, 'gmap_defx', $d['x']);
	$tikilib->set_user_preference($userwatch, 'gmap_defy', $d['y']);
	$tikilib->set_user_preference($userwatch, 'gmap_defz', $d['z']);
} elseif ($user and isset($_REQUEST['reset_default'])) {
	$tikilib->set_user_preference($userwatch, 'gmap_defx', $prefs['gmap_defaultx']);
	$tikilib->set_user_preference($userwatch, 'gmap_defy', $prefs['gmap_defaulty']);
	$tikilib->set_user_preference($userwatch, 'gmap_defz', $prefs['gmap_defaultz']);
}

$prefs['gmap_defaultx'] = $tikilib->get_user_preference($userwatch,'gmap_defx',$prefs['gmap_defaultx']);
$prefs['gmap_defaulty'] = $tikilib->get_user_preference($userwatch,'gmap_defy',$prefs['gmap_defaulty']);
$prefs['gmap_defaultz'] = $tikilib->get_user_preference($userwatch,'gmap_defz',$prefs['gmap_defaultz']);
$smarty->assign('gmap_defaultx',$prefs['gmap_defaultx']);
$smarty->assign('gmap_defaulty',$prefs['gmap_defaulty']);
$smarty->assign('gmap_defaultz',$prefs['gmap_defaultz']);

$smarty->assign('input','n');
if ($user and isset($_REQUEST['for'])) {
	if ($_REQUEST['for'] == 'user') {
		if (isset($_REQUEST['point']) and is_array($_REQUEST['point'])) {
			$p = $_REQUEST['point'];
			if ($p['x'] > -90 and $p['x'] < 90) { $tikilib->set_user_preference($userwatch, 'lon', $p['x']); }
			if ($p['y'] > -90 and $p['y'] < 90) { $tikilib->set_user_preference($userwatch, 'lat', $p['y']); }
			if ($p['z'] > 0 and $p['z'] < 20) { $tikilib->set_user_preference($userwatch, 'zoom', $p['z']); }
		}
		$pointx = $tikilib->get_user_preference($userwatch,'lon','');
		$pointy = $tikilib->get_user_preference($userwatch,'lat','');
		$pointz = $tikilib->get_user_preference($userwatch,'zoom',$prefs['gmap_defaultz']);
		$smarty->assign('pointx',$pointx);
		$smarty->assign('pointy',$pointy);
		$smarty->assign('pointz',$pointz);
		$smarty->assign('input','y');
		$smarty->assign('extraquery','?for=user');
		$smarty->assign('backurl','tiki-user_preferences.php');
		$smarty->assign('backlink',tra('Back to preferences'));
	}
	if ($_REQUEST['for'] == 'item') {
	  if (isset($_REQUEST['point']) and is_array($_REQUEST['point'])) {
	    echo "OK";
	    if(isset($_REQUEST['itemId']) && isset($_REQUEST['fieldId'])){
	      echo "OK";
	      $p = $_REQUEST['point'];
	      if ( ($p['x'] > -90 and $p['x'] < 90) &&
		   ($p['y'] > -90 and $p['y'] < 90) &&
		   ($p['z'] > 0 and $p['z'] < 20)      ){

		$G_query="UPDATE `tiki_tracker_item_fields` SET `value`=? WHERE `itemId`=? AND `fieldId`=?";
		$trklib->query($G_query,array($p['x'].','.$p['y'].','.$p['z'], (int)$_REQUEST['itemId'], (int)$_REQUEST['fieldId']));
	      }
	    }
	  }
	  $smarty->assign('input','y');
	  $xyz = $trklib->get_item_value($_REQUEST['trackerId'],$_REQUEST['itemId'],$_REQUEST['fieldId']);
	  $first_comma=strpos($xyz,',');
	  $second_comma=strpos($xyz,',',$first_comma+1);
	  if(!$second_comma){
	    $second_comma=strlen($xyz);
	    $xyz.=",11";
	  }
	  $pointx = substr($xyz,0,$first_comma);
	  $pointy = substr($xyz,$first_comma+1,$second_comma-$first_comma-1);
	  $pointz = substr($xyz,$second_comma+1);
	  $smarty->assign('pointx',$pointx);
	  $smarty->assign('pointy',$pointy);
	  $smarty->assign('pointz',$pointz);
	  $smarty->assign('extraquery','?for=item&amp;itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId'].'&amp;fieldId='.$_REQUEST['fieldId']);
	  $smarty->assign('backurl','tiki-view_tracker_item.php?itemId='.$_REQUEST['itemId'].'&amp;trackerId='.$_REQUEST['trackerId']);
	  $smarty->assign('backlink',tra('Back to item'));
	}
}
if ($user and isset($_REQUEST['recenter']) and $pointx and $pointy) {
	$smarty->assign('gmap_defaultx',$pointx);
	$smarty->assign('gmap_defaulty',$pointy);
}

$smarty->assign('mid','tiki-gmap_locator.tpl');
$smarty->display('tiki.tpl');
?>
