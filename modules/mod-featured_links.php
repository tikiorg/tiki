<?php
$ranking = $tikilib->get_featured_links($module_rows);
$smarty->assign('featuredLinks',$ranking);
?>