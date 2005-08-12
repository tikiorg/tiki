<?php

// Includes an article field
// Usage:
// {ARTICLE(Id=>articleId, Field=>FieldName)}{ARTICLE}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.
function wikiplugin_article_help() {
	return tra("Include an article").":<br />~np~{ARTICLE(Field=>[,Id=>])}{ARTICLE}<br />Id is optional. if not given, last article is used. default field is
	heading.~/np~";
}
function wikiplugin_article($data, $params) {
	global $tikilib;
	include_once('lib/stats/statslib.php');

	extract ($params,EXTR_SKIP);

	if (!isset($Id)) {
		global $artlib;
		if (!is_object($artlib)) {
			include "lib/articles/artlib.php";
		}
		$last = $artlib->list_articles(0,1);
		$Id = $last['data'][0]["articleId"];
	}
	if (!isset($Field)) {
		$Field = 'heading';
	} 

	$article_data = $tikilib->get_article($Id);
	return $article_data[$Field];
}

?>
