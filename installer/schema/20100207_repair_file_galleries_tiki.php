<?php

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $tikilib;
include_once('tiki-setup_base.php');

function upgrade_20100207_repair_file_galleries_tiki( $installer ) {

	$cant = $installer->getOne('SELECT COUNT(*) FROM `tiki_file_galleries` WHERE `parentId` = -1 and `type` <> \'system\';');
	
	if ($cant > 0) {
		$sysId = $installer->getOne('SELECT `galleryId` FROM `tiki_file_galleries` WHERE `type` = \'system\';');
		$pref = $installer->getOne('SELECT COUNT(*) FROM `tiki_preferences` WHERE `name` = \'fgal_root_id\';');
		if ($pref > 0) {
			$result = $installer->query('UPDATE `tiki_preferences` SET `value` = ? WHERE `name` = \'fgal_root_id\';', $sysId);
		} else {
			$result = $installer->query('INSERT INTO `tiki_preferences` (`name`, `value`) VALUES (\'fgal_root_id\', ? );', $sysId);
		}
		$result = $installer->query('UPDATE `tiki_file_galleries` SET `parentId` = ? WHERE `parentId` = -1 and `type` <> \'system\'', array($sysId));
	}
}

