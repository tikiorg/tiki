<?php

/**
* Params: 
* - title : if is "title", show the title of the post, else show the date of creation
*/
$ranking = $tikilib->list_posts(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastBlogPosts', $ranking["data"]);
$smarty->assign('modLastBlogPostsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>