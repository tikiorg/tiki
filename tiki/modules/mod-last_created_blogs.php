<?php
$ranking = $tikilib->list_blogs(0,$module_rows,'created_desc','');
$smarty->assign('modLastCreatedBlogs',$ranking["data"]);
?>