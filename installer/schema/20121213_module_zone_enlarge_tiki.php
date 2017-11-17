<?php

function pre_20121213_module_zone_enlarge_tiki($installer)
{
	// Drop the index and ignore errors, no built-in syntax
	$installer->queryError('DROP INDEX `namePosOrdParam` ON `tiki_modules`', $error);
}
