<?php

$ranking = $tikilib->list_submissions(0, $module_rows, 'created_desc', '', '');

$smarty->assign('modLastSubmissions', $ranking["data"]);

?>