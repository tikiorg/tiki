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
		$connectlib = new TikiConnect();
		$info = $connectlib->buildConnectData();

		//$info = $connectlib->diffDataWithLastSent($info);
		return $info;
	}

	function action_send($input) {
		global $prefs;

		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved to administrators during development'), 403);
		}

		include_once 'lib/core/TikiConnect.php';
		$connectlib = new TikiConnect();

		$controller = new Services_RemoteController($prefs['connect_server'], 'connect');

		$tikilib = TikiLib::lib('tiki');
		$confirmedGuid = $connectlib->getConfirmedGuid();

		if ( empty($confirmedGuid) || empty($prefs['connect_guid'])) {	// not connected?

			$pending = $connectlib->getPendingGuid();

			if (empty($pending)) {
				$data = $controller->receive( array( 'connect_data' => array( 'cmd' => 'new' )));

				if ($data['status'] === 'pending' && !empty($data['guid'])) {
					$connectlib->recordConnection(null, $data['status'], $data['guid']);
				}

			} else {
				$data = $controller->receive( array( 'connect_data' => array( 'cmd' => 'confirm', 'guid' => $pending )));

				if ($data && !empty($data['guid']) && $data['status'] === 'confirmed') {
					if ($data['guid'] === $pending) {
						$tikilib->set_preference('connect_guid', $pending);
						$connectlib->recordConnection(null, $data['status'], $pending);
					}
				} else {
					$data = array(
						'status' => 'error',
						'message' => tra('Something went wrong. Tiki Connect is still experimental.'),
					);
				}
			}

		} else if ($prefs['connect_guid'] === $confirmedGuid) {
			$odata = $connectlib->buildConnectData();	//$this->action_list();
			//$diffdata = $connectlib->diffDataWithLastSent($odata);	// maybe later

			$odata['cmd'] = 'send';
			$odata['guid'] = $prefs['connect_guid'];


			$data = $controller->receive( array( 'connect_data' => $odata ));

			if ($data) {
				$status = 'sent';
				$connectlib->recordConnection($odata, $status, $prefs['connect_guid']);
			}
		}
		return $data;
	}

	function action_receive($input) {
		include_once 'lib/core/TikiConnect.php';
		$connectlib = new TikiConnect();
		$rdata = array(
//			'input' => $input,		// for testing only
//			'server' => $_SERVER,
//			'post' => $_POST,
//			'get' => $_GET,
		);

		if (!empty($_POST['connect_data'])) {
			$connectData = $_POST['connect_data'];
			$connectData['cmd'] = isset($connectData['cmd']) ? $connectData['cmd'] : '';
			
			if ($connectData['cmd'] === 'new') {

				$status = 'pending';
				$guid = uniqid(rand(), true);

				$connectlib->recordConnection(null, $status, $guid, true);

				// send back confirm message
				$rdata = array(
					'status' => $status,
					'message' => tra('Please confirm you want to participate in Tiki Connect'),
					'guid' => $guid,
				);

			} else if ($connectData['cmd'] === 'confirm' && !empty($connectData['guid'])) {

				if ($connectlib->isPendingGuid($connectData['guid'])) {
					
					$status = 'confirmed';
					$guid = $connectData['guid'];

					$connectlib->recordConnection(null, $status, $guid, true);

					// send back welcome message
					$rdata = array(
						'status' => $status,
						'message' => tra('Welcome to Tiki Connect'),
						'guid' => $guid,
					);
					
				} else {
					$rdata = array(
						'status' => 'error',
						'message' => tra('Something went wrong. Tiki Connect is still experimental.'),
					);
				}

			} else if ($connectData['cmd'] === 'send' && !empty($connectData)) {

				$guid = $connectData['guid'];

				if ($connectlib->isConfirmedGuid($guid)) {
					$status = 'received';

					$connectlib->recordConnection($connectData, $status, $guid, true);

					$rdata = array(
						'status' => $status,
						'message' => tra('Connect data received, thanks'),
					);
				}
			}
		}
		return $rdata;
	}
}

