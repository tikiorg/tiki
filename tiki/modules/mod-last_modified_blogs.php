<?php
$ranking = $tikilib->list_blogs(0,$module_rows,'lastModif_desc','');
$smarty->assign('modLastModifiedBlogs',$ranking["data"]);
?>