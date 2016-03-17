<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

if (isset($_REQUEST["workspace"])) {
	check_ticket('admin-inc-workspace');
}
ask_ticket('admin-inc-workspace');

if ($prefs['feature_areas'] === 'y') {
	$areaslib = TikiLib::lib('areas');

	// updating table tiki_areas
	if (isset($_REQUEST['update_areas'])) {
		check_ticket('admin-inc-workspace');
		$pass = $areaslib->update_areas();
		if ($pass !== true) {
			$smarty->assign_by_ref('error', $pass);
		}
	}

	// building overview
	$areas_table = $areaslib->table('tiki_areas');

	$result = $areas_table->fetchAll(array('categId', 'perspectives', 'exclusive', 'share_common', 'enabled'), $conditions);
	$areas = array();
	$perspectivelib = TikiLib::lib('perspective');
	$perspectives = array();

	foreach ($result as $item) {
		$area = array();
		$area['categId'] = $item['categId'];
		$area['exclusive'] = $item['exclusive'];
		$area['share_common'] = $item['share_common'];
		$area['enabled'] = $item['enabled'];
		$area['perspectives'] = array();
		foreach (unserialize($item['perspectives']) as $pers) {
			if (!array_key_exists($pers, $perspectives)) {
				$perspectives[$pers] = $perspectivelib->get_perspective($pers);
			}

			$area['perspectives'][] = $perspectives[$pers];
		}
		$area['categName'] = $areaslib->get_category_name($item['categId']);
		$area['description'] = $areaslib->get_category_description($item['categId']);
		$areas[] = $area;
	}

	$smarty->assign_by_ref('areas', $areas);

}
