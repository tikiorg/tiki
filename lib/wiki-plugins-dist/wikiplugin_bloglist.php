<?php

// Includes an article field
// Usage:
// {BLOGLIST(Id=>blogId)}{BLOGLIST}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.
function wikiplugin_bloglist_help() {
	return tra("Use BLOGLIST to include posts from a blog. Syntax is").":<br />~np~{BLOGLIST(Id=n, Items=n)}{BLOGLIST}~/np~<br />" . tra(" where Id is the blog Id and Items is the max number of posts to display"). "<br />" . tra("Ex: ~np~{BLOGLIST(Id=2, Items=15)}{BLOGLIST}~/np~");
}
function wikiplugin_bloglist($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);

	if (!isset($Id)) {
		$text = ("<b>missing blog Id for BLOGLIST plugins</b><br />");
		$text .= wikiplugin_bloglist_help();
		return $text;
	}
//	if (!isset($Field)) {
//		$Field = 'heading';
//	}
	$text="<div class=\"blogtools\"><table><tr><th>" . tra("Date") . "</th><th>" . tra("Title") . "</th><th>" . tra("Author") . "</th></tr>\n";
	$query = "select `postId`, `title`, `user`, `created`  from `tiki_blog_posts` where `blogId`=? order by `created` desc";
	$result = $tikilib->query($query, array($Id));
	$i=0;
    	while (($res = $result->fetchRow()) && ($i < $Items)) {
        	$text.="<tr><td>" . TikiLib::date_format("%d/%M/%Y %H:%M", $res["created"]) . "</td>";
		$text.="<td><a href=\"tiki-view_blog_post.php?blogId=" . $Id . "&postId=" . $res["postId"] . "\">" . $res["title"] . "</a></td>";
        	$text.= "<td>" . $res["user"] . "</td></tr>\n";
		$i++;
  	}
	$text.="</table></div>\n";


	return $text;
}

?>
