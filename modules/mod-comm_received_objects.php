<?php

$ranking = $tikilib->list_received_pages(0, -1, $sort_mode = 'pageName_asc', '');

$smarty->assign('modReceivedPages', $ranking["cant"]);

?>