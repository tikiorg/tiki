<?php

$ranking = $tikilib->list_faqs(0, $module_rows, 'hits_desc', '');

$smarty->assign('modTopVisitedFaqs', $ranking["data"]);

?>