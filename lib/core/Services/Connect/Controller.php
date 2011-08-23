<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Connect_Controller
{

	function setUp()
	{
		global $prefs;

		if ($prefs['connect_feature'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
	}

	function action_list($input = null)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved to administrators during development'), 403);
		}
		include_once 'lib/core/TikiConnect.php';
		$controller = new TikiConnect();
		$info = $controller->buildArray();

		return $info;
	}

	function action_send($input) {
		global $prefs;

		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved to administrators during development'), 403);
		}

		$controller = new Services_RemoteController($prefs['connect_server'], 'connect');

		$tikilib = TikiLib::lib('tiki');
		if (empty($prefs['connect_guid'])) {

			$data = $controller->receive( array( 'connect_data' => array( 'status' => 'new' ) ));

			if ($data && !empty($data['guid'])) {
				$tikilib->set_preference('connect_guid', $data['guid']);
			}

		} else {
			$odata = $this->action_list();

			$data = $controller->receive( array( 'connect_data' => $odata ));

			if ($data) {
				$tikilib->set_preference('connect_last_post', $tikilib->now);
			}
		}
		return $data;
	}

	function action_receive($input) {
		if (!empty($_POST['connect_data'])) {
			if (empty($_POST['connect_data']['prefs']['connect_guid'])) {
				// send back welcome message
				return array(
					'status' => 'pending',
					'message' => tra('Welcome to Tiki Connect'),
					'guid' => uniqid(rand(), true),
				);
			} else {
				// TODO check the guid is one of mine
				return array(
					'status' => 'received',
					'message' => tra('Connect data received, thanks'),
				);
			}
		}
		return array(
			'input' => $input,
			'server' => $_SERVER,
			'post' => $_POST,
			'get' => $_GET,
		);
	}
}

