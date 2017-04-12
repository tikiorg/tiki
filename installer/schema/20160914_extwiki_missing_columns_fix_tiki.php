<?php


function upgrade_20160914_extwiki_missing_columns_fix_tiki($installer)
{
	$exists = $installer->query("SHOW COLUMNS FROM `tiki_extwiki` LIKE 'indexname'");

	if (! $exists->numRows()) {
		$installer->query("ALTER TABLE `tiki_extwiki` ADD COLUMN `indexname` VARCHAR(20), ADD COLUMN `groups` VARCHAR(1024);");
	}

}
