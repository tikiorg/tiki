<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_JCapture_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_jcapture'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}
	}

	function action_capture($input)
	{
		global $page, $base_host, $url_scheme, $url_path;
		$smarty = TikiLib::lib('smarty');

		$area = $input->data->area();

		$cookies = '';
		foreach (array_keys($_COOKIE) as $cookieName) {
			$cookies .= bin2hex($cookieName) . '=' . bin2hex($_COOKIE[$cookieName]) . ';';
		}

		if (0) {	// test for IE one day
			$smarty->assign('ie_applet_attrs', 'classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" codebase="http://java.sun.com/update/1.6.0/jinstall-6u22-windows-i586.cab"');
		}


		$smarty->assign('doku_base', bin2hex($url_path));
		$smarty->assign('sectok', 'TODO');
		$smarty->assign('cookies', $cookies);
		$smarty->assign('host', $url_scheme === 'http' ? $base_host . ':80' : $base_host);
		$smarty->assign('page', $page);
		$smarty->assign('edit_area', $area);
		$smarty->assign('authtok', '');			// unused?

		return array();
	}

	function action_upload($input) {
		global $user, $tiki_p_upload_files;

		if (!$user) {
			//throw new Services_Exception_NotAvailable(print_r($input, true));
			throw new Services_Exception_NotAvailable(tr('Not logged in'));
		}

		$fileController = new Services_File_Controller();
		$ret = $fileController->action_upload($input);

		return $ret;
	}

}

/* some temporary debugging info (for java dev)

	dokuBase: 2f74696b692f7472756e6b2f
	host: http://localhost
	sectok: 1234
	cookies: 1234
	page: HomePage
	edid: editwiki

 */
