<?php

// Includes an article field
// Usage:
// {ARTICLE(Id=>articleId, Field=>FieldName)}{ARTICLE}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.

function wikiplugin_article_help() {
        $help = tra("Includes an article");
        $help .="<br />";
        $help .= tra("~np~{ARTICLE(Field=>[,Id=>])}{ARTICLE}~/np~");
        $help .= "<br />";
        $help .= tra("Id is optional. If not given, last article is used. Default field is heading.");

        return $help;
}
function wikiplugin_article($data, $params) {
	global $tikilib,$user,$userlib;
	include_once('lib/stats/statslib.php');

	extract ($params,EXTR_SKIP);

	if (!isset($Id)) {
		global $artlib;	include_once('lib/articles/artlib.php');

		$last = $artlib->list_articles(0,1);
		$Id = $last['data'][0]["articleId"];
	}
	if (!isset($Field)) {
		$Field = 'heading';
	} 

	if ($tiki_p_admin_cms == 'y' || $tikilib->user_has_perm_on_object($user, $articleId, 'article', 'tiki_p_edit_article') || ($article_data["author"] == $user && $article_data["creator_edit"] == 'y')) {
	      $add="&nbsp;<a href='tiki-edit_article.php?articleId=$Id'><img src='/pics/icons/page_edit.png' style='border:0px;'></a>";
	} else {
	      $add="";
	}

	$article_data = $tikilib->get_article($Id);
	return $article_data[$Field].$add;
}

?>
