<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

function wikiplugin_article_info() {
	return array(
		'name' => tra('Article'),
		'documentation' => 'PluginArticle',
		'description' => tra('Display a field of an article'),
		'prefs' => array( 'feature_articles', 'wikiplugin_article' ),
		'icon' => 'pics/icons/layout_content.png',
		'params' => array(
			'Field' => array(
				'required' => false,
				'name' => tra('Field'),
				'description' => tra('The article field to display. Default field is Heading.'),
				'filter' => 'word',
				'default' => 'heading'
			),
			'Id' => array(
				'required' => false,
				'name' => tra('Article ID'),
				'description' => tra('The article to display. If no value is provided, most recent article will be used.'),
				'filter' => 'digits',
				'default' => ''
			),
		),
	);
}

function wikiplugin_article($data, $params) {
	global $tikilib,$user,$userlib,$tiki_p_admin_cms;
	global $statslib; include_once('lib/stats/statslib.php');

	extract ($params,EXTR_SKIP);

	if (empty($Id)) {
		global $artlib;	include_once('lib/articles/artlib.php');

		$Id = $artlib->get_most_recent_article_id();
	}
	if (!isset($Field)) {
		$Field = 'heading';
	} 

	if ($tiki_p_admin_cms == 'y' || $tikilib->user_has_perm_on_object($user, $Id, 'article', 'tiki_p_edit_article') || (isset($article_data) && $article_data["author"] == $user && $article_data["creator_edit"] == 'y')) {
		$add="&nbsp;<a href='tiki-edit_article.php?articleId=$Id' class='editplugin'><img src='pics/icons/page_edit.png' style='border:none' /></a>";
	} else {
		$add="";
	}

	global $artlib; require_once 'lib/articles/artlib.php';
	$article_data = $artlib->get_article($Id);
	if (isset($article_data[$Field])) {
		return $article_data[$Field].$add;
	}
}
