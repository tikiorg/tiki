<?php

if ($feature_directory == 'y') {
	$ranking = $tikilib->dir_list_all_valid_sites2(0, $module_rows, 'created_desc', '');

	$smarty->assign('modLastdirSites', $ranking["data"]);
}

?>