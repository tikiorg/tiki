<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $installer
 */
function upgrade_20110610_readd_sefurl_index_left_tiki($installer)
{
	$result = $installer->fetchAll("SHOW INDEX FROM `tiki_sefurl_regex_out` WHERE `Key_name`='left'");

	if ($result) {
		$result = $installer->query("DROP INDEX `left` ON `tiki_sefurl_regex_out`");
	}
	$installer->query("ALTER TABLE `tiki_sefurl_regex_out` ADD UNIQUE `left` (`left`(128))");
}
