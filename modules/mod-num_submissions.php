<?php
$ranking = $tikilib->list_submissions(0,-1,'created_desc', '', '');
$smarty->assign('modNumSubmissions',$ranking["cant"]);
?>