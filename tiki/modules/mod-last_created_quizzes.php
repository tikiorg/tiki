<?php

$ranking = $tikilib->list_quizzes(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastCreatedQuizzes', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>