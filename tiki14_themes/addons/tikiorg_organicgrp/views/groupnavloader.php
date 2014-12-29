<?php

namespace tikiaddon\tikiorg\organicgrp;

function groupnavloader($data, $params)
{
	if (!isset($params['from'])) {
		return 'Please specify parameter: from';
	}

	$app = \TikiAddons::get('tikiorg_organicgrp');
	$api = new \TikiAddons_Api_Group;

	if (!isset($_REQUEST['organicgroup']) && !empty($_REQUEST['page'])) {
		$info = $api->getOrganicGroupInfoForItem('wiki page', $_REQUEST['page']);
		$cat = $info['cat'];
		$ogid = $info['organicgroup'];
		$app->smarty->assign('groupTrackerItemId', $ogid);
		$_REQUEST['organicgroup'] = $ogid;
		if (!isset($_REQUEST['cat'])) {
			$_REQUEST['cat'] = $cat;
		}
	}
	if (!isset($_REQUEST['organicgroup']) && !empty($_REQUEST['itemId'])) {
		$info = $api->getOrganicGroupInfoForItem('trackeritem', $_REQUEST['itemId']);
		$cat = $info['cat'];
		$ogid = $info['organicgroup'];
		$app->smarty->assign('groupTrackerItemId', $ogid);
		$_REQUEST['organicgroup'] = $ogid;
		if (!isset($_REQUEST['cat'])) {
			$_REQUEST['cat'] = $cat;
		}
	}

	// if og is private, always redirect to home page
	if (isset($_REQUEST['organicgroup'])) {
		$ogname = 'tikiorg_organicgrp_' . $_REQUEST['organicgroup'];
		global $user;
		if ($_REQUEST['page'] != 'tikiorg_organicgrp_grouphomepage' && $api->organicGroupIsPrivate($ogname) && !\TikiLib::lib('user')->user_is_in_group($user, $ogname)) {
			header( 'Location: tiki-index.php?page=tikiorg_organicgrp_grouphomepage&itemId=' . $_REQUEST['organicgroup']);
		}
	}

	$app->smarty->assign('groupnavfrom', $params['from']);
	return $app->smarty->fetch('tikiorg-groupnavloader.tpl');
}
