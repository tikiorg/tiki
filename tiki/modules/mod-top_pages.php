<?php
$ranking = $tikilib->get_top_pages($module_rows);
$smarty->assign('modTopPages',$ranking);
?>