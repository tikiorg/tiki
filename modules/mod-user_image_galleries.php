<?php
$ranking = $tikilib->get_user_galleries($user,$module_rows);
$smarty->assign('modUserG',$ranking);
?>