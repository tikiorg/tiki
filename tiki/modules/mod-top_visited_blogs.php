<?php
$ranking = $tikilib->list_blogs(0,$module_rows,'hits_desc','');
$smarty->assign('modTopVisitedBlogs',$ranking["data"]);
?>