<?php
$ranking = $tikilib->list_files(0, $module_rows, 'downloads_desc','');
$smarty->assign('modTopFiles',$ranking["data"]);
?>