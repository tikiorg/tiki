<?php
include 'tiki-setup.php';

if ($feature_gmap != 'y') {
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
	$tikilib->set_user_preference($userwatch, 'gmap_defx', $gmap_defaultx);
	$tikilib->set_user_preference($userwatch, 'gmap_defy', $gmap_defaulty);
	$tikilib->set_user_preference($userwatch, 'gmap_defz', $gmap_defaultz);
}

$gmap_defaultx = $tikilib->get_user_preference($userwatch,'gmap_defx',$gmap_defaultx);
$gmap_defaulty = $tikilib->get_user_preference($userwatch,'gmap_defy',$gmap_defaulty);
$gmap_defaultz = $tikilib->get_user_preference($userwatch,'gmap_defz',$gmap_defaultz);
$smarty->assign('gmap_defaultx',$gmap_defaultx);
$smarty->assign('gmap_defaulty',$gmap_defaulty);
$smarty->assign('gmap_defaultz',$gmap_defaultz);

$smarty->assign('input','n');
if (isset($_REQUEST['for'])) {
	if ($_REQUEST['for'] == 'user') {
		if (isset($_REQUEST['point']) and is_array($_REQUEST['point'])) {
			$p = $_REQUEST['point'];
			if ($p['x'] > -90 and $p['x'] < 90) { $tikilib->set_user_preference($userwatch, 'lon', $p['x']); }
			if ($p['y'] > -90 and $p['y'] < 90) { $tikilib->set_user_preference($userwatch, 'lat', $p['y']); }
			if ($p['z'] > 0 and $p['z'] < 20) { $tikilib->set_user_preference($userwatch, 'zoom', $p['z']); }
		}
		$pointx = $tikilib->get_user_preference($userwatch,'lon','');
		$pointy = $tikilib->get_user_preference($userwatch,'lat','');
		$pointz = $tikilib->get_user_preference($userwatch,'zoom',$gmap_defaultz);
		$smarty->assign('pointx',$pointx);
		$smarty->assign('pointy',$pointy);
		$smarty->assign('pointz',$pointz);
		$smarty->assign('input','y');
		$smarty->assign('extraquery','?for=user');
		$smarty->assign('backurl','tiki-user_preferences.php');
		$smarty->assign('backlink',tra('Back to preferences'));
	}
}
if (isset($_REQUEST['recenter']) and $pointx and $pointy) {
	$smarty->assign('gmap_defaultx',$pointx);
	$smarty->assign('gmap_defaulty',$pointy);
}

$smarty->assign('mid','tiki-gmap_locator.tpl');
$smarty->display('tiki.tpl');
?>
