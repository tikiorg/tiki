<?php

$ranking = $tikilib->get_random_pages($module_rows);

$smarty->assign('modRandomPages', $ranking);

?>