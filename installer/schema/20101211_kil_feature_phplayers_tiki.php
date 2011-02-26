<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20101211_kil_feature_phplayers_tiki( $installer ) {
	$result = $installer->getOne( "SELECT COUNT(*) FROM `tiki_preferences` WHERE `name` = 'feature_phplayers' AND `value` =  'y'");
	if( $result > 0 ) {
		$installer->query( "REPLACE `tiki_preferences` SET `name` = 'feature_cssmenus', `value` = 'y'; DELETE FROM `tiki_preferences` WHERE `name` = 'feature_phplayers';");
	}
}
