<?php
$ranking = $tikilib->get_user_pages($user,$module_rows);
$smarty->assign('modUserPages',$ranking);
?>