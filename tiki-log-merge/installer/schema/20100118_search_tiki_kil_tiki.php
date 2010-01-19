<?php

function upgrade_20100118_search_tiki_kil_tiki( $installer ) {
	$installer->query("DROP TABLE `tiki_searchwords`");
	$installer->query("DROP TABLE `tiki_searchsyllable`");
	$installer->query("DROP TABLE `tiki_searchindex`");
	$result = $installer->query( "SELECT * FROM `tiki_preferences` WHERE `name`='feature_search_fulltext' AND `value`='y'" );
	if( $row = $result->fetchRow() ) {
		$installer->query( "update `tiki_preferences` set `value`='y' WHERE `name`='feature_search'");
	}
}

