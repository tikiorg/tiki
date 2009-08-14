<?php
///
// $Id$
// \brief Show last comments in blogs
//

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $bloglib, $smarty;
include_once ('lib/blogs/bloglib.php');
$comments = $bloglib->list_blog_post_comments('y', $module_rows);

$smarty->assign('comments', $comments['data']);
$smarty->assign('nonums', isset($module_params['nonums']) ? $module_params['nonums'] : 'n');
$smarty->assign('module_rows', $module_rows);
$smarty->assign('moretooltips', isset($module_params['moretooltips']) ? $module_params['moretooltips'] : 'n');
