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
if (!function_exists("blog_last_comments")) {
function blog_last_comments($limit)
{
    $query = "SELECT b.`title` as blogPostTitle, b.`postId`, c.`threadId`, c.`title` as commentTitle, `commentDate`,`userName`
	    FROM `tiki_comments` c, `tiki_blog_posts` b
	    WHERE `objectType`='post' AND b.`postId`=c.`object`
	    ORDER BY `commentDate` desc";

    global $bloglib;
	include_once ('lib/blogs/bloglib.php');

    global $tikilib;
    global $user;
    $result = $tikilib->query($query, array(), $limit, 0);
    $ret = array();

    while ($res = $result->fetchRow())
    {
      //WYSIWYCA hack: the $limit will not be respected
      if ($tikilib->user_has_perm_on_object($user,$res["postId"],'post','tiki_p_read_blog')) {
		/// check if the blog post is marked private
		$priv = '';
		if ($res2 = $bloglib->get_post($res['postId'])) {
			$priv = $res2['priv'];
		}
		if ($priv != 'y' || ($user && $user == $res2["user"]) || $tiki_p_blog_admin == 'y') {
			$aux["blogPostTitle"] = $res["blogPostTitle"];
			$aux["postId"] = $res["postId"];
			$aux["threadId"] = $res["threadId"];
			$aux["commentTitle"]= $res["commentTitle"];
			$aux["commentDate"] = $res["commentDate"];
			$aux["user"] = $res["userName"];
			$ret[] = $aux;
		}
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
