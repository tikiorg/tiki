<?php
$ranking = $tikilib->list_files(0, $module_rows, 'created_desc','');
$smarty->assign('modLastFiles',$ranking["data"]);
?>