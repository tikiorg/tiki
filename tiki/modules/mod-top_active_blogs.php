<?php
$ranking = $tikilib->list_blogs(0,$module_rows,'activity_desc','');
$smarty->assign('modTopActiveBlogs',$ranking["data"]);
?>