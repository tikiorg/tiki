<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_ftp_help() {
	$help = tra('Download box for a file on ftp server.');
	$help .= "~np~{FTP(server=, user=, password=, title=)}file{FTP}~/np~";
	return $help;
}

function wikiplugin_ftp_info() {
	return array(
		'name' => tra('FTP'),
		'documentation' => tra('PluginFTP'),
		'description' => tra('Download box for a file on an FTP server.'),
		'prefs' => array( 'wikiplugin_ftp' ),
		'validate' => 'all',
		'body' => tra('file name'),
		'params' => array(
			'server' => array(
				'required' => true,
				'name' => tra('Server Name'),
				'description' => tra('Name of the server where the FTP account is housed. Example: ') . 'ftp.myserver.com',
				'default' => ''
			),
			'user' => array(
				'required' => true,
				'name' => tra('User Name'),
				'description' => tra('User name needed to access the FTP account'),
				'default' => ''
			),
			'password' =>array(
				'required' => true,
				'name' => tra('Password'),
				'description' => tra('Password needed to access the FTP account'),
				'default' => ''
			),
			'title' =>array(
				'required' => false,
				'name' => tra('Download Button Label'),
				'description' => tra('Label for the FTP download button'),
				'default' => ''
			)
		),
	);
}

function wikiplugin_ftp($data, $params) {
	global $smarty;
	extract ($params,EXTR_SKIP);
	if (empty($server) || empty($user) || empty($password)) {
		return tra('missing parameters');
	}
	if (!empty($_REQUEST['ftp_download']) && $_REQUEST['file'] == $data) {
		if (!($conn_id = ftp_connect($server))) {
			ftp_close($conn_id);
			return tra('Connection failed');
		}
		if (!($login_result = ftp_login($conn_id, $user, $password))) {
			ftp_close($conn_id);
			return tra('Incorrect param');
		}
		$local = "temp/$data";
		if (!ftp_get($conn_id, $local, $data, FTP_BINARY)) {
			ftp_close($conn_id);
			return tra('failed');
		}
		ftp_close($conn_id);
		$content = file_get_contents($local);
		$type = filetype($local);
		unlink($local);
		header ("Content-type: $type");
		header("Content-Disposition: attachment; filename=\"$data\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		echo "$content";
		die;

	} else {
		if (isset($title)) {
			$smarty->assign_by_ref('title', $title);
		}
		$smarty->assign_by_ref('file', $data);
		return $smarty->fetch('wiki-plugins/wikiplugin_ftp.tpl');
	}
}
