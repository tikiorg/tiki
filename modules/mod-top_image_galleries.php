<?php
$ranking = $tikilib->list_galleries(0, $module_rows, 'hits_desc','admin','');
$smarty->assign('modTopGalleries',$ranking["data"]);
?>