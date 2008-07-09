<?php

function wikiplugin_posts_help() {
        $help = tra("Includes blog posts listing into a wiki page");
        $help .= "<br />";
        $help .= tra("~np~{POSTS(max=>5, blogId=>id)}{POSTS}~/np~");

        return $help;
}

function wikiplugin_posts($data,$params) {
	global $smarty, $prefs, $tiki_p_read_blog, $tikilib;
	include_once ('lib/blogs/bloglib.php');
	extract($params,EXTR_SKIP);
	if (($prefs['feature_blogs'] !=  'y') || ($tiki_p_read_blog != 'y')) {
		// the feature is disabled or the user can't read blogs
		return('');
	}
	if (!isset($blogId)) {
	    return tra('blogId is mandatory');
	}
	if (!isset($max))
	    $max='5';

	$blog = $tikilib->get_blog($blogId);
	$posts = $bloglib->list_blog_posts($blogId, '', $max);
	$smarty->assign('blog_title', $blog['title']);
	$smarty->assign('posts', $posts['data']);
	return "~np~ ".$smarty->fetch('tiki-list_blog_posts.tpl')." ~/np~";
}
?>
