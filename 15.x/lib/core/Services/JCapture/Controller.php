<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once('lib/auth/tokens.php');

class Services_JCapture_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_jcapture'] !== 'y') {
			throw new Services_Exception_Disabled(tr('feature_jcapture'));
		}
		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}
		if ($prefs['auth_token_access'] !== 'y') {
			throw new Services_Exception_Disabled(tr('auth_token_access'));
		}
	}

	function action_capture($input)
	{
		global $base_host, $prefs, $user, $tikiroot;
		$smarty = TikiLib::lib('smarty');

		$area = $input->area->text();
		$page = $input->page->text();
		$page = urldecode($page);
		$page = TikiLib::lib('tiki')->take_away_accent($page);

		/* Perform suggested seperator substitutions. */
		$page = TikiLib::lib('tiki')->substituteSeparators($page);

		$uploader = $tikiroot . 'tiki-ajax_services.php';

		$tokenlib = AuthTokens::build($prefs);
		$groups = TikiLib::lib('user')->get_user_groups($user);
		$parameters = array('user' => $user, 'controller' => 'jcapture','action' => 'upload');
		$token = $tokenlib->createToken($uploader, $parameters, $groups, array('hits' => 1));
		$parameters['TOKEN'] = $token;
		$uploader = $base_host . $uploader . '?' . http_build_query($parameters, '', '&');	// NB the "entry" url for createToken has to be without base_host

		$smarty->assign('page', $page);
		$smarty->assign('edit_area', $area);
		$smarty->assign('uploader', $uploader);

		return array();
	}

	function action_upload($input)
	{
		global $prefs, $is_token_access, $detailtoken;

		if (!$is_token_access) {
			throw new Services_Exception_NotAvailable(tr('Not authorized'));
		}

		$fileController = new Services_File_Controller();

		if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$input->offsetSet('size', $_FILES['Filedata']['size']);
			$input->offsetSet('name', $_FILES['Filedata']['name']);
			$input->offsetSet('type', $_FILES['Filedata']['type']);
			$input->offsetSet('galleryId', $prefs['fgal_for_jcapture']);
			$input->offsetSet('data', base64_encode(file_get_contents($_FILES['Filedata']['tmp_name'])));
			$params = json_decode($detailtoken['parameters']);
			if ($params && isset($params->user)) {
				$input->offsetSet('user', $params->user);
			}

			$ret = $fileController->action_upload($input);
		} else {
			$ret = array();
		}

		return $ret;
	}

}

/* some temporary debugging info (for java dev)

	dokuBase: 2f74696b692f7472756e6b2f
	host: http://localhost
	sectok: 1234
	cookies: 1234
	pageName: HomePage
	edid: editwiki

 */
