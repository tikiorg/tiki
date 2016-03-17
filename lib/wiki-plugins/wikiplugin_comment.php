<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_comment_info()
{
	return array(
		'name' => tra('Comment'),
		'documentation' => 'PluginComment',
		'description' => tra('Display a comment area for a specified object'),
		'prefs' => array( 'wikiplugin_comment' ),
		'format' => 'html',
		'iconname' => 'comment',
		'introduced' => 8,
		'params' => array(
			'objectType' => array(
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Object type the comment is associated with'),
				'since' => '8.0',
				'filter' => 'text',
				'options' => array(
					array('text' => tr('Tracker Item'), 'value' => 'trackeritem'),
					array('text' => tr('Image Gallery'), 'value' => 'image gallery'),
					array('text' => tr('Image'), 'value' => 'image'),
					array('text' => tr('File Gallery'), 'value' => 'file gallery'),
					array('text' => tr('File'), 'value' => 'file'),
					array('text' => tr('Article'), 'value' => 'article'),
					array('text' => tr('Submission'), 'value' => 'submission'),
					array('text' => tr('Forum'), 'value' => 'forum'),
					array('text' => tr('Blog'), 'value' => 'blog'),
					array('text' => tr('Blog Post'), 'value' => 'blog post'),
					array('text' => tr('Wiki Page'), 'value' => 'wiki page'),
					array('text' => tr('History'), 'value' => 'history'),
					array('text' => tr('FAQ'), 'value' => 'faq'),
					array('text' => tr('Survey'), 'value' => 'survey'),
					array('text' => tr('Newsletter'), 'value' => 'newsletter'),
				),
				'default' => tr('wiki page'),
			),
			'objectId' => array(
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('Object ID'),
				'since' => '8.0',
				'filter' => 'digits',
				'default' => tr('The current wiki page to which you have added the plugin'),
				'profile_reference' => 'type_in_param',
			),
		)
	);
}

function wikiplugin_comment($data, $params)
{
	global $page;
	$smarty = TikiLib::lib('smarty');
	$params = array_merge(
		array(
			"objectId"=> $page,
			"objectType"=> "wiki page"
		),
		$params
	);

	$smarty->assign('wikiplugin_comment_objectId', $params['objectId']);
	$smarty->assign('wikiplugin_comment_objectType', $params['objectType']);
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_comment.tpl');
	return $ret;
}
