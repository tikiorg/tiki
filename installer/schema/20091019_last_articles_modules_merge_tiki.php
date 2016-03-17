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
function upgrade_20091019_last_articles_modules_merge_tiki($installer)
{
	$result = $installer->query("select moduleId, params from tiki_modules where name='last_articles'; ");
	while ($row = $result->fetchRow()) {
		$params = $row['params'];
		$params = str_ireplace('showImg', 'img', $params);
		$params = str_ireplace('lang=', 'langfilter=', $params);
		$params = str_ireplace('showDate', 'showpubl', $params);
		$installer->query("update tiki_modules set params='" . $params . "', name='articles' where moduleId=" . $row['moduleId'] . "; ");
	}
}
