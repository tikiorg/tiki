<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

if ($feature_directory == 'y') {
	$ranking = $tikilib->dir_stats();

	$smarty->assign('modDirStats', $ranking);
}

?>
