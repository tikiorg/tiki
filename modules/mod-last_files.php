<?php

if (isset($module_params["galleryId"])) {
	$ranking = $tikilib->get_files(0, $module_rows, 'created_desc', '', $module_params["galleryId"]);
} else {
	$ranking = $tikilib->list_files(0, $module_rows, 'created_desc', '');
}

$smarty->assign('modLastFiles', $ranking["data"]);

?>