<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
* Params: 
* - title : if it is "title", show the title of the post, else show the date of
*   creation
* - blogid : if it is set, the list is filtered for that blogId, -1 = show posts
*   from  all blogs
*/
$ranking = $tikilib->list_posts(0, $module_rows, 'created_desc', '', (isset($module_params["blogid"]) ? $module_params["blogid"] : -1 ) );

$smarty->assign('modLastBlogPosts', $ranking["data"]);
$smarty->assign('modLastBlogPostsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
