<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_comment_info()
{
	return [
		'name' => tra('Comment'),
		'documentation' => 'PluginComment',
		'description' => tra('Display a comment area for a specified object'),
		'prefs' => [ 'wikiplugin_comment' ],
		'format' => 'html',
		'iconname' => 'comment',
		'introduced' => 8,
		'params' => [
			'objectType' => [
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Object type the comment is associated with'),
				'since' => '8.0',
				'filter' => 'text',
				'options' => [
					['text' => tr('Tracker Item'), 'value' => 'trackeritem'],
					['text' => tr('Image Gallery'), 'value' => 'image gallery'],
					['text' => tr('Image'), 'value' => 'image'],
					['text' => tr('File Gallery'), 'value' => 'file gallery'],
					['text' => tr('File'), 'value' => 'file'],
					['text' => tr('Article'), 'value' => 'article'],
					['text' => tr('Submission'), 'value' => 'submission'],
					['text' => tr('Forum'), 'value' => 'forum'],
					['text' => tr('Blog'), 'value' => 'blog'],
					['text' => tr('Blog Post'), 'value' => 'blog post'],
					['text' => tr('Wiki Page'), 'value' => 'wiki page'],
					['text' => tr('History'), 'value' => 'history'],
					['text' => tr('FAQ'), 'value' => 'faq'],
					['text' => tr('Survey'), 'value' => 'survey'],
					['text' => tr('Newsletter'), 'value' => 'newsletter'],
				],
				'default' => tr('wiki page'),
			],
			'objectId' => [
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('Object ID'),
				'since' => '8.0',
				'filter' => 'digits',
				'default' => tr('The current wiki page to which you have added the plugin'),
				'profile_reference' => 'type_in_param',
			],
		]
	];
}

function wikiplugin_comment($data, $params)
{
	global $page;
	$smarty = TikiLib::lib('smarty');
	$params = array_merge(
		[
			"objectId" => $page,
			"objectType" => "wiki page"
		],
		$params
	);

	$smarty->assign('wikiplugin_comment_objectId', $params['objectId']);
	$smarty->assign('wikiplugin_comment_objectType', $params['objectType']);
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_comment.tpl');
	return $ret;
}
