<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

/**
* Params: 
* - title : if is "title", show the title of the post, else show the date of creation
*/
$ranking = $tikilib->list_posts(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastBlogPosts', $ranking["data"]);
$smarty->assign('modLastBlogPostsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
