<?php

require_once ('lib/categories/categlib.php');

//test
$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('WhatsRelated', $WhatsRelated);


?>
