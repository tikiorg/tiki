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
		global $page;
		$smarty = TikiLib::lib('smarty');

		$area = $input->data->area();

		$cookies = '';
		foreach (array_keys($_COOKIE) as $cookieName) {
			$cookies .= bin2hex($cookieName) . '=' . bin2hex($_COOKIE[$cookieName]) . ';';
		}


		$smarty->assign('doku_base', '');
		$smarty->assign('sectok', '');
		$smarty->assign('cookies', $cookies);
		//$smarty->assign('base_host', '');
		$smarty->assign('page', $page);
		$smarty->assign('edit_area', $area);
		$smarty->assign('authtok', '');

		return array();
	}

}

