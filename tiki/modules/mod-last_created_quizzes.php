<?php
$ranking = $tikilib->list_quizzes(0,$module_rows,'created_desc','');
$smarty->assign('modLastCreatedQuizzes',$ranking["data"]);
?>