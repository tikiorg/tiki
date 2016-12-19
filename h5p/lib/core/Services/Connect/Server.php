<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Connect_Server
{
	private $connectlib;

	function setUp()
	{
		global $prefs;

		if ($prefs['connect_feature'] !== 'y') {
			throw new Services_Exception(tr('Connect Feature disabled'), 403);
		}
		if ($prefs['connect_server_mode'] !== 'y') {
			throw new Services_Exception(tr('Connect server mode disabled'), 403);
		}
		$this->connectlib = TikiLib::lib('connect_server');
	}

	function action_new($input) 
	{
		$rdata = array();

		$caplib = $this->getCaptcha();
		$capkey = $caplib->generate();

		$status = 'pending';
		$guid = uniqid(rand(), true);

		$captcha = strip_tags($caplib->render());

		$this->connectlib->recordConnection($status, $guid, $caplib->captcha->getWord(), true);	// save the catcha id as the data
		// temporary fix for now, save the captcha word in there - validate doesn't seem to keep the session in this context

		// send back confirm message
		$rdata['status'] = $status;
		$rdata['message'] = tr('Please confirm that you want to participate in Tiki Connect') . "\n" . $captcha;
		$rdata['guid'] = $guid;
		
		//$rdata['debug']['capkey'] = $capkey;
		//$rdata['debug']['caplib'] = serialize($caplib);
		return $rdata;
	}

	function action_confirm($input) 
	{
		$rdata = array();


		$connectData = $input->connect_data->xss();

		if (!empty($connectData)) {
			$caplib = $this->getCaptcha();
			
			$capword = $this->connectlib->isPendingGuid($connectData['guid']);
			//$valid = $caplib->validate(array('captcha' => array('input' => $connectData['captcha'], 'id' => $capkey)));
			// $caplib->validate never seems to validate here
			
			$valid = !empty($capword) && $connectData['captcha'] === $capword;
			if ($valid) {
				if (!empty($capword)) {

					$guid = $connectData['guid'];
					$this->connectlib->removeGuid($guid, true);
					$status = 'confirmed';
					$this->connectlib->recordConnection($status, $guid, '', true);

					// send back welcome message
					$rdata['status'] = $status;
					$rdata['message'] = tra('Welcome to Tiki Connect, please click "Send Info" when you want to make a connection.');
					$rdata['guid'] = $guid;

				} else {
					$rdata['status'] = 'error';
					$rdata['message'] = tra('There was a problem at the server (Tiki Connect is still experimental).');
				}
			} else {
				$this->connectlib->removeGuid($connectData['guid'], true);
				$status = 'error';
				$message = tra('CAPTCHA code problem.') . "\n" . $caplib->getErrors();
				$this->connectlib->recordConnection($status, $connectData['guid'], $message, true);
				$rdata['status'] = $status;
				$rdata['message'] = $message;
				//$rdata['debug']['capkey'] = $capkey;
				//$rdata['debug']['caplib'] = serialize($caplib);
			}
		}
		return $rdata;

	}

	function action_receive($input) 
	{
		$rdata = array();

		$connectData = $input->connect_data->xss();
		if (!empty($connectData)) {

			$guid = $connectData['guid'];

			if ($this->connectlib->isConfirmedGuid($guid)) {
				$status = 'received';

				$this->connectlib->recordConnection($status, $guid, $connectData, true);

				$rdata = array(
					'status' => $status,
					'message' => tra('Connect data received, thanks'),
				);
			} else {	// guid not recorded here
				$status = 'error';
				$message = tra('Your Tiki site is not registered here yet. Please try again.');
				$this->connectlib->recordConnection($status, $guid, $message, true);
				$rdata = array(
					'status' => $status,
					'newguid' => uniqid(rand(), true),
					'message' => $message,
				);
			}
		}
		return $rdata;
	}

	function action_cancel($input) 
	{

		$connectData = $input->connect_data->xss();
		$guid = $connectData['guid'];
		$isPending = $this->connectlib->isPendingGuid($guid);
		
		if ($guid && !empty($isPending)) {
			$this->connectlib->removeGuid($guid, true);
		}
		return $guid . ' "' . $isPending . '"';
	}
	
	private function getCaptcha()
	{
		$captchalib = TikiLib::lib('captcha');
		$caplib = new Captcha('dumb');
		$caplib->captcha->setKeepSession(true)->setUseNumbers(false)->setWordlen(5);
		return $caplib;
	}

}

