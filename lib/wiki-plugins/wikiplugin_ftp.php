<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_ftp_info()
{
	return array(
		'name' => tra('FTP'),
		'documentation' => 'PluginFTP',
		'description' => tra('Create a button for downloading a file from an FTP server'),
		'prefs' => array( 'wikiplugin_ftp' ),
		'validate' => 'all',
		'body' => tra('File name on the server'),
		'iconname' => 'upload',
		'introduced' => 3,
		'params' => array(
			'server' => array(
				'required' => true,
				'name' => tra('Server Name'),
				'description' => tra('Name of the server for the FTP account. Example: ')
					. '<code>ftp.myserver.com</code>',
				'since' => '3.0',
				'filter' => 'text',
				'default' => ''
			),
			'user' => array(
				'required' => true,
				'name' => tra('Username'),
				'description' => tra('Username for the FTP account'),
				'since' => '3.0',
				'filter' => 'username',
				'default' => ''
			),
			'password' =>array(
				'required' => true,
				'name' => tra('Password'),
				'description' => tra('Password for the FTP account'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => ''
			),
			'title' =>array(
				'required' => false,
				'name' => tra('Download Button Label'),
				'description' => tra('Label for the FTP download button'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => ''
			)
		),
	);
}

function wikiplugin_ftp($data, $params)
{
	extract($params, EXTR_SKIP);
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
		header("Content-type: $type");
		header("Content-Disposition: attachment; filename=\"$data\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		echo "$content";
		die;

	} else {
		$smarty = TikiLib::lib('smarty');
		if (isset($title)) {
			$smarty->assign('ftptitle', $title);
		}
		$smarty->assign_by_ref('file', $data);
		return $smarty->fetch('wiki-plugins/wikiplugin_ftp.tpl');
	}
}
