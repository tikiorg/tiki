<?php

$ranking = $tikilib->list_submissions(0, $module_rows, 'created_desc', '', '');

$smarty->assign('modLastSubmissions', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>