<?php

$ranking = $tikilib->list_faqs(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastCreatedFaqs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>