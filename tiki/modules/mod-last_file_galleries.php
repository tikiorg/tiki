<?php

$ranking = $tikilib->list_visible_file_galleries(0, $module_rows, 'lastModif_desc', 'admin', '');

$smarty->assign('modLastFileGalleries', $ranking["data"]);

?>