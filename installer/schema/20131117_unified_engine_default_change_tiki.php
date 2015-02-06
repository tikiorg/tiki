<?php

// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * default for unified_engine changed between tiki 11 and 12 - this maintains previous default setting
 *
 * @param $installer
 */
function upgrade_20131117_unified_engine_default_change_tiki($installer)
{
	$value = $installer->getOne("SELECT `value` FROM `tiki_preferences` WHERE `name` = 'unified_engine'");

	if (!$value) {	// default values can be empty
		$preferences = $installer->table('tiki_preferences');
		$preferences->insertOrUpdate(array('value' => 'lucene'), array('name' => 'unified_engine'));
	}
}
