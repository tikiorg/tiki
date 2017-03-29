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
		'format' => 'html',
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
				'filter' => 'text',
				'default' => '',
			),
			'source_url' => array(
				'required' => false,
				'name' => tra('Source URL'),
				'description' => tra('The URL to the source quoted.'),
				'since' => '16',
				'filter' => 'text',
				'default' => '',
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('Date when the statement quoted was made') . ' (<code>' . tr('YYYY-MM-DD') . '</code>)',
				'filter' => 'date',
				'default' => '',
				'since' => '16',
			),
			'parse' => array(
				'required' => false,
				'name' => tra('Parse Quote'),
				'description' => tra('Parse quote contents'),
				'options' => array(
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'filter' => 'digits',
				'default' => '1',
				'since' => '16',
			)
		),
	);
}

function wikiplugin_quote($data, $params)
{
	global $smarty, $tikilib;

	$source_url = '';
	$date = null;

	if ($params['thread_id']) {
		$commentslib = TikiLib::lib('comments');
		$comment_info = $commentslib->get_comment($params['thread_id']);
		$replyto = $comment_info['userName'];
	} elseif ($params['replyto']) {
		$replyto = $params['replyto'];
	}
	if ($params['source_url']) {
		$source_url = $params['source_url'];
	}
	if ($params['date']) {
		$date = strtotime($params['date']);
	}
	if ($params['parse'] === "0") {
		$data = trim($data);
	} else {
		$data = $tikilib->parse_data(trim($data));
	}

	$smarty->assign('date', $date);
	$smarty->assign('comment_info', $comment_info);
	$smarty->assign('replyto', $replyto);
	$smarty->assign('data', $data);
	$smarty->assign('source_url', trim($source_url));

	return $smarty->fetch("wiki-plugins/wikiplugin_quote.tpl");
}
