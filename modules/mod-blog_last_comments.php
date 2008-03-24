<?php
//
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-blog_last_comments.php,v 1.1 2006-01-26 23:28:08 amette Exp $
// \brief Show last comments on wiki pages
//

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists("blog_last_comments")) {
function blog_last_comments($limit)
{
    $query = "SELECT b.`title` as blogPostTitle, b.`postId`, c.`title` as commentTitle, `commentDate`,`userName`
	    FROM `tiki_comments` c, `tiki_blog_posts` b
	    WHERE `objectType`='post' AND b.`postId`=c.`object`
	    ORDER BY `commentDate` desc";
    global $tikilib;
    global $user;
    $result = $tikilib->query($query, array(), $limit, 0);
    $ret = array();

    while ($res = $result->fetchRow())
    {
      //WYSIWYCA hack: the $limit will not be respected
      if($tikilib->user_has_perm_on_object($user,$res["postId"],'post','tiki_p_read_blog')) {
        $aux["blogPostTitle"] = $res["blogPostTitle"];
	$aux["postId"] = $res["postId"];
        $aux["commentTitle"]= $res["commentTitle"];
        $aux["commentDate"] = $res["commentDate"];
        $aux["user"] = $res["userName"];
        $ret[] = $aux;
      }
    }
    return $ret;
}
}

$comments = blog_last_comments($module_rows);
$smarty->assign('comments', $comments);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('module_rows', $module_rows);
$smarty->assign('moretooltips', isset($module_params["moretooltips"]) ? $module_params["moretooltips"] : 'n');

?>
