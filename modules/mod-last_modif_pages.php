<?php
$ranking = $tikilib->list_pages(0, $module_rows, 'lastModif_desc');
$smarty->assign('modLastModif',$ranking["data"]);
?>