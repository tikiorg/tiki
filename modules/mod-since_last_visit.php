<?php
$nvi_info = $tikilib->get_news_from_last_visit($user);
$smarty->assign('nvi_info',$nvi_info);
?>