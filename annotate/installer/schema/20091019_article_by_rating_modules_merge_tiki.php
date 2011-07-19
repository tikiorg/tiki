<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20091019_article_by_rating_modules_merge_tiki( $installer )
{
	$result = $installer->query( "select moduleId, params from tiki_modules where name='article_by_rating'; " );
	while( $row = $result->fetchRow() ) {
		$params = $row['params'];
		$params = str_ireplace('showImg=', 'img=', $params);
		$params = str_ireplace('lang=', 'langfilter=', $params);
		$params = str_ireplace('showDate=', 'showpubl=', $params);
		if ($params) $params .= "&";
		$params .= "sort=rating_desc&show_rating_selector=y";
		$installer->query( "update tiki_modules set params='" . $params . "', name='articles' where moduleId=" . $row['moduleId'] . "; " );
	}
}
