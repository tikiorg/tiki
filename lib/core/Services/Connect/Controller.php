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
					$connectlib->recordConnection($data['status'], $data['guid']);
				}

			} else {
				$data = $controller->receive( array(
					'connect_data' => array(
					'cmd' => 'confirm',
					'guid' => $pending,
					'captcha' => $input->captcha->filter(),
				)));

				if ($data && !empty($data['guid']) && $data['status'] === 'confirmed') {
					if ($data['guid'] === $pending) {
						$tikilib->set_preference('connect_guid', $pending);
						$connectlib->recordConnection($data['status'], $pending);
					}
				} else {
					$connectlib->removeGuid($pending);
					$data = array(
						'status' => 'error',
						'message' => empty($data['message']) ? tra('Something went wrong. Tiki Connect is still experimental. Please try again.') : $data['message'],
					);
				}
			}

		} else if ($prefs['connect_guid'] === $confirmedGuid) {
			$odata = $connectlib->buildConnectData();	//$this->action_list();
			//$diffdata = $connectlib->diffDataWithLastSent($odata);	// maybe later

			$odata['cmd'] = 'send';
			$odata['guid'] = $prefs['connect_guid'];


			$data = $controller->receive( array( 'connect_data' => $odata ));

			if ($data && $data['status'] === 'received') {
				$status = 'sent';
				$connectlib->recordConnection($status, $prefs['connect_guid'], $odata);
			} else {
				$connectlib->removeGuid($confirmedGuid);
				$tikilib->set_preference('connect_guid', '');
			}
		}
		return $data;
	}

	function action_receive($input) {
		include_once 'lib/core/TikiConnect.php';
		$connectlib = new TikiConnect();
		$rdata = array( 'debug' => array(
			'input' => $input,		// for testing only
			'server' => $_SERVER,
			'post' => $_POST,
			'get' => $_GET,
		));

		if (!empty($_POST['connect_data'])) {
			$connectData = $_POST['connect_data'];
			$connectData['cmd'] = isset($connectData['cmd']) ? $connectData['cmd'] : '';

			require_once('lib/captcha/captchalib.php');
			$caplib = new Captcha('dumb');
			$caplib->captcha->setKeepSession(true)->setUseNumbers(false)->setWordlen(4);
			$capkey = $caplib->generate();

			if ($connectData['cmd'] === 'new') {

				$status = 'pending';
				$guid = uniqid(rand(), true);

				$captcha = strip_tags($caplib->render());

				$connectlib->recordConnection($status, $guid, $caplib->captcha->getWord(), true);	// save the catcha id as the data
				// temporary fix for now, save the captcha word in there - validate doesn't seem to keep the session in this context

				// send back confirm message
				$rdata = array(
					'status' => $status,
					'message' => tr('Please confirm you want to participate in Tiki Connect' . "\n" . $captcha),
					'guid' => $guid,
				);
				//$rdata['debug']['capkey'] = $capkey;
				//$rdata['debug']['caplib'] = serialize($caplib);

			} else if ($connectData['cmd'] === 'confirm' && !empty($connectData['guid']) && !empty($connectData['captcha'])) {

				$capkey = $connectlib->isPendingGuid($connectData['guid']);
				$valid = $caplib->validate(array('captcha' => array('input' => $connectData['captcha'], 'id' => $capkey)));
				// $caplib->validate never seems to validate here
				$valid = $connectData['captcha'] === $capkey;
				if ($valid) {
					if (!empty($capkey)) {

						$status = 'confirmed';
						$guid = $connectData['guid'];

						$connectlib->recordConnection($status, $guid, null, true);

						// send back welcome message
						$rdata = array(
							'status' => $status,
							'message' => tra('Welcome to Tiki Connect'),
							'guid' => $guid,
						);
					} else {
						$rdata['status'] = 'error';
						$rdata['message'] = tra('Something went wrong on the server. Tiki Connect is still experimental.');
					}
				} else {
					$connectlib->removeGuid($connectData['guid'], true);
					$rdata['status'] = 'error';
					$rdata['message'] = tra('Captcha code problem.') . "\n" . $caplib->getErrors();
					//$rdata['debug']['capkey'] = $capkey;
					//$rdata['debug']['caplib'] = serialize($caplib);
				}


			} else if ($connectData['cmd'] === 'send' && !empty($connectData)) {

				$guid = $connectData['guid'];

				if ($connectlib->isConfirmedGuid($guid)) {
					$status = 'received';

					$connectlib->recordConnection($status, $guid, $connectData, true);

					$rdata = array(
						'status' => $status,
						'message' => tra('Connect data received, thanks'),
					);
				} else {	// guid not recorded here
					$status = 'error';
					$connectlib->recordConnection($status, $guid, null, true);
					$rdata = array(
						'status' => $status,
						'newguid' => uniqid(rand(), true),
						'message' => tra('Your Tiki is not registered here yet, please try again.'),
					);
				}
			}
		}
		return $rdata;
	}
}

