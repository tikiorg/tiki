<?php

// Includes an article field
// Usage:
// {ARTICLE(Id=>articleId, Field=>FieldName)}{ARTICLE}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.
function wikiplugin_article_help() {
	return tra("Include an article").":<br />~np~{ARTICLE(Id=> ,Field=>)}{ARTICLE}~/np~";
}
function wikiplugin_article($data, $params) {
	global $tikilib;

	extract ($params);

	if (!isset($Id)) {
		return ("<b>missing article ID for plugin ARTICLE</b><br/>");
	}
	if (!isset($Field)) {
		$Field = 'heading';
	}

	$article_data = $tikilib->get_article($Id);
	return $article_data[$Field];
}

?>
