<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_quote_info()
{
	return array(
		'name' => tra('Quote'),
		'documentation' => 'PluginQuote',
		'description' => tra('Format text as a quote'),
		'prefs' => array( 'wikiplugin_quote' ),
		'body' => tra('Quoted text'),
		'iconname' => 'quotes',
		'introduced' => 1,
		'filter' => 'text',
		'tags' => array( 'basic' ),
		'url' => 'URL',
		'params' => array(
			'replyto' => array(
				'required' => false,
				'name' => tra('Reply To'),
				'description' => tra('Name of the quoted person.'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			),
			'thread_id' => array(
				'required' => false,
				'name' => tra('Thread Id for Forum replies'),
				'description' => tra('The thread Id of the comment being replied to in forums. Overwrites replyto'),
				'since' => '15',
				'filter' => '	text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_quote($data, $params, $url='')
{
	global $smarty;


	if ($params['thread_id']) {
		$commentslib = TikiLib::lib('comments');
		$comment_info = $commentslib->get_comment($params['thread_id']);
		$replyto = $comment_info['userName'];
	} elseif ($params['replyto']) {
		$replyto = $params['replyto'];
	}

	$smarty->assign('comment_info', $comment_info);
	$smarty->assign('replyto', $replyto);
	$smarty->assign('data', trim($data));
	$smarty->assign('url', trim($url));

	return $smarty->fetch("wiki-plugins/wikiplugin_quote.tpl");
}
