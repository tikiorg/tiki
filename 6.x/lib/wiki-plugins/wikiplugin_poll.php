<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Includes a poll
// Usage:
// {POLL(pollId=>1)}Title{POLL}

function wikiplugin_poll_help() {
	$help = tra("Displays the output of a poll, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{POLL(pollId=>1)}Good Poll{POLL}~/np~";
	return $help;
}

function wikiplugin_poll_info() {
	return array(
		'name' => tra('Poll'),
		'documentation' => tra('PluginPoll'),
		'description' => tra('Displays the output of a poll, fields are indicated with numeric ids.'),
		'prefs' => array( 'feature_polls', 'wikiplugin_poll' ),
		'body' => tra('Title'),
		'params' => array(
			'pollId' => array(
				'required' => true,
				'name' => tra('Poll'),
				'description' => tra('Numeric value representing the poll ID'),
				'default' => ''
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show poll title (shown by default).'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			)
		)
	);
}

function wikiplugin_poll($data, $params) {
	global $smarty, $polllib, $trklib, $tikilib, $dbTiki, $userlib, $tiki_p_admin, $prefs, $_REQUEST, $user;
	$default = array('showtitle' => 'y');
	$params = array_merge($default, $params);

	extract ($params,EXTR_SKIP);

	if (!isset($pollId)) {
	    $smarty->assign('msg', tra("missing poll ID for plugin POLL"));
	    return $smarty->fetch("error_simple.tpl");
	} else {
	    include_once ('lib/polls/polllib.php');


	    $poll_info = $polllib->get_poll($pollId);
	    $options = $polllib->list_poll_options($pollId);

	    $smarty->assign_by_ref('menu_info', $poll_info);
	    $smarty->assign_by_ref('channels', $options);
	    $smarty->assign_by_ref('poll_title', $data);
		$smarty->assign_by_ref('showtitle', $showtitle);
	    $smarty->assign('ownurl', $tikilib->httpPrefix(). $_SERVER["REQUEST_URI"]);

	    ask_ticket('poll-form');

	    // Display the template
	    return '~np~'.$smarty->fetch("tiki-plugin_poll.tpl").'~/np~';
	}
}
