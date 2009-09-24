<?php

// Includes an article field
// Usage:
// {BLOGLIST(Id=>blogId)}{BLOGLIST}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.
function wikiplugin_bloglist_help() {
	return tra("Use BLOGLIST to include posts from a blog. Syntax is").":<br />~np~{BLOGLIST(Id=n, Items=n)}{BLOGLIST}~/np~<br /> " . tra("where Id is the blog Id and Items is the max number of posts to display"). "<br />" . tra("Ex: ~np~{BLOGLIST(Id=2, Items=15)}{BLOGLIST}~/np~");
}

function wikiplugin_bloglist_info() {
	return array(
		'name' => tra('Blog List'),
		'documentation' => 'PluginBlogList',		
		'description' => tra('Use BLOGLIST to include posts from a blog.'),
		'prefs' => array( 'feature_blogs', 'wikiplugin_bloglist' ),
		'params' => array(
			'Id' => array(
				'required' => true,
				'name' => tra('Blog ID'),
				'description' => tra('Numeric value'),
			),
			'Items' => array(
				'required' => false,
				'name' => tra('Items'),
				'description' => tra('Maximum amount of entries to list.'),
			),
			'author' => array(
				'required' => false,
				'name' => tra('Author'),
				'description' => tra('Author'),
			),
				
		),
	);
}

function wikiplugin_bloglist($data, $params) {
	global $tikilib, $smarty;

	if (!isset($params['Id'])) {
		$text = ("<b>missing blog Id for BLOGLIST plugins</b><br />");
		$text .= wikiplugin_bloglist_help();
		return $text;
	}

	if (!isset($params['max'])) $params['max'] = -1;
	if (!isset($params['offset'])) $params['offset'] = 0;
	if (!isset($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
	if (!isset($params['find'])) $params['find'] = '';
	if (!isset($params['author'])) $params['author'] = '';

	$blogItems = $tikilib->list_posts($params['offset'], $params['max'], $params['sort_mode'], $params['find'], $params['Id'], $params['author']);
	$smarty->assign_by_ref('blogItems', $blogItems['data']);
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_bloglist.tpl');
	return '~np~'.$ret.'~/np~';
}
