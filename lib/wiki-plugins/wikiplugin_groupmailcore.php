<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display controls etc for GroupMail 
// Started: jonnybradley July 2009

function wikiplugin_groupmailcore_help() {
        return tra("Displays GroupMail functions on a wiki page").":<br />~np~{groupmail_core()}{groupmail}~/np~";
}

function wikiplugin_groupmailcore_info() {
	return array(
		'name' => tra('GroupMailCore'),
		'documentation' => tra('PluginGroupMailCore'),
		'description' => tra('Displays GroupMail functions on a wiki page. Usually set up using a plugin alias created by the GroupMail profile.'),
		'prefs' => array('wikiplugin_groupmailcore', 'feature_trackers'),
		//'extraparams' => true,
		'params' => array(
			'fromEmail' => array(
				'required' => true,
				'name' => tra('From Email'),
				'description' => tra('Email address to report.'),
				'default' => ''
			),
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker Id'),
				'description' => tra('Id of GroupMail Logs tracker (set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'fromFId' => array(
				'required' => true,
				'name' => tra('From Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'operatorFId' => array(
				'required' => true,
				'name' => tra('Operator Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'subjectFId' => array(
				'required' => true,
				'name' => tra('Subject Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'messageFId' => array(
				'required' => true,
				'name' => tra('Message Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'contentFId' => array(
				'required' => true,
				'name' => tra('Content Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'accountFId' => array(
				'required' => true,
				'name' => tra('Account Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
			'datetimeFId' => array(
				'required' => true,
				'name' => tra('Datetime Field Id'),
				'description' => tra('Id of GroupMail Logs tracker field (usually set up in alias by profile).'),
				'filter' => 'digits',
				'default' => ''
			),
		),
	);
}

function wikiplugin_groupmailcore($data, $params) {
	global $tikilib;
	require_once('lib/wiki-plugins/wikiplugin_trackerlist.php');
	
	$trackerparams = array();
	$trackerparams['trackerId'] = $params['trackerId'];
	$trackerparams['fields'] =  $params['fromFId'].':'.$params['operatorFId'].':'.$params['subjectFId'].':'.$params['datetimeFId'];
	$trackerparams['popup'] = $params['fromFId'].':'.$params['contentFId'];
	$trackerparams['filterfield'] = $params['fromFId'].':'.$params['accountFId'];
	$trackerparams['filtervalue'] = $params['fromEmail'].':'.$params['accountName'];
	$trackerparams['stickypopup'] = 'n';
	$trackerparams['showlinks'] ='y';
	$trackerparams['shownbitems'] ='n';
	$trackerparams['showinitials'] ='n';
	$trackerparams['showstatus'] ='n';
	$trackerparams['showcreated'] = 'n';
	$trackerparams['showlastmodif'] = 'n';
	
	$data = wikiplugin_trackerlist('', $trackerparams);
	
	return $data;
}
