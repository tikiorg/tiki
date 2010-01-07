<?php

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20091019_last_articles_modules_merge_tiki( $installer )
{
	$result = $installer->query( "select moduleId, params from tiki_modules where name='last_articles'; " );
	while( $row = $result->fetchRow() ) {
		$params = $row['params'];
		$params = str_ireplace('showImg', 'img', $params);
		$params = str_ireplace('lang=', 'langfilter=', $params);
		$params = str_ireplace('showDate', 'showpubl', $params);
		$installer->query( "update tiki_modules set params='" . $params . "', name='articles' where moduleId=" . $row['moduleId'] . "; " );
	}
}
